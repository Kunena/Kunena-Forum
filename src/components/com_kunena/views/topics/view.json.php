<?php
/**
 * Kunena Latest Json
 *
 * @package       Kunena.json_kunenalatest
 *
 * @copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die;

class KunenaViewTopics extends KunenaView
{

	/**
	 * @param null $tpl
	 *
	 * @return mixed|void
	 *
	 * @since version
	 * @throws Exception
	 */
	function display($tpl = null)
	{
		list($count, $topics) = KunenaForumTopicHelper::getLatestTopics(false, 0, 55);

		$template = KunenaFactory::getTemplate();
		$list     = array();

		foreach ($topics as $topic)
		{
			$lastuser = $topic->getLastPostAuthor()->userid;
			$users    = KunenaUserHelper::get($lastuser);

			$response           = new stdClass();
			$response->id       = $topic->id;
			$response->subject  = KunenaHtmlParser::parseText($topic->subject);
			$response->category = $topic->getCategory()->name;
			$response->icon     = $topic->getIcon($topic->getCategory()->iconset);
			$response->message  = KunenaHtmlParser::stripBBCode($topic->last_post_message);
			$response->started  = $topic->getFirstPostTime()->toKunena('config_post_dateformat');
			$response->tooltip  = KunenaHtmlParser::stripBBCode($topic->last_post_message, 200, false);
			$response->author   = $topic->getLastPostAuthor()->username;
			$response->avatar   = $topic->getLastPostAuthor()->getAvatarImage($template->params->get('avatarType'), 'thumb');
			$response->rank     = $users->getRank($topic->getCategory()->id, 'title');
			$response->time     = $topic->getLastPostTime()->toKunena('config_post_dateformat');

			if ($topic->unread)
			{
				$response->unread = true;
			}
			else
			{
				$response->unread = false;
			}

			$list[] = $response;
		}

		$json2 = array(
			'Count'  => $count,
			'Topics' => $list
		);

		$json = json_encode($json2, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

		echo $json;
	}
}
