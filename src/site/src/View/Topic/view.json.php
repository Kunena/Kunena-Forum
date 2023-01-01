<?php

/**
 * Kunena Latest Json
 *
 * @package       Kunena.json_kunenalatest
 *
 * @Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Site\View\Topic;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\View\KunenaView;
use stdClass;

/**
 * @since   Kunena 6.0
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
        $id                        = Factory::getApplication()->input->getInt('id');
        $topic                     = KunenaTopicHelper::get($id);
        $topic->subject            = KunenaParser::parseText($topic->subject);
        $topic->first_post_message = KunenaParser::stripBBCode($topic->first_post_message);
        $topic->last_post_message  = KunenaParser::stripBBCode($topic->last_post_message);
        $messages                  = KunenaMessageHelper::getMessagesByTopic($topic, 0, $topic->posts);

        $list     = [];
        $template = KunenaFactory::getTemplate();

        foreach ($messages as $message) {
            $user              = KunenaUserHelper::get($message->userid);
            $response          = new stdClass();
            $response->id      = $message->id;
            $response->message = KunenaParser::stripBBCode(KunenaMessageHelper::get($message->id)->message);
            $response->author  = $user->username;
            $response->avatar  = $user->getAvatarImage($template->params->get('avatarType'), 'thumb');
            $response->rank    = $user->getRank($topic->getCategory()->id, 'title');
            $response->time    = KunenaDate::getInstance($message->time)->toKunena('config_postDateFormat');

            $list[] = $response;
        }

        $json2 = [
            'Count'    => $topic,
            'Messages' => $list,
        ];

        $json = json_encode($json2, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        echo $json;
    }
}
