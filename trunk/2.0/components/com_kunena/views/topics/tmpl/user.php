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
<div id="Kunena">
<?php
$this->displayMenu ();
$this->displayLoginBox ();
$this->displayAnnouncement ();
$this->getModulePosition ( 'kunena_announcement' );
?>
<!-- B: List Actions -->
<table class="klist-actions">
	<tr>
		<td class="klist-actions-info-all">
			<strong><?php echo intval($this->total) ?></strong>
			<?php echo JText::_('COM_KUNENA_DISCUSSIONS')?>
		</td>

		<td class="klist-jump-all"><?php $this->displayForumJump () ?></td>

		<td class="klist-pages-all"><?php echo $this->getPagination ( 5 ); ?></td>
	</tr>
</table>
<!-- F: List Actions -->

<?php echo $this->loadTemplate('embed'); ?>

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
$this->displayFooter ();
?>
</div>