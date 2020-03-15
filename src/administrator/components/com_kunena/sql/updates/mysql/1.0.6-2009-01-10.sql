-- FireBoard 1.0.5RC1/RC2 => Kunena 1.0.6

ALTER TABLE `#__kunena_messages`
  ADD KEY `parent_hits`(`parent`, `hits`);

ALTER TABLE `#__kunena_users`
  ADD KEY `posts` (`posts`);
ALTER TABLE `#__kunena_users`
  ADD KEY `uhits` (`uhits`);
