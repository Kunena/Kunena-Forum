<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

jimport('joomla.application.component.view');

/**
 * The HTML Kunena Dashboard View
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaViewDashboard extends JView
{
	/**
	 * Display the view
	 *
	 * @access	public
	 */
	function display($tpl = null)
	{
		$versions	= $this->get('Versions');
		$upgrades	= $this->get('Upgrades');
		$initAcl	= $this->get('AclInitialised');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			//return false;
		}

		$this->assignRef('versions',	$versions);
		$this->assignRef('upgrades',	$upgrades);
		$this->assign('init_acl',		$initAcl);

		parent::display($tpl);
	}

	/**
	 * Build the default toolbar.
	 *
	 * @access	protected
	 * @return	void
	 * @since	1.0
	 */
	function buildDefaultToolBar()
	{
		JToolBarHelper::title('Kunena: '.JText::_('Dashboard'), 'k');

		// We can't use the toolbar helper here because there is no generic popup button.
		$bar = &JToolBar::getInstance('toolbar');
		$bar->appendButton('Popup', 'export', 'FB Toolbar Import Configuration', 'index.php?option=com_kunena&view=config&layout=import&tmpl=component', 570, 500);
		$bar->appendButton('Standard', 'config', 'FB Toolbar Configuration', 'config.display', false);

		JToolBarHelper::help('index', true);
	}
}
