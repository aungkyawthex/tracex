<?php
$metaDescription = $metaDescription ?? "Sign in or create your TraceX account";
include __DIR__ . '/head.php';
?>
  <link rel="stylesheet" href="/src/output.css">
  <link rel="stylesheet" href="/public/assets/vendor/fontawesome-free-7.1.0-web/css/all.min.css">
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
  <div class="w-full max-w-md">
    <?= $content ?? '' ?>
  </div>
</body>
</html>
