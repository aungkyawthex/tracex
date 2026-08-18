<?php
if (!isset($title)) {
  $title = "TraceX";
}
if (!isset($metaDescription)) {
  $metaDescription = "Smart expense tracking and budgeting for a financially savvy life.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
  <title><?= htmlspecialchars($title) ?></title>
  <link rel="icon" type="image/png" href="/public/assets/favicon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/src/output.css">
  <!-- fontawesome -->
  <link rel="stylesheet" href="/public/assets/vendor/fontawesome-free-7.1.0-web/css/all.min.css">
  <link rel="stylesheet" href="/public/assets/vendor/flatpickr/flatpickr.min.css">
</head>
<body class="bg-gray-100">
  <div class="flex h-screen overflow-hidden">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden">
      <?php
        include __DIR__ . '/navbar.php';
      ?>
      <main class="flex-1 overflow-y-auto p-6 bg-gray-100">
        <?= $content ?? '' ?>
      </main>
    </div>
  </div>

  <script src="/public/assets/vendor/flatpickr/flatpickr.min.js"></script>
  <script src="/public/assets/vendor/chartjs/chart.umd.js"></script>
  <script src="/public/assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>
  <script>
    if (document.getElementById('date-range')) {
      flatpickr("#date-range", {
        mode: "range",
        dateFormat: "Y-m-d",
      });
    }

    if (document.getElementById('expense_date')) {
      flatpickr("#expense_date", {
        dateFormat: "Y-m-d",
        defaultDate: "today"
      });
    }

    if (document.getElementById('edit_expense_date')) {
      flatpickr("#edit_expense_date", {
        dateFormat: "Y-m-d",
      });
    }
  </script>
  <script src="/public/assets/js/button-loading.js"></script>
  <script src="/public/assets/js/app.main.js"></script>
</body>
</html>
