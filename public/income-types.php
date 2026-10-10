<?php
require __DIR__ . '/../src/auth_page.php';

$title = 'Income Types - TraceX';
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$incomeTypeIcons = ['briefcase', 'laptop', 'store', 'chart-line', 'house', 'gift', 'coins', 'hand-holding-dollar', 'building-columns', 'question'];

$readIncomeType = function (int $excludeId = 0) use ($pdo, $userId, $incomeTypeIcons): array {
    $params = [
        ':user_id' => $userId,
        ':name' => trim((string) ($_POST['name'] ?? '')),
        ':icon' => (string) ($_POST['icon'] ?? ''),
        ':color' => (string) ($_POST['color'] ?? ''),
    ];

    // Names must be unique across the default types and the user's own types.
    $dupStmt = $pdo->prepare("
        SELECT 1 FROM income_types
        WHERE name = :name AND (user_id IS NULL OR user_id = :user_id) AND id <> :id
        LIMIT 1
    ");
    $dupStmt->execute([':name' => $params[':name'], ':user_id' => $userId, ':id' => $excludeId]);

    $error = match (true) {
        $params[':name'] === '' || mb_strlen($params[':name']) > 100 => 'Name is required and must be 100 characters or fewer.',
        !in_array($params[':icon'], $incomeTypeIcons, true) => 'Selected icon is invalid.',
        preg_match('/^#[0-9A-Fa-f]{6}$/', $params[':color']) !== 1 => 'Selected color is invalid.',
        (bool) $dupStmt->fetchColumn() => 'An income type with this name already exists.',
        default => null,
    };

    return [$params, $error];
};

$ownsIncomeType = function (int $id) use ($pdo, $userId): bool {
    $stmt = $pdo->prepare("SELECT 1 FROM income_types WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $id, ':user_id' => $userId]);

    return (bool) $stmt->fetchColumn();
};

if ($isPost && isset($_POST['btnSaveIncomeType'])) {
    verifyCsrf();
    [$params, $error] = $readIncomeType();
    if ($error !== null) {
        setFlashAndRedirect('error', $error, 'income-types.php');
    }

    $pdo->prepare("
        INSERT INTO income_types (user_id, name, icon, color)
        VALUES (:user_id, :name, :icon, :color)
    ")->execute($params);
    setFlashAndRedirect('success', 'Income type has been added!', 'income-types.php');
}

if ($isPost && isset($_POST['btnUpdateIncomeType'])) {
    verifyCsrf();
    $id = (int) ($_POST['edit_income_type_id'] ?? 0);
    if (!$ownsIncomeType($id)) {
        setFlashAndRedirect('error', 'Income type not found or access denied.', 'income-types.php');
    }

    [$params, $error] = $readIncomeType($id);
    if ($error !== null) {
        setFlashAndRedirect('error', $error, 'income-types.php');
    }

    $pdo->prepare("
        UPDATE income_types
        SET name = :name, icon = :icon, color = :color
        WHERE id = :id AND user_id = :user_id
    ")->execute($params + [':id' => $id]);
    setFlashAndRedirect('success', 'Income type has been updated!', 'income-types.php');
}

if ($isPost && isset($_POST['btnDeleteIncomeType'])) {
    verifyCsrf();
    $id = (int) ($_POST['id'] ?? 0);
    if (!$ownsIncomeType($id)) {
        setFlashAndRedirect('error', 'Income type not found or access denied.', 'income-types.php');
    }

    $usageStmt = $pdo->prepare("SELECT 1 FROM incomes WHERE income_type_id = :id LIMIT 1");
    $usageStmt->execute([':id' => $id]);
    if ($usageStmt->fetchColumn()) {
        setFlashAndRedirect('error', 'This income type is used by existing income records.', 'income-types.php');
    }

    $pdo->prepare("DELETE FROM income_types WHERE id = :id AND user_id = :user_id")
        ->execute([':id' => $id, ':user_id' => $userId]);
    setFlashAndRedirect('success', 'Income type has been deleted!', 'income-types.php');
}

$stmt = $pdo->prepare("
    SELECT t.id, t.user_id, t.name, t.icon, t.color, COUNT(i.id) AS income_count
    FROM income_types t
    LEFT JOIN incomes i ON i.income_type_id = t.id AND i.user_id = :user_id
    WHERE t.user_id IS NULL OR t.user_id = :user_id
    GROUP BY t.id, t.user_id, t.name, t.icon, t.color
    ORDER BY t.user_id IS NULL DESC, t.name ASC
");
$stmt->execute([':user_id' => $userId]);
$incomeTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
include __DIR__ . '/../views/income-types/income-type-view.php';
$content = ob_get_clean();
include __DIR__ . '/../views/components/layout.php';
include __DIR__ . '/../views/components/flash-toast.php';
