-- Kunena 3.0.3 => 3.1.0

-- Add index to speed up the new queries.
ALTER TABLE `#__kunena_topics` ADD KEY `last_post_id` (`last_post_id`);
