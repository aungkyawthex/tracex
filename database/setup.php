<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/helpers/errors.php';

class DatabaseMigration
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function run(bool $fresh = false, bool $seed = true, bool $killConnections = false): void
    {
        if ($fresh) {
            echo "Fresh mode: dropping existing tables...\n";
            $this->dropAllTables($killConnections);
        }

        $this->createTables();
        $this->normalizeCharsets();

        if ($seed) {
            $this->seedDefaults();
        }

        echo "Setup completed successfully.\n";
    }

    private function createTables(): void
    {
        $this->createUsersTable();
        $this->createCategoriesTable();
        $this->createPaymentMethodsTable();
        $this->createExpensesTable();
        $this->createBudgetsTable();
        $this->createSavingsTable();
        $this->createSavingTransactionsTable();
    }

    private function normalizeCharsets(): void
    {
        $tables = [
            'users',
            'categories',
            'payment_methods',
            'expenses',
            'budgets',
            'savings',
            'saving_transactions',
        ];

        foreach ($tables as $table) {
            $this->pdo->exec("
                ALTER TABLE {$table}
                CONVERT TO CHARACTER SET utf8mb4
                COLLATE utf8mb4_unicode_ci
            ");
        }

        echo "Normalized table charsets to utf8mb4\n";
    }

    private function createUsersTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(150) NOT NULL,
                password VARCHAR(255) NOT NULL,
                role ENUM('user','admin') NOT NULL DEFAULT 'user',
                remember_token VARCHAR(64) DEFAULT NULL,
                token_expiry DATETIME DEFAULT NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        $this->pdo->exec($sql);
        echo "Created/verified: users\n";
    }

    private function createCategoriesTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS categories (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                description VARCHAR(255) DEFAULT NULL,
                icon VARCHAR(100) DEFAULT NULL,
                color VARCHAR(50) DEFAULT NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        $this->pdo->exec($sql);
        echo "Created/verified: categories\n";
    }

    private function createPaymentMethodsTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS payment_methods (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                user_id BIGINT UNSIGNED DEFAULT NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY user_id (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        $this->pdo->exec($sql);
        echo "Created/verified: payment_methods\n";
    }

    private function createExpensesTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS expenses (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id BIGINT UNSIGNED NOT NULL,
                category_id BIGINT UNSIGNED NOT NULL,
                payment_method_id BIGINT UNSIGNED NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                note TEXT,
                expense_date DATE NOT NULL,
                status ENUM('paid','unpaid') DEFAULT 'paid',
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_user_date (user_id, expense_date),
                KEY idx_user_category_date (user_id, category_id, expense_date),
                KEY idx_category (category_id),
                KEY idx_payment_method (payment_method_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        $this->pdo->exec($sql);
        echo "Created/verified: expenses\n";
    }

    private function createBudgetsTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS budgets (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id BIGINT UNSIGNED NOT NULL,
                category_id BIGINT UNSIGNED NOT NULL,
                amount DECIMAL(12,2) NOT NULL,
                month_year DATE NOT NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_user_category_month (user_id, category_id, month_year),
                KEY idx_budgets_user_month (user_id, month_year),
                CONSTRAINT fk_budgets_user
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                CONSTRAINT fk_budgets_category
                    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
                CONSTRAINT budgets_chk_amount CHECK (amount > 0)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        $this->pdo->exec($sql);
        echo "Created/verified: budgets\n";
    }

    private function createSavingsTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS savings (
                id INT NOT NULL AUTO_INCREMENT,
                user_id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                target_amount DECIMAL(12,2) NOT NULL DEFAULT '0.00',
                start_date DATE DEFAULT NULL,
                target_date DATE DEFAULT NULL,
                status ENUM('active','completed','cancelled') DEFAULT 'active',
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_savings_user (user_id),
                CONSTRAINT fk_savings_user
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        $this->pdo->exec($sql);
        echo "Created/verified: savings\n";
    }

    private function createSavingTransactionsTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS saving_transactions (
                id INT NOT NULL AUTO_INCREMENT,
                saving_id INT NOT NULL,
                user_id BIGINT UNSIGNED NOT NULL,
                type ENUM('deposit','withdraw') NOT NULL,
                amount DECIMAL(12,2) NOT NULL,
                note TEXT,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_transactions_saving (saving_id),
                KEY idx_transactions_user (user_id),
                KEY idx_transactions_user_type_date (user_id, type, created_at),
                CONSTRAINT fk_transactions_saving
                    FOREIGN KEY (saving_id) REFERENCES savings(id) ON DELETE CASCADE,
                CONSTRAINT fk_transactions_user
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                CONSTRAINT saving_transactions_chk_1 CHECK (amount > 0)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        $this->pdo->exec($sql);
        echo "Created/verified: saving_transactions\n";
    }

    private function seedDefaults(): void
    {
        $this->seedCategories();
        $this->seedPaymentMethods();
    }

    private function seedCategories(): void
    {
        $defaultCategories = [
            ['Food & Dining', 'Restaurants, groceries, and food delivery', 'utensils', '#EF4444'],
            ['Transportation', 'Fuel, public transport, taxi, maintenance', 'bus', '#3B82F6'],
            ['Entertainment', 'Movies, games, concerts, hobbies', 'film', '#8B5CF6'],
            ['Utilities', 'Electricity, water, internet, phone bills', 'lightbulb', '#F59E0B'],
            ['Shopping', 'Clothing, electronics, personal items', 'shopping-cart', '#EC4899'],
            ['Healthcare', 'Medical, pharmacy, insurance', 'heart', '#10B981'],
            ['Education', 'Books, courses, tuition fees', 'graduation-cap', '#6366F1'],
            ['Travel', 'Flights, hotels, vacation expenses', 'plane', '#06B6D4'],
            ['Bills & Payments', 'Rent, subscriptions, and bills', 'file-invoice-dollar', '#F97316'],
            ['Others', 'Miscellaneous expenses', 'question', '#6B7280'],
        ];

        $existsStmt = $this->pdo->prepare("SELECT id FROM categories WHERE name = :name LIMIT 1");
        $insertStmt = $this->pdo->prepare("
            INSERT INTO categories (name, description, icon, color)
            VALUES (:name, :description, :icon, :color)
        ");

        foreach ($defaultCategories as $category) {
            $existsStmt->execute([':name' => $category[0]]);
            if ($existsStmt->fetchColumn()) {
                continue;
            }

            $insertStmt->execute([
                ':name' => $category[0],
                ':description' => $category[1],
                ':icon' => $category[2],
                ':color' => $category[3],
            ]);
        }

        echo "Seeded: default categories\n";
    }

    private function seedPaymentMethods(): void
    {
        $defaultMethods = ['Cash', 'Card', 'Bank Transfer', 'Digital Wallet'];

        $existsStmt = $this->pdo->prepare("SELECT id FROM payment_methods WHERE user_id IS NULL AND name = :name LIMIT 1");
        $insertStmt = $this->pdo->prepare("INSERT INTO payment_methods (name, user_id) VALUES (:name, NULL)");

        foreach ($defaultMethods as $method) {
            $existsStmt->execute([':name' => $method]);
            if ($existsStmt->fetchColumn()) {
                continue;
            }

            $insertStmt->execute([':name' => $method]);
        }

        echo "Seeded: default payment methods\n";
    }

    public function dropAllTables(bool $killConnections = false): void
    {
        $tables = ['saving_transactions', 'savings', 'expenses', 'budgets', 'payment_methods', 'categories', 'users'];

        if ($killConnections) {
            $this->killOtherConnections();
        }

        // Fail fast instead of hanging for metadata locks.
        $this->pdo->exec("SET SESSION lock_wait_timeout = 10");
        $this->pdo->exec("SET SESSION innodb_lock_wait_timeout = 10");
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        foreach ($tables as $table) {
            echo "Dropping: {$table}...\n";
            $this->pdo->exec("DROP TABLE IF EXISTS {$table}");
            echo "Dropped: {$table}\n";
        }
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    }

    private function killOtherConnections(): void
    {
        $currentId = (int) $this->pdo->query("SELECT CONNECTION_ID()")->fetchColumn();
        $currentDb = (string) $this->pdo->query("SELECT DATABASE()")->fetchColumn();

        $rows = $this->pdo->query("SHOW PROCESSLIST")->fetchAll(PDO::FETCH_ASSOC);
        $killed = 0;
        $failed = 0;

        foreach ($rows as $row) {
            $id = isset($row['Id']) ? (int) $row['Id'] : 0;
            $db = (string) ($row['db'] ?? '');

            if ($id <= 0 || $id === $currentId || $db !== $currentDb) {
                continue;
            }

            try {
                $this->pdo->exec("KILL {$id}");
                $killed++;
            } catch (PDOException $e) {
                $failed++;
                fwrite(STDERR, "Warning: could not kill connection {$id}: {$e->getMessage()}\n");
            }
        }

        echo "Killed {$killed} other connection(s) on DB '{$currentDb}'.\n";

        if ($failed > 0) {
            echo "Failed to kill {$failed} connection(s); a fresh setup may block on locks.\n";
        }
    }
}

function printUsage(): void
{
    echo "Usage:\n";
    echo "  php database/setup.php             # create/verify tables + seed defaults\n";
    echo "  php database/setup.php --fresh     # drop all, recreate tables, seed defaults\n";
    echo "  php database/setup.php --no-seed   # create/verify tables without seed data\n";
    echo "  php database/setup.php --fresh --kill-connections\n";
    echo "  php database/setup.php --fresh --no-seed\n";
}

try {
    $dsn = "mysql:host={$DB_HOST};port=3306;dbname={$DB_NAME};charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 10,
    ]);

    $args = $argv ?? [];
    $fresh = in_array('--fresh', $args, true);
    $seed = !in_array('--no-seed', $args, true);
    $killConnections = in_array('--kill-connections', $args, true);

    if (in_array('--help', $args, true) || in_array('-h', $args, true)) {
        printUsage();
        exit(0);
    }

    $migration = new DatabaseMigration($pdo);
    $migration->run($fresh, $seed, $killConnections);
} catch (Throwable $e) {
    logThrowable($e, 'database setup failed');
    fwrite(STDERR, "Database setup failed: " . $e->getMessage() . PHP_EOL);
    exit(1);
}
