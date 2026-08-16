-- Alive On Record community pledge schema
-- Runtime migrations in includes/pledge_store.php mirror this audited schema.

CREATE TABLE IF NOT EXISTS pledges (
    id INTEGER PRIMARY KEY,
    public_id TEXT NOT NULL UNIQUE,
    display_name TEXT NOT NULL CHECK (length(display_name) BETWEEN 2 AND 80),
    pledge_version TEXT NOT NULL,
    consent_version TEXT NOT NULL,
    removal_token_hash TEXT NOT NULL CHECK (length(removal_token_hash) = 64),
    submitted_at_utc TEXT NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_pledges_submitted
ON pledges(submitted_at_utc DESC, id DESC);

PRAGMA optimize;
