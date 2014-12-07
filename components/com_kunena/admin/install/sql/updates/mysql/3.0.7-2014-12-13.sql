-- Kunena 2.0.3 => 3.0.7

-- Fix ranks id by default.
UPDATE `#__kunena_users` SET rank=1 WHERE rank=0;

