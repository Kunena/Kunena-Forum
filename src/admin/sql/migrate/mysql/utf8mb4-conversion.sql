--
-- Step 1 of the UTF-8 Multibyte (utf8mb4) conversion for MySQL
--
-- Drop indexes which will be added again in step 2, utf8mb4-conversion-02.sql.
--
-- Do not rename this file or any other of the utf8mb4-conversion-*.sql
-- files unless you want to change PHP code, too.
--
-- This file here will be processed ignoring any exceptions caused by indexes
-- to be dropped do not exist.
--
-- The file for step 2 will the be processed with reporting exceptions.
--

ALTER TABLE `#__kunena_aliases`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_announcement`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_attachments`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_categories`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_configuration`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_logs`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_topics`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_messages`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_messages_text`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_polls`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_polls_options`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_polls_users`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_ranks`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_rate`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_sessions`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_smileys`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_thankyou`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_user_categories`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_user_read`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_user_topics`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_users`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_users_banned`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_version`
    ENGINE =InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

