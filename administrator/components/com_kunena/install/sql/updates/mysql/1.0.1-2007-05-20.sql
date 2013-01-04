-- FireBoard 1.0.0 => 1.0.1

-- Add new groups table (dropped later in 2.0.0-2011-08-01.sql).
CREATE TABLE IF NOT EXISTS `#__kunena_groups`(
	`id` int(4) NOT NULL  auto_increment,
	`title` varchar(255) NULL,
	PRIMARY KEY (id) ) DEFAULT CHARACTER SET utf8;

-- Add default group, which was never used..
INSERT INTO	`#__kunena_groups`			VALUES ('1', 'Registered User');

ALTER TABLE	`#__kunena_attachments`		ADD KEY `mesid`(`mesid`);

ALTER TABLE	`#__kunena_categories`		DROP KEY `catid`;
ALTER TABLE	`#__kunena_categories`		DROP KEY `catparent`;
ALTER TABLE	`#__kunena_categories`		ADD PRIMARY KEY(`id`);
ALTER TABLE	`#__kunena_categories`		ADD KEY `parent`(`parent`);
ALTER TABLE	`#__kunena_categories`		ADD KEY `published_pubaccess_id`(`published`,`pub_access`,`id`);

ALTER TABLE	`#__kunena_messages`		DROP KEY `id`;
ALTER TABLE	`#__kunena_messages`		ADD KEY `hold_time`(`hold`,`time`);
ALTER TABLE	`#__kunena_messages`		ADD KEY `locked`(`locked`);
ALTER TABLE	`#__kunena_messages`		ADD KEY `time`(`time`);

ALTER TABLE	`#__kunena_messages_text`	DROP KEY `mesid`;
ALTER TABLE	`#__kunena_messages_text`	ADD PRIMARY KEY(`mesid`);

ALTER TABLE	`#__kunena_moderation`		DROP KEY `catid`;

ALTER TABLE	`#__kunena_users`			ADD `group_id` int(4) NULL DEFAULT '1' AFTER `karma_time`;
ALTER TABLE	`#__kunena_users`			ADD `uhits` int(11) NULL  DEFAULT '0' AFTER `group_id`;
ALTER TABLE	`#__kunena_users`			ADD KEY `group_id`(`group_id`);

ALTER TABLE	`#__kunena_whoisonline`		ADD KEY `userid`(`userid`);

-- Get rid of <br> tags in favor of BBCode.
UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "&lt;br>&lt;br>", "\n\n");
