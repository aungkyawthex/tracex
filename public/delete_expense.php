<?php
require __DIR__ . '/../src/auth_page.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['btnDeleteExpense'])) {
  setFlashAndRedirect('error', 'Invalid delete request.', 'expenses.php');
}

verifyCsrf();

$expenseId = (int) ($_POST['id'] ?? 0);
if ($expenseId <= 0) {
  setFlashAndRedirect('error', 'Invalid expense ID.', 'expenses.php');
}

$stmt = $pdo->prepare('DELETE FROM expenses WHERE id = :id AND user_id = :user_id');
$stmt->execute([
  ':id' => $expenseId,
  ':user_id' => $_SESSION['user_id'],
]);

$deleted = $stmt->rowCount() > 0;
setFlashAndRedirect($deleted ? 'success' : 'error', $deleted ? 'Expense has been deleted!' : 'Expense not found or access denied.', 'expenses.php');
