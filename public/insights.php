<?php
require __DIR__ . '/../src/auth_page.php';

$title = 'Insights - TraceX';

  $currentMonth = date('Y-m-01');
  $nextMonth = date('Y-m-01', strtotime('+1 month'));

  // Get spending per category this month
  $spendingStmt = $pdo->prepare("
    SELECT c.name, c.color, SUM(e.amount) as total_spent, COUNT(e.id) as expense_count, MAX(e.amount) as max_expense
    FROM expenses e
    JOIN categories c ON e.category_id = c.id
    WHERE e.user_id = :user_id AND e.expense_date >= :start_date AND e.expense_date < :end_date
    GROUP BY e.category_id, c.name, c.color
    ORDER BY total_spent DESC
  ");
  $spendingStmt->execute([
    ':user_id' => $userId,
    ':start_date' => $currentMonth,
    ':end_date' => $nextMonth
  ]);
  $categorySpending = $spendingStmt->fetchAll(PDO::FETCH_ASSOC);

  // Get all expenses for this month to show recent transactions per category
  $expensesStmt = $pdo->prepare("
    SELECT e.id, e.amount, e.note, e.expense_date, c.name as category_name
    FROM expenses e
    JOIN categories c ON e.category_id = c.id
    WHERE e.user_id = :user_id AND e.expense_date >= :start_date AND e.expense_date < :end_date
    ORDER BY e.expense_date DESC, e.id DESC
  ");
  $expensesStmt->execute([
    ':user_id' => $userId,
    ':start_date' => $currentMonth,
    ':end_date' => $nextMonth
  ]);
  $allExpenses = $expensesStmt->fetchAll(PDO::FETCH_ASSOC);
  
  $expensesByCategory = [];
  foreach ($allExpenses as $expense) {
      $catName = $expense['category_name'];
      if (!isset($expensesByCategory[$catName])) {
          $expensesByCategory[$catName] = [];
      }
      $expensesByCategory[$catName][] = $expense;
  }

  // Get budgets for this month
  $budgetStmt = $pdo->prepare("
    SELECT c.name, b.amount as budget_amount
    FROM budgets b
    JOIN categories c ON b.category_id = c.id
    WHERE b.user_id = :user_id AND b.month_year = :month_year
  ");
  $budgetStmt->execute([
    ':user_id' => $userId,
    ':month_year' => $currentMonth
  ]);
  $budgets = $budgetStmt->fetchAll(PDO::FETCH_ASSOC);

  // Create a map of category to budget
  $budgetMap = [];
  foreach ($budgets as $budget) {
    $budgetMap[$budget['name']] = $budget['budget_amount'];
  }

  $totalSpent = array_sum(array_column($categorySpending, 'total_spent'));

  // Calculate Daily Average
  $daysInMonth = date('t');
  $currentDay = date('j');
  $isCurrentMonth = date('Y-m') === date('Y-m', strtotime($currentMonth));
  $daysPassed = $isCurrentMonth ? $currentDay : $daysInMonth;
  $dailyAverage = $daysPassed > 0 ? $totalSpent / $daysPassed : 0;

  // Get previous month spending for trend comparison
  $prevMonthStart = date('Y-m-01', strtotime('-1 month'));
  $prevMonthEnd = date('Y-m-01');
  $prevSpendingStmt = $pdo->prepare("
    SELECT SUM(amount) as total_spent
    FROM expenses
    WHERE user_id = :user_id AND expense_date >= :start_date AND expense_date < :end_date
  ");
  $prevSpendingStmt->execute([
    ':user_id' => $userId,
    ':start_date' => $prevMonthStart,
    ':end_date' => $prevMonthEnd
  ]);
  $prevTotalSpent = $prevSpendingStmt->fetchColumn() ?: 0;
  
  $trend = 0;
  if ($prevTotalSpent > 0) {
      $trend = (($totalSpent - $prevTotalSpent) / $prevTotalSpent) * 100;
  } else if ($totalSpent > 0) {
      $trend = 100; // 100% increase if prev was 0 and now we have spending
  }

  // Get Top 5 Largest Expenses
  $topExpensesStmt = $pdo->prepare("
    SELECT e.id, e.amount, e.note, e.expense_date, c.name as category_name, c.color as category_color
    FROM expenses e
    JOIN categories c ON e.category_id = c.id
    WHERE e.user_id = :user_id AND e.expense_date >= :start_date AND e.expense_date < :end_date
    ORDER BY e.amount DESC
    LIMIT 5
  ");
  $topExpensesStmt->execute([
    ':user_id' => $userId,
    ':start_date' => $currentMonth,
    ':end_date' => $nextMonth
  ]);
  $topExpenses = $topExpensesStmt->fetchAll(PDO::FETCH_ASSOC);

  // Generate insights
  $insights = [];
  foreach ($categorySpending as $spend) {
    $category = $spend['name'];
    $spent = $spend['total_spent'];
    $percentage = $totalSpent > 0 ? ($spent / $totalSpent) * 100 : 0;

    $insight = [
      'category' => $category,
      'color' => $spend['color'],
      'spent' => $spent,
      'expense_count' => $spend['expense_count'],
      'max_expense' => $spend['max_expense'] ?? 0,
      'avg_expense' => $spend['expense_count'] > 0 ? $spent / $spend['expense_count'] : 0,
      'percentage' => round($percentage, 1),
      'budget' => $budgetMap[$category] ?? null,
      'over_budget' => isset($budgetMap[$category]) && $spent > $budgetMap[$category],
      'recent_expenses' => array_slice($expensesByCategory[$category] ?? [], 0, 3),
      'suggestions' => []
    ];

    // Simple suggestions based on category and spending
    if ($category === 'Food & Dining' && $percentage > 30) {
      $insight['suggestions'][] = "Consider cooking at home more often to reduce dining out expenses.";
    }
    if ($category === 'Entertainment' && $percentage > 20) {
      $insight['suggestions'][] = "Look for free or low-cost entertainment alternatives.";
    }
    if ($category === 'Transportation' && $percentage > 25) {
      $insight['suggestions'][] = "Try carpooling or using public transport to save on fuel.";
    }
    if ($insight['over_budget']) {
      $insight['suggestions'][] = "You're over budget for this category. Consider reducing spending here.";
    }
    if ($percentage > 40) {
      $insight['suggestions'][] = "This category takes up a large portion of your spending. Review if all expenses are necessary.";
    }

    $insights[] = $insight;
  }

  ob_start();
  include __DIR__ . '/../views/insights/insights-view.php';

  $content = ob_get_clean();
  include __DIR__ . '/../views/components/layout.php';
?>
