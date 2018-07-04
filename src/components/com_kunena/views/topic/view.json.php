<?php
/**
 * Kunena Latest Json
 *
 * @package       Kunena.json_kunenalatest
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die;

class KunenaViewTopic extends KunenaView
{

	function display($tpl = null)
	{
		$topic = KunenaForumTopicHelper::get(9);

		$messages = KunenaForumMessageHelper::getMessagesByTopic($topic, 0, $topic->posts);
		$list = array();
		$template = KunenaFactory::getTemplate();

		foreach ($messages as $message)
		{
			$user = KunenaUserHelper::get($message->userid);
			$response           = new stdClass();
			$response->id       = $message->id;
			$response->message  = KunenaHtmlParser::stripBBCode(KunenaForumMessageHelper::get($message->id)->message);
			$response->author   = $user->username;
			$response->avatar   = $user->getAvatarImage($template->params->get('avatarType'), 'thumb');
			$response->rank     = $user->getRank($topic->getCategory()->id, 'title');
			$response->time     = KunenaDate::getInstance($message->time)->toKunena('config_post_dateformat');

			$list[] = $response;
		}

		$json2 = array(
			'Count'        => $topic,
			'Messages'     => $list
		);

		$json = json_encode($json2, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		echo $json;
	}
}
