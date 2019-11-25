<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Private
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined('_JEXEC') or die();
/**
 * Private message finder.
 */
class KunenaPrivateMessageFinder extends KunenaDatabaseObjectFinder
{
    /**
     * @var string
     * @since Kunena
     */
    protected $table = '#__kunena_private';
    public function filterByUser(KunenaUser $user)
    {
        if (!$user->userid)
        {
            $this->skip = true;
        }
        else
        {
            $this->query->innerJoin('#__kunena_private_user_map AS um ON a.id=um.private_id');
            $this->query->where("um.user_id = {$this->db->quote($user->userid)}");
        }
        return $this;
    }
    public function filterByMessage(KunenaForumMessage $message)
    {
        if (!$message->id)
        {
            $this->skip = true;
        }
        else
        {
            $this->query->innerJoin('#__kunena_private_post_map AS pm ON a.id=pm.private_id');
            $this->query->where("pm.message_id = {$this->db->quote($message->id)}");
        }
        return $this;
    }
    public function filterByMessageIds(array $ids)
    {
        if (empty($ids))
        {
            $this->skip = true;
        }
        else
        {
            $this->query->innerJoin('#__kunena_private_post_map AS pm ON a.id=pm.private_id');
            $this->query->where("pm.message_id IN (".implode(',', $ids).")");
        }
        return $this;
    }
    /**
     * Get private messages.
     *
     * @return array|KunenaPrivateMessage[]
     */
    public function find()
    {
        return $this->load(parent::find());
    }
    public function firstOrNew()
    {
        $results = $this->find();
        $first = array_pop($results);
        return $first ? $first : new KunenaPrivateMessage;
    }
    protected function load(array $ids)
    {
        if (empty($ids)) return array();
        $query = $this->db->getQuery(true);
        $query->select('*')->from('#__kunena_private')->where('id IN('.implode(',', $ids).')');
        $this->db->setQuery($query);
        $results = $this->db->loadObjectList('id', 'KunenaPrivateMessage');
        return $results;
    }
} 