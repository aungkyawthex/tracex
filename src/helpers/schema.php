<?php

if (!function_exists('tableHasColumn')) {
    function tableHasColumn(PDO $pdo, string $table, string $column): bool {
        static $cache = [];
        $key = $table . '.' . $column;

        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        $stmt = $pdo->prepare("
            SELECT 1
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table_name
              AND COLUMN_NAME = :column_name
            LIMIT 1
        ");
        $stmt->execute([
            ':table_name' => $table,
            ':column_name' => $column,
        ]);

        $cache[$key] = (bool) $stmt->fetchColumn();
        return $cache[$key];
    }
}
