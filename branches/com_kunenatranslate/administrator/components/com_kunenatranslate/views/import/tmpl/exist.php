<?php 
/**
 * @version $Id$
 * Kunena Translate Component
 * 
 * @package	Kunena Translate
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined('_JEXEC') or die('Restricted access'); 
JToolBarHelper::save('import', 'Import');
JToolBarHelper::cancel();
fb($this->exist);
?>
<form action="index.php" method="post" name="adminForm">
<table class="adminlist">
	<tbody>
		<tr>
			<td><?php echo JText::_('Client'); ?></td>
			<td><?php  ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('Language'); ?></td>
			<td><?php   ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('add missing labels'); ?></td>
			<td><?php   ?></td>
		</tr>
	</tbody>
</table>
<input type="hidden" name="controller" value="import" />
<input type="hidden" name="option" value="com_kunenatranslate" />
<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
