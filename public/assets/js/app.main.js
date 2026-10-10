document.addEventListener('DOMContentLoaded', () => {
    const monthlyExpensesCanvas = document.getElementById('monthlyExpensesChart');
    if(!monthlyExpensesCanvas) return;

    const dashboardData = window.dashboardData || {};
    const monthlyData = dashboardData.monthly || {};
    const monthlyLabels = Array.isArray(monthlyData.labels) && monthlyData.labels.length ? monthlyData.labels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
    const monthlyValues = Array.isArray(monthlyData.values) && monthlyData.values.length ? monthlyData.values : [100000, 190000, 150000, 200000, 180000, 220000, 170000];

    const monthlyExpensesCtx = monthlyExpensesCanvas.getContext('2d');
        new Chart(monthlyExpensesCtx, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Expenses',
                    data: monthlyValues,
                    backgroundColor: '#6366F1',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

    //Breakdown Chart
    const expenseBreakdownCanvas = document.getElementById('expenseBreakdownChart');
    if (expenseBreakdownCanvas) {
        const dashboardData = window.dashboardData || {};
        const breakdownData = dashboardData.breakdown || {};
        const breakdownLabels = Array.isArray(breakdownData.labels) && breakdownData.labels.length
            ? breakdownData.labels
            : ['Food', 'Transportation', 'Utilities', 'Entertainment', 'Shopping', 'Others'];
        const breakdownValues = Array.isArray(breakdownData.values) && breakdownData.values.length
            ? breakdownData.values
            : [25, 15, 20, 10, 20, 10];

        const expenseBreakdownCtx = expenseBreakdownCanvas.getContext('2d');
        new Chart(expenseBreakdownCtx, {
            type: 'doughnut',
            data: {
                labels: breakdownLabels,
                datasets: [{
                    data: breakdownValues,
                    backgroundColor: [
                        '#6366F1',
                        '#10B981',
                        '#F59E0B',
                        '#8B5CF6',
                        '#EF4444',
                        '#64748B'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                },
                cutout: '70%'
            }
        });
    }
})
    // categories
    function openEditCategoryModal(btn) {
    document.getElementById('edit_category_id').value = btn.dataset.id;
    document.getElementById('categoryName').value = btn.dataset.name;
    document.getElementById('monthlyBudget').value = btn.dataset.budget;
    document.getElementById('editCategoryModal').classList.remove('hidden');
    }
    function closeEditCategoryModal() {
        document.getElementById('editCategoryModal').classList.add('hidden');
    }
    // expenses
    function openAddExpenseModal() {
        document.getElementById('expenseModal').classList.remove('hidden');
    }
    function closeAddExpenseModal() {
        document.getElementById('expenseModal').classList.add('hidden');
    }

    function openEditExpenseModal(btn) {
    const rawStatus = (btn.dataset.status ?? '').toString().trim().toLowerCase();
    const isPaid = rawStatus === '1' || rawStatus === 'true' || rawStatus === 'paid' || rawStatus === 'yes' || rawStatus === 'on';

    document.getElementById('edit_expense_id').value = btn.dataset.id;
    document.getElementById('edit_expense_date').value = btn.dataset.date;
    document.getElementById('edit_amount').value = btn.dataset.amount;
    document.getElementById('edit_category').value = btn.dataset.category;
    document.getElementById('edit_payment_method').value = btn.dataset.paymentMethodId;
    document.getElementById('edit_note').value = btn.dataset.note;
    document.getElementById('edit_status').checked = isPaid;

    document.getElementById('editExpenseModal').classList.remove('hidden');
    }

    function closeEditExpenseModal() {
        document.getElementById('editExpenseModal').classList.add('hidden');
    }

    function openDeleteExpenseModal(id) {
        document.getElementById('delete-id').value = id;
        document.getElementById('deleteExpenseModal').classList.remove('hidden');
    }

    function closeDeleteExpenseModal() {
        document.getElementById('deleteExpenseModal').classList.add('hidden');
    }
    // savings
    function openAddSavingModal() {
        const modal = document.getElementById('savingModal');
        if (!modal) return;
        modal.classList.remove('hidden');
    }

    function closeAddSavingModal() {
        const modal = document.getElementById('savingModal');
        if (!modal) return;
        modal.classList.add('hidden');
    }

    function openEditSavingModal(btn) {
        const modal = document.getElementById('editSavingModal');
        if (!modal) return;

        document.getElementById('edit_saving_id').value = btn.dataset.id || '';
        document.getElementById('edit_saving_name').value = btn.dataset.name || '';
        document.getElementById('edit_saving_description').value = btn.dataset.description || '';
        document.getElementById('edit_saving_target_amount').value = btn.dataset.targetAmount || '';
        document.getElementById('edit_saving_start_date').value = btn.dataset.startDate || '';
        document.getElementById('edit_saving_target_date').value = btn.dataset.targetDate || '';
        document.getElementById('edit_saving_status').value = btn.dataset.status || 'active';

        modal.classList.remove('hidden');
    }

    function closeEditSavingModal() {
        const modal = document.getElementById('editSavingModal');
        if (!modal) return;
        modal.classList.add('hidden');
    }

    function openDeleteSavingModal(id) {
        const modal = document.getElementById('deleteSavingModal');
        if (!modal) return;

        document.getElementById('delete-saving-id').value = id;
        modal.classList.remove('hidden');
    }

    function closeDeleteSavingModal() {
        const modal = document.getElementById('deleteSavingModal');
        if (!modal) return;
        modal.classList.add('hidden');
    }

    function openSavingTransactionModal(btn) {
        const modal = document.getElementById('savingTransactionModal');
        if (!modal) return;

        const savingId = btn.dataset.savingId || '';
        const savingName = btn.dataset.savingName || 'Saving Goal';
        const currentAmountRaw = parseFloat(btn.dataset.currentAmount || '0');
        const currentAmount = Number.isFinite(currentAmountRaw) ? currentAmountRaw : 0;

        document.getElementById('transaction_saving_id').value = savingId;
        document.getElementById('transaction_type').value = 'deposit';
        document.getElementById('transaction_amount').value = '';
        document.getElementById('transaction_note').value = '';
        document.getElementById('savingTransactionMeta').textContent = `${savingName} | Current: ${currentAmount.toLocaleString()} MMK`;

        modal.classList.remove('hidden');
    }

    function closeSavingTransactionModal() {
        const modal = document.getElementById('savingTransactionModal');
        if (!modal) return;
        modal.classList.add('hidden');
    }
    // budgets
    function openAddBudgetModal() {
        const modal = document.getElementById('budgetModal');
        if (!modal) return;
        modal.classList.remove('hidden');
    }

    function closeAddBudgetModal() {
        const modal = document.getElementById('budgetModal');
        if (!modal) return;
        modal.classList.add('hidden');
    }

    function openEditBudgetModal(btn) {
        const modal = document.getElementById('editBudgetModal');
        if (!modal) return;

        document.getElementById('edit_budget_id').value = btn.dataset.id || '';
        document.getElementById('edit_budget_category_id').value = btn.dataset.categoryId || '';
        document.getElementById('edit_budget_amount').value = btn.dataset.amount || '';
        document.getElementById('edit_budget_month').value = btn.dataset.month || '';

        modal.classList.remove('hidden');
    }

    function closeEditBudgetModal() {
        const modal = document.getElementById('editBudgetModal');
        if (!modal) return;
        modal.classList.add('hidden');
    }

    function openDeleteBudgetModal(id) {
        const modal = document.getElementById('deleteBudgetModal');
        if (!modal) return;

        document.getElementById('delete-budget-id').value = id;
        modal.classList.remove('hidden');
    }

    function closeDeleteBudgetModal() {
        const modal = document.getElementById('deleteBudgetModal');
        if (!modal) return;
        modal.classList.add('hidden');
    }
    // incomes
    function toggleModal(id, show) {
        document.getElementById(id)?.classList.toggle('hidden', !show);
    }

    function openAddIncomeModal() { toggleModal('incomeModal', true); }
    function closeAddIncomeModal() { toggleModal('incomeModal', false); }
    function closeEditIncomeModal() { toggleModal('editIncomeModal', false); }
    function closeDeleteIncomeModal() { toggleModal('deleteIncomeModal', false); }

    function openEditIncomeModal(btn) {
        document.getElementById('edit_income_id').value = btn.dataset.id || '';
        document.getElementById('edit_income_type_id').value = btn.dataset.typeId || '';
        document.getElementById('edit_income_amount').value = btn.dataset.amount || '';
        document.getElementById('edit_income_month').value = btn.dataset.month || '';
        document.getElementById('edit_income_note').value = btn.dataset.note || '';
        toggleModal('editIncomeModal', true);
    }

    function openDeleteIncomeModal(id) {
        document.getElementById('delete-income-id').value = id;
        toggleModal('deleteIncomeModal', true);
    }
    // income types
    function openAddIncomeTypeModal() { toggleModal('incomeTypeModal', true); }
    function closeAddIncomeTypeModal() { toggleModal('incomeTypeModal', false); }
    function closeEditIncomeTypeModal() { toggleModal('editIncomeTypeModal', false); }
    function closeDeleteIncomeTypeModal() { toggleModal('deleteIncomeTypeModal', false); }

    function openEditIncomeTypeModal(btn) {
        document.getElementById('edit_income_type_id').value = btn.dataset.id || '';
        document.getElementById('edit_income_type_name').value = btn.dataset.name || '';
        document.getElementById('edit_income_type_icon').value = btn.dataset.icon || '';
        document.getElementById('edit_income_type_color').value = btn.dataset.color || '#6B7280';
        toggleModal('editIncomeTypeModal', true);
    }

    function openDeleteIncomeTypeModal(id) {
        document.getElementById('delete-income-type-id').value = id;
        toggleModal('deleteIncomeTypeModal', true);
    }
    // reports
    // Expense Trend Chart
    const expenseTrendCanvas = document.getElementById('expenseTrendChart');
    if (expenseTrendCanvas) {
    const trendCtx = expenseTrendCanvas.getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
        labels: ['Jun 1', 'Jun 5', 'Jun 10', 'Jun 15', 'Jun 20', 'Jun 25', 'Jun 30'],
        datasets: [{
            label: 'Daily Expenses',
            data: [85, 120, 95, 245, 180, 150, 90],
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            borderColor: '#6366F1',
            borderWidth: 2,
            tension: 0.3,
            fill: true
        }]
        },
        options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
            display: false
            },
            tooltip: {
            mode: 'index',
            intersect: false
            }
        },
        scales: {
            y: {
            beginAtZero: true,
            grid: {
                drawBorder: false
            }
            },
            x: {
            grid: {
                display: false
            }
            }
        }
        }
    });
    }

    // Category Distribution Chart
    const categoryDistributionCanvas = document.getElementById('categoryDistributionChart');
    if (categoryDistributionCanvas) {
    const categoryCtx = categoryDistributionCanvas.getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
        labels: ['Food & Dining', 'Shopping', 'Transportation', 'Utilities', 'Entertainment', 'Others'],
        datasets: [{
            data: [1245, 845, 520, 320, 215, 100],
            backgroundColor: [
            '#EF4444',
            '#10B981',
            '#3B82F6',
            '#F59E0B',
            '#8B5CF6',
            '#64748B'
            ],
            borderWidth: 0
        }]
        },
        options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
            position: 'right',
            labels: {
                boxWidth: 12,
                padding: 20
            }
            }
        },
        cutout: '70%'
        }
    });
    }