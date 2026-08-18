<?php
require __DIR__ . '/../src/auth_page.php';

$title = 'Account';
$updateErrors = [];

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnUpdateProfile'])) {
    verifyCsrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '') {
      $updateErrors['name'] = 'Name is required.';
    }

    if ($email === '') {
      $updateErrors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $updateErrors['email'] = 'Invalid email format.';
    } else {
      $emailCheckStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1");
      $emailCheckStmt->execute([
        ':email' => $email,
        ':id' => $userId
      ]);
      if ($emailCheckStmt->fetchColumn()) {
        $updateErrors['email'] = 'Email is already exists.';
      }
    }

    if (empty($updateErrors)) {
      try {
        $updateStmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
        $updateStmt->execute([
          ':name' => $name,
          ':email' => $email,
          ':id' => $userId
        ]);

        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        header("Location: profile.php");
        exit;
      } catch (PDOException $e) {
        logThrowable($e, 'profile update failed');

        if ($e->getCode() === '23000') {
          $updateErrors['email'] = 'Email is already exists.';
        } else {
          $updateErrors['update'] = 'Could not update your profile. Please try again.';
        }
      }
    }
  }

  $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
  $stmt->execute([':user_id' => $userId]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  $statsStmt = $pdo->prepare("
    SELECT
      (SELECT COUNT(*) FROM expenses WHERE user_id = :user_id) AS expenses_added,
      (SELECT COUNT(*) FROM savings WHERE user_id = :user_id) AS savings_goals
  ");
  $statsStmt->execute([':user_id' => $userId]);
  $stats = $statsStmt->fetch(PDO::FETCH_ASSOC) ?: [];

  $expensesAdded = (int) ($stats['expenses_added'] ?? 0);
  $savingsGoals = (int) ($stats['savings_goals'] ?? 0);
  $categoriesTotal = getVisibleCategoryCount($pdo, $userId);
  $daysActive = 0;
  if (!empty($user['created_at'])) {
    $createdAtTs = strtotime((string) $user['created_at']);
    if ($createdAtTs !== false) {
      $daysActive = max(1, (int) floor((time() - $createdAtTs) / 86400) + 1);
    }
  }

  $editMode = isset($_GET['edit']);

  ob_start();
?>

<div class="flex-1">
  <?php include __DIR__ . '/../views/profile/heading.php'; ?>
  <div class="space-y-6">
    <?php
    include __DIR__ . '/../views/profile/profile-card.php';
    include __DIR__ . '/../views/profile/statistics.php';
    ?>
  </div>
</div>

<?php
  $content = ob_get_clean();
  include __DIR__ . '/../views/components/layout.php';
?>
