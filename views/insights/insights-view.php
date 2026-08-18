<div class="mb-8">
  <h1 class="text-2xl font-bold text-gray-900">Spending Insights</h1>
  <p class="mt-2 text-gray-600">Analyze your spending patterns and get personalized savings tips for <?= date('F Y') ?>.</p>
</div>

<?php if (empty($categorySpending)): ?>
  <div class="bg-white rounded-lg shadow p-6">
    <p class="text-gray-500">No expenses recorded for this month yet.</p>
  </div>
<?php else: ?>
  <!-- Spending Overview -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
      <h3 class="text-sm font-medium text-gray-500 mb-1">Total Spent</h3>
      <div class="flex items-baseline space-x-2">
        <p class="text-2xl font-bold text-gray-900"><?= number_format($totalSpent, 2) ?> MMK</p>
      </div>
      <?php if (isset($trend)): ?>
        <p class="mt-1 text-sm <?= $trend > 0 ? 'text-red-600' : 'text-green-600' ?>">
          <i class="fas <?= $trend > 0 ? 'fa-arrow-up' : 'fa-arrow-down' ?> mr-1"></i>
          <?= number_format(abs($trend), 1) ?>% vs last month
        </p>
      <?php endif; ?>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
      <h3 class="text-sm font-medium text-gray-500 mb-1">Daily Average</h3>
      <p class="text-2xl font-bold text-gray-900"><?= number_format($dailyAverage, 2) ?> MMK</p>
      <p class="mt-1 text-sm text-gray-500">Based on <?= $daysPassed ?> days</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
      <h3 class="text-sm font-medium text-gray-500 mb-1">Categories</h3>
      <p class="text-2xl font-bold text-gray-900"><?= count($categorySpending) ?></p>
      <p class="mt-1 text-sm text-gray-500">Active this month</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
      <h3 class="text-sm font-medium text-gray-500 mb-1">Top Category</h3>
      <p class="text-2xl font-bold text-gray-900 truncate"><?= htmlspecialchars($categorySpending[0]['name'] ?? 'N/A') ?></p>
      <p class="mt-1 text-sm text-gray-500"><?= number_format($categorySpending[0]['total_spent'] ?? 0, 2) ?> MMK</p>
    </div>
  </div>

  <!-- Category Spending Chart -->
  <div class="bg-white rounded-lg shadow p-6 mb-8">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">Spending by Category</h2>
    <div class="h-64 md:h-72">
      <canvas id="spendingChart" class="w-full h-full"></canvas>
    </div>
  </div>

  <!-- Detailed Insights -->
  <div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">Detailed Insights & Tips</h2>
    <div class="space-y-6">
      <?php foreach ($insights as $insight): ?>
        <div class="border-b border-gray-200 pb-6 last:border-b-0">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-medium text-gray-900"><?= htmlspecialchars($insight['category']) ?></h3>
            <div class="flex items-center space-x-4">
              <span class="text-sm text-gray-500"><?= $insight['percentage'] ?>% of total</span>
              <?php if ($insight['over_budget']): ?>
                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Over Budget</span>
              <?php endif; ?>
            </div>
          </div>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 bg-gray-50 p-4 rounded-lg">
            <div>
              <p class="text-xs text-gray-500 uppercase tracking-wider">Spent</p>
              <p class="text-lg font-semibold text-gray-900"><?= number_format($insight['spent'], 2) ?> MMK</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 uppercase tracking-wider">Transactions</p>
              <p class="text-lg font-semibold text-gray-900"><?= $insight['expense_count'] ?></p>
            </div>
            <div>
              <p class="text-xs text-gray-500 uppercase tracking-wider">Average</p>
              <p class="text-lg font-semibold text-gray-900"><?= number_format($insight['avg_expense'], 2) ?> MMK</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 uppercase tracking-wider">Largest</p>
              <p class="text-lg font-semibold text-gray-900"><?= number_format($insight['max_expense'], 2) ?> MMK</p>
            </div>
          </div>
          
          <?php if ($insight['budget']): ?>
            <div class="mb-4 flex items-center justify-between bg-blue-50 p-3 rounded-lg">
               <div>
                  <span class="text-sm text-gray-500">Budget:</span>
                  <span class="font-medium text-gray-900"><?= number_format($insight['budget'], 2) ?> MMK</span>
               </div>
               <div>
                  <span class="text-sm text-gray-500">Remaining:</span>
                  <span class="font-medium <?= $insight['spent'] > $insight['budget'] ? 'text-red-600' : 'text-green-600' ?>">
                     <?= number_format($insight['budget'] - $insight['spent'], 2) ?> MMK
                  </span>
               </div>
            </div>
          <?php endif; ?>

          <?php if (!empty($insight['recent_expenses'])): ?>
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Recent Transactions</h4>
              <div class="space-y-2">
                <?php foreach ($insight['recent_expenses'] as $exp): ?>
                  <div class="flex justify-between items-center text-sm bg-white border border-gray-100 p-2 rounded">
                    <div class="flex items-center space-x-3">
                      <span class="text-gray-500 text-xs"><?= date('M d', strtotime($exp['expense_date'])) ?></span>
                      <span class="text-gray-900 truncate max-w-[150px] md:max-w-xs"><?= htmlspecialchars($exp['note'] ?: 'No note') ?></span>
                    </div>
                    <span class="font-medium text-gray-900"><?= number_format($exp['amount'], 2) ?> MMK</span>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
          <?php if (!empty($insight['suggestions'])): ?>
            <div class="bg-blue-50 rounded-lg p-4">
              <h4 class="text-sm font-medium text-blue-900 mb-2">Savings Tips:</h4>
              <ul class="list-disc list-inside space-y-1">
                <?php foreach ($insight['suggestions'] as $suggestion): ?>
                  <li class="text-sm text-blue-800"><?= htmlspecialchars($suggestion) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Top Expenses -->
  <?php if (!empty($topExpenses)): ?>
  <div class="bg-white rounded-lg shadow p-6 mt-8">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">Top Largest Expenses</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php foreach ($topExpenses as $expense): ?>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <?= date('M d, Y', strtotime($expense['expense_date'])) ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" style="background-color: <?= htmlspecialchars($expense['category_color']) ?>20; color: <?= htmlspecialchars($expense['category_color']) ?>">
                  <?= htmlspecialchars($expense['category_name']) ?>
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <?= htmlspecialchars($expense['note'] ?: '-') ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                <?= number_format($expense['amount'], 2) ?> MMK
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
<?php endif; ?>

<script>
  <?php if (!empty($categorySpending)): ?>
    document.addEventListener('DOMContentLoaded', () => {
      if (!window.Chart) {
        return;
      }

      const canvas = document.getElementById('spendingChart');
      if (!canvas) {
        return;
      }

      const ctx = canvas.getContext('2d');
      const spendingChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: <?= json_encode(array_column($categorySpending, 'name')) ?>,
          datasets: [{
            data: <?= json_encode(array_column($categorySpending, 'total_spent')) ?>,
            backgroundColor: [
              '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
              '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6B7280'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = ((context.parsed / total) * 100).toFixed(1);
                  return context.label + ': ' + context.parsed.toFixed(2) + ' MMK (' + percentage + '%)';
                }
              }
            }
          }
        }
      });
    });
  <?php endif; ?>
</script>