<?php
    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/helpers/errors.php';
    require_once __DIR__ . '/helpers/schema.php';

    date_default_timezone_set('Asia/Yangon');

    registerErrorHandlers();

    try {
        $pdo = new PDO(
            "mysql:host={$DB_HOST};port=3306;dbname={$DB_NAME};charset=utf8mb4", 
            $DB_USER, 
            $DB_PASS, 
            [
                PDO::ATTR_ERRMODE=> PDO::ERRMODE_EXCEPTION, 
                PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
            ]
        );
    } catch (PDOException $e) {
        abortWithError('Database connection failed. Please try again later.', $e);
    }

?>
