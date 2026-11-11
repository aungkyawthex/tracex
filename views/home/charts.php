<!-- charts -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Monthly Expenses</h2>
            <!-- <div class="flex">
                <button class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-l-lg">Month</button>
                <button class="px-3 py-1 text-sm bg-white text-gray-600 border border-l-0 border-gray-200">Quarter</button>
                <button class="px-3 py-1 text-sm bg-white text-gray-600 border border-l-0 border-gray-200 rounded-r-lg">Year</button>
            </div> -->
        </div>
        <div class="h-64">
            <canvas id="monthlyExpensesChart"></canvas>
        </div>
    </div>
    
    <!-- expense breakdown -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Expense Breakdown</h2>
        <div class="h-64">
            <canvas id="expenseBreakdownChart"></canvas>
        </div>
    </div>
</div>

<script>
    window.dashboardData = {
        monthly: {
            labels: <?= json_encode($monthlyChartLabels, JSON_UNESCAPED_UNICODE) ?>,
            values: <?= json_encode($monthlyChartValues) ?>
        },
        breakdown: {
            labels: <?= json_encode($breakdownLabels, JSON_UNESCAPED_UNICODE) ?>,
            values: <?= json_encode($breakdownValues) ?>
        }
    };
</script>
