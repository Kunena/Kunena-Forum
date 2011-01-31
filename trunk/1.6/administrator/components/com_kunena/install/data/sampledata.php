<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved..
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.org
 */

defined( '_JEXEC' ) or die('Restricted access');

// This file contains initial sample data for the forum

class KText {
	function _($param) {
		return JText::_(str_replace('\n', "\n", $param));
	}
}

function installSampleData()
{
	jimport ( 'joomla.utilities.date' );

	$db = JFactory::getDBO();
	$posttime = new JDate();
	$my = JFactory::getUser();
	$queries = array();

	$query = "INSERT INTO `#__kunena_ranks`
	(`rank_id`, `rank_title`, `rank_min`, `rank_special`, `rank_image`) VALUES
	(1, {$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_RANK1'))}, 0, 0, 'rank1.gif'),
	(2, {$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_RANK2'))}, 20, 0, 'rank2.gif'),
	(3, {$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_RANK3'))}, 40, 0, 'rank3.gif'),
	(4, {$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_RANK4'))}, 80, 0, 'rank4.gif'),
	(5, {$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_RANK5'))}, 160, 0, 'rank5.gif'),
	(6, {$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_RANK6'))}, 320, 0, 'rank6.gif'),
	(7, {$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_RANK_ADMIN'))}, 0, 1, 'rankadmin.gif'),
	(8, {$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_RANK_MODERATOR'))}, 0, 1, 'rankmod.gif'),
	(9, {$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_RANK_SPAMMER'))}, 0, 1, 'rankspammer.gif'),
	(10, {$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_RANK_BANNED'))}, 0, 1, 'rankbanned.gif');";

	$queries[] = array ('kunena_ranks', $query);

	$query = "INSERT INTO `#__kunena_smileys`
	(`id`,`code`,`location`,`greylocation`,`emoticonbar`) VALUES
	(1, 'B)', 'cool.png', 'cool-grey.png', 1),
	(2, '8)', 'cool.png', 'cool-grey.png', 0),
	(3, '8-)', 'cool.png', 'cool-grey.png', 0),
	(4, ':-(', 'sad.png', 'sad-grey.png', 0),
	(5, ':(', 'sad.png', 'sad-grey.png', 1),
	(6, ':sad:', 'sad.png', 'sad-grey.png', 0),
	(7, ':cry:', 'sad.png', 'sad-grey.png', 0),
	(8, ':)', 'smile.png', 'smile-grey.png', 1),
	(9, ':-)', 'smile.png', 'smile-grey.png', 0),
	(10, ':cheer:', 'cheerful.png', 'cheerful-grey.png', 1),
	(11, ';)', 'wink.png', 'wink-grey.png', 1),
	(12, ';-)', 'wink.png', 'wink-grey.png', 0),
	(13, ':wink:', 'wink.png', 'wink-grey.png', 0),
	(14, ';-)', 'wink.png', 'wink-grey.png', 0),
	(15, ':P', 'tongue.png', 'tongue-grey.png', 1),
	(16, ':p', 'tongue.png', 'tongue-grey.png', 0),
	(17, ':-p', 'tongue.png', 'tongue-grey.png', 0),
	(18, ':-P', 'tongue.png', 'tongue-grey.png', 0),
	(19, ':razz:', 'tongue.png', 'tongue-grey.png', 0),
	(20, ':angry:', 'angry.png', 'angry-grey.png', 1),
	(21, ':mad:', 'angry.png', 'angry-grey.png', 0),
	(22, ':unsure:', 'unsure.png', 'unsure-grey.png', 1),
	(23, ':o', 'shocked.png', 'shocked-grey.png', 0),
	(24, ':-o', 'shocked.png', 'shocked-grey.png', 0),
	(25, ':O', 'shocked.png', 'shocked-grey.png', 0),
	(26, ':-O', 'shocked.png', 'shocked-grey.png', 0),
	(27, ':eek:', 'shocked.png', 'shocked-grey.png', 0),
	(28, ':ohmy:', 'shocked.png', 'shocked-grey.png', 1),
	(29, ':huh:', 'wassat.png', 'wassat-grey.png', 1),
	(30, ':?', 'confused.png', 'confused-grey.png', 0),
	(31, ':-?', 'confused.png', 'confused-grey.png', 0),
	(32, ':???', 'confused.png', 'confused-grey.png', 0),
	(33, ':dry:', 'ermm.png', 'ermm-grey.png', 1),
	(34, ':ermm:', 'ermm.png', 'ermm-grey.png', 0),
	(35, ':lol:', 'grin.png', 'grin-grey.png', 1),
	(36, ':X', 'sick.png', 'sick-grey.png', 0),
	(37, ':x', 'sick.png', 'sick-grey.png', 0),
	(38, ':sick:', 'sick.png', 'sick-grey.png', 1),
	(39, ':silly:', 'silly.png', 'silly-grey.png', 1),
	(40, ':y32b4:', 'silly.png', 'silly-grey.png', 0),
	(41, ':blink:', 'blink.png', 'blink-grey.png', 1),
	(42, ':blush:', 'blush.png', 'blush-grey.png', 1),
	(43, ':oops:', 'blush.png', 'blush-grey.png', 1),
	(44, ':kiss:', 'kissing.png', 'kissing-grey.png', 1),
	(45, ':rolleyes:', 'blink.png', 'blink-grey.png', 0),
	(46, ':roll:', 'blink.png', 'blink-grey.png', 0),
	(47, ':woohoo:', 'w00t.png', 'w00t-grey.png', 1),
	(48, ':side:', 'sideways.png', 'sideways-grey.png', 1),
	(49, ':S', 'dizzy.png', 'dizzy-grey.png', 1),
	(50, ':s', 'dizzy.png', 'dizzy-grey.png', 0),
	(51, ':evil:', 'devil.png', 'devil-grey.png', 1),
	(52, ':twisted:', 'devil.png', 'devil-grey.png', 0),
	(53, ':whistle:', 'whistling.png', 'whistling-grey.png', 1),
	(54, ':pinch:', 'pinch.png', 'pinch-grey.png', 1),
	(55, ':D', 'laughing.png', 'laughing-grey.png', 0),
	(56, ':-D', 'laughing.png', 'laughing-grey.png', 0),
	(57, ':grin:', 'laughing.png', 'laughing-grey.png', 0),
	(58, ':laugh:', 'laughing.png', 'laughing-grey.png', 0),
	(59, ':|', 'neutral.png', 'neutral-grey.png', 0),
	(60, ':-|', 'neutral.png', 'neutral-grey.png', 0),
	(61, ':neutral:', 'neutral.png', 'neutral-grey.png', 0),
	(62, ':mrgreen:', 'mrgreen.png', 'mrgreen-grey.png', 0),
	(63, ':?:', 'question.png', 'question-grey.png', 0),
	(64, ':!:', 'exclamation.png', 'exclamation-grey.png', 0),
	(65, ':arrow:', 'arrow.png', 'arrow-grey.png', 0),
	(66, ':idea:', 'idea.png', 'idea-grey.png', 0)";

	$queries[] = array ('kunena_smileys', $query);

	$query="INSERT INTO `#__kunena_categories`
	(`id`, `parent`, `name`, `pub_access`, `ordering`, `published`, `description`, `headerdesc`, `numTopics`, `numPosts`, `allow_polls`) VALUES
	(1, 0, ".$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_SECTION_TITLE')).", 0, 1, 1, ".$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_SECTION_DESC')).", ".$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_SECTION_HEADER')).", 0, 0, 0),
	(2, 1, ".$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_CATEGORY1_TITLE')).", 0, 1, 1, ".$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_CATEGORY1_DESC')).", ".$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_CATEGORY1_HEADER')).",1 ,1, 0),
	(3, 1, ".$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_CATEGORY2_TITLE')).", 0, 2, 1, ".$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_CATEGORY2_DESC')).", ".$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_CATEGORY2_HEADER')).",0 ,0, 1);";

	$queries[] = array ('kunena_categories', $query);

	$query="INSERT INTO `#__kunena_messages`
	(`id`, `parent`, `thread`, `catid`, `userid`, `name`, `subject`, `time`, `ip`) VALUES
	(1, 0, 1, 2, ".$db->quote($my->id).", 'Kunena', ".$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_POST1_SUBJECT')).", ".$posttime->toUnix().", '127.0.0.1');";

	$queries[] = array ('kunena_messages', $query);

	$query="INSERT INTO `#__kunena_messages_text`
	(`mesid`, `message`) VALUES
	(1, ".$db->quote(KText::_('COM_KUNENA_SAMPLEDATA_POST1_TEXT')).");";

	$queries[] = array ('kunena_messages_text', $query);

	$counter = 0;
	foreach ($queries as $query)
	{
		// Only insert sample/default data if table is empty
		$db->setQuery("SELECT COUNT(*) FROM ".$db->nameQuote($db->getPrefix().$query[0]));
		$count = $db->loadResult();

		if (!$count) {
			$db->setQuery($query[1]);
			$db->query();
			if ($db->getErrorNum ())
				throw new KunenaInstallerException ( $db->getErrorMsg (), $db->getErrorNum () );
			$counter++;
		}
	}
	return $counter;
}
