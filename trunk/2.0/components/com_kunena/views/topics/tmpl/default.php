<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
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

<?php if (!empty( $this->topics )) : ?>
		<td class="klist-pages-all"><?php echo $this->getPagination ( 'latest', 3 ); ?></td>
<?php endif; ?>
	</tr>
</table>
<!-- F: List Actions -->

<div class="kblock kflat">
	<div class="kheader">
		<?php if (!empty($this->actionDropdown)) : ?>
		<span class="kcheckbox select-toggle"><input id="kcbcheckall" type="checkbox" name="toggle" value="" /></span>
		<?php endif; ?>
		<h2><span><?php echo $this->escape($this->headerText); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
			<form action="index.php" method="post" name="kBulkActionForm">
				<table class="kblocktable<?php echo $this->escape($this->category->class_sfx); ?>" id="kflattable">

					<?php if (empty ( $this->topics ) && empty ( $this->subcategories )) : ?>
					<tr class="krow2"><td class="kcol-first"><?php echo JText::_('COM_KUNENA_VIEW_NO_POSTS') ?></td></tr>

					<?php else : ?>
						<?php $this->displayRows (); ?>

					<?php  if ( !empty($this->actionDropdown) || !empty($this->embedded) ) : ?>
					<!-- Bulk Actions -->
					<tr class="krow1">
						<td colspan="<?php echo empty($this->actionDropdown) ? 5 : 6 ?>" class="kcol-first krowmoderation">
							<?php if (!empty($this->embedded)) echo CKunenaLink::GetShowLatestLink(JText::_('COM_KUNENA_MORE'), $this->func , 'follow'); ?>
							<?php if (!empty($this->actionDropdown)) : ?>
							<?php echo JHTML::_('select.genericlist', $this->actionDropdown, 'do', 'class="inputbox" size="1"', 'value', 'text', 0, 'kBulkChooseActions'); ?>
							<?php if ($this->actionMove) :
								$options = array (JHTML::_ ( 'select.option', '0', "&nbsp;" ));
								echo JHTML::_('kunenaforum.categorylist', 'bulkactions', 0, $options, array(), 'class="inputbox fbs" size="1" disabled="disabled"', 'value', 'text', 0);
								endif;?>
							<input type="submit" name="kBulkActionsGo" class="kbutton" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
							<?php endif; ?>
						</td>
					</tr>
					<!-- /Bulk Actions -->
					<?php endif; ?>
					<?php endif; ?>
				</table>
				<input type="hidden" name="option" value="com_kunena" />
				<input type="hidden" name="func" value="bulkactions" />
				<?php echo JHTML::_( 'form.token' ); ?>
			</form>
		</div>
	</div>
</div>

<!-- B: List Actions -->
<table class="klist-actions">
	<tr>
		<td class="klist-actions-info-all">
			<strong><?php echo intval($this->total) ?></strong>
			<?php echo JText::_('COM_KUNENA_DISCUSSIONS')?>
		</td>
<?php if (count ( $this->topics ) > 0) : ?>
		<td class="klist-pages-all"><?php echo $this->getPagination ( 'latest', 3 ); ?></td>
<?php endif; ?>
	</tr>
</table>
<!-- F: List Actions -->

<?php
$this->displayWhoIsOnline ();
$this->displayStats ();
?>