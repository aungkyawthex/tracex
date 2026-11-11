<?php
$title = $title ?? "TraceX";
$metaDescription = $metaDescription ?? "Smart expense tracking and budgeting for a financially savvy life.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
  <title><?= htmlspecialchars($title) ?></title>
  <link rel="icon" type="image/png" href="/public/assets/favicon.png">
  <link rel="preload" href="/public/assets/fonts.php?file=fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="/public/assets/fonts.php?file=fa-regular-400.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400..800&display=swap" rel="stylesheet">
