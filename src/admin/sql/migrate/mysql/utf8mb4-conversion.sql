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
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_announcement`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_attachments`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_categories`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_configuration`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_logs`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_topics`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_messages`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_messages_text`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_polls`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_polls_options`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_polls_users`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_ranks`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_rate`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_sessions`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_smileys`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_thankyou`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_user_categories`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_user_read`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_user_topics`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_users`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_users_banned`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__kunena_version`
     CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

