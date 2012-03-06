<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default
 * @subpackage Topics
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$this->displayAnnouncement ();
$this->getModulePosition ( 'kunena_announcement' );
?>
<!-- B: List Actions -->
<table class="klist-actions">
	<tr>
		<td class="klist-actions-info-all">
			<strong><?php echo intval($this->total) ?></strong>
			<?php echo JText::_('COM_KUNENA_USERPOSTS') ?>
		</td>

		<td class="klist-times-all">
			<form action="<?php echo $this->escape(JURI::getInstance()->toString());?>" id="timeselect" name="timeselect" method="post" target="_self">
			<?php $this->displayTimeFilter('sel', 'class="inputboxusl" onchange="this.form.submit()" size="1"') ?>
			</form>
		</td>

		<td class="klist-jump-all"><?php $this->displayForumJump () ?></td>

		<td class="klist-pages-all"><?php echo $this->getPagination ( 5 ); ?></td>
	</tr>
</table>
<!-- F: List Actions -->

<?php echo $this->loadTemplateFile('embed'); ?>

<!-- B: List Actions -->
<table class="klist-actions">
	<tr>
		<td class="klist-actions-info-all">
			<strong><?php echo intval($this->total) ?></strong>
			<?php echo JText::_('COM_KUNENA_DISCUSSIONS')?>
		</td>
		<td class="klist-pages-all"><?php echo $this->getPagination ( 5 ); ?></td>
	</tr>
</table>
<!-- F: List Actions -->

<?php
$this->displayWhoIsOnline ();
$this->displayStatistics ();
?>
