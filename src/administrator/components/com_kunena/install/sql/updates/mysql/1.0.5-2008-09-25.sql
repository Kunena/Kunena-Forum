-- FireBoard 1.0.4 => 1.0.5RC1

ALTER TABLE	`#__kunena_announcement`	MODIFY `title` tinytext NOT NULL AFTER `id`;

UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "&amp;amp;#039;", "'");
UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "&amp;amp;quot;", '"');
UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "&amp;amp;nbsp;", " ");
UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "&amp;lt;br />", "\n");
UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "&amp;lt;br>", "\n");
UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "&amp;amp;lt;", "&lt;");
UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "&amp;amp;gt;", "&gt;");
UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "&amp;amp;amp;", "&amp;");
UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "[IMG", "[img");
UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "[/IMG]", "[/img]");
UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "[URL", "[url");
UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "[/URL]", "[/url]");
UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "[QUOTE", "[quote");
UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "[/QUOTE]", "[/quote]");
UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "[CODE", "[code");
UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "[/CODE", "[/code");

UPDATE		`#__kunena_users`			SET `rank`=8 WHERE `moderator`=1 AND `rank`=0;
UPDATE		`#__kunena_users`			SET `signature` = REPLACE(`signature`, "&amp;amp;#039;", "'");
UPDATE		`#__kunena_users`			SET `signature` = REPLACE(`signature`, "&amp;amp;quot;", '"');
UPDATE		`#__kunena_users`			SET `signature` = REPLACE(`signature`, "&amp;amp;nbsp;", " ");
UPDATE		`#__kunena_users`			SET `signature` = REPLACE(`signature`, "&amp;lt;br />", "\n");
UPDATE		`#__kunena_users`			SET `signature` = REPLACE(`signature`, "&amp;lt;br>", "\n");
UPDATE		`#__kunena_users`			SET `signature` = REPLACE(`signature`, "&amp;amp;lt;", "&lt;");
UPDATE		`#__kunena_users`			SET `signature` = REPLACE(`signature`, "&amp;amp;gt;", "&gt;");
UPDATE		`#__kunena_users`			SET `signature` = REPLACE(`signature`, "&amp;amp;amp;", "&amp;");
UPDATE		`#__kunena_users`			SET `signature` = REPLACE(`signature`, "[IMG", "[img");
UPDATE		`#__kunena_users`			SET `signature` = REPLACE(`signature`, "[/IMG]", "[/img]");
UPDATE		`#__kunena_users`			SET `signature` = REPLACE(`signature`, "[URL", "[url");
UPDATE		`#__kunena_users`			SET `signature` = REPLACE(`signature`, "[/URL]", "[/url]");
UPDATE		`#__kunena_users`			SET `signature` = REPLACE(`signature`, "[QUOTE", "[quote");
UPDATE		`#__kunena_users`			SET `signature` = REPLACE(`signature`, "[/QUOTE]", "[/quote]");
UPDATE		`#__kunena_users`			SET `signature` = REPLACE(`signature`, "[CODE", "[code");
UPDATE		`#__kunena_users`			SET `signature` = REPLACE(`signature`, "[/CODE", "[/code");
