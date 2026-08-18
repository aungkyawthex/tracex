<?php
require __DIR__ . '/../src/auth_page.php';

$title = 'Dashboard - TraceX';
$currentMonthStart = date('Y-m-01');
$nextMonthStart = date('Y-m-01', strtotime('+1 month'));
$lastMonthStart = date('Y-m-01', strtotime('-1 month'));
$sixMonthStart = date('Y-m-01', strtotime('-5 months'));

$periodParams = [
  ':user_id' => $userId,
  ':current_start_sum' => $currentMonthStart,
  ':next_start_sum' => $nextMonthStart,
  ':last_start_sum' => $lastMonthStart,
  ':last_current_start_sum' => $currentMonthStart,
  ':range_start' => $lastMonthStart,
  ':range_end' => $nextMonthStart,
];

$getPeriodTotals = function (string $table, string $dateColumn, string $filter = '') use ($pdo, $periodParams): array {
  $stmt = $pdo->prepare("\n    SELECT\n      COALESCE(SUM(CASE WHEN {$dateColumn} >= :current_start_sum AND {$dateColumn} < :next_start_sum THEN amount ELSE 0 END), 0) AS current_total,\n      COALESCE(SUM(CASE WHEN {$dateColumn} >= :last_start_sum AND {$dateColumn} < :last_current_start_sum THEN amount ELSE 0 END), 0) AS last_total\n    FROM {$table}\n    WHERE user_id = :user_id\n      AND {$dateColumn} >= :range_start\n      AND {$dateColumn} < :range_end\n      {$filter}\n  ");
  $stmt->execute($periodParams);

  return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['current_total' => 0, 'last_total' => 0];
};

$getChange = function (float $current, float $previous): array {
  return [
    'isUp' => $current >= $previous,
    'percent' => $previous > 0 ? (($current - $previous) / $previous) * 100 : ($current > 0 ? 100 : 0),
  ];
};

$expenseTotals = $getPeriodTotals('expenses', 'expense_date');
$totalExpenses = (float) $expenseTotals['current_total'];
$lastMonthTotal = (float) $expenseTotals['last_total'];
$expenseChange = $getChange($totalExpenses, $lastMonthTotal);
$isUp = $expenseChange['isUp'];
$percent = $expenseChange['percent'];

$budgetTotals = $getPeriodTotals('budgets', 'month_year');
$monthlyBudgetTotal = (float) $budgetTotals['current_total'];
$lastMonthBudgetTotal = (float) $budgetTotals['last_total'];
$budgetChange = $getChange($monthlyBudgetTotal, $lastMonthBudgetTotal);
$budgetIsUp = $budgetChange['isUp'];
$budgetPercent = $budgetChange['percent'];

$savingsTotals = $getPeriodTotals('saving_transactions', 'created_at', "AND type = 'deposit'");
$monthlySavingsDeposits = (float) $savingsTotals['current_total'];
$lastMonthSavingsDeposits = (float) $savingsTotals['last_total'];
$savingsChange = $getChange($monthlySavingsDeposits, $lastMonthSavingsDeposits);
$savingsIsUp = $savingsChange['isUp'];
$savingsPercent = $savingsChange['percent'];

$categoriesCount = getVisibleCategoryCount($pdo, $userId);

$monthMap = [];
for ($i = 5; $i >= 0; $i--) {
  $monthMap[date('Y-m', strtotime("-{$i} months"))] = 0.0;
}

$stmt = $pdo->prepare("\n  SELECT DATE_FORMAT(expense_date, '%Y-%m') AS ym, COALESCE(SUM(amount), 0) AS total\n  FROM expenses\n  WHERE user_id = :user_id\n    AND expense_date >= :six_month_start\n    AND expense_date < :next_start\n  GROUP BY DATE_FORMAT(expense_date, '%Y-%m')\n  ORDER BY ym ASC\n");
$stmt->execute([
  ':user_id' => $userId,
  ':six_month_start' => $sixMonthStart,
  ':next_start' => $nextMonthStart,
]);

foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
  if (isset($monthMap[$row['ym']])) {
    $monthMap[$row['ym']] = (float) $row['total'];
  }
}

$monthlyChartLabels = array_map(fn(string $month) => date('M', strtotime($month . '-01')), array_keys($monthMap));
$monthlyChartValues = array_values($monthMap);

$stmt = $pdo->prepare("\n  SELECT COALESCE(c.name, 'Uncategorized') AS category_name, COALESCE(SUM(e.amount), 0) AS total\n  FROM expenses e\n  LEFT JOIN categories c ON c.id = e.category_id\n  WHERE e.user_id = :user_id\n    AND e.expense_date >= :current_start\n    AND e.expense_date < :next_start\n  GROUP BY e.category_id, c.name\n  ORDER BY total DESC\n  LIMIT 6\n");
$stmt->execute([
  ':user_id' => $userId,
  ':current_start' => $currentMonthStart,
  ':next_start' => $nextMonthStart,
]);
$breakdownRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$breakdownLabels = array_column($breakdownRows, 'category_name');
$breakdownValues = array_map(fn(array $row) => (float) $row['total'], $breakdownRows);

$stmt = $pdo->prepare("\n  SELECT\n    b.id,\n    b.amount,\n    b.month_year,\n    COALESCE(c.name, 'Uncategorized') AS category_name,\n    COALESCE(SUM(e.amount), 0) AS spent_amount\n  FROM budgets b\n  LEFT JOIN categories c ON c.id = b.category_id\n  LEFT JOIN expenses e\n    ON e.user_id = b.user_id\n   AND e.category_id = b.category_id\n   AND e.expense_date >= :current_start\n   AND e.expense_date < :next_start\n  WHERE b.user_id = :user_id\n    AND b.month_year >= :current_start\n    AND b.month_year < :next_start\n  GROUP BY b.id, b.amount, b.month_year, c.name\n  ORDER BY b.amount DESC, b.id DESC\n  LIMIT 5\n");
$stmt->execute([
  ':user_id' => $userId,
  ':current_start' => $currentMonthStart,
  ':next_start' => $nextMonthStart,
]);
$budgetProgressItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("\n  SELECT\n    e.expense_date,\n    e.note,\n    e.amount,\n    e.status,\n    COALESCE(c.name, 'Uncategorized') AS category_name\n  FROM expenses e\n  LEFT JOIN categories c ON c.id = e.category_id\n  WHERE e.user_id = :user_id\n  ORDER BY e.expense_date DESC, e.id DESC\n  LIMIT 5\n");
$stmt->execute([':user_id' => $userId]);
$recentTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
include __DIR__ . '/../views/home/welcome-text-and-cards.php';
include __DIR__ . '/../views/home/charts.php';
include __DIR__ . '/../views/home/transactions-and-progress.php';

$content = ob_get_clean();
include __DIR__ . '/../views/components/layout.php';
