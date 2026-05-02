-- Add transaction_code column to payments table if it doesn't exist
ALTER TABLE payments ADD COLUMN IF NOT EXISTS transaction_code VARCHAR(100) AFTER payment_method_id;
ALTER TABLE payments ADD INDEX IF NOT EXISTS idx_transaction_code (transaction_code);
