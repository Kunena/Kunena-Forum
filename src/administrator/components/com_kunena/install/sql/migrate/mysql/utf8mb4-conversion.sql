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
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_announcement`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_attachments`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_categories`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_configuration`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_logs`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_topics`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_messages`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_messages_text`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_polls`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_polls_options`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_polls_users`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_ranks`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_rate`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_sessions`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_smileys`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_thankyou`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_user_categories`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_user_read`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_user_topics`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_users`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_users_banned`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__kunena_version`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
