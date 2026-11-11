<div class="bg-white rounded-xl shadow-sm p-6 mt-2">
  <h3 class="text-lg font-semibold mb-6 text-gray-800">Profile Statistics</h3>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="text-center p-6 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
      <p class="text-3xl font-bold text-blue-600 mb-2"><?= number_format((int) ($expensesAdded ?? 0)) ?></p>
      <p class="text-sm text-gray-600 font-medium">Expenses Added</p>
    </div>
    <div class="text-center p-6 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
      <p class="text-3xl font-bold text-green-600 mb-2"><?= number_format((int) ($categoriesTotal ?? 0)) ?></p>
      <p class="text-sm text-gray-600 font-medium">Categories</p>
    </div>
    <div class="text-center p-6 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
      <p class="text-3xl font-bold text-blue-600 mb-2"><?= number_format((int) ($savingsGoals ?? 0)) ?></p>
      <p class="text-sm text-gray-600 font-medium">Savings Goals</p>
    </div>
    <div class="text-center p-6 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
      <p class="text-3xl font-bold text-purple-600 mb-2"><?= number_format((int) ($daysActive ?? 0)) ?></p>
      <p class="text-sm text-gray-600 font-medium">Days Active</p>
    </div>
  </div>
</div>
