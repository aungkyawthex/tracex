<?php
$navItems = [
  ['label' => 'Dashboard', 'path' => 'public/index.php', 'icon' => 'fas fa-house'],
  ['label' => 'Expenses', 'path' => 'public/expenses.php', 'icon' => 'fa-solid fa-dollar-sign'],
  ['label' => 'Categories', 'path' => 'public/categories.php', 'icon' => 'fas fa-tags'],
  ['label' => 'Savings', 'path' => 'public/savings.php', 'icon' => 'fas fa-piggy-bank'],
  ['label' => 'Budgets', 'path' => 'public/budgets.php', 'icon' => 'fas fa-wallet'],
  ['label' => 'Insights', 'path' => 'public/insights.php', 'icon' => 'fas fa-chart-line'],
];
?>
<div class="hidden md:flex md:shrink-0">
  <div class="flex flex-col w-64 bg-blue-800">
    <div class="bg-blue-900 p-1">
      <a href="<?= url('index.php') ?>" class="flex items-center space-x-2">
        <img src="/public/assets/favicon.png" alt="TraceX favicon" class="h-14 w-14 rounded-md object-contain">
        <p class="text-white font-bold text-2xl">TraceX</p>
      </a>
    </div>
    <div class="flex flex-col flex-1 px-4 py-4 overflow-y-auto">
      <div class="space-y-1">
<?php
$extraClass = '';
include __DIR__ . '/sidebar-nav.php';
?>
      </div>
    </div>
  </div>
</div>

<div id="mobile-sidebar" class="fixed inset-0 z-40 md:hidden pointer-events-none">
  <div id="mobile-sidebar-backdrop" class="absolute inset-0 bg-black/40 opacity-0 transition-opacity duration-300 ease-out"></div>
  <aside id="mobile-sidebar-panel" class="absolute left-0 top-0 h-full w-64 bg-blue-800 transform -translate-x-full transition-transform duration-300 ease-out shadow-xl">
    <div class="flex items-center justify-between gap-2 bg-blue-900 p-3">
      <a href="<?= url('index.php') ?>" class="mobile-sidebar-link min-w-0 flex items-center">
        <img src="/public/assets/favicon.png" alt="TraceX favicon" class="h-8 w-8 rounded-md object-contain">
        <p class="text-white ms-2 font-bold text-2xl">TraceX</p>
      </a>
      <button id="mobile-sidebar-close" type="button" class="shrink-0 inline-flex h-8 w-8 items-center justify-center rounded-md text-white/90 hover:bg-blue-800 hover:text-white cursor-pointer">
        <i class="fas fa-xmark text-lg"></i>
      </button>
    </div>
    <div class="flex flex-col px-4 py-4 overflow-y-auto">
      <div class="space-y-1">
<?php
$extraClass = 'mobile-sidebar-link';
include __DIR__ . '/sidebar-nav.php';
?>
      </div>
    </div>
  </aside>
</div>
