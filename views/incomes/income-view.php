<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Income</h1>
        <p class="text-gray-600">Record and review your monthly income</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="income-types.php" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 font-medium text-gray-700 transition duration-150 hover:bg-gray-50">
            <i class="fas fa-tags mr-1"></i>
            Manage Types
        </a>
        <button onclick="openAddIncomeModal()" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 font-medium text-white transition duration-150 hover:bg-blue-700 cursor-pointer">
            <i class="fas fa-plus mr-1"></i>
            New Income
        </button>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-sm text-gray-500">This Month (<?= date('F Y') ?>)</p>
        <p class="mt-1 text-2xl font-bold text-green-700"><?= number_format($monthTotal, 0) ?> MMK</p>
    </div>
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-sm text-gray-500">All Time</p>
        <p class="mt-1 text-2xl font-bold text-gray-800"><?= number_format($overallTotal, 0) ?> MMK</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($incomes)): ?>
                    <?php foreach ($incomes as $income): ?>
                        <?php
                            $color = $income['type_color'] ?? '#6B7280';
                            $monthTs = strtotime($income['month_year']);
                        ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= date('F Y', $monthTs) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="shrink-0 h-8 w-8 rounded-full flex items-center justify-center" style="background-color: <?= htmlspecialchars($color) ?>1A">
                                        <i class="fa-solid fa-<?= htmlspecialchars($income['type_icon'] ?? 'question') ?> text-sm" style="color: <?= htmlspecialchars($color) ?>"></i>
                                    </div>
                                    <span class="ml-3 text-sm text-gray-900"><?= htmlspecialchars($income['type_name'] ?? '-') ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-700"><?= number_format((float) $income['amount'], 0) ?> MMK</td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?= htmlspecialchars($income['note'] ?? '-') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button
                                    onclick="openEditIncomeModal(this)"
                                    data-id="<?= (int) $income['id'] ?>"
                                    data-type-id="<?= (int) $income['income_type_id'] ?>"
                                    data-amount="<?= htmlspecialchars((string) $income['amount']) ?>"
                                    data-month="<?= date('Y-m', $monthTs) ?>"
                                    data-note="<?= htmlspecialchars($income['note'] ?? '') ?>"
                                    class="text-indigo-600 hover:text-indigo-900 cursor-pointer mr-3"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="openDeleteIncomeModal(<?= (int) $income['id'] ?>)" class="text-red-600 hover:text-red-900 cursor-pointer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No income recorded yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$formModals = [
    ['id' => 'incomeModal', 'form' => 'incomeForm', 'prefix' => '', 'heading' => 'Add Income', 'button' => 'btnSaveIncome', 'label' => 'Save Income', 'loading' => 'Saving...', 'close' => 'closeAddIncomeModal'],
    ['id' => 'editIncomeModal', 'form' => 'editIncomeForm', 'prefix' => 'edit_', 'heading' => 'Edit Income', 'button' => 'btnUpdateIncome', 'label' => 'Update Income', 'loading' => 'Updating...', 'close' => 'closeEditIncomeModal'],
];
?>
<?php foreach ($formModals as ['id' => $modalId, 'form' => $formId, 'prefix' => $prefix, 'heading' => $heading, 'button' => $btnName, 'label' => $btnLabel, 'loading' => $loadingText, 'close' => $closeFn]): ?>
<div id="<?= $modalId ?>" class="fixed z-50 inset-0 overflow-y-auto hidden">
    <div class="flex min-h-screen items-end justify-center px-3 pt-4 pb-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75 backdrop-blur"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block w-full max-w-md align-bottom bg-white rounded-t-xl sm:rounded-lg text-left overflow-y-auto shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-10 max-h-screen">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-sack-dollar text-green-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900"><?= $heading ?></h3>
                        <div class="mt-2">
                            <form id="<?= $formId ?>" method="POST" action="">
                                <?= csrfField() ?>
                                <?php if ($prefix !== ''): ?>
                                    <input type="hidden" name="edit_income_id" id="edit_income_id">
                                <?php endif; ?>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="<?= $prefix ?>income_month" class="block text-sm font-medium text-gray-700">Month</label>
                                        <input type="month" id="<?= $prefix ?>income_month" name="month_year" value="<?= date('Y-m') ?>" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                    <div>
                                        <label for="<?= $prefix ?>income_amount" class="block text-sm font-medium text-gray-700">Amount</label>
                                        <input type="number" step="0.01" min="0.01" id="<?= $prefix ?>income_amount" name="amount" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="<?= $prefix ?>income_type_id" class="block text-sm font-medium text-gray-700">Type</label>
                                    <select id="<?= $prefix ?>income_type_id" name="income_type_id" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        <option value="">Select type</option>
                                        <?php foreach ($incomeTypes as $type): ?>
                                            <option value="<?= (int) $type['id'] ?>"><?= htmlspecialchars($type['name'] ?? '') ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="<?= $prefix ?>income_note" class="block text-sm font-medium text-gray-700">Note <span class="text-gray-400">(optional)</span></label>
                                    <textarea id="<?= $prefix ?>income_note" name="note" rows="2" class="mt-1 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" name="<?= $btnName ?>" data-loading-text="<?= $loadingText ?>" form="<?= $formId ?>" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                    <?= $btnLabel ?>
                </button>
                <button onclick="<?= $closeFn ?>()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php
$deleteModal = [
    'id' => 'deleteIncomeModal',
    'title' => 'Delete Income',
    'message' => 'Are you sure you want to delete this income record? This action cannot be undone.',
    'inputId' => 'delete-income-id',
    'button' => 'btnDeleteIncome',
    'close' => 'closeDeleteIncomeModal',
];
include __DIR__ . '/../components/delete-modal.php';
