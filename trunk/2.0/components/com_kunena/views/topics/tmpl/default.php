<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<?php
$this->displayAnnouncement ();
CKunenaTools::showModulePosition ( 'kunena_announcement' );
?>
<!-- B: List Actions -->
<table class="klist-actions">
	<tr>
		<td class="klist-actions-info-all">
			<strong><?php echo intval($this->total) ?></strong>
			<?php echo JText::_('COM_KUNENA_DISCUSSIONS')?>
		</td>

		<td class="klist-times-all">
			<form id="timeselect" name="timeselect" method="post" target="_self" action="<?php echo $this->escape(JURI::getInstance()->toString());?>">
			<?php
			// make the select list for time selection
			$timesel[] = JHTML::_('select.option', -1, JText::_('COM_KUNENA_SHOW_ALL'));
			$timesel[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_SHOW_LASTVISIT'));
			$timesel[] = JHTML::_('select.option', 4, JText::_('COM_KUNENA_SHOW_4_HOURS'));
			$timesel[] = JHTML::_('select.option', 8, JText::_('COM_KUNENA_SHOW_8_HOURS'));
			$timesel[] = JHTML::_('select.option', 12, JText::_('COM_KUNENA_SHOW_12_HOURS'));
			$timesel[] = JHTML::_('select.option', 24, JText::_('COM_KUNENA_SHOW_24_HOURS'));
			$timesel[] = JHTML::_('select.option', 48, JText::_('COM_KUNENA_SHOW_48_HOURS'));
			$timesel[] = JHTML::_('select.option', 168, JText::_('COM_KUNENA_SHOW_WEEK'));
			$timesel[] = JHTML::_('select.option', 720, JText::_('COM_KUNENA_SHOW_MONTH'));
			$timesel[] = JHTML::_('select.option', 8760, JText::_('COM_KUNENA_SHOW_YEAR'));
			echo JHTML::_('select.genericlist', $timesel, 'sel', 'class="inputboxusl" onchange="this.form.submit()" size="1"', 'value', 'text', $this->state->get('list.time'));
			?>
			</form>
		</td>

		<td class="klist-jump-all"><?php $this->displayForumJump () ?></td>

		<td class="klist-pages-all"><?php echo $this->getPagination ( 'latest', 3 ); ?></td>
	</tr>
</table>
<!-- F: List Actions -->

<?php echo $this->loadTemplate('clean'); ?>

<!-- B: List Actions -->
<table class="klist-actions">
	<tr>
		<td class="klist-actions-info-all">
			<strong><?php echo intval($this->total) ?></strong>
			<?php echo JText::_('COM_KUNENA_DISCUSSIONS')?>
		</td>
		<td class="klist-pages-all"><?php echo $this->getPagination ( 'latest', 3 ); ?></td>
	</tr>
</table>
<!-- F: List Actions -->

<?php
$this->displayWhoIsOnline ();
$this->displayStats ();
?>