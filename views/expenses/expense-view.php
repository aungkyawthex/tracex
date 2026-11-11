<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Spent</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if(count($expenses) > 0 ): ?>
                    <?php foreach($expenses as $expense): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"> <?= date("M j, Y", strtotime($expense['expense_date'] ?? '')) ?> </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"> <?= htmlspecialchars($expense['note'] ?? '-') ?> </div>
                            </td>

                            <?php
                                $categories = [
                                    'Food & Dining'   => ['icon' => 'fa-utensils',     'bg' => 'bg-red-100',      'text' => 'text-red-800'],
                                    'Utilities'       => ['icon' => 'fa-lightbulb',    'bg' => 'bg-yellow-100',   'text' => 'text-yellow-800'],
                                    'Transportation'  => ['icon' => 'fa-car',          'bg' => 'bg-blue-100',     'text' => 'text-blue-800'],
                                    'Entertainment'   => ['icon' => 'fa-film',         'bg' => 'bg-purple-100',   'text' => 'text-purple-800'],
                                    'Shopping'        => ['icon' => 'fa-shopping-cart', 'bg' => 'bg-rose-100',    'text' => 'text-rose-800'],
                                    'Healthcare'      => ['icon' => 'fa-heartbeat',     'bg' => 'bg-green-100',   'text' => 'text-green-800'],
                                    'Travel'          => ['icon' => 'fa-plane',         'bg' => 'bg-sky-100',  'text' => 'text-cyan-800'],
                                    'Education'       => ['icon' => 'fa-book',          'bg' => 'bg-blue-100',  'text' => 'text-blue-800'],
                                    'Bills & Payments' => ['icon' => 'fa-file-invoice-dollar', 'bg' => 'bg-orange-100', 'text' => 'text-orange-800'],
                                    'Others'          => ['icon' => 'fa-ellipsis-h',     'bg' => 'bg-gray-100', 'text' => 'text-gray-800']
                                ];
                                // Normalize category name - trim and remove any non-printable characters
                                $cat = preg_replace('/[\x00-\x1F\x7F]/u', '', trim($expense['category_name'] ?? '-'));
                                $catInfo = ['icon' => 'fa-question', 'bg' => 'bg-gray-100', 'text' => 'text-gray-800'];
                                
                                // Try direct lookup first
                                if (isset($categories[$cat])) {
                                    $catInfo = $categories[$cat];
                                } else {
                                    // Case-insensitive lookup with normalized comparison
                                    foreach ($categories as $key => $value) {
                                        $normalizedKey = preg_replace('/[\x00-\x1F\x7F]/u', '', trim($key));
                                        if (strcasecmp($normalizedKey, $cat) === 0) {
                                            $catInfo = $value;
                                            break;
                                        }
                                    }
                                }
                            ?>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex items-center text-xs leading-5 font-semibold rounded-full <?= htmlspecialchars($catInfo['bg']) ?> <?= htmlspecialchars($catInfo['text']) ?>">
                                    <i class="fas <?= htmlspecialchars($catInfo['icon']) ?> mr-1"></i> <?= htmlspecialchars($cat) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($expense['payment_method'] ?? '') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"> <?= number_format($expense['amount'] ?? '0', 0) ?> Ks</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                    $expenseStatus = strtolower(trim((string) ($expense['status'] ?? '')));
                                    $isPaid = in_array($expenseStatus, ['1', 'true', 'paid', 'yes', 'on'], true);
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $isPaid ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>"> <?= $isPaid ? "Paid" : "Pending" ?> </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="openEditExpenseModal(this)" 
                                    data-id="<?= (int) ($expense['id'] ?? 0) ?>"
                                    data-date="<?= htmlspecialchars($expense['expense_date'] ?? '') ?>"
                                    data-amount="<?= htmlspecialchars((string) ($expense['amount'] ?? '')) ?>"
                                    data-category="<?= (int) ($expense['category_id'] ?? 0) ?>"
                                    data-payment-method-id="<?= (int) ($expense['payment_method_id'] ?? 0) ?>"
                                    data-note="<?= htmlspecialchars($expense['note'] ?? '') ?>"
                                    data-status="<?= ($expense['status'] ?? '') ?>"
                                    class="text-blue-600 hover:text-blue-900 cursor-pointer mr-3"> 
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="openDeleteExpenseModal(<?= (int) ($expense['id'] ?? 0) ?>)"
                                    class="text-red-600 hover:text-red-900 cursor-pointer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center"> No expenses found. </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
