<?php
$formatMmk = fn(float $amount): string => number_format($amount, 0) . ' MMK';
$netClass = fn(float $net): string => $net < 0 ? 'text-red-600' : 'text-green-700';
$formatRate = fn(?float $rate): string => $rate === null ? '—' : number_format($rate, 1) . '%';
?>
<div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Reports</h1>
        <p class="text-gray-600">Income, expenses and what's left over &middot; <?= $view === 'monthly' ? $year : 'All time' ?></p>
    </div>
    <form method="GET" action="report.php" class="flex items-center gap-2">
        <div class="inline-flex rounded-lg border border-gray-300 bg-white p-1">
            <?php foreach (['monthly' => 'Monthly', 'yearly' => 'Yearly'] as $value => $label): ?>
                <a href="report.php?view=<?= $value ?>&year=<?= $year ?>" class="px-4 py-1.5 text-sm font-medium rounded-md <?= $view === $value ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' ?>"><?= $label ?></a>
            <?php endforeach; ?>
        </div>
        <?php if ($view === 'monthly'): ?>
            <input type="hidden" name="view" value="monthly">
            <label for="report-year" class="sr-only">Year</label>
            <select id="report-year" name="year" onchange="this.form.submit()" class="h-10 px-3 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <?php foreach ($years as $option): ?>
                    <option value="<?= $option ?>" <?= $option === $year ? 'selected' : '' ?>><?= $option ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </form>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php
    $cards = [
        ['label' => 'Total Income', 'value' => $formatMmk($totalIncome), 'class' => 'text-green-700', 'icon' => 'fa-sack-dollar', 'iconClass' => 'bg-green-100 text-green-600'],
        ['label' => 'Total Expenses', 'value' => $formatMmk($totalExpense), 'class' => 'text-red-600', 'icon' => 'fa-receipt', 'iconClass' => 'bg-red-100 text-red-600'],
        ['label' => 'Net (Income − Expenses)', 'value' => $formatMmk($totalNet), 'class' => $netClass($totalNet), 'icon' => 'fa-scale-balanced', 'iconClass' => 'bg-blue-100 text-blue-600'],
        ['label' => 'Savings Rate', 'value' => $formatRate($totalRate), 'class' => $netClass($totalNet), 'icon' => 'fa-piggy-bank', 'iconClass' => 'bg-purple-100 text-purple-600'],
    ];
    ?>
    <?php foreach ($cards as $card): ?>
        <div class="bg-white rounded-lg shadow p-5 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500"><?= $card['label'] ?></p>
                <p class="mt-1 text-2xl font-bold <?= $card['class'] ?>"><?= $card['value'] ?></p>
            </div>
            <div class="h-11 w-11 shrink-0 rounded-full flex items-center justify-center <?= $card['iconClass'] ?>">
                <i class="fas <?= $card['icon'] ?>"></i>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4"><?= $view === 'monthly' ? 'Monthly' : 'Yearly' ?> Income vs Expenses</h2>
    <div class="h-80">
        <canvas id="incomeExpenseChart"></canvas>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow overflow-hidden lg:col-span-2">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <?php foreach ([$view === 'monthly' ? 'Month' : 'Year', 'Income', 'Expenses', 'Difference', 'Savings Rate'] as $i => $heading): ?>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider <?= $i === 0 ? 'text-left' : 'text-right' ?>"><?= $heading ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?= $row['label'] ?></td>
                            <td class="px-6 py-3 whitespace-nowrap text-sm text-right text-gray-900"><?= $formatMmk($row['income']) ?></td>
                            <td class="px-6 py-3 whitespace-nowrap text-sm text-right text-gray-900"><?= $formatMmk($row['expense']) ?></td>
                            <td class="px-6 py-3 whitespace-nowrap text-sm text-right font-medium <?= $netClass($row['net']) ?>"><?= $formatMmk($row['net']) ?></td>
                            <td class="px-6 py-3 whitespace-nowrap text-sm text-right text-gray-500"><?= $formatRate($row['rate']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td class="px-6 py-3 text-sm text-gray-900">Total</td>
                        <td class="px-6 py-3 text-sm text-right text-gray-900"><?= $formatMmk($totalIncome) ?></td>
                        <td class="px-6 py-3 text-sm text-right text-gray-900"><?= $formatMmk($totalExpense) ?></td>
                        <td class="px-6 py-3 text-sm text-right <?= $netClass($totalNet) ?>"><?= $formatMmk($totalNet) ?></td>
                        <td class="px-6 py-3 text-sm text-right text-gray-500"><?= $formatRate($totalRate) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Income by Type</h2>
        <?php if (empty($incomeByType)): ?>
            <p class="text-sm text-gray-500">No income recorded for this period.</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($incomeByType as $type): ?>
                    <?php
                        $share = (float) $type['total'] / $totalIncome * 100;
                        $color = htmlspecialchars($type['color'] ?? '#6B7280');
                    ?>
                    <div>
                        <div class="flex justify-between mb-1 text-sm font-medium text-gray-700">
                            <span><?= htmlspecialchars($type['name']) ?></span>
                            <span><?= $formatMmk((float) $type['total']) ?> (<?= number_format($share, 0) ?>%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full" style="width: <?= number_format($share, 2, '.', '') ?>%; background-color: <?= $color ?>"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('incomeExpenseChart');
        if (!window.Chart || !canvas) return;

        new Chart(canvas, {
            data: {
                labels: <?= json_encode(array_column($rows, 'label')) ?>,
                datasets: [
                    { type: 'line', label: 'Net', data: <?= json_encode(array_column($rows, 'net')) ?>, borderColor: '#2563EB', backgroundColor: '#2563EB', tension: 0.3 },
                    { type: 'bar', label: 'Income', data: <?= json_encode(array_column($rows, 'income')) ?>, backgroundColor: '#10B981', borderRadius: 4 },
                    { type: 'bar', label: 'Expenses', data: <?= json_encode(array_column($rows, 'expense')) ?>, backgroundColor: '#EF4444', borderRadius: 4 },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString()} MMK` } },
                },
                scales: { y: { ticks: { callback: (value) => value.toLocaleString() } } },
            },
        });
    });
</script>
