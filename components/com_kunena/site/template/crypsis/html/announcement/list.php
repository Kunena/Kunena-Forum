<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<div class="well well-small">
	<h2 class="page-header">
		<span> <?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?>
			<?php if (!empty($this->actions['add'])) : ?>
				<div class="btn-group pull-right"><a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <i class="icon-cog"></i> <span class="caret"></span> </a>
					<ul class="dropdown-menu actions" style="min-width:0 !important;">
						<li> <a href="index.php?option=com_kunena&view=announcement&layout=create" ><i class="hasTip icon-plus tip" title="Add"></i> Add</a> </li>
						<li> <a href="index.php?option=com_kunena&view=category&layout=manage" ><i class="hasTip icon-delete tip" title="Delete"></i> Delete</a> </li>
						<li> <a href="index.php?option=com_kunena&view=category&layout=manage" ><i class="hasTip icon-edit tip" title="Edit"></i> Edit</a> </li>
						<li> <a href="index.php?option=com_kunena&view=category&layout=manage" ><i class="hasTip icon-ok tip" title="Edit"></i> Publish</a> </li>
						<li> <a href="index.php?option=com_kunena&view=category&layout=manage" ><i class="hasTip icon-remove tip" title="Edit"></i> Unpublished</a> </li>
					</ul>
				</div>
			<?php endif; ?>
		</span>
	</h2>
	<div class="row-fluid column-row">
		<div class="span12 column-item">
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement') ?>" method="post" id="adminForm" name="adminForm">
				<input type="hidden" name="boxchecked" value="0" />
				<?php echo JHtml::_( 'form.token' ); ?>
				<table class="adminlist table table-striped">
					<thead>
						<tr>
							<?php if ($this->actions): ?>
								<th class="span1">
									<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
								</th>
							<?php endif; ?>
							<th class="span1"><?php echo JText::_('COM_KUNENA_ANN_ID'); ?></th>
							<th class="span1"><?php echo JText::_('COM_KUNENA_ANN_DATE'); ?></th>
							<th class="span1"><?php echo JText::_('COM_KUNENA_ANN_TITLE'); ?></th>
							<?php if ($this->actions): ?>
								<th class="span1"><?php echo JText::_('COM_KUNENA_ANN_PUBLISH'); ?></th>
								<th class="span1"><?php echo JText::_('COM_KUNENA_ANN_EDIT'); ?></th>
								<th class="span1"><?php echo JText::_('COM_KUNENA_ANN_DELETE'); ?></th>
							<?php endif; ?>
						</tr>
					</thead>
					<tbody>
						<?php $this->displayItems() ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="7">
								<div class="pull-left">
									<!-- Bulk Actions -->
									<?php  if ( !empty($this->announcementActions) ) : ?>
										<?php echo JHtml::_('select.genericlist', $this->announcementActions, 'task', 'class="inputbox kchecktask" size="1"', 'value', 'text', 0, 'kchecktask'); ?>
										<input type="submit" name="kcheckgo" class="btn" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />

									<?php endif; ?>
									<!-- /Bulk Actions -->
								</div>
								<div class="pull-right">
									<?php echo $pagination = $this->getPagination(5); ?>
								</div>
							</td>
						</tr>
					</tfoot>
				</table>
			</form>
		</div>
	</div>
</div>
