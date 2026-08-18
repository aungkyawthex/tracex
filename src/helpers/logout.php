<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/../bootstrap.php';

startSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method not allowed.');
}

verifyCsrf();

if (isset($_SESSION['user_id'])) {
  $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL, token_expiry = NULL WHERE id = ?");
  $stmt->execute([(int) $_SESSION['user_id']]);
}

$_SESSION = array();

// delete cookie
if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, 
    $params["path"], $params["domain"],
    $params["secure"], $params["httponly"]
  );
}

session_destroy();

// delete remember token
if(isset($_COOKIE['remember_token'])) {
  setcookie('remember_token', '', time() - 3600, '/');
}

header("Location: /index.php");
exit();
