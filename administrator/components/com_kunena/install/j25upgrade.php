<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena 2.0 jUpgrade migration class from Joomla! 1.5 to Joomla! 2.5
 *
 */
class jUpgradeComponentKunena extends jUpgradeExtensions {

	public function __construct($step = null) {
		// Joomla 2.5 support
		if (file_exists(JPATH_LIBRARIES.'/cms/version/version.php')) require_once JPATH_LIBRARIES.'/cms/version/version.php';

		parent::__construct($step);
	}

	/**
	 * Check if Kunena migration is supported.
	 *
	 * @return	boolean
	 * @since	1.6.4
	 */
	protected function detectExtension() {
		// Install Kunena 2.0 only into Joomla 2.5
		return version_compare(JVERSION, '2.5', '>=');
	}

	/**
	 * Get tables to be migrated.
	 *
	 * @return	array	List of tables without prefix
	 * @since	1.6.4
	 */
	protected function getCopyTables() {
		require_once JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';
		require_once KPATH_ADMIN . '/install/schema.php';
		$schema = new KunenaModelSchema();
		$tables = $schema->getSchemaTables('');
		return array_values($tables);
	}

	/**
	 * Copy kunena_categories table from old site to new site.
	 *
	 * You can create custom copy functions for all your tables.
	 *
	 * If you want to copy your table in many smaller chunks,
	 * please store your custom state variables into $this->state and return false.
	 * Returning false will force jUpgrade to call this function again,
	 * which allows you to continue import by reading $this->state before continuing.
	 *
	 * @return	boolean Ready (true/false)
	 * @since	1.6.4
	 * @throws	Exception
	 */
	protected function copyTable_kunena_categories($table) {
		$this->source = $this->destination = "#__{$table}";

		// Clone table
		$this->cloneTable($this->source, $this->destination);

		// Get data
		$rows = parent::getSourceData('*');

		// Do some custom post processing on the list.
		foreach ($rows as &$row) {
			if (!isset($row['accesstype']) || $row['accesstype'] == 'none' ) {
				if ($row['admin_access'] != 0) {
					$row['admin_access'] = $this->mapUserGroup($row['admin_access']);
				}
				if ($row['pub_access'] == -1) {
					// All registered
					$row['pub_access'] = 2;
					$row['pub_recurse'] = 1;
				} elseif ($row['pub_access'] == 0) {
					// Everybody
					$row['pub_access'] = 1;
					$row['pub_recurse'] = 1;
				} elseif ($row['pub_access'] == 1) {
					// Nobody
					$row['pub_access'] = 8;
				} else {
					// User groups
					$row['pub_access'] = $this->mapUserGroup($row['pub_access']);
				}
			} elseif ($row['accesstype'] == 'joomla.level') {
				// Convert Joomla access levels
				$row['access']++;
			}
		}
		$this->setDestinationData($rows);
		return true;
	}

	/**
	 * Migrate custom information.
	 *
	 * This function gets called after all folders and tables have been copied.
	 *
	 * If you want to split this task into smaller chunks,
	 * please store your custom state variables into $this->state and return false.
	 * Returning false will force jUpgrade to call this function again,
	 * which allows you to continue import by reading $this->state before continuing.
	 *
	 * @return	boolean Ready (true/false)
	 * @since	1.6.4
	 * @throws	Exception
	 */
	protected function migrateExtensionCustom() {
		require_once JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

		// Need to initialize application
		jimport ('joomla.environment.uri');
		$app = JFactory::getApplication('administrator');

		// Get component object
		$component = JTable::getInstance ( 'extension', 'JTable', array('dbo'=>$this->db_new) );
		$component->load(array('type'=>'component', 'element'=>$this->name));

		// First fix all broken menu items
		$query = "UPDATE #__menu SET component_id={$this->db_new->quote($component->extension_id)} WHERE type = 'component' AND link LIKE '%option={$this->name}%'";
		$this->db_new->setQuery ( $query );
		$this->db_new->query ();

		$menumap = $this->getMapList('menus');

		// Get all menu items from the component (JMenu style)
		$query	= $this->db_new->getQuery(true);
		$query->select('*');
		$query->from('#__menu');
		$query->where("component_id = {$component->extension_id}");
		$query->where('client_id = 0');
		$query->order('lft');
		$this->db_new->setQuery($query);
		$menuitems = $this->db_new->loadObjectList('id');
		foreach ($menuitems as &$menuitem) {
			// Get parent information.
			$parent_tree = array();
			if (isset($menuitems[$menuitem->parent_id])) {
				$parent_tree  = $menuitems[$menuitem->parent_id]->tree;
			}
			// Create tree.
			$parent_tree[] = $menuitem->id;
			$menuitem->tree = $parent_tree;

			// Create the query array.
			$url = str_replace('index.php?', '', $menuitem->link);
			$url = str_replace('&amp;','&',$url);
			parse_str($url, $menuitem->query);
		}

		// Update menu items
		foreach ($menuitems as $menuitem) {
			if (!isset($menuitem->query['view'])) continue;
			$update = false;
			switch ($menuitem->query['view']) {
				case 'home':
					// Update default menu item
					if (!empty($menuitem->query['defaultmenu'])) {
						$menuitem->query['defaultmenu'] = $menumap[$menuitem->query['defaultmenu']]->new;
						$update = true;
					}
					break;
			}
			if ($update) {
				// Update menuitem link
				$query_string = array();
				foreach ($menuitem->query as $k => $v) {
					$query_string[] = $k.'='.$v;
				}
				$menuitem->link = 'index.php?'.implode('&', $query_string);

				// Save menu object
				$menu = JTable::getInstance ( 'menu', 'JTable', array('dbo'=>$this->db_new) );
				$menu->bind(get_object_vars($menuitem), array('tree', 'query'));
				$success = $menu->check();
				if ($success) {
					$success = $menu->store();
				}
				if (!$success) echo "ERROR";
			}
		}
		// Replace Joomla! 1.5 manifest file with Joomla! 2.5 version
		jimport('joomla.filesystem.file');
		$manifest25 = JPATH_ADMINISTRATOR.'/components/com_kunena/kunena.j25.xml';
		$manifest15 = JPATH_ADMINISTRATOR.'/components/com_kunena/kunena.xml';
		if (file_exists($manifest25)) {
			$content = file_get_contents($manifest25);
			// Take care of Git install
			$content = preg_replace('/@kunenaversion@/', preg_replace('/-GIT/i', '', KunenaForum::version()), $content);
			$content = preg_replace('/@kunenaversiondate@/', KunenaForum::versionDate(), $content);
			$content = preg_replace('/@kunenaversionname@/', KunenaForum::versionName(), $content);
			JFile::write($manifest15, $content);
			JFile::delete($manifest25);
		}

		jimport('joomla.plugin.helper');

		// Mark Kunena as discovered and install it
		$component->client_id = 1;
		$component->state = -1;
		$component->store();
		jimport('joomla.installer.installer');
		$installer = JInstaller::getInstance();
		$installer->discover_install($component->extension_id);
		// Start Kunena installer
		require_once dirname ( __FILE__ ) . '/model.php';
		$kunena = new KunenaModelInstall();
		// Install all plugins
		$kunena->stepPlugins();
		// Install English language
		$kunena->installLanguage('en-GB', 'English');

		return true;
	}
}
