<?php
require __DIR__ . '/../src/auth_page.php';

$title = 'Budgets - TraceX';
$errors = [];

function normalizeBudgetMonth(string $value): ?string
{
    $value = trim($value);

    if (preg_match('/^\d{4}-\d{2}$/', $value) === 1) {
        $ts = strtotime($value . '-01');
        return $ts === false ? null : date('Y-m-01', $ts);
    }

    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
        $ts = strtotime($value);
        return $ts === false ? null : date('Y-m-01', $ts);
    }

    return null;
}

$categories = getVisibleLookupRows($pdo, 'categories', 'id, name', $userId);

$validateBudget = function (bool $isUpdate) use ($pdo, $userId): array {
    $fields = [
        'id' => isset($_POST['edit_budget_id']) ? (int) $_POST['edit_budget_id'] : 0,
        'categoryId' => isset($_POST['category_id']) ? (int) $_POST['category_id'] : 0,
        'amount' => $_POST['amount'] ?? '',
        'monthYear' => normalizeBudgetMonth((string) ($_POST['month_year'] ?? '')),
    ];
    $errors = [];

    if ($isUpdate && $fields['id'] <= 0) {
        $errors['id'] = 'Invalid budget ID.';
    }
    if ($fields['categoryId'] <= 0) {
        $errors['category_id'] = 'Category is required.';
    }
    if (!is_numeric($fields['amount']) || (float) $fields['amount'] <= 0) {
        $errors['amount'] = 'Amount must be greater than zero.';
    }
    if ($fields['monthYear'] === null) {
        $errors['month_year'] = 'Month is required.';
    }

    if (!isset($errors['category_id']) && !isVisibleLookupId($pdo, 'categories', $fields['categoryId'], $userId)) {
        $errors['category_id'] = 'Selected category is invalid.';
    }

    if (empty($errors)) {
        $sql = "
            SELECT id
            FROM budgets
            WHERE user_id = :user_id
              AND category_id = :category_id
              AND month_year = :month_year
        ";
        $params = [
            ':user_id' => $userId,
            ':category_id' => $fields['categoryId'],
            ':month_year' => $fields['monthYear'],
        ];

        if ($isUpdate) {
            $sql .= ' AND id <> :id';
            $params[':id'] = $fields['id'];
        }

        $sql .= ' LIMIT 1';
        $dupStmt = $pdo->prepare($sql);
        $dupStmt->execute($params);

        if ($dupStmt->fetchColumn()) {
            $errors['duplicate'] = 'Budget already exists for this category and month.';
        }
    }

    return [$fields, $errors];
};

$handleBudgetException = function (PDOException $e, string $operation): void {
    $message = "Something went wrong while {$operation} budget.";
    if (($e->getCode() ?? '') === '23000') {
        $message = 'Budget already exists for this category and month.';
    }

    setFlashAndRedirect('error', $message, 'budgets.php');
};

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnSaveBudget'])) {
    verifyCsrf();
    [$fields, $errors] = $validateBudget(false);

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO budgets (user_id, category_id, amount, month_year)
                VALUES (:user_id, :category_id, :amount, :month_year)
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':category_id' => $fields['categoryId'],
                ':amount' => (float) $fields['amount'],
                ':month_year' => $fields['monthYear'],
            ]);
            setFlashAndRedirect('success', 'Budget has been added!', 'budgets.php');
        } catch (PDOException $e) {
            $handleBudgetException($e, 'creating');
        }
    }

    setFlashAndRedirect('error', array_values($errors)[0], 'budgets.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnUpdateBudget'])) {
    verifyCsrf();
    [$fields, $errors] = $validateBudget(true);

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE budgets
                SET category_id = :category_id,
                    amount = :amount,
                    month_year = :month_year
                WHERE id = :id AND user_id = :user_id
            ");
            $stmt->execute([
                ':id' => $fields['id'],
                ':user_id' => $userId,
                ':category_id' => $fields['categoryId'],
                ':amount' => (float) $fields['amount'],
                ':month_year' => $fields['monthYear'],
            ]);

            if ($stmt->rowCount() === 0) {
                setFlashAndRedirect('error', 'Budget not found or access denied.', 'budgets.php');
            }

            setFlashAndRedirect('success', 'Budget has been updated!', 'budgets.php');
        } catch (PDOException $e) {
            $handleBudgetException($e, 'updating');
        }
    }

    setFlashAndRedirect('error', array_values($errors)[0], 'budgets.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDeleteBudget'])) {
    verifyCsrf();
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($id <= 0) {
        setFlashAndRedirect('error', 'Invalid budget ID.', 'budgets.php');
    }

    $stmt = $pdo->prepare("DELETE FROM budgets WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        ':id' => $id,
        ':user_id' => $userId,
    ]);

    if ($stmt->rowCount() === 0) {
        setFlashAndRedirect('error', 'Budget not found or access denied.', 'budgets.php');
    }

    setFlashAndRedirect('success', 'Budget has been deleted!', 'budgets.php');
}

$stmt = $pdo->prepare("
    SELECT
        b.*,
        c.name AS category_name,
        COALESCE(SUM(e.amount), 0) AS spent_amount
    FROM budgets b
    LEFT JOIN categories c ON c.id = b.category_id
    LEFT JOIN expenses e
      ON e.user_id = b.user_id
     AND e.category_id = b.category_id
     AND e.expense_date >= b.month_year
     AND e.expense_date < DATE_ADD(b.month_year, INTERVAL 1 MONTH)
    WHERE b.user_id = :user_id
    GROUP BY b.id, b.user_id, b.category_id, b.amount, b.month_year, b.created_at, b.updated_at, c.name
    ORDER BY b.month_year DESC, b.id DESC
");
$stmt->execute([':user_id' => $userId]);
$budgets = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
include __DIR__ . '/../views/budgets/budget-view.php';
$content = ob_get_clean();
include __DIR__ . '/../views/components/layout.php';
include __DIR__ . '/../views/components/flash-toast.php';
