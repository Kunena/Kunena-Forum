<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Model;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Attachments Model for Kunena
 *
 * @since   Kunena 2.0
 */
class AttachmentsModel extends ListModel
{
    /**
     * @param   array  $config  config
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id',
                'post',
                'username',
                'size',
                'folder',
                'filetype',
                'filename',
            ];
        }

        parent::__construct($config);
    }

    /**
     * @param   null  $ordering
     * @param   null  $direction
     *
     * @return void
     *
     * @throws Exception
     * @since  Kunena 6.0
     */
    /*
    public function getForm($data = [], $loadData = true): void
    {
        // TODO: Implement getForm() method.
    }*/

    /*
    * Method to auto-populate the model state.
    *
    * Note. Calling getState in this method will result in recursion.
    *
    * @param   string  $ordering   ordering
    * @param   string  $direction  direction
    *
    * @return  void
    *
    * @throws  Exception
    * @since   Kunena 1.6
    *
    */
    protected function populateState($ordering = 'filename', $direction = 'asc')
    {
        $app = Factory::getApplication();

        // Adjust the context to support modal layouts.
        $layout = $app->input->get('layout');
        $this->context = 'com_kunena.admin.attachments';

        if ($layout) {
            $this->context .= '.' . $layout;
        }

        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $filename = $this->getUserStateFromRequest($this->context . '.filter.filename', 'filter_filename', '');
        $this->setState('filter.filename', $filename);

        $filetype = $this->getUserStateFromRequest($this->context . '.filter.filetype', 'filter_filetype', '');
        $this->setState('filter.filetype', $filetype);

        $size = $this->getUserStateFromRequest($this->context . '.filter.size', 'filter_size', '');
        $this->setState('filter.size', $size);

        $username = $this->getUserStateFromRequest($this->context . '.filter.username', 'filter_username', '');
        $this->setState('filter.username', $username);

        $post = $this->getUserStateFromRequest($this->context . '.filter.post', 'filter_post', '');
        $this->setState('filter.post', $post);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * @param   string  $id  id
     *
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected function getStoreId($id = ''): string
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.filename');
        $id .= ':' . $this->getState('filter.filetype');
        $id .= ':' . $this->getState('filter.size');
        $id .= ':' . $this->getState('filter.username');
        $id .= ':' . $this->getState('filter.post');

        return parent::getStoreId($id);
    }

    /**
     * Retrieve the KunenaAttachment objects associated with user informations and message informations
     *
     * @param   string   $query       The query.
     * @param   integer  $limitstart  Offset.
     * @param   integer  $limit       The number of records.
     *
     * @return  KunenaAttachment[]
     *
     * @throws null
     * @throws Exception
     * @since   Kunena 6.0
     */
    protected function _getList($query, $limitstart = 0, $limit = 0)
    {
        $this->_db->setQuery($query, $limitstart, $limit);
        $ids     = $this->_db->loadColumn();
        $results = KunenaAttachmentHelper::getById($ids);
        $userids = [];
        $mesids  = [];

        foreach ($results as $result) {
            $userids[$result->userid] = $result->userid;
            $mesids[$result->mesid]   = $result->mesid;
        }

        KunenaUserHelper::loadUsers($userids);
        KunenaMessageHelper::getMessages($mesids);

        return $results;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected function getListQuery(): QueryInterface
    {
        $db    = $this->getDatabase();
        $query = $db->createQuery();

        $query->select(
            $this->getState(
                'list.select',
                'a.id'
            )
        );

        $query->from('#__kunena_attachments AS a');

        $query->select('m.subject AS post_title');
        $query->select('m.name AS user_title');
        $query->join('LEFT', '#__kunena_messages AS m ON m.id = a.mesid');

        // Filter by search.
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->quote('%' . $db->escape($search, true) . '%');
                $query->where('(a.filename LIKE ' . $search . ' OR a.filename_real LIKE ' . $search . ' OR a.filetype LIKE ' . $search . ' OR m.name LIKE ' . $search . ' OR m.subject LIKE ' . $search . ' OR a.id LIKE ' . $search . ')');
            }
        }

        $filter = $this->getState('filter.filename');

        if (!empty($filter)) {
            $filename = $db->quote('%' . $db->escape($filter, true) . '%');
            $query->where('(a.filename LIKE ' . $filename . ' OR a.filename_real LIKE ' . $filename . ')');
        }

        $filter = $this->getState('filter.filetype');

        if (!empty($filter)) {
            $filetype = $db->quote('%' . $db->escape($filter, true) . '%');
            $query->where('(a.filetype LIKE ' . $filetype . ')');
        }

        // TODO: support < > and ranges
        $filter = $this->getState('filter.size');

        if (!empty($filter)) {
            $size = $db->quote('%' . $db->escape($filter, true) . '%');
            $query->where('(a.size LIKE ' . $size . ')');
        }

        $filter = $this->getState('filter.username');

        if (!empty($filter)) {
            $username = $db->quote('%' . $db->escape($filter, true) . '%');
            $query->where('(m.name LIKE ' . $username . ')');
        }

        $filter = $this->getState('filter.post');

        if (!empty($filter)) {
            $post = $db->quote('%' . $db->escape($filter, true) . '%');
            $query->where('(m.subject LIKE ' . $post . ')');
        }

        // Add the list ordering clause.
        $direction = strtoupper($this->state->get('list.direction'));

        switch ($this->state->get('list.ordering')) {
            case 'filename':
                $query->order('a.filename ' . $direction);
                break;
            case 'filetype':
                $query->order('a.filetype ' . $direction);
                break;
            case 'size':
                $query->order('a.size ' . $direction);
                break;
            case 'username':
                $query->order('m.name ' . $direction);
                break;
            case 'post':
                $query->order('m.subject ' . $direction);
                break;
            default:
                $query->order('a.id ' . $direction);
        }

        return $query;
    }
}
