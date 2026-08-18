<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- recent transactions -->
    <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Recent Transactions</h2>
            <a href="/public/expenses.php" class="text-sm text-indigo-600 hover:text-indigo-500">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($recentTransactions)): ?>
                        <?php foreach ($recentTransactions as $txn): ?>
                            <?php
                                $status = strtolower(trim((string) ($txn['status'] ?? '')));
                                $isPaid = in_array($status, ['1', 'true', 'paid', 'yes', 'on'], true);
                            ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('M j, Y', strtotime($txn['expense_date'])) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($txn['note'] ?: '-') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($txn['category_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= number_format((float)$txn['amount'], 0) ?> MMK</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $isPaid ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                        <?= $isPaid ? 'Paid' : 'Pending' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-sm text-gray-500 text-center">No transactions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- budget progress -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Budget Progress</h2>
        <div class="space-y-4">
            <?php if (!empty($budgetProgressItems)): ?>
                <?php foreach ($budgetProgressItems as $budgetItem): ?>
                    <?php
                        $amount = (float) ($budgetItem['amount'] ?? 0);
                        $spent = (float) ($budgetItem['spent_amount'] ?? 0);
                        $progressData = getBudgetProgress($amount, $spent);
                        $progressClamped = $progressData['progressClamped'];
                        $barClass = $progressData['barClass'];
                    ?>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($budgetItem['category_name'] ?? '-') ?></span>
                            <span class="text-sm font-medium text-gray-700"><?= number_format($spent, 0) ?>/<?= number_format($amount, 0) ?> MMK</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="<?= $barClass ?> h-2.5 rounded-full" style="width: <?= number_format($progressClamped, 2, '.', '') ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-sm text-gray-500">No budgets found for this month.</p>
            <?php endif; ?>
        </div>
        
        <div class="mt-6">
            <a href="<?= url('public/budgets.php') ?>" class="block text-center w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150">
                Add New Budget
            </a>
        </div>
    </div>
</div>
