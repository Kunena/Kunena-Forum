-- Kunena 1.0.6+ => 1.0.8

UPDATE		`#__kunena_users`			SET `view`='flat';

UPDATE		`#__kunena_messages_text`	SET `message` = REPLACE(`message`, "com_fireboard", "com_kunena");

UPDATE		`#__kunena_users`			SET `signature` = REPLACE(`signature`, "com_fireboard", "com_kunena");
