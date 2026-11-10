-- Add an optional avatar selected from the website's built-in avatar list.
ALTER TABLE users
    ADD COLUMN avatar_image VARCHAR(255) DEFAULT NULL AFTER phone;
