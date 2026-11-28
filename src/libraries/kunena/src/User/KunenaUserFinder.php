<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      User
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\User;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Database\Finder\KunenaFinder;

/**
 * Class \Kunena\Forum\Libraries\User\KunenaUserFinder
 *
 * @since   Kunena 6.0
 */
class KunenaUserFinder extends KunenaFinder
{
    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $table = '#__users';

    /**
     * @var     KunenaConfig|mixed
     * @since   Kunena 6.0
     */
    protected mixed $config = null;

    /**
     * Constructor.
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->config = KunenaConfig::getInstance();
        $this->limit  = $this->config->userlistRows;

        $this->query->leftJoin($this->db->quoteName('#__kunena_users', 'ku') . ' ON ' . $this->db->quoteName('ku.userid') . ' = ' . $this->db->quoteName('a.id'));
    }

    /**
     * @param   array  $ignore  ignore
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function filterByConfiguration(array $ignore = []): KunenaFinder
    {
        if ($this->config->userlistCountUsers == '1') {
            $this->query->where('(' . $this->db->quoteName('a.block') . ' = 0 OR ' . $this->db->quoteName('a.activation') . '="")');
        } elseif ($this->config->userlistCountUsers == '2') {
            $this->query->where('(' . $this->db->quoteName('a.block') . ' = 0 AND ' . $this->db->quoteName('a.activation') . '="")');
        } elseif ($this->config->userlistCountUsers == '3') {
            $this->query->where($this->db->quoteName('a.block') . ' = 0');
        }

        // Hide super admins from the list
        if ($this->config->superAdminUserlist && $ignore) {
            $this->query->where($this->db->quoteName('a.id') . ' NOT IN (' . implode(',', $ignore) . ')');
        }

        return $this;
    }

    /**
     * @param   string  $search     search
     * @param   bool    $fromStart  Only the names which begin with the text (the setting of the user list).
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function filterByName(string $search, bool $fromStart = false): KunenaFinder
    {
        // No search, no condition (the search of the names reads the users whose names match: an empty text matches them all).
        if (trim($search) !== '') {
            // The user is found by the name which the forum shows of him, not by the names which it keeps for itself.
            $this->query->where(KunenaUserName::getSearchCondition($search, 'a', false, $fromStart));
        }

        return $this;
    }

    /**
     * Filter by time, either on registration or last visit date.
     *
     * @param   Date|null  $starting  Starting date or null if older than ending date.
     * @param   Date|null  $ending    Ending date or null if newer than starting date.
     * @param   bool       $register  True = registration date, False = last visit date.
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function filterByTime(?Date $starting = null, ?Date $ending = null, $register = true): KunenaFinder
    {
        $name = $register ? 'registerDate' : 'lastvisitDate';

        if ($starting && $ending) {
            $this->query->where($this->db->quoteName('a.' . $name) . ' BETWEEN ' . $this->db->quote($starting->toSql()) . ' AND ' . $this->db->quote($ending->toSql()));
        } elseif ($starting) {
            $this->query->where($this->db->quoteName('a.' . $name) . ' > ' . $this->db->quote($starting->toSql()));
        } elseif ($ending) {
            $this->query->where($this->db->quoteName('a.' . $name) . ' <= ' . $this->db->quote($ending->toSql()));
        }

        return $this;
    }

    /**
     * Filter the users by type (banned, admin, moderator...)
     *
     * @param   string  $usertype  Set the type of user to filter
     *
     * @return  $this
     *
     * @since   Kunena 7.0.0
     */
    public function filterByUserType(string $usertype): KunenaFinder
    {
        $dateNow = Factory::getDate()->toSql();

        // Select users banned
        if ($usertype == 'banned') {
            // A ban which is running (the opposite of the condition of a user below). Without the brackets the OR undid the
            // other conditions of the query: the name which is searched, the users which the list hides.
            $this->query->where('(' . $this->db->quoteName('ku.banned') . ' != ' . $this->db->quote('1000-01-01 00:00:00') . ' AND ' . $this->db->quoteName('ku.banned') . ' > ' . $this->db->quote($dateNow) . ')');
        } elseif ($usertype == 'globalmod') {
            $this->query->where($this->db->quoteName('ku.moderator') . ' =1 ');
        } elseif ($usertype == 'user') {
            // A user of Joomla who has no row of the forum yet (he never came to the forum) is a user too: the join gives him no
            // moderator and no ban (NULL), which the conditions left out.
            $this->query->where('COALESCE(' . $this->db->quoteName('ku.moderator') . ', 0) = 0');
            $this->query->where('(' . $this->db->quoteName('ku.banned') . ' IS NULL OR ' . $this->db->quoteName('ku.banned') . ' = ' . $this->db->quote('1000-01-01 00:00:00') . ' OR ' . $this->db->quoteName('ku.banned') . '<=' . $this->db->quote($dateNow) . ')');
            // Not a super user. The join of the groups gave a row for each group of a user, so a user of two groups was counted
            // twice (the total and the pages of the list were wrong), and a super user who is in another group too was listed.
            $this->query->where('NOT EXISTS (SELECT 1 FROM ' . $this->db->quoteName('#__user_usergroup_map', 'uum') . ' WHERE ' . $this->db->quoteName('uum.user_id')
                . ' = ' . $this->db->quoteName('a.id') . ' AND ' . $this->db->quoteName('uum.group_id') . ' = 8)');
        }

        return $this;
    }

    /**
     * Get users.
     *
     * @return array
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    public function find(): array
    {
        $results = parent::find();

        return KunenaUserHelper::loadUsers($results);
    }
}
