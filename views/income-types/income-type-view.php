<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="incomes.php" class="text-sm text-blue-600 hover:text-blue-800"><i class="fas fa-arrow-left mr-1"></i> Back to Income</a>
        <h1 class="text-2xl font-bold text-gray-800">Income Types</h1>
        <p class="text-gray-600">Organize where your income comes from</p>
        <button onclick="openAddIncomeTypeModal()" class="mt-3 inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 font-medium text-white transition duration-150 hover:bg-blue-700 cursor-pointer sm:hidden">
            <i class="fas fa-plus mr-1"></i>
            New Type
        </button>
    </div>
    <button onclick="openAddIncomeTypeModal()" class="hidden bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 sm:inline-flex items-center cursor-pointer">
        <i class="fas fa-plus mr-1"></i>
        New Type
    </button>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Income Records</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($incomeTypes)): ?>
                    <?php foreach ($incomeTypes as $type): ?>
                        <?php
                            $color = $type['color'] ?? '#6B7280';
                            $isCustom = $type['user_id'] !== null;
                        ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="shrink-0 h-10 w-10 rounded-full flex items-center justify-center" style="background-color: <?= htmlspecialchars($color) ?>1A">
                                        <i class="fa-solid fa-<?= htmlspecialchars($type['icon'] ?? 'question') ?>" style="color: <?= htmlspecialchars($color) ?>"></i>
                                    </div>
                                    <div class="ml-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($type['name'] ?? '') ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-medium <?= $isCustom ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' ?>">
                                    <?= $isCustom ? 'Custom' : 'Default' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= (int) ($type['income_count'] ?? 0) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <?php if ($isCustom): ?>
                                    <button
                                        onclick="openEditIncomeTypeModal(this)"
                                        data-id="<?= (int) $type['id'] ?>"
                                        data-name="<?= htmlspecialchars($type['name'] ?? '') ?>"
                                        data-icon="<?= htmlspecialchars($type['icon'] ?? '') ?>"
                                        data-color="<?= htmlspecialchars($color) ?>"
                                        class="text-indigo-600 hover:text-indigo-900 cursor-pointer mr-3"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="openDeleteIncomeTypeModal(<?= (int) $type['id'] ?>)" class="text-red-600 hover:text-red-900 cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-gray-400" title="Default types cannot be changed"><i class="fas fa-lock"></i></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No income types found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$formModals = [
    ['id' => 'incomeTypeModal', 'form' => 'incomeTypeForm', 'prefix' => '', 'heading' => 'Add Income Type', 'button' => 'btnSaveIncomeType', 'label' => 'Save Type', 'loading' => 'Saving...', 'close' => 'closeAddIncomeTypeModal'],
    ['id' => 'editIncomeTypeModal', 'form' => 'editIncomeTypeForm', 'prefix' => 'edit_', 'heading' => 'Edit Income Type', 'button' => 'btnUpdateIncomeType', 'label' => 'Update Type', 'loading' => 'Updating...', 'close' => 'closeEditIncomeTypeModal'],
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
                    <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-tags text-blue-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900"><?= $heading ?></h3>
                        <div class="mt-2">
                            <form id="<?= $formId ?>" method="POST" action="">
                                <?= csrfField() ?>
                                <?php if ($prefix !== ''): ?>
                                    <input type="hidden" name="edit_income_type_id" id="edit_income_type_id">
                                <?php endif; ?>
                                <div class="mb-4">
                                    <label for="<?= $prefix ?>income_type_name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" id="<?= $prefix ?>income_type_name" name="name" maxlength="100" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. Side Project" required>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="<?= $prefix ?>income_type_icon" class="block text-sm font-medium text-gray-700">Icon</label>
                                        <select id="<?= $prefix ?>income_type_icon" name="icon" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                            <?php foreach ($incomeTypeIcons as $icon): ?>
                                                <option value="<?= $icon ?>"><?= ucwords(str_replace('-', ' ', $icon)) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="<?= $prefix ?>income_type_color" class="block text-sm font-medium text-gray-700">Color</label>
                                        <input type="color" id="<?= $prefix ?>income_type_color" name="color" value="#10B981" class="mt-1 h-10 p-1 w-full border border-gray-300 rounded-md cursor-pointer" required>
                                    </div>
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
    'id' => 'deleteIncomeTypeModal',
    'title' => 'Delete Income Type',
    'message' => 'Are you sure you want to delete this income type? Types that are used by income records cannot be deleted.',
    'inputId' => 'delete-income-type-id',
    'button' => 'btnDeleteIncomeType',
    'close' => 'closeDeleteIncomeTypeModal',
];
include __DIR__ . '/../components/delete-modal.php';
