<?php
include __DIR__ . '/head.php';
?>
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
  <script src="/public/assets/js/components/button-loading.js"></script>
  <script src="/public/assets/js/app.main.js"></script>
</body>
</html>
