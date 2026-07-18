-- Bee Framework modern routing / BeeModel example
-- UP
CREATE TABLE IF NOT EXISTS `example_articles` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(160) NOT NULL,
    `slug` VARCHAR(180) NOT NULL,
    `excerpt` VARCHAR(255) NOT NULL DEFAULT '',
    `content` TEXT NOT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'draft',
    `views` INT UNSIGNED NOT NULL DEFAULT 0,
    `metadata` JSON NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `example_articles_slug_unique` (`slug`),
    KEY `example_articles_status_created_index` (`status`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DOWN (execute manually to revert)
-- DROP TABLE IF EXISTS `example_articles`;
