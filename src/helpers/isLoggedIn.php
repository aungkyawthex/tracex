<?php
  require_once __DIR__ . '/session.php';
  startSession();

  if(!isset($_SESSION['user_id'])) {
    header("Location: /login/index.php");
    exit;
  }

?>
