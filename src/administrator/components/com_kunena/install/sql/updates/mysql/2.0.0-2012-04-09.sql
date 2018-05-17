-- Kunena 2.0.0

ALTER TABLE `#__kunena_users_banned`
  ADD KEY `created_time` (created_time);
