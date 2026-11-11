<?php
require __DIR__ . '/../src/auth_page.php';

$title = 'Reports - TraceX';

$view = ($_GET['view'] ?? 'monthly') === 'yearly' ? 'yearly' : 'monthly';

$yearsStmt = $pdo->prepare("
    SELECT YEAR(month_year) AS y FROM incomes WHERE user_id = :user_id
    UNION
    SELECT YEAR(expense_date) FROM expenses WHERE user_id = :user_id
    ORDER BY y DESC
");
$yearsStmt->execute([':user_id' => $userId]);
$years = array_map('intval', $yearsStmt->fetchAll(PDO::FETCH_COLUMN));
$currentYear = (int) date('Y');
if (!in_array($currentYear, $years, true)) {
    array_unshift($years, $currentYear);
}

$year = (int) ($_GET['year'] ?? $currentYear);
if (!in_array($year, $years, true)) {
    $year = $currentYear;
}

// Monthly view covers one calendar year; yearly view covers all time.
$range = $view === 'monthly'
    ? [':start' => "{$year}-01-01", ':end' => ($year + 1) . '-01-01']
    : [':start' => '1000-01-01', ':end' => '9999-01-01'];
$groupBy = $view === 'monthly' ? 'MONTH' : 'YEAR';

$sumByPeriod = function (string $table, string $dateColumn) use ($pdo, $userId, $range, $groupBy): array {
    $stmt = $pdo->prepare("
        SELECT {$groupBy}({$dateColumn}) AS period, SUM(amount) AS total
        FROM {$table}
        WHERE user_id = :user_id AND {$dateColumn} >= :start AND {$dateColumn} < :end
        GROUP BY period
    ");
    $stmt->execute([':user_id' => $userId] + $range);

    return array_map('floatval', $stmt->fetchAll(PDO::FETCH_KEY_PAIR));
};

$incomeByPeriod = $sumByPeriod('incomes', 'month_year');
$expenseByPeriod = $sumByPeriod('expenses', 'expense_date');

$savingsRate = fn(float $income, float $expense): ?float => $income > 0 ? ($income - $expense) / $income * 100 : null;

$periods = $view === 'monthly' ? range(1, 12) : array_reverse($years);
$rows = [];
foreach ($periods as $period) {
    $income = $incomeByPeriod[$period] ?? 0.0;
    $expense = $expenseByPeriod[$period] ?? 0.0;
    $rows[] = [
        'label' => $view === 'monthly' ? date('M', mktime(0, 0, 0, $period, 1)) : (string) $period,
        'income' => $income,
        'expense' => $expense,
        'net' => $income - $expense,
        'rate' => $savingsRate($income, $expense),
    ];
}

$totalIncome = array_sum($incomeByPeriod);
$totalExpense = array_sum($expenseByPeriod);
$totalNet = $totalIncome - $totalExpense;
$totalRate = $savingsRate($totalIncome, $totalExpense);

$typeStmt = $pdo->prepare("
    SELECT t.name, t.color, SUM(i.amount) AS total
    FROM incomes i
    JOIN income_types t ON t.id = i.income_type_id
    WHERE i.user_id = :user_id AND i.month_year >= :start AND i.month_year < :end
    GROUP BY t.id, t.name, t.color
    ORDER BY total DESC
");
$typeStmt->execute([':user_id' => $userId] + $range);
$incomeByType = $typeStmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
include __DIR__ . '/../views/reports/report-view.php';
$content = ob_get_clean();
include __DIR__ . '/../views/components/layout.php';
