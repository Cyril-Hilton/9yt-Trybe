-- Mark the shop_orders migration as completed
-- Run this in your MySQL/database client

INSERT INTO migrations (migration, batch)
VALUES ('2025_11_12_000002_create_shop_orders_table', 1)
ON DUPLICATE KEY UPDATE migration = migration;

-- Verify the migrations table
SELECT * FROM migrations ORDER BY id DESC LIMIT 5;
