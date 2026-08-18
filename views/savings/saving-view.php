<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Savings</h1>
        <p class="text-gray-600">Track and manage your savings goals</p>
        <button onclick="openAddSavingModal()" class="mt-3 inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 font-medium text-white transition duration-150 hover:bg-blue-700 cursor-pointer sm:hidden">
            <i class="fas fa-plus mr-1"></i>
            New Saving
        </button>
    </div>
    <button onclick="openAddSavingModal()" class="hidden bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 sm:inline-flex items-center cursor-pointer">
        <i class="fas fa-plus mr-1"></i>
        New Saving
    </button>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($savings)): ?>
                    <?php foreach ($savings as $saving): ?>
                        <?php
                            $target = (float) ($saving['target_amount'] ?? 0);
                            $current = (float) ($saving['current_amount'] ?? 0);
                            $progress = $target > 0 ? min(100, max(0, ($current / $target) * 100)) : 0;
                            $status = $saving['status'] ?? 'active';
                            $statusClass = 'bg-blue-100 text-blue-800';
                            if ($status === 'completed') {
                                $statusClass = 'bg-green-100 text-green-800';
                            } elseif ($status === 'cancelled') {
                                $statusClass = 'bg-gray-100 text-gray-700';
                            }
                        ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($saving['name'] ?? '-') ?></div>
                                <div class="text-xs text-gray-500"><?= htmlspecialchars($saving['description'] ?? '') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= number_format($target, 0) ?> MMK</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= number_format($current, 0) ?> MMK</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-44 bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: <?= number_format($progress, 2, '.', '') ?>%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1"><?= number_format($progress, 0) ?>%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>"><?= htmlspecialchars(ucfirst($status)) ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button
                                    onclick="openSavingTransactionModal(this)"
                                    data-saving-id="<?= (int) ($saving['id'] ?? 0) ?>"
                                    data-saving-name="<?= htmlspecialchars($saving['name'] ?? '') ?>"
                                    data-current-amount="<?= htmlspecialchars((string) ($saving['current_amount'] ?? '0')) ?>"
                                    class="text-green-600 hover:text-green-900 cursor-pointer mr-3"
                                >
                                    <i class="fas fa-right-left mr-1"></i> Transaction
                                </button>
                                <button
                                    onclick="openEditSavingModal(this)"
                                    data-id="<?= (int) ($saving['id'] ?? 0) ?>"
                                    data-name="<?= htmlspecialchars($saving['name'] ?? '') ?>"
                                    data-description="<?= htmlspecialchars($saving['description'] ?? '') ?>"
                                    data-target-amount="<?= htmlspecialchars((string) ($saving['target_amount'] ?? '')) ?>"
                                    data-start-date="<?= htmlspecialchars($saving['start_date'] ?? '') ?>"
                                    data-target-date="<?= htmlspecialchars($saving['target_date'] ?? '') ?>"
                                    data-status="<?= htmlspecialchars($saving['status'] ?? 'active') ?>"
                                    class="text-indigo-600 hover:text-indigo-900 cursor-pointer mr-3"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="openDeleteSavingModal(<?= (int) ($saving['id'] ?? 0) ?>)" class="text-red-600 hover:text-red-900 cursor-pointer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No savings goals found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden mt-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Recent Saving Transactions</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saving Goal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($savingTransactions)): ?>
                    <?php foreach ($savingTransactions as $txn): ?>
                        <?php $isDeposit = ($txn['type'] ?? '') === 'deposit'; ?>   
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('M j, Y g:i A', strtotime($txn['created_at'])) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($txn['saving_name'] ?? '-') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $isDeposit ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $isDeposit ? 'Deposit' : 'Withdraw' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium <?= $isDeposit ? 'text-green-700' : 'text-red-700' ?>">
                                <?= $isDeposit ? '+' : '-' ?><?= number_format((float) ($txn['amount'] ?? 0), 0) ?> MMK
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($txn['note'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No saving transactions yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="savingModal" class="fixed z-50 inset-0 overflow-y-auto hidden">
    <div class="flex min-h-screen items-end justify-center px-3 pt-4 pb-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75 backdrop-blur"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block w-full max-w-md align-bottom bg-white rounded-t-xl sm:rounded-lg text-left overflow-y-auto shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-10 max-h-screen">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-piggy-bank text-blue-600"></i>
                    </div>
                    <div class="mt-3 sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Add Saving Goal</h3>
                        <div class="mt-2">
                            <form id="savingForm" method="POST" action="">
                                <?= csrfField() ?>
                                <div class="mb-4">
                                    <label for="saving_name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" id="saving_name" name="name" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Emergency Fund">
                                </div>
                                <div class="mb-4">
                                    <label for="saving_description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea id="saving_description" rows="3" name="description" class="mt-1 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Optional description"></textarea>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="saving_target_amount" class="block text-sm font-medium text-gray-700">Target Amount</label>
                                        <input type="number" step="0.01" id="saving_target_amount" name="target_amount" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                                    </div>
                                    <div>
                                        <label for="saving_status" class="block text-sm font-medium text-gray-700">Status</label>
                                        <select id="saving_status" name="status" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="active">Active</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="saving_start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                        <input type="date" id="saving_start_date" name="start_date" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label for="saving_target_date" class="block text-sm font-medium text-gray-700">Target Date</label>
                                        <input type="date" id="saving_target_date" name="target_date" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" name="btnSaveSaving" form="savingForm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                    Save Goal
                </button>
                <button onclick="closeAddSavingModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<div id="savingTransactionModal" class="fixed z-50 inset-0 overflow-y-auto hidden">
    <div class="flex min-h-screen items-end justify-center px-3 pt-4 pb-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75 backdrop-blur"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block w-full max-w-md align-bottom bg-white rounded-t-xl sm:rounded-lg text-left overflow-y-auto shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-10 max-h-screen">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-right-left text-blue-600"></i>
                    </div>
                    <div class="mt-3 sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Add Saving Transaction</h3>
                        <p id="savingTransactionMeta" class="text-xs text-gray-500 mt-1"></p>
                        <div class="mt-2">
                            <form id="savingTransactionForm" method="POST" action="">
                                <?= csrfField() ?>
                                <input type="hidden" name="saving_id" id="transaction_saving_id">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="transaction_type" class="block text-sm font-medium text-gray-700">Type</label>
                                        <select id="transaction_type" name="type" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="deposit">Deposit</option>
                                            <option value="withdraw">Withdraw</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="transaction_amount" class="block text-sm font-medium text-gray-700">Amount</label>
                                        <input type="number" step="0.01" id="transaction_amount" name="amount" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                                    </div>
                                </div>
                                <div>
                                    <label for="transaction_note" class="block text-sm font-medium text-gray-700">Note</label>
                                    <textarea id="transaction_note" rows="3" name="note" class="mt-1 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Optional note"></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" name="btnSaveSavingTransaction" form="savingTransactionForm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                    Save Transaction
                </button>
                <button onclick="closeSavingTransactionModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<div id="editSavingModal" class="fixed z-50 inset-0 overflow-y-auto hidden">
    <div class="flex min-h-screen items-end justify-center px-3 pt-4 pb-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75 backdrop-blur"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block w-full max-w-md align-bottom bg-white rounded-t-xl sm:rounded-lg text-left overflow-y-auto shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-10 max-h-screen">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-piggy-bank text-blue-600"></i>
                    </div>
                    <div class="mt-3 sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Saving Goal</h3>
                        <div class="mt-2">
                            <form id="editSavingForm" method="POST" action="">
                                <?= csrfField() ?>
                                <input type="hidden" name="edit_saving_id" id="edit_saving_id">
                                <div class="mb-4">
                                    <label for="edit_saving_name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" id="edit_saving_name" name="name" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="mb-4">
                                    <label for="edit_saving_description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea id="edit_saving_description" rows="3" name="description" class="mt-1 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="edit_saving_target_amount" class="block text-sm font-medium text-gray-700">Target Amount</label>
                                        <input type="number" step="0.01" id="edit_saving_target_amount" name="target_amount" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label for="edit_saving_status" class="block text-sm font-medium text-gray-700">Status</label>
                                        <select id="edit_saving_status" name="status" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="active">Active</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="edit_saving_start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                        <input type="date" id="edit_saving_start_date" name="start_date" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label for="edit_saving_target_date" class="block text-sm font-medium text-gray-700">Target Date</label>
                                        <input type="date" id="edit_saving_target_date" name="target_date" class="mt-1 h-10 p-2 w-full sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" name="btnUpdateSaving" form="editSavingForm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                    Update Goal
                </button>
                <button onclick="closeEditSavingModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-3 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<div id="deleteSavingModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
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
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Saving Goal</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Are you sure you want to delete this saving goal? This action cannot be undone.</p>
                        </div>
                    </div>
                </div>
            </div>
            <form action="" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="id" id="delete-saving-id">
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" name="btnDeleteSaving" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                        Delete
                    </button>
                    <button onclick="closeDeleteSavingModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
