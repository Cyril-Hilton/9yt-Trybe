-- Fix: Mark shop_orders migration as completed
-- Run this SQL query in your MySQL database (via phpMyAdmin, MySQL Workbench, or command line)

-- First, check what migrations are already recorded
SELECT * FROM migrations ORDER BY id DESC LIMIT 10;

-- Insert the shop_orders migration record (it will skip if it already exists)
INSERT INTO migrations (migration, batch)
SELECT '2025_11_12_000002_create_shop_orders_table',
       COALESCE(MAX(batch), 0) + 1
FROM migrations
WHERE NOT EXISTS (
    SELECT 1 FROM migrations
    WHERE migration = '2025_11_12_000002_create_shop_orders_table'
);

-- Verify the fix worked
SELECT * FROM migrations WHERE migration LIKE '%shop_orders%';

-- Now you can run: php artisan migrate
-- And it should skip this migration successfully!
