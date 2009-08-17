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

// Display the main toolbar.
$this->_displayMainToolbar();

// Add the component HTML helper path.
JHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the JavaScript behaviors.
JHTML::_('behavior.switcher');
JHTML::_('behavior.tooltip');
?>

<div id="jx-config">
	<div id="submenu-box">
		<div class="t">
			<div class="t">
				<div class="t"></div>
	 		</div>
		</div>
		<div class="m">
			<ul id="submenu">
				<li><a id="setup" class="active"><?php echo JText::_('Setup'); ?></a></li>
				<li><a id="security"><?php echo JText::_('Security'); ?></a></li>
				<li><a id="integration"><?php echo JText::_('Integration'); ?></a></li>
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
			<div id="page-setup">
				<?php echo $this->loadTemplate('setup'); ?>
			</div>

			<div id="page-security">
				<?php echo $this->loadTemplate('security'); ?>
			</div>

			<div id="page-integration">
				<?php echo $this->loadTemplate('integration'); ?>
			</div>
		</div>
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>