/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP TABLE IF EXISTS `budgets`;
CREATE TABLE `budgets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `category_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `month_year` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `payment_method_id` bigint unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `note` text,
  `expense_date` date NOT NULL,
  `status` enum('paid','unpaid') DEFAULT 'paid',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_date` (`user_id`,`expense_date`),
  KEY `idx_category` (`category_id`),
  KEY `idx_payment_method` (`payment_method_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `payment_methods`;
CREATE TABLE `payment_methods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `saving_transactions`;
CREATE TABLE `saving_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `saving_id` int NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `type` enum('deposit','withdraw') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `note` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_transactions_saving` (`saving_id`),
  KEY `idx_transactions_user` (`user_id`),
  CONSTRAINT `fk_transactions_saving` FOREIGN KEY (`saving_id`) REFERENCES `savings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_transactions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `saving_transactions_chk_1` CHECK ((`amount` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `savings`;
CREATE TABLE `savings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `target_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `start_date` date DEFAULT NULL,
  `target_date` date DEFAULT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_savings_user` (`user_id`),
  CONSTRAINT `fk_savings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `remember_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `budgets` (`id`, `user_id`, `category_id`, `amount`, `month_year`, `created_at`, `updated_at`) VALUES
(1, 1, 5, '50000.00', '2026-02-01', '2026-02-19 09:16:32', '2026-02-19 09:16:32'),
(2, 1, 6, '36.00', '1995-03-01', '2026-02-19 09:23:59', '2026-02-19 09:23:59'),
(3, 1, 9, '5000.00', '2026-04-01', '2026-02-19 11:25:28', '2026-02-19 11:25:28'),
(4, 1, 9, '50000.00', '2026-02-01', '2026-02-19 11:25:39', '2026-02-19 11:25:39');
INSERT INTO `categories` (`id`, `name`, `description`, `icon`, `color`, `created_at`, `updated_at`) VALUES
(1, 'Food & Dining', 'Restaurants, groceries, and food delivery', 'shopping-bag', '#EF4444', '2026-01-16 09:28:32', '2026-01-16 09:28:32'),
(2, 'Transportation', 'Fuel, public transport, taxi, maintenance', 'truck', '#F59E0B', '2026-01-16 09:28:32', '2026-01-16 09:28:32'),
(3, 'Entertainment', 'Movies, games, concerts, hobbies', 'film', '#8B5CF6', '2026-01-16 09:28:32', '2026-01-16 09:28:32'),
(4, 'Utilities', 'Electricity, water, internet, phone bills', 'lightbulb', '#06B6D4', '2026-01-16 09:28:32', '2026-01-16 09:28:32'),
(5, 'Shopping', 'Clothing, electronics, personal items', 'shopping-cart', '#EC4899', '2026-01-16 09:28:32', '2026-01-16 09:28:32'),
(6, 'Healthcare', 'Medical, pharmacy, insurance', 'heart', '#10B981', '2026-01-16 09:28:32', '2026-01-16 09:28:32'),
(7, 'Education', 'Books, courses, tuition fees', 'graduation-cap', '#6366F1', '2026-01-16 09:28:32', '2026-01-16 09:28:32'),
(8, 'Travel', 'Flights, hotels, vacation expenses', 'globe', '#F97316', '2026-01-16 09:28:32', '2026-01-16 09:28:32'),
(9, 'Bills & Payments', 'Rent, loan payments, subscriptions', 'file-invoice-dollar', '#84CC16', '2026-01-16 09:28:32', '2026-01-16 09:28:32'),
(10, 'Other', 'Miscellaneous expenses', 'question', '#6B7280', '2026-01-16 09:28:32', '2026-01-16 09:28:32');
INSERT INTO `expenses` (`id`, `user_id`, `category_id`, `payment_method_id`, `amount`, `note`, `expense_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 4, '63.00', 'Esse error sed cupi', '2026-02-18', 'paid', '2026-02-18 16:43:20', '2026-02-18 16:43:20'),
(2, 1, 9, 1, '56.00', 'Dolore odio aut offi', '2026-02-18', 'paid', '2026-02-18 16:43:41', '2026-02-18 16:43:41'),
(3, 1, 7, 2, '27.00', 'Sunt dolores dolores', '2026-02-18', 'paid', '2026-02-18 16:43:44', '2026-02-18 16:43:56'),
(4, 1, 7, 6, '8.00', 'Odio itaque qui duci', '2026-02-18', 'paid', '2026-02-18 16:43:53', '2026-02-18 16:43:53'),
(5, 1, 8, 4, '100.00', '', '2026-01-14', '', '2026-02-18 16:49:20', '2026-02-18 16:49:40');
INSERT INTO `payment_methods` (`id`, `name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Card', NULL, '2026-01-16 09:36:22', '2026-01-16 09:36:22'),
(2, 'Cash', NULL, '2026-01-16 09:36:22', '2026-01-16 09:36:22'),
(3, 'Bank Transfer', NULL, '2026-01-16 09:36:22', '2026-01-16 09:36:22'),
(4, 'KBZ Pay', NULL, '2026-01-16 09:36:22', '2026-01-16 09:36:22'),
(5, 'Wave Pay', NULL, '2026-01-16 09:36:22', '2026-01-16 09:36:22'),
(6, 'UAB Pay', NULL, '2026-01-16 09:36:22', '2026-01-16 09:36:22');
INSERT INTO `saving_transactions` (`id`, `saving_id`, `user_id`, `type`, `amount`, `note`, `created_at`) VALUES
(1, 1, 1, 'deposit', '20.00', NULL, '2026-02-18 16:59:41'),
(2, 1, 1, 'deposit', '10.00', 'Commodo aut quod vol', '2026-02-19 09:16:13'),
(3, 1, 1, 'deposit', '28.00', 'Quos perspiciatis r', '2026-02-19 09:24:30'),
(4, 1, 1, 'withdraw', '9.00', 'Numquam aperiam culp', '2026-02-19 09:24:43'),
(5, 2, 1, 'deposit', '25.00', 'Nobis velit eum dign', '2026-02-19 11:25:10');
INSERT INTO `savings` (`id`, `user_id`, `name`, `description`, `target_amount`, `start_date`, `target_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Dante Bailey', 'Iure qui quibusdam r', '56.00', '2026-02-18', '2026-03-18', 'active', '2026-02-18 16:52:01', '2026-02-18 16:53:36'),
(2, 1, 'Barclay Mccullough', 'Ex odit quos quisqua', '1600.00', '2026-02-19', '2026-04-11', 'active', '2026-02-19 11:24:58', '2026-02-19 11:25:05');
-- Credentials are intentionally not shipped in this dump.
-- Register a user through the app, or set the password hash locally.
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`, `remember_token`, `token_expiry`) VALUES
(1, 'Demo User', 'demo@example.com', '', 'user', '2026-02-18 16:36:40', '2026-02-19 11:23:38', NULL, NULL);


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
