<?php
require __DIR__ . '/../src/auth_page.php';
require_once __DIR__ . '/../src/helpers/savings.php';

$title = 'Savings - TraceX';
$errors = [];

$validateSaving = function (bool $isUpdate): array {
    $fields = [
        'id' => isset($_POST['edit_saving_id']) ? (int) $_POST['edit_saving_id'] : 0,
        'name' => trim($_POST['name'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'targetAmount' => $_POST['target_amount'] ?? '',
        'startDate' => trim($_POST['start_date'] ?? ''),
        'targetDate' => trim($_POST['target_date'] ?? ''),
        'status' => trim($_POST['status'] ?? 'active'),
    ];
    $errors = [];

    if ($isUpdate && $fields['id'] <= 0) {
        $errors['id'] = 'Invalid saving ID.';
    }
    if ($fields['name'] === '') {
        $errors['name'] = 'Saving name is required.';
    }
    if (!is_numeric($fields['targetAmount']) || (float) $fields['targetAmount'] <= 0) {
        $errors['target_amount'] = 'Target amount must be greater than zero.';
    }
    if ($fields['startDate'] !== '' && strtotime($fields['startDate']) === false) {
        $errors['start_date'] = 'Invalid start date.';
    }
    if ($fields['targetDate'] !== '' && strtotime($fields['targetDate']) === false) {
        $errors['target_date'] = 'Invalid target date.';
    }
    if ($fields['startDate'] !== '' && $fields['targetDate'] !== '' && strtotime($fields['targetDate']) < strtotime($fields['startDate'])) {
        $errors['target_date'] = 'Target date must be after start date.';
    }

    $allowedStatuses = ['active', 'completed', 'cancelled'];
    if (!in_array($fields['status'], $allowedStatuses, true)) {
        $fields['status'] = 'active';
    }

    return [$fields, $errors];
};

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnSaveSaving'])) {
    verifyCsrf();
    [$fields, $errors] = $validateSaving(false);

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO savings (user_id, name, description, target_amount, start_date, target_date, status)
            VALUES (:user_id, :name, :description, :target_amount, :start_date, :target_date, :status)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':name' => $fields['name'],
            ':description' => $fields['description'] !== '' ? $fields['description'] : null,
            ':target_amount' => (float) $fields['targetAmount'],
            ':start_date' => $fields['startDate'] !== '' ? $fields['startDate'] : null,
            ':target_date' => $fields['targetDate'] !== '' ? $fields['targetDate'] : null,
            ':status' => $fields['status'],
        ]);

        setFlashAndRedirect('success', 'Saving has been added!', 'savings.php');
    }

    setFlashAndRedirect('error', array_values($errors)[0], 'savings.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnUpdateSaving'])) {
    verifyCsrf();
    [$fields, $errors] = $validateSaving(true);

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE savings
            SET name = :name,
                description = :description,
                target_amount = :target_amount,
                start_date = :start_date,
                target_date = :target_date,
                status = :status
            WHERE id = :id AND user_id = :user_id
        ");
        $stmt->execute([
            ':id' => $fields['id'],
            ':user_id' => $userId,
            ':name' => $fields['name'],
            ':description' => $fields['description'] !== '' ? $fields['description'] : null,
            ':target_amount' => (float) $fields['targetAmount'],
            ':start_date' => $fields['startDate'] !== '' ? $fields['startDate'] : null,
            ':target_date' => $fields['targetDate'] !== '' ? $fields['targetDate'] : null,
            ':status' => $fields['status'],
        ]);

        if ($stmt->rowCount() === 0) {
            setFlashAndRedirect('error', 'Saving not found or access denied.', 'savings.php');
        }

        setFlashAndRedirect('success', 'Saving has been updated!', 'savings.php');
    }

    setFlashAndRedirect('error', array_values($errors)[0], 'savings.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDeleteSaving'])) {
    verifyCsrf();
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($id <= 0) {
        setFlashAndRedirect('error', 'Invalid saving ID.', 'savings.php');
    }

    $stmt = $pdo->prepare("DELETE FROM savings WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        ':id' => $id,
        ':user_id' => $userId,
    ]);

    if ($stmt->rowCount() === 0) {
        setFlashAndRedirect('error', 'Saving not found or access denied.', 'savings.php');
    }

    setFlashAndRedirect('success', 'Saving has been deleted!', 'savings.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnSaveSavingTransaction'])) {
    verifyCsrf();
    $savingId = isset($_POST['saving_id']) ? (int) $_POST['saving_id'] : 0;
    $type = trim($_POST['type'] ?? '');
    $amount = $_POST['amount'] ?? '';
    $note = trim($_POST['note'] ?? '');

    if ($savingId <= 0) {
        $errors['saving_id'] = 'Invalid saving goal.';
    }
    if (!in_array($type, ['deposit', 'withdraw'], true)) {
        $errors['type'] = 'Invalid transaction type.';
    }
    if (!is_numeric($amount) || (float) $amount <= 0) {
        $errors['amount'] = 'Amount must be greater than zero.';
    }

    if (empty($errors)) {
        $currentAmount = getSavingCurrentAmount($pdo, $savingId, $userId);
        if ($currentAmount === null) {
            $errors['saving_id'] = 'Saving goal not found or access denied.';
        } elseif ($type === 'withdraw' && (float) $amount > $currentAmount) {
            $errors['amount'] = 'Withdrawal amount exceeds current savings.';
        }
    }

    if (!empty($errors)) {
        setFlashAndRedirect('error', array_values($errors)[0], 'savings.php');
    }

    $stmt = $pdo->prepare("
        INSERT INTO saving_transactions (saving_id, user_id, type, amount, note)
        VALUES (:saving_id, :user_id, :type, :amount, :note)
    ");
    $stmt->execute([
        ':saving_id' => $savingId,
        ':user_id' => $userId,
        ':type' => $type,
        ':amount' => (float) $amount,
        ':note' => $note !== '' ? $note : null,
    ]);

    setFlashAndRedirect('success', 'Saving transaction added.', 'savings.php');
}

$stmt = $pdo->prepare("
    SELECT
        s.*,
        COALESCE(SUM(CASE WHEN st.type = 'deposit' THEN st.amount ELSE -st.amount END), 0) AS current_amount
    FROM savings s
    LEFT JOIN saving_transactions st ON st.saving_id = s.id
    WHERE s.user_id = :user_id
    GROUP BY s.id
    ORDER BY s.created_at DESC, s.id DESC
");
$stmt->execute([':user_id' => $userId]);
$savings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT st.*, s.name AS saving_name
    FROM saving_transactions st
    INNER JOIN savings s ON s.id = st.saving_id
    WHERE st.user_id = :user_id
    ORDER BY st.created_at DESC, st.id DESC
    LIMIT 20
");
$stmt->execute([':user_id' => $userId]);
$savingTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
include __DIR__ . '/../views/savings/saving-view.php';
$content = ob_get_clean();
include __DIR__ . '/../views/components/layout.php';
include __DIR__ . '/../views/components/flash-toast.php';
