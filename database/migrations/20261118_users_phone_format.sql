UPDATE users
SET phone = NULL
WHERE phone IS NOT NULL
  AND NOT (
      CHAR_LENGTH(phone) IN (10, 11)
      AND phone REGEXP '^0[0-9]{9,10}$'
      AND LEFT(phone, 1) = '0'
  );

ALTER TABLE users
    MODIFY COLUMN phone VARCHAR(11) NULL DEFAULT NULL,
    ADD CONSTRAINT chk_users_phone_format CHECK (
        phone IS NULL OR (
            CHAR_LENGTH(phone) IN (10, 11)
            AND phone REGEXP '^0[0-9]{9,10}$'
            AND LEFT(phone, 1) = '0'
        )
    );
