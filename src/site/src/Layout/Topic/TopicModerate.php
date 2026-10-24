<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Topic;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutTopicModerate
 *
 * @since   Kunena 4.0
 */
class TopicModerate extends KunenaLayout
{
    /**
     * @var     KunenaMessage
     * @since   Kunena 6.0
     */
    public $message;

    /**
     * @var     KunenaTopic
     * @since   Kunena 6.0
     */
    public mixed $topic = null;

    /**
     * @var     KunenaCategory
     * @since   Kunena 6.0
     */
    public mixed $category = null;

    public mixed $output = null;

    public mixed $user = null;

    public mixed $headerText = null;

    public mixed $pagination = null;

    public mixed $config = null;

    public mixed $uri = null;

    public mixed $title = null;

    public mixed $topicIcons = null;

    public mixed $userLink = null;

    public mixed $catid = null;

    public mixed $banHistory = null;

    public mixed $replies = null;

    public mixed $banInfo = null;

    public mixed $ktemplate = null;

    /**
     * Method to get the options of the topic
     *
     * @return  array
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function getTopicOptions()
    {
        $options = [];

        // Start with default options.
        if (!$this->message) {
            $options[] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_MODERATION_MERGE_TOPIC'));
        } else {
            $options[] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_MODERATION_CREATE_TOPIC'));
        }

        $options[] = HTMLHelper::_('select.option', -1, Text::_('COM_KUNENA_MODERATION_ENTER_TOPIC'));

        // Then list a few topics.
        $db     = Factory::getContainer()->get('DatabaseDriver');
        $params = [
            'orderby' => 'tt.last_post_time DESC',
            'where'   => " AND tt.id != {$db->quote($this->topic->id)} ", ];
        list($total, $topics) = KunenaTopicHelper::getLatestTopics($this->category->id, 0, 30, $params);

        foreach ($topics as $topic) {
            $options[] = HTMLHelper::_('select.option', $topic->id, $this->escape($topic->subject));
        }

        return $options;
    }

    /**
     * Method to get the list of categories
     *
     * @return  string
     *
     * @since   Kunena 6.0
     */
    public function getCategoryList()
    {
        $options = [];
        $params  = ['sections' => 0, 'catid' => 0];

        return HTMLHelper::_(
            'kunenaforum.categorylist',
            'targetcategory',
            0,
            $options,
            $params,
            'class="inputbox kmove_selectbox form-select"',
            'value',
            'text',
            $this->category->id,
            'kmod_categories'
        );
    }
}
