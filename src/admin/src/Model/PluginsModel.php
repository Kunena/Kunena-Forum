<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kunena\Forum\Administrator\Model;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\Plugins\Administrator\Model\PluginsModel as JoomlaPluginsModel;
use Joomla\Database\QueryInterface;

/**
 * Class KunenaAdminModelPlugins
 *
 * @since   Kunena 6.0
 */
class PluginsModel extends JoomlaPluginsModel
{
    /**
     * Constructor.
     *
     * @param   MVCFactoryInterface|null  $factory  mvc
     * @param   array                     $config   An optional associative array of configuration settings.
     *
     * @throws Exception
     * @since   Kunena 1.6
     *
     * @see     JController
     */
    public function __construct($config = [], MVCFactoryInterface $factory = null)
    {
        parent::__construct($config, $factory);

        $this->option = 'com_kunena';
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function populateState($ordering = 'folder', $direction = 'asc')
    {
        $this->context = 'com_kunena.admin.plugins';

        parent::populateState($ordering, $direction);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected function getListQuery(): QueryInterface
    {
        $db    = $this->getDatabase();
        $query = parent::getListQuery();

        // List only Kunena related plugins
        $query->where('(' . $db->quoteName('folder') . ' = ' . $db->quote('kunena') . ' OR ' . $db->quoteName('element') . ' LIKE ' . $db->quote('%kunena%') . ')');

        return $query;
    }
}
