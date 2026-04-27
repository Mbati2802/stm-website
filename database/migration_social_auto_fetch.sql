-- ============================================================
-- Migration: Social auto-fetch support (Facebook + Instagram)
-- ============================================================
-- Adds columns to social_updates so auto-fetched posts can be
-- upserted (refreshed) instead of duplicated each cron run.

ALTER TABLE social_updates ADD COLUMN external_id VARCHAR(190) NULL;
ALTER TABLE social_updates ADD COLUMN external_source VARCHAR(40) NULL;
ALTER TABLE social_updates ADD COLUMN auto_fetched TINYINT(1) DEFAULT 0;
ALTER TABLE social_updates ADD COLUMN posted_at DATETIME NULL;
ALTER TABLE social_updates ADD COLUMN fetched_at DATETIME NULL;

-- Unique key prevents duplicates per platform post
CREATE UNIQUE INDEX idx_social_external ON social_updates (external_source, external_id);
CREATE INDEX idx_social_auto ON social_updates (auto_fetched);
CREATE INDEX idx_social_posted_at ON social_updates (posted_at);
