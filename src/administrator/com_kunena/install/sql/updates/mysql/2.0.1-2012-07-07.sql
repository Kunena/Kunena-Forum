-- Kunena 2.0.0 => 2.0.1

ALTER TABLE	`#__kunena_announcement`	MODIFY `created_by` int(11) NOT NULL default '0' AFTER `title`;

ALTER TABLE	`#__kunena_categories`		MODIFY `admin_access` int(11) NOT NULL default '0' AFTER `pub_recurse`;

ALTER TABLE	`#__kunena_user_read`		MODIFY `time` int(11) NOT NULL AFTER `message_id`;
ALTER TABLE	`#__kunena_user_read`		ADD KEY `time` (time);


UPDATE #__kunena_polls_options AS a
	INNER JOIN #__kunena_topics AS t ON t.id=a.pollid 
	LEFT JOIN #__kunena_polls AS p ON p.id=a.pollid 
	SET a.pollid=t.poll_id 
	WHERE t.poll_id>0 AND p.id IS NULL;

UPDATE #__kunena_polls_users AS a
	INNER JOIN #__kunena_topics AS t ON t.id=a.pollid 
	LEFT JOIN #__kunena_polls AS p ON p.id=a.pollid 
	SET a.pollid=t.poll_id 
	WHERE t.poll_id>0 AND p.id IS NULL;
