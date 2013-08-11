-- Kunena 2.0.0

UPDATE		`#__kunena_sessions`		SET `allowed` = 'na';

REPLACE INTO #__kunena_topics
		(id, category_id, subject, locked, hold, ordering, posts, hits, moved_id, 
		first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, 
		last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name) 
	SELECT m.id, m.catid AS category_id, m.subject, m.locked, m.hold, m.ordering, 1 AS posts, m.hits, th.thread AS moved_id, 
		th.id AS first_post_id, th.time AS first_post_time, th.userid AS first_post_userid, tt.message AS first_post_message, th.name AS first_post_guest_name,
		th.id AS last_post_id, th.time AS last_post_time, th.userid AS last_post_userid, tt.message AS last_post_message, th.name AS last_post_guest_name
		FROM (SELECT m.*, CAST(SUBSTRING(t.message, LENGTH(t.message)+2-LOCATE('=',REVERSE(t.message))) AS UNSIGNED) AS last_post_id
			FROM #__kunena_messages as m
			INNER JOIN #__kunena_messages_text AS t ON m.id = t.mesid
			WHERE m.moved=1) as m
		INNER JOIN #__kunena_messages AS th ON m.last_post_id=th.id
		INNER JOIN #__kunena_messages_text as tt ON th.id=tt.mesid;

DELETE m, t FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid WHERE m.moved>0;
DELETE FROM #__kunena_messages WHERE moved>0;
