<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Budgets</h1>
        <p class="text-gray-600">Track and manage your monthly budgets</p>
        <button onclick="openAddBudgetModal()" class="mt-3 inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 font-medium text-white transition duration-150 hover:bg-blue-700 cursor-pointer sm:hidden">
            <i class="fas fa-plus mr-1"></i>
            New Budget
        </button>
    </div>
    <button onclick="openAddBudgetModal()" class="hidden bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 sm:inline-flex items-center cursor-pointer">
        <i class="fas fa-plus mr-1"></i>
        New Budget
    </button>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($budgets)): ?>
                    <?php foreach ($budgets as $budget): ?>
                        <?php
                            $amount = (float) ($budget['amount'] ?? 0);
                            $spent = (float) ($budget['spent_amount'] ?? 0);
                            $remaining = $amount - $spent;
                            $progressData = getBudgetProgress($amount, $spent);
                            $progress = $progressData['progress'];
                            $progressClamped = $progressData['progressClamped'];
                            $barClass = $progressData['barClass'];
                            $monthValue = date('Y-m', strtotime($budget['month_year']));
                            $monthLabel = date('F Y', strtotime($budget['month_year']));
                        ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($monthLabel) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($budget['category_name'] ?? '-') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= number_format($amount, 0) ?> MMK</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= number_format($spent, 0) ?> MMK</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm <?= $remaining < 0 ? 'text-red-700' : 'text-green-700' ?>">
                                <?= number_format($remaining, 0) ?> MMK
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-40 bg-gray-200 rounded-full h-2.5">
                                    <div class="<?= $barClass ?> h-2.5 rounded-full" style="width: <?= number_format($progressClamped, 2, '.', '') ?>%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1"><?= number_format($progress, 0) ?>%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button
                                    onclick="openEditBudgetModal(this)"
                                    data-id="<?= (int) ($budget['id'] ?? 0) ?>"
                                    data-category-id="<?= (int) ($budget['category_id'] ?? 0) ?>"
                                    data-amount="<?= htmlspecialchars((string) ($budget['amount'] ?? '')) ?>"
                                    data-month="<?= htmlspecialchars($monthValue) ?>"
                                    class="text-indigo-600 hover:text-indigo-900 cursor-pointer mr-3"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="openDeleteBudgetModal(<?= (int) ($budget['id'] ?? 0) ?>)" class="text-red-600 hover:text-red-900 cursor-pointer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No budgets found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="budgetModal" class="fixed z-50 inset-0 overflow-y-auto hidden">
    <div class="flex min-h-screen items-end justify-center px-3 pt-4 pb-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75 backdrop-blur"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block w-full max-w-md align-bottom bg-white rounded-t-xl sm:rounded-lg text-left overflow-y-auto shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-10 max-h-screen">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-wallet text-blue-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Add Monthly Budget</h3>
                        <div class="mt-2">
                            <form id="budgetForm" method="POST" action="">
                                <?= csrfField() ?>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="budget_month" class="block text-sm font-medium text-gray-700">Month</label>
                                        <input type="month" id="budget_month" name="month_year" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                    <div>
                                        <label for="budget_amount" class="block text-sm font-medium text-gray-700">Amount</label>
                                        <input type="number" step="0.01" id="budget_amount" name="amount" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00" required>
                                    </div>
                                </div>
                                <div>
                                    <label for="budget_category_id" class="block text-sm font-medium text-gray-700">Category</label>
                                    <select id="budget_category_id" name="category_id" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        <option value="">Select category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= (int) ($category['id'] ?? 0) ?>"><?= htmlspecialchars($category['name'] ?? '') ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" name="btnSaveBudget" form="budgetForm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                    Save Budget
                </button>
                <button onclick="closeAddBudgetModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<div id="editBudgetModal" class="fixed z-50 inset-0 overflow-y-auto hidden">
    <div class="flex min-h-screen items-end justify-center px-3 pt-4 pb-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75 backdrop-blur"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block w-full max-w-md align-bottom bg-white rounded-t-xl sm:rounded-lg text-left overflow-y-auto shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-10 max-h-screen">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-wallet text-blue-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Monthly Budget</h3>
                        <div class="mt-2">
                            <form id="editBudgetForm" method="POST" action="">
                                <?= csrfField() ?>
                                <input type="hidden" name="edit_budget_id" id="edit_budget_id">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="edit_budget_month" class="block text-sm font-medium text-gray-700">Month</label>
                                        <input type="month" id="edit_budget_month" name="month_year" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                    <div>
                                        <label for="edit_budget_amount" class="block text-sm font-medium text-gray-700">Amount</label>
                                        <input type="number" step="0.01" id="edit_budget_amount" name="amount" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                </div>
                                <div>
                                    <label for="edit_budget_category_id" class="block text-sm font-medium text-gray-700">Category</label>
                                    <select id="edit_budget_category_id" name="category_id" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        <option value="">Select category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= (int) ($category['id'] ?? 0) ?>"><?= htmlspecialchars($category['name'] ?? '') ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" name="btnUpdateBudget" form="editBudgetForm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                    Update Budget
                </button>
                <button onclick="closeEditBudgetModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-3 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<div id="deleteBudgetModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex min-h-screen items-end justify-center px-3 pt-4 pb-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block w-full max-w-md align-bottom bg-white rounded-t-xl sm:rounded-lg text-left overflow-y-auto shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-10 max-h-screen">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-exclamation text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Budget</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Are you sure you want to delete this budget? This action cannot be undone.</p>
                        </div>
                    </div>
                </div>
            </div>
            <form action="" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="id" id="delete-budget-id">
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" name="btnDeleteBudget" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                        Delete
                    </button>
                    <button onclick="closeDeleteBudgetModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
