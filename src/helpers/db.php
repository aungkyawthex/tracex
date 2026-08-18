<?php
require_once __DIR__ . '/schema.php';

if (!function_exists('getUserScopeCondition')) {
  function getUserScopeCondition(PDO $pdo, string $table, int $userId): array
  {
    if (!tableHasColumn($pdo, $table, 'user_id')) {
      return ['sql' => '', 'params' => []];
    }

    return [
      'sql' => '(user_id IS NULL OR user_id = :user_id)',
      'params' => [':user_id' => $userId],
    ];
  }
}

if (!function_exists('getVisibleLookupRows')) {
  function getVisibleLookupRows(PDO $pdo, string $table, string $columns, int $userId): array
  {
    $scope = getUserScopeCondition($pdo, $table, $userId);
    $sql = "SELECT {$columns} FROM {$table}";

    if ($scope['sql'] !== '') {
      $sql .= " WHERE {$scope['sql']}";
    }

    $sql .= ' ORDER BY name ASC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($scope['params']);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}

if (!function_exists('isVisibleLookupId')) {
  function isVisibleLookupId(PDO $pdo, string $table, int $id, int $userId): bool
  {
    $scope = getUserScopeCondition($pdo, $table, $userId);
    $sql = "SELECT id FROM {$table} WHERE id = :id";
    $params = [':id' => $id] + $scope['params'];

    if ($scope['sql'] !== '') {
      $sql .= " AND {$scope['sql']}";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return (bool) $stmt->fetchColumn();
  }
}

if (!function_exists('getVisibleCategoryCount')) {
  function getVisibleCategoryCount(PDO $pdo, int $userId): int
  {
    $scope = getUserScopeCondition($pdo, 'categories', $userId);
    $sql = 'SELECT COUNT(*) FROM categories';

    if ($scope['sql'] !== '') {
      $sql .= " WHERE {$scope['sql']}";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($scope['params']);

    return (int) $stmt->fetchColumn();
  }
}
