<?php
$metaDescription = $metaDescription ?? "Sign in or create your TraceX account";
include __DIR__ . '/head.php';
?>
  <link rel="stylesheet" href="/src/output.css">
  <link rel="stylesheet" href="/public/assets/vendor/fontawesome-free-7.1.0-web/css/all.min.css">
  <link rel="stylesheet" href="/public/assets/css/fontawesome-fonts.css">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
  <main class="flex-1 flex items-center justify-center px-4 py-12 sm:px-6">
    <div class="w-full max-w-sm content-enter">
      <a href="<?= url('index.php') ?>" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
        <i class="fa-solid fa-arrow-left text-xs"></i>
        Back to home
      </a>
      <div class="text-center mb-8">
        <?php $variant = 'dark'; $size = 'md'; include __DIR__ . '/logo.php'; ?>
      </div>
      <?= $content ?? '' ?>
    </div>
  </main>
  <footer>
    <?php include __DIR__ . '/copyright.php'; ?>
  </footer>
  <script src="/public/assets/js/password-toggle.js"></script>
  <script src="/public/assets/js/button-loading.js"></script>
  <script src="/public/assets/js/app.main.js"></script>
</body>
</html>
