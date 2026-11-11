<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Categories</h1>
        <p class="text-gray-600">View your expense count with categories</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between p-4 border-b border-gray-200">
        <div class="mb-4 md:mb-0">
            <form action="categories.php" method="GET" class="flex items-center space-x-2">
                <div class="relative max-w-xs">
                    <label for="search" class="sr-only">Search</label>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" value="<?php if(isset($_GET['search'])) echo htmlspecialchars($_GET['search']); ?>" id="search" name="search" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-full leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-sky-600 sm:text-sm placeholder:italic" placeholder="Search categories...">
                </div>
                <button type="submit" class="bg-sky-600 hover:bg-sky-700 border-2 border-sky-600 text-white px-4 py-2 rounded-full cursor-pointer">Search</button>
                <a href="categories.php" class="border-2 border-gray-300 hover:bg-gray-300 text-gray-500 hover:text-white px-5 py-2 rounded-full cursor-pointer">Reset</a>
            </form>
        </div>
        <!-- <div class="flex">
            <button class="px-3 py-1 border border-gray-300 cursor-pointer rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-download mr-1"></i> Export
            </button>
        </div> -->
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Expenses</th>
                    <!-- <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th> -->
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                    $category_bg = [
                        'utensils' => 'bg-red-100',
                        'bus' => 'bg-blue-100',
                        'film' => 'bg-purple-100',
                        'lightbulb' => 'bg-yellow-100',
                        'shopping-cart' => 'bg-rose-100',
                        'heart' => 'bg-green-100',
                        'graduation-cap' => 'bg-blue-100',
                        'plane' => 'bg-sky-100',
                        'file-invoice-dollar' => 'bg-orange-100',
                        'question' => 'bg-gray-100'
                    ];
                ?>

                <?php if(count($categories) > 0): ?>
                    <?php foreach($categories as $category): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="shrink-0 h-10 w-10 rounded-full <?= $category_bg[$category['icon']] ?? 'bg-gray-100' ?> flex items-center justify-center">
                                        <i class="fa-solid fa-<?= htmlspecialchars($category['icon'] ?? 'question') ?>" style="color: <?= htmlspecialchars($category['color'] ?? '#6B7280') ?>"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"> <?= htmlspecialchars($category['name'] ?? 'N/A') ?> </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"> <?= htmlspecialchars($category['description'] ?? 'N/A') ?> </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"> <?= $expenseCounts[$category['id']] ?? 0 ?> </td>
                            <!-- <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="openEditCategoryModal()" 
                                    class="text-blue-600 hover:text-blue-900 mr-3 cursor-pointer" 
                                    title="Edit Category">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td> -->
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center"> No categories found. </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
