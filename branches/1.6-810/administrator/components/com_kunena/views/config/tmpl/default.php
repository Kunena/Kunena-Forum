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



	<form action="index.php?option=com_kunena" method="post" name="adminForm" autocomplete="off">
			<div>
				<?php echo $this->loadTemplate('setup'); ?>
			</div>
		</div>
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>