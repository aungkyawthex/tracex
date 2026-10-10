<?php
require __DIR__ . '/../src/auth_page.php';

$title = 'Income - TraceX';
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';

$incomeTypes = getVisibleLookupRows($pdo, 'income_types', 'id, name', $userId);

$readIncome = function () use ($pdo, $userId): array {
    $note = trim((string) ($_POST['note'] ?? ''));
    $params = [
        ':user_id' => $userId,
        ':income_type_id' => (int) ($_POST['income_type_id'] ?? 0),
        ':amount' => $_POST['amount'] ?? '',
        ':month_year' => normalizeMonth((string) ($_POST['month_year'] ?? '')),
        ':note' => $note === '' ? null : $note,
    ];

    $error = match (true) {
        !is_numeric($params[':amount']) || (float) $params[':amount'] <= 0 => 'Amount must be greater than zero.',
        $params[':month_year'] === null => 'Month is required.',
        !isVisibleLookupId($pdo, 'income_types', $params[':income_type_id'], $userId) => 'Please select a valid income type.',
        default => null,
    };

    return [$params, $error];
};

if ($isPost && isset($_POST['btnSaveIncome'])) {
    verifyCsrf();
    [$params, $error] = $readIncome();
    if ($error !== null) {
        setFlashAndRedirect('error', $error, 'incomes.php');
    }

    $pdo->prepare("
        INSERT INTO incomes (user_id, income_type_id, amount, month_year, note)
        VALUES (:user_id, :income_type_id, :amount, :month_year, :note)
    ")->execute($params);
    setFlashAndRedirect('success', 'Income has been added!', 'incomes.php');
}

if ($isPost && isset($_POST['btnUpdateIncome'])) {
    verifyCsrf();
    [$params, $error] = $readIncome();
    if ($error !== null) {
        setFlashAndRedirect('error', $error, 'incomes.php');
    }

    // Ownership is checked separately because MySQL reports 0 affected rows when nothing changed.
    $params[':id'] = (int) ($_POST['edit_income_id'] ?? 0);
    $ownerStmt = $pdo->prepare("SELECT 1 FROM incomes WHERE id = :id AND user_id = :user_id");
    $ownerStmt->execute([':id' => $params[':id'], ':user_id' => $userId]);
    if (!$ownerStmt->fetchColumn()) {
        setFlashAndRedirect('error', 'Income not found or access denied.', 'incomes.php');
    }

    $pdo->prepare("
        UPDATE incomes
        SET income_type_id = :income_type_id,
            amount = :amount,
            month_year = :month_year,
            note = :note
        WHERE id = :id AND user_id = :user_id
    ")->execute($params);
    setFlashAndRedirect('success', 'Income has been updated!', 'incomes.php');
}

if ($isPost && isset($_POST['btnDeleteIncome'])) {
    verifyCsrf();
    $stmt = $pdo->prepare("DELETE FROM incomes WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => (int) ($_POST['id'] ?? 0), ':user_id' => $userId]);

    if ($stmt->rowCount() === 0) {
        setFlashAndRedirect('error', 'Income not found or access denied.', 'incomes.php');
    }

    setFlashAndRedirect('success', 'Income has been deleted!', 'incomes.php');
}

$stmt = $pdo->prepare("
    SELECT i.*, t.name AS type_name, t.icon AS type_icon, t.color AS type_color
    FROM incomes i
    JOIN income_types t ON t.id = i.income_type_id
    WHERE i.user_id = :user_id
    ORDER BY i.month_year DESC, i.id DESC
");
$stmt->execute([':user_id' => $userId]);
$incomes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$currentMonth = date('Y-m-01');
$monthTotal = 0.0;
$overallTotal = 0.0;
foreach ($incomes as $income) {
    $overallTotal += (float) $income['amount'];
    if ($income['month_year'] === $currentMonth) {
        $monthTotal += (float) $income['amount'];
    }
}

ob_start();
include __DIR__ . '/../views/incomes/income-view.php';
$content = ob_get_clean();
include __DIR__ . '/../views/components/layout.php';
include __DIR__ . '/../views/components/flash-toast.php';
