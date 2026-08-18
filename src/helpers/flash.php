<?php

  function setFlash($type, $message) {
    $_SESSION['flash'] = [
      'type' => $type,
      'message' => $message
    ];
  }

  function getFlash() {
    if(!empty($_SESSION['flash'])) {
      $flash = $_SESSION['flash'];
      unset($_SESSION['flash']);
      return $flash;
    }
    return null;
  }

  function setFlashAndRedirect($type, $message, $location) {
    setFlash($type, $message);
    header("Location: {$location}");
    exit;
  }

?>