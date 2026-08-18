<?php
require __DIR__ . '/../src/auth_page.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['btnUpdateExpense'])) {
  setFlashAndRedirect('error', 'Invalid update request.', 'expenses.php');
}

verifyCsrf();

$expenseId = (int) ($_POST['edit_expense_id'] ?? 0);
$expenseDate = trim($_POST['expense_date'] ?? '');
$amount = $_POST['amount'] ?? '';
$categoryId = (int) ($_POST['category_id'] ?? 0);
$paymentMethodId = (int) ($_POST['payment_method'] ?? 0);
$note = trim($_POST['note'] ?? '');
$errors = [];

if ($expenseId <= 0) {
  $errors[] = 'Invalid expense ID.';
}
if ($expenseDate === '' || strtotime($expenseDate) === false) {
  $errors[] = 'Invalid expense date.';
}
if (!is_numeric($amount) || (float) $amount <= 0) {
  $errors[] = 'Amount must be greater than zero.';
}
if ($categoryId <= 0) {
  $errors[] = 'Category is required.';
}
if ($paymentMethodId <= 0) {
  $errors[] = 'Payment method is required.';
}

if (!$errors && !isVisibleLookupId($pdo, 'categories', $categoryId, $userId)) {
  $errors[] = 'Selected category is invalid.';
}
if (!$errors && !isVisibleLookupId($pdo, 'payment_methods', $paymentMethodId, $userId)) {
  $errors[] = 'Selected payment method is invalid.';
}

if ($errors) {
  setFlashAndRedirect('error', $errors[0], 'expenses.php');
}

$stmt = $pdo->prepare('UPDATE expenses SET expense_date = :expense_date, amount = :amount, category_id = :category_id, payment_method_id = :payment_method_id, note = :note, status = :status WHERE id = :id AND user_id = :user_id');
$stmt->execute([
  ':expense_date' => $expenseDate,
  ':amount' => (float) $amount,
  ':category_id' => $categoryId,
  ':payment_method_id' => $paymentMethodId,
  ':note' => $note,
  ':status' => isset($_POST['paid']) ? 'paid' : 'unpaid',
  ':id' => $expenseId,
  ':user_id' => $userId,
]);

$updated = $stmt->rowCount() > 0;
setFlashAndRedirect($updated ? 'success' : 'error', $updated ? 'Expense has been updated!' : 'Expense not found or access denied.', 'expenses.php');
