-- Kunena 2.0.2 => 2.0.3

UPDATE `#__kunena_topics`
SET hold = '4'
WHERE first_post_id = '0';
