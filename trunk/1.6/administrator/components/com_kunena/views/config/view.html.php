<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * The HTML Kunena configuration view.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.6
 */
class KunenaViewConfig extends JView
{
	/**
	 * Method to display the view.
	 *
	 * @param	string	A template file to load.
	 * @return	mixed	JError object on failure, void on success.
	 * @throws	object	JError
	 * @since	1.6
	 */
	public function display($tpl = null)
	{
		// Initialize variables.
		$user	= JFactory::getUser();

		// Load the view data.
		$state	= $this->get('State');
		$form	= $this->get('Options');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Push out the view data.
		$this->assignRef('state',	$state);
		$this->assignRef('options',	$form);

		// Add submenu
		$contents = '';
		ob_start();
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_kunena' . DS . 'views' . DS . 'config' . DS . 'tmpl' . DS . 'navigation.php' );

		$contents = ob_get_contents();
		ob_end_clean();

		$document	=& JFactory::getDocument();

		$document->setBuffer($contents, 'modules', 'submenu');

		// Add selection list options
		$option_lists = array();

		// Default history selection options. Value is time in hours
		$option_lists['history'] = array
		(
			JHTML::_('select.option', '1', JText::_('1 Hour')),
			JHTML::_('select.option', '6', JText::_('6 Hours')),
			JHTML::_('select.option', '12', JText::_('12 Hours')),
			JHTML::_('select.option', '24', JText::_('1 Day')),
			JHTML::_('select.option', '168', JText::_('1 Week')),
			JHTML::_('select.option', '720', JText::_('1 Month')),
			JHTML::_('select.option', '2160', JText::_('3 Month')),
			JHTML::_('select.option', '4320', JText::_('6 Month')),
			JHTML::_('select.option', '8760', JText::_('1 Year')),
			JHTML::_('select.option', '17520', JText::_('2 Years')),
			JHTML::_('select.option', '0', JText::_('Unlimited'))
		);

		// RSS feed types
		$option_lists['rss_feed_type'] = array
		(
			JHTML::_('select.option', 'thread', JText::_('By Thread')),
			JHTML::_('select.option', 'message', JText::_('By Message'))
		);

		// Types of user name display
		$option_lists['user_name_display'] = array
		(
		    // Joomla username
			JHTML::_('select.option', 'username', JText::_('User Name')),
			// Joomla Full Name
			JHTML::_('select.option', 'fullname', JText::_('Full Name')),
			// First token of full name (generally first name)+ first letter of second token
			JHTML::_('select.option', 'shortname', JText::_('Short Name')),
			// Joomla email address
			JHTML::_('select.option', 'email', JText::_('eMail')),
			// First letter of email + ***@domain
			JHTML::_('select.option', 'obscured', JText::_('Obscured eMail'))
		);

		// Template selection - dynamic
		// TODO: Add dynamic template selection
		$option_lists['template'] = array
		(
			JHTML::_('select.option', 'default', JText::_('default'))
		);

		$option_lists['date_format'] = array
		(
			JHTML::_('select.option', 'short', JText::_('Short')),
			JHTML::_('select.option', 'long', JText::_('Long'))
		);

		// Kunena default view - does not necessarily mean MVC view - this is from an enduser perspective
		$option_lists['default_view'] = array
		(
			JHTML::_('select.option', 'recent', JText::_('Recent Discussions')),
			JHTML::_('select.option', 'myrecent', JText::_('My Discussions')),
			JHTML::_('select.option', 'category', JText::_('Forum Categories'))
		);

		// Kunena markup language for content
		$option_lists['markup'] = array
		(
			JHTML::_('select.option', 'bbcode', JText::_('BB-Code')),
			JHTML::_('select.option', 'html', JText::_('HTML'))
		);

		// Kunena communication type
		$option_lists['communication_type'] = array
		(
			JHTML::_('select.option', 'email', JText::_('eMail')),
			JHTML::_('select.option', 'pm', JText::_('Private Messaging'))
		);

		// Kunena communication format
		$option_lists['communication_format'] = array
		(
			JHTML::_('select.option', 'html', JText::_('HTML')),
			JHTML::_('select.option', 'plain', JText::_('Plain Text'))
		);

		// Kunena subscription type
		$option_lists['subscription_type'] = array
		(
			JHTML::_('select.option', 'individual', JText::_('Individual Messages')),
			JHTML::_('select.option', 'firstreply', JText::_('First Reply since last visit')),
			JHTML::_('select.option', 'daily', JText::_('Daily Summary')),
			JHTML::_('select.option', 'weekly', JText::_('Weekly Summary'))
		);

		// Kunena notification options
		$option_lists['notification_options'] = array
		(
			JHTML::_('select.option', 'newuser', JText::_('New user')),
			JHTML::_('select.option', 'firstmessage', JText::_('First Message of a new user')),
			JHTML::_('select.option', 'pending', JText::_('Message pending approval')),
			JHTML::_('select.option', 'reporting', JText::_('Message reported')),
			JHTML::_('select.option', 'edited', JText::_('Message edited')),
			JHTML::_('select.option', 'moved', JText::_('Message moved')),
			JHTML::_('select.option', 'deleted', JText::_('Message deleted')),
			JHTML::_('select.option', 'tagged', JText::_('Message tagged')),
			JHTML::_('select.option', 'rated', JText::_('Message rated')),
			JHTML::_('select.option', 'newcategory', JText::_('New category created')),
			JHTML::_('select.option', 'modifycategory', JText::_('Category modified')),
			JHTML::_('select.option', 'announcement', JText::_('Announcement')),
			JHTML::_('select.option', 'banned', JText::_('Banned user visit'))
		);

		// Kunena profile integration
		$option_lists['profile_integration'] = array
		(
			JHTML::_('select.option', 'kunena', JText::_('Kunena')),
			JHTML::_('select.option', 'communitybuilder', JText::_('Community Builder')),
			JHTML::_('select.option', 'jomsocial', JText::_('JomSocial'))
		);

		// Kunena avatar integration
		$option_lists['avatar_integration'] = array
		(
			JHTML::_('select.option', 'kunena', JText::_('Kunena')),
			JHTML::_('select.option', 'communitybuilder', JText::_('Community Builder')),
			JHTML::_('select.option', 'jomsocial', JText::_('JomSocial')),
			JHTML::_('select.option', 'gravatar', JText::_('Gravatar'))
		);

		// Kunena pm integration
		$option_lists['pm_integration'] = array
		(
			JHTML::_('select.option', 'none', JText::_('None')),
			JHTML::_('select.option', 'communitybuilder', JText::_('Community Builder')),
			JHTML::_('select.option', 'jomsocial', JText::_('JomSocial')),
			JHTML::_('select.option', 'uddeim', JText::_('uddeIM'))
		);

		// Kunena userpoints integration
		$option_lists['userpoints_integration'] = array
		(
			JHTML::_('select.option', 'none', JText::_('None')),
			JHTML::_('select.option', 'communitybuilder', JText::_('Community Builder')),
			JHTML::_('select.option', 'jomsocial', JText::_('JomSocial')),
			JHTML::_('select.option', 'alpha', JText::_('AlphaUserPoint'))
		);

		// Kunena spam protection level
		$option_lists['spam_protection_level'] = array
		(
			JHTML::_('select.option', 'none', JText::_('None')),
			JHTML::_('select.option', 'review', JText::_('Review Messages')),
			JHTML::_('select.option', 'recaptcha', JText::_('reCAPTCHA')),
		);

		// Kunena private forums root category
		// TODO: Make thsi dynamic based on existing category structure
		$option_lists['private_forums_root'] = array
		(
			JHTML::_('select.option', '0', JText::_('None Selected'))
		);

		// Get ACL groups
		$acl		=& JFactory::getACL();
		$option_lists['acl_groups'] = $acl->get_group_children_tree( null, 'USERS', false );


		$this->assignRef( 'option_lists', $option_lists );


		// Render the layout.
		parent::display($tpl);
	}

	protected function _displayMainToolbar()
	{
		JToolBarHelper::title('Kunena: '.JText::_('Configuration'), 'config');

		// We can't use the toolbar helper here because there is no generic link button.
		$bar = &JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', 'config', 'Import/Export', 'index.php?option=com_kunena&view=config&layout=import');

		JToolBarHelper::divider();

		JToolBarHelper::save('save');
		JToolBarHelper::apply('apply');
		JToolBarHelper::cancel('cancel');
	}

	protected function _displayImportToolbar()
	{
		JToolBarHelper::title('Kunena: '.JText::_('Configuration Import/Export'), 'config');

		JToolBarHelper::custom('import', 'import', 'import', 'Import', false);
		JToolBarHelper::custom('export', 'export', 'export', 'Export', false);

		// We can't use the toolbar helper here because there is no generic link button.
		$bar = &JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', 'cancel', 'Cancel', 'index.php?option=com_kunena&view=config');
	}
}