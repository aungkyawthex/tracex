<?php
require __DIR__ . '/../src/auth_page.php';

$title = 'Categories - TraceX';
$conditions = [];
$params = [];

$scope = getUserScopeCondition($pdo, 'categories', $userId);
if ($scope['sql'] !== '') {
  $conditions[] = $scope['sql'];
  $params += $scope['params'];
}

$search = trim($_GET['search'] ?? '');
if ($search !== '') {
  $conditions[] = '(name LIKE :search OR description LIKE :search)';
  $params[':search'] = "%{$search}%";
}

$sql = 'SELECT * FROM categories';
if ($conditions) {
  $sql .= ' WHERE ' . implode(' AND ', $conditions);
}
$sql .= ' ORDER BY name ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT category_id, COUNT(*) AS total FROM expenses WHERE user_id = :user_id GROUP BY category_id');
$stmt->execute([':user_id' => $userId]);
$expenseCounts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

ob_start();
include __DIR__ . '/../views/categories/category-view.php';
$content = ob_get_clean();
include __DIR__ . '/../views/components/layout.php';
