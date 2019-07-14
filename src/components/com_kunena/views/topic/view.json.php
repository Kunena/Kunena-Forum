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

use Joomla\CMS\Factory;

class KunenaViewTopic extends KunenaView
{

	/**
	 * @param   null $tpl
	 *
	 * @return mixed|void
	 *
	 * @since version
	 * @throws Exception
	 */
	function display($tpl = null)
	{
		$id                        = Factory::getApplication()->input->getInt('id');
		$topic                     = KunenaForumTopicHelper::get($id);
		$topic->subject            = KunenaHtmlParser::parseText($topic->subject);
		$topic->first_post_message = KunenaHtmlParser::stripBBCode($topic->first_post_message);
		$topic->last_post_message  = KunenaHtmlParser::stripBBCode($topic->last_post_message);
		$messages                  = KunenaForumMessageHelper::getMessagesByTopic($topic, 0, $topic->posts);

		$list     = array();
		$template = KunenaFactory::getTemplate();

		foreach ($messages as $message)
		{
			$user              = KunenaUserHelper::get($message->userid);
			$response          = new stdClass;
			$response->id      = $message->id;
			$response->message = KunenaHtmlParser::stripBBCode(KunenaForumMessageHelper::get($message->id)->message);
			$response->author  = $user->username;
			$response->avatar  = $user->getAvatarImage($template->params->get('avatarType'), 'thumb');
			$response->rank    = $user->getRank($topic->getCategory()->id, 'title');
			$response->time    = KunenaDate::getInstance($message->time)->toKunena('config_post_dateformat');

			$list[] = $response;
		}

		$json2 = array(
			'Count'    => $topic,
			'Messages' => $list
		);

		$json = json_encode($json2, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

		echo $json;
	}
}
