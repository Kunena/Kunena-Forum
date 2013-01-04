-- Kunena 2.0

ALTER TABLE	`#__kunena_users`			MODIFY `avatar` varchar(255) NULL; -- Also new in Kunena 1.6.5
ALTER TABLE	`#__kunena_users`			ADD `thankyou` int(11) NULL default '0' AFTER `showOnline`;

ALTER TABLE	`#__kunena_user_categories`	DROP INDEX `category_id`;
ALTER TABLE `#__kunena_user_categories`	ADD `role` tinyint(4) NOT NULL default '0' AFTER `category_id`,
ALTER TABLE `#__kunena_user_categories`	ADD KEY `category_subscribed` (category_id,subscribed),
ALTER TABLE `#__kunena_user_categories`	ADD KEY `role` (role);


INSERT INTO #__kunena_user_categories (user_id, category_id, role)
	SELECT u.userid AS user_id, IF(m.catid>0,m.catid,0) AS category_id, 1 AS role
		FROM #__kunena_users AS u
		LEFT JOIN #__kunena_moderation AS m ON u.userid=m.userid
		LEFT JOIN #__kunena_categories AS c ON m.catid=c.id
		WHERE u.moderator='1' AND (m.catid IS NULL OR c.moderated='1')
	ON DUPLICATE KEY UPDATE role=1;
