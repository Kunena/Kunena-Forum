<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined( '_JEXEC' ) or die('Restricted access');

// This file contains initial sample data for the forum

function installSamples()
{
	jimport('joomla.utilities.date');
	$db = &JFactory::getDBO();
	$posttime = new JDate();
	$queries = array();

	$query = "INSERT INTO `#__kunena_ranks` (
	`rank_id`, `rank_title`, `rank_min`, `rank_special`, `rank_image`) VALUES
	(1, 'Fresh Boarder', 0, 0, 'rank1.gif'),
	(2, 'Junior Boarder', 20, 0, 'rank2.gif'),
	(3, 'Senior Boarder', 40, 0, 'rank3.gif'),
	(4, 'Expert Boarder', 80, 0, 'rank4.gif'),
	(5, 'Gold Boarder', 160, 0, 'rank5.gif'),
	(6, 'Platinum Boarder', 320, 0, 'rank6.gif'),
	(7, 'Administrator', 0, 1, 'rankadmin.gif'),
	(8, 'Moderator', 0, 1, 'rankmod.gif'),
	(9, 'Spammer', 0, 1, 'rankspammer.gif');";

	$queries[] = array ('#__kunena_ranks', $query);

	$query = "INSERT INTO `#__kunena_groups`
	(`id`,`title`) VALUES
	(1,'Registered User');";

	$queries[] = array ('#__kunena_groups', $query);

	$query = ">INSERT INTO `#__kunena_smileys`(`id`,`code`,`location`,`greylocation`,`emoticonbar`) VALUES
	(1,'B)','cool.png','cool-grey.png',1),
	(2,':(','sad.png','sad-grey.png',1),
	(3,':)','smile.png','smile-grey.png',1),
	(4,':-)','smile.png','smile-grey.png',0),
	(5,':-(','sad.png','sad-grey.png',0),
	(6,':laugh:','laughing.png','laughing-grey.png',1),
	(7,':cheer:','cheerful.png','cheerful-grey.png',1),
	(8,';)','wink.png','wink-grey.png',1),
	(9,';-)','wink.png','wink-grey.png',0),
	(10,':P','tongue.png','tongue-grey.png',1),
	(12,':X','sick.png','sick-grey.png',0),
	(13,':x','sick.png','sick-grey.png',0),
	(14,':angry:','angry.png','angry-grey.png',1),
	(15,':mad:','angry.png','angry-grey.png',0),
	(16,':unsure:','unsure.png','unsure-grey.png',1),
	(17,':ohmy:','shocked.png','shocked-grey.png',1),
	(18,':huh:','wassat.png','wassat-grey.png',1),
	(19,':dry:','ermm.png','ermm-grey.png',1),
	(20,':ermm:','ermm.png','ermm-grey.png',0),
	(21,':lol:','grin.png','grin-grey.png',1),
	(22,':sick:','sick.png','sick-grey.png',1),
	(23,':silly:','silly.png','silly-grey.png',1),
	(24,':y32b4:','silly.png','silly-grey.png',0),
	(25,':blink:','blink.png','blink-grey.png',1),
	(26,':blush:','blush.png','blush-grey.png',1),
	(27,':kiss:','kissing.png','kissing-grey.png',1),
	(28,':rolleyes:','blink.png','blink-grey.png',0),
	(29,':woohoo:','w00t.png','w00t-grey.png',1),
	(30,':side:','sideways.png','sideways-grey.png',1),
	(31,':S','dizzy.png','dizzy-grey.png',1),
	(32,':s','dizzy.png','dizzy-grey.png',0),
	(33,':evil:','devil.png','devil-grey.png',1),
	(34,':whistle:','whistling.png','whistling-grey.png',1),
	(35,':pinch:','pinch.png','pinch-grey.png',1),
	(36,':p','tongue.png','tongue-grey.png',0),
	(37,':D','laughing.png','laughing-grey.png',0);";

	$queries[] = array ('#__kunena_smileys', $query);

	$query="INSERT INTO `#__kunena_categories` VALUES
	(1, 0, '".addslashes(_KUNENA_SAMPLE_MAIN_CATEGORY_TITLE)."', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 1, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, '".addslashes(_KUNENA_SAMPLE_MAIN_CATEGORY_DESC)."', '".addslashes(_KUNENA_SAMPLE_MAIN_CATEGORY_HEADER)."', '', 0, 0, 0, NULL),
	(2, 1, '".addslashes(_KUNENA_SAMPLE_FORUM1_TITLE)."', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 1, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, '".addslashes(_KUNENA_SAMPLE_FORUM1_DESC)."', '".addslashes(_KUNENA_SAMPLE_FORUM1_HEADER)."', '', 0, 0, 0, NULL),
	(3, 1, '".addslashes(_KUNENA_SAMPLE_FORUM2_TITLE)."', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 2, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, '".addslashes(_KUNENA_SAMPLE_FORUM2_DESC)."', '".addslashes(_KUNENA_SAMPLE_FORUM2_HEADER)."', '', 0, 0, 0, NULL);";

	$queries[] = array ('#__kunena_categories', $query);

	$query="INSERT INTO `#__kunena_messages` VALUES
	(1, 0, 1, 2, 'Kunena', 62, 'info@kunena.com', '".addslashes(_KUNENA_SAMPLE_POST1_SUBJECT)."', ".$posttime->toUnix(true).", '127.0.0.1',
			0, 0, 0, 0, 0, 0, NULL, NULL, NULL,'".addslashes(_KUNENA_SAMPLE_POST1_TEXT)."');";

	$queries[] = array ('#__kunena_messages', $query);

	$query="INSERT INTO `#__kunena_threads` (`id`, `catid`, `topic_subject`, `posts`,
`first_post_id`, `first_post_time`, `first_post_userid`, `first_post_name`, `first_post_email`, `first_post_message`,
`last_post_id`, `last_post_time`, `last_post_userid`, `last_post_name`, `last_post_email`, `last_post_message`) VALUES
	('1', '2', '".addslashes(_KUNENA_SAMPLE_POST1_SUBJECT)."', '1',
		'1', '".$posttime->toUnix(true)."', '62', 'Kunena', 'info@kunena.com', '".addslashes(_KUNENA_SAMPLE_POST1_TEXT)."',
		'1', '".$posttime->toUnix(true)."', '62', 'Kunena', 'info@kunena.com', '".addslashes(_KUNENA_SAMPLE_POST1_TEXT)."');";

	$queries[] = array ('#__kunena_threads', $query);

	foreach ($queries as $query)
	{
		// Only insert sample/default data if table is empty
		$db->setQuery("SELECT COUNT(*) FROM ".$db->nameQuote($query[0]));
		$count = $db->loadResult();

		if (!$count) {
			$db->setQuery($query[1]);
			$db->query();
		}
	}

	// FIXME: Update statistics (= create functions/API to add messages etc..)
	// CKunenaTools::reCountBoards();
}
