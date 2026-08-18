<?php

require __DIR__ . '/helpers/url.php';
require __DIR__ . '/helpers/flash.php';
require_once __DIR__ . '/helpers/isLoggedIn.php';
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/helpers/db.php';
require_once __DIR__ . '/helpers/format.php';
require_once __DIR__ . '/helpers/csrf.php';

$userId = (int) $_SESSION['user_id'];
