-- One-time migration to remove the unused address field from existing databases.
ALTER TABLE users
    DROP COLUMN address;
