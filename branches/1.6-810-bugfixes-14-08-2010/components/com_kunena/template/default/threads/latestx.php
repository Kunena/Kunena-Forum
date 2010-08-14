<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/
// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
?>

<?php
$this->displayAnnouncement ();
CKunenaTools::showModulePosition ( 'kunena_announcement' );
?>
<!-- B: List Actions -->
<table class="klist-actions">
	<tr>
<?php if ($this->mode=='posts') : ?>
		<td class="klist-actions-info-all">
			<strong><?php echo $this->escape($this->total)?></strong>
			<?php echo $this->escape($this->header); ?>
		</td>
<?php else: ?>
		<td class="klist-actions-info-all">
			<strong><?php echo $this->escape($this->total)?></strong>
			<?php echo JText::_('COM_KUNENA_DISCUSSIONS')?>
		</td>

		<?php if ($this->func != 'mylatest' && $this->func != 'noreplies') : ?>
		<td class="klist-times-all">
			<?php
			// make the select list for time
			$timesel[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_SHOW_LASTVISIT'));
			$timesel[] = JHTML::_('select.option', 4, JText::_('COM_KUNENA_SHOW_4_HOURS'));
			$timesel[] = JHTML::_('select.option', 8, JText::_('COM_KUNENA_SHOW_8_HOURS'));
			$timesel[] = JHTML::_('select.option', 12, JText::_('COM_KUNENA_SHOW_12_HOURS'));
			$timesel[] = JHTML::_('select.option', 24, JText::_('COM_KUNENA_SHOW_24_HOURS'));
			$timesel[] = JHTML::_('select.option', 48, JText::_('COM_KUNENA_SHOW_48_HOURS'));
			$timesel[] = JHTML::_('select.option', 168, JText::_('COM_KUNENA_SHOW_WEEK'));
			$timesel[] = JHTML::_('select.option', 720, JText::_('COM_KUNENA_SHOW_MONTH'));
			$timesel[] = JHTML::_('select.option', 8760, JText::_('COM_KUNENA_SHOW_YEAR'));
			// build the html select list
			// FIXME: time selection does not work
			echo JHTML::_('select.genericlist', $timesel, 'ktime-selection', 'class="inputboxusl" onchange="document.location.href=\'index.php?option=com_kunena&view=latest&sel=\'+this.options[this.selectedIndex].value;" size="1"', 'value', 'text', $this->escape($this->show_list_time));
						?>
		</td>
		<?php endif; ?>

		<td class="klist-jump-all">
			<?php $this->displayForumJump () ?>
		</td>
<?php endif; ?>

<?php
//pagination 1
if (count ( $this->messages ) > 0) :
	echo '<td class="klist-pages-all">';
	$maxpages = 5 - 2; // odd number here (# - 2)
	echo $pagination = $this->getPagination ( $this->func, $this->show_list_time, $this->page, $this->totalpages, $maxpages );
	echo '</td>';
endif;
?>
	</tr>
</table>
<!-- F: List Actions -->

<?php
if (count ( $this->threads ) > 0) :
	$this->displayItems ();
?>

<!-- B: List Actions -->
<table class="klist-actions">
	<tr>
		<td class="klist-actions-info-all">
			<strong>
				<?php echo $this->total?>
			</strong>
				<?php echo $this->mode=='posts' ? $this->escape($this->header) : JText::_('COM_KUNENA_DISCUSSIONS')?>
		</td>
			<?php
				//pagination 1
				if (count ( $this->messages ) > 0) :
					echo '<td class="klist-pages-all">';
					echo $pagination;
					echo '</td>';
				endif;
			?>
	</tr>
</table>
<!-- F: List Actions -->

<?php endif; ?>

<?php
$this->displayWhoIsOnline ();
$this->displayStats ();
?>