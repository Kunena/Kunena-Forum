<?php
/**
 * Kunena Latest Json
 *
 * @package       Kunena.json_kunenalatest
 *
 * @copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Site\View\Topics;

\defined('_JEXEC') or die;

use Exception;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\View\KunenaView;
use stdClass;

/**
 * @package     ${NAMESPACE}
 *
 * @since       version
 */
class json extends KunenaView
{
	/**
	 * @param   null  $tpl  tmpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		list($count, $topics) = KunenaTopicHelper::getLatestTopics(false, 0, 55);

		$template = KunenaFactory::getTemplate();
		$list     = [];

		foreach ($topics as $topic)
		{
			$lastuser = $topic->getLastPostAuthor()->userid;
			$users    = KunenaUserHelper::get($lastuser);

			$response           = new stdClass;
			$response->id       = $topic->id;
			$response->subject  = KunenaParser::parseText($topic->subject);
			$response->category = $topic->getCategory()->name;
			$response->icon     = $topic->getIcon($topic->getCategory()->iconset);
			$response->message  = KunenaParser::stripBBCode($topic->last_post_message);
			$response->started  = $topic->getFirstPostTime()->toKunena('config_postDateFormat');
			$response->tooltip  = KunenaParser::stripBBCode($topic->last_post_message, 200, false);
			$response->author   = $topic->getLastPostAuthor()->username;
			$response->avatar   = $topic->getLastPostAuthor()->getAvatarImage($template->params->get('avatarType'), 'thumb');
			$response->rank     = $users->getRank($topic->getCategory()->id, 'title');
			$response->time     = $topic->getLastPostTime()->toKunena('config_postDateFormat');

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
