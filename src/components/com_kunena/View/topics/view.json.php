<?php
/**
 * Kunena Latest Json
 *
 * @package       Kunena.json_kunenalatest
 *
 * @copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Site\View\Topics;

defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Forum\Topic;
use Kunena\Forum\Libraries\Forum\Topic\TopicHelper;
use Kunena\Forum\Libraries\Html\Parser;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\View\View;
use stdClass;
use function defined;

/**
 * @package     ${NAMESPACE}
 *
 * @since       version
 */
class json extends View
{
	/**
	 * @param   null  $tpl tmpl
	 *
	 * @return  mixed|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 */
	public function display($tpl = null)
	{
		list($count, $topics) = TopicHelper::getLatestTopics(false, 0, 55);

		$template = KunenaFactory::getTemplate();
		$list     = [];

		foreach ($topics as $topic)
		{
			$lastuser = $topic->getLastPostAuthor()->userid;
			$users    = KunenaUserHelper::get($lastuser);

			$response           = new stdClass;
			$response->id       = $topic->id;
			$response->subject  = Parser::parseText($topic->subject);
			$response->category = $topic->getCategory()->name;
			$response->icon     = $topic->getIcon($topic->getCategory()->iconset);
			$response->message  = Parser::stripBBCode($topic->last_post_message);
			$response->started  = $topic->getFirstPostTime()->toKunena('config_post_dateformat');
			$response->tooltip  = Parser::stripBBCode($topic->last_post_message, 200, false);
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

		$json2 = [
			'Count'  => $count,
			'Topics' => $list,
		];

		$json = json_encode($json2, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

		echo $json;
	}
}
