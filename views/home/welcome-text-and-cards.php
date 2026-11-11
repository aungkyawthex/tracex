<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-gray-600">Overview for <?= date('F Y') ?>. Financial summaries and budget progress are based on this month's activity.</p>
</div>
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Expenses</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"> <?= number_format($totalExpenses, 2) ?> Ks</h3>
                <p class="<?= $isUp ? 'text-red-500' : 'text-green-500' ?> text-sm mt-2">
                    <i class="fas fa-arrow-<?= $isUp ? 'up' : 'down' ?>"></i> <?= number_format($percent, 0) ?>% from last month
                </p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-wallet text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Monthly Budget</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"> <?= number_format($monthlyBudgetTotal, 2) ?> Ks </h3>
                <p class="<?= $budgetIsUp ? 'text-red-500' : 'text-green-500' ?> text-sm mt-2">
                    <i class="fas fa-arrow-<?= $budgetIsUp ? 'up' : 'down' ?> mr-1"></i> <?= number_format($budgetPercent, 0) ?>% from last month
                </p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-chart-line text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Categories</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"> <?= (int)($categoriesCount ?? 0) ?></h3>
                <p class="text-gray-500 text-sm mt-2">
                    <i class="fas fa-circle text-blue-500 mr-1"></i> Categories
                </p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-tags text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Savings</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"> <?= number_format($monthlySavingsDeposits, 2) ?> Ks </h3>
                <p class="<?= $savingsIsUp ? 'text-green-500' : 'text-red-500' ?> text-sm mt-2">
                    <i class="fas fa-arrow-<?= $savingsIsUp ? 'up' : 'down' ?> mr-1"></i> <?= number_format($savingsPercent, 0) ?>% from last month
                </p>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
                <i class="fas fa-piggy-bank text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>
