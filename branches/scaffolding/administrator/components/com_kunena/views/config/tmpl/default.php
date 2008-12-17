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

JHTML::_('behavior.switcher');
JHTML::_('behavior.tooltip');
JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHTML::stylesheet('system.css', 'administrator/templates/system/css/');
JHTML::stylesheet('default.css', 'administrator/components/com_kunena/media/css/');
?>

<div id="kunena-config">

	<div id="submenu-box">
		<div class="t">
			<div class="t">
				<div class="t"></div>
	 		</div>
		</div>
		<div class="m">
			<ul id="submenu">
				<li><a id="component" class="active"><?php echo JText::_('FB Config Tab Basics'); ?></a></li>
				<li><a id="spam"><?php echo JText::_('FB Config Tab Frontend'); ?></a></li>
				<li><a id="bookmarking"><?php echo JText::_('FB Config Tab Security'); ?></a></li>
				<li><a id="blocking"><?php echo JText::_('FB Config Tab Avatars'); ?></a></li>
			</ul>
			<div class="clr"></div>
		</div>
		<div class="b">
			<div class="b">
	 			<div class="b"></div>
			</div>
		</div>
	</div>

	<form action="index.php?option=com_kunena" method="post" name="adminForm" autocomplete="off">
		<div id="config-document">
			<div id="page-component">
				<fieldset>
					<legend><?php echo JText::_('FB Config Tab Basics'); ?></legend>
					<?php echo JHTML::_('Kunena.params', 'params', $this->config->toString(), 'models/forms/config/component.xml'); ?>
				</fieldset>
			</div>

			<div id="page-spam">
				<fieldset>
					<legend><?php echo JText::_('FB Config Tab Frontend'); ?></legend>
					<?php echo JHTML::_('Kunena.params', 'params', $this->config->toString(), 'models/forms/config/frontend.xml'); ?>
				</fieldset>
			</div>

			<div id="page-bookmarking">
				<fieldset>
					<legend><?php echo JText::_('FB Config Tab Security'); ?></legend>
					<?php echo JHTML::_('Kunena.params', 'params', $this->config->toString(), 'models/forms/config/security.xml'); ?>
				</fieldset>
			</div>

			<div id="page-blocking">
				<fieldset>
					<legend><?php echo JText::_('FB Config Tab Avatars'); ?></legend>
					<?php echo JHTML::_('Kunena.params', 'params', $this->config->toString(), 'models/forms/config/avatars.xml'); ?>
				</fieldset>
			</div>
		</div>
		<?php echo JHTML::_('Kunena.params', 'params', $this->config->toString(), 'models/forms/config/hidden.xml'); ?>
		<input type="hidden" name="task" value="" />
	</form>
</div>