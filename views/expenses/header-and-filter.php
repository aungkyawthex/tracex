<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Expenses</h1>
        <p class="text-gray-600">Track and manage your expenses</p>
        <button onclick="openAddExpenseModal()" class="mt-3 inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 font-medium text-white transition duration-150 hover:bg-blue-700 cursor-pointer sm:hidden">
            <i class="fas fa-plus mr-1"></i>
            New Expense
        </button>
    </div>
    <button onclick="openAddExpenseModal()" class="hidden bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 sm:inline-flex items-center cursor-pointer">
        <i class="fas fa-plus mr-1"></i>
        New Expense
    </button>
</div>
<!-- filter -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form action="expenses.php" method="GET">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="date-range" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                <input id="date-range" name="date_range" value="<?php if(isset($_GET['date_range'])) echo htmlspecialchars($_GET['date_range']); ?>" type="text" class="flatpickr block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md placeholder:italic" placeholder="Select date range">
            </div>
            <div>
                <label for="category-filter" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="category-filter" name="category_id" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">Select Category </option>
                        <?php foreach($category_items as $category_item): ?>
                            <option value="<?= (int) ($category_item['id'] ?? 0) ?>" <?php if (isset($_GET['category_id']) && $_GET['category_id'] == $category_item['id']) echo 'selected'; ?>> <?= htmlspecialchars($category_item['name'] ?? '') ?> </option>
                        <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="amount-range" class="block text-sm font-medium text-gray-700 mb-1">Amount Range</label>
                <div class="flex space-x-2">
                    <input value="<?php if(isset($_GET['min_amount'])) echo htmlspecialchars($_GET['min_amount']); ?>" type="number" id="min-amount" name="min_amount" placeholder="Min" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <input value="<?php if(isset($_GET['max_amount'])) echo htmlspecialchars($_GET['max_amount']); ?>" type="number" id="max-amount" name="max_amount" placeholder="Max" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                </div>
            </div>
            <div class="flex items-end space-x-3 justify-end">
                <button type="submit" class="border border-sky-600 bg-sky-600 hover:bg-sky-700 text-white font-medium py-2 px-4 rounded-full transition duration-150 cursor-pointer">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="expenses.php" class="border border-gray-300 text-gray-500 hover:text-white hover:bg-gray-300 font-medium py-2 px-4 rounded-full transition duration-150 cursor-pointer text-center">
                    <i class="fa-solid fa-rotate-right mr-1"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>
