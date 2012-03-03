<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// FIXME: add pagination
?>
	<div id="kannouncements">
		<?php if (!empty($this->actions['add'])) echo JHtml::_('kunenaforum.link', $this->actions['add'], JText::_('COM_KUNENA_ANN_ADD'). ' &raquo;', JText::_('COM_KUNENA_ANN_ADD'), 'kheader-link') ?>
		<h2 class="kheader">
			<a title="<?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') ?>" rel="kannouncements-detailsbox">
				<?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') ?>
			</a>
		</h2>

		<div class="kdetailsbox kannouncements-details" id="kannouncements-detailsbox" >
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement') ?>" method="post" id="adminForm" name="adminForm">
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="task" value="" />
				<?php echo JHTML::_( 'form.token' ); ?>
				<table class="kannouncement">
					<tbody id="kannouncement_body">
						<tr class="ksth">
							<?php if (!empty($this->actions)): ?>
							<th class="kcol-annid">#</th>
							<?php endif; ?>
							<th class="kcol-annid"><?php echo JText::_('COM_KUNENA_ANN_ID'); ?></th>
							<th class="kcol-anndate"><?php echo JText::_('COM_KUNENA_ANN_DATE'); ?></th>
							<th class="kcol-anndate"><?php echo JText::_('COM_KUNENA_ANN_AUTHOR'); ?></th>
							<th class="kcol-anntitle"><?php echo JText::_('COM_KUNENA_ANN_TITLE'); ?></th>
							<?php if (!empty($this->actions)): ?>
							<th class="kcol-annpublish"><?php echo JText::_('COM_KUNENA_ANN_PUBLISH'); ?></th>
							<th class="kcol-annedit"><?php echo JText::_('COM_KUNENA_ANN_EDIT'); ?></th>
							<th class="kcol-anndelete"><?php echo JText::_('COM_KUNENA_ANN_DELETE'); ?></th>
							<?php endif; ?>
						</tr>

						<?php $this->displayItems() ?>

					</tbody>
				</table>
			</form>
		</div>
		<div class="clr"></div>
	</div>
