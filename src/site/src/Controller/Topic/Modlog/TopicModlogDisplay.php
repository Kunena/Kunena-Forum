<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\Modlog;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Log\KunenaLog;
use Kunena\Forum\Libraries\Pagination\KunenaPagination;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class TopicModlogDisplay: the log of the moderation actions, for the moderators and the administrators.
 *
 * Administrators see everything, a moderator sees the actions in the categories which he moderates.
 *
 * @since   Kunena 7.2
 */
class TopicModlogDisplay extends KunenaControllerDisplay
{
    /**
     * Entries per page.
     *
     * @since   Kunena 7.2
     */
    private const int LIMIT = 25;

    /**
     * @var     object[]
     * @since   Kunena 7.2
     */
    public mixed $entries = [];

    /**
     * @var     KunenaPagination
     * @since   Kunena 7.2
     */
    public mixed $pagination = null;

    /**
     * Values of the filters: operation, catid and user.
     *
     * @var     array
     * @since   Kunena 7.2
     */
    public mixed $logFilters = ['operation' => '', 'catid' => 0, 'user' => ''];

    /**
     * The operations which are in the log, as value => label.
     *
     * @var     array
     * @since   Kunena 7.2
     */
    public mixed $operations = [];

    /**
     * The categories the log can be filtered on, as id => name.
     *
     * @var     array
     * @since   Kunena 7.2
     */
    public mixed $logCategories = [];

    /**
     * The topics of the entries which the viewer may read, by id.
     *
     * @var     array
     * @since   Kunena 7.2
     */
    public mixed $topics = [];

    /**
     * Is the collection of moderation data switched on (the option "Collect data" of the configuration).
     *
     * @var     boolean
     * @since   Kunena 7.2
     */
    public mixed $collecting = true;

    /**
     * @var     boolean
     * @since   Kunena 7.2
     */
    public mixed $isAdmin = false;

    /**
     * @var     string
     * @since   Kunena 7.2
     */
    protected mixed $name = 'Topic/Modlog';

    /**
     * Load the log entries the user may see.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 7.2
     */
    protected function before()
    {
        parent::before();

        $me = KunenaUserHelper::getMyself();

        // The labels of the operations are in the language file of the backend.
        KunenaFactory::loadLanguage('com_kunena.views', 'admin');

        $categories = [];

        foreach (KunenaCategoryHelper::getCategories(false, false, 'read') as $category) {
            if ($me->isModerator($category)) {
                $categories[(int) $category->id] = $category->name;
            }
        }

        $this->isAdmin = $me->isAdmin();

        if (!$me->exists() || (!$this->isAdmin && !$categories)) {
            // Only the moderators and the administrators may see the log.
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), $me->exists() ? 403 : 401);
        }

        $this->collecting    = (bool) $this->config->logModeration;
        $this->logCategories = $categories;

        $this->logFilters = [
            'operation' => strtoupper($this->input->getCmd('fop', '')),
            'catid'     => $this->input->getInt('fcat', 0),
            'user'      => trim($this->input->getString('fuser', '')),
        ];

        $db = Factory::getContainer()->get(DatabaseInterface::class);

        // The categories in which the viewer may see actions: all for an administrator (also the actions which are not
        // in a category, like a ban), the moderated ones for a moderator.
        $scope = [];

        if (!$this->isAdmin) {
            $scope[] = $db->quoteName('l.category_id') . ' IN (' . implode(',', array_map('intval', array_keys($categories))) . ')';
        }

        $where = array_merge([$db->quoteName('l.type') . ' = ' . KunenaLog::TYPE_MODERATION], $scope);

        // The operations which are in the log (for the filter).
        $query = $db->createQuery()
            ->select('DISTINCT ' . $db->quoteName('l.operation'))
            ->from($db->quoteName('#__kunena_logs', 'l'))
            ->where($where);

        foreach ((array) $db->setQuery($query)->loadColumn() as $operation) {
            $label = Text::_('COM_KUNENA_' . $operation);

            if ($label === 'COM_KUNENA_' . $operation) {
                // An operation without a translation (of a plugin, for example): make a readable text of its name.
                $label = ucfirst(strtolower(str_replace('_', ' ', preg_replace('/^LOG_/', '', $operation))));
            }

            $this->operations[$operation] = $label;
        }

        asort($this->operations);

        if ($this->logFilters['operation'] !== '' && isset($this->operations[$this->logFilters['operation']])) {
            $where[] = $db->quoteName('l.operation') . ' = ' . $db->quote($this->logFilters['operation']);
        } else {
            $this->logFilters['operation'] = '';
        }

        if ($this->logFilters['catid'] && ($this->isAdmin || isset($categories[$this->logFilters['catid']]))) {
            $where[] = $db->quoteName('l.category_id') . ' = ' . (int) $this->logFilters['catid'];
        } else {
            $this->logFilters['catid'] = 0;
        }

        if ($this->logFilters['user'] !== '') {
            $like    = $db->quote('%' . $db->escape($this->logFilters['user'], true) . '%', false);
            $where[] = '(' . $db->quoteName('u.username') . ' LIKE ' . $like . ' OR ' . $db->quoteName('u.name') . ' LIKE ' . $like . ')';
        }

        $count = $db->createQuery()
            ->select('COUNT(*)')
            ->from($db->quoteName('#__kunena_logs', 'l'))
            ->leftJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('l.user_id'))
            ->where($where);
        $total = (int) $db->setQuery($count)->loadResult();

        $start            = max(0, $this->input->getInt('start', 0));
        $this->pagination = new KunenaPagination($total, $start, self::LIMIT);

        $query = $db->createQuery()
            ->select([
                $db->quoteName('l.id'), $db->quoteName('l.user_id'), $db->quoteName('l.category_id'), $db->quoteName('l.topic_id'),
                $db->quoteName('l.target_user'), $db->quoteName('l.time'), $db->quoteName('l.operation'),
                $db->quoteName('c.name', 'category_name'), $db->quoteName('t.subject', 'topic_subject'),
            ])
            ->from($db->quoteName('#__kunena_logs', 'l'))
            ->leftJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('l.user_id'))
            ->leftJoin($db->quoteName('#__kunena_categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('l.category_id'))
            ->leftJoin($db->quoteName('#__kunena_topics', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('l.topic_id'))
            ->where($where)
            ->order($db->quoteName('l.id') . ' DESC');

        $query->setLimit(self::LIMIT, $this->pagination->limitstart);
        $this->entries = (array) $db->setQuery($query)->loadObjectList();

        // Load the users and the topics of the entries in one go.
        $userIds  = [];
        $topicIds = [];

        foreach ($this->entries as $entry) {
            $userIds[]  = (int) $entry->user_id;
            $userIds[]  = (int) $entry->target_user;
            $topicIds[] = (int) $entry->topic_id;
        }

        KunenaUserHelper::loadUsers(array_filter($userIds));
        $this->topics = $topicIds ? KunenaTopicHelper::getTopics(array_values(array_unique(array_filter($topicIds)))) : [];
    }

    /**
     * Prepare document.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 7.2
     */
    protected function prepareDocument()
    {
        $this->setTitle(Text::_('COM_KUNENA_MODLOG'));
        $this->setDescription(Text::_('COM_KUNENA_MODLOG'));
        $this->app->getDocument()->setMetaData('robots', 'noindex, nofollow');
    }
}
