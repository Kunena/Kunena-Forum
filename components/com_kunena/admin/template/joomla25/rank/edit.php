<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Ranks
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewRank $this */

$document = JFactory::getDocument();
$iconPath = json_encode(JUri::root(true).'/');
$document->addScriptDeclaration("function update_rank(newimage) {
	document.rank_image.src = {$iconPath} + newimage;
}");
?>
<div id="kunena" class="admin override">
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<div id="j-sidebar-container" class="span2">
					<div id="sidebar">
						<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?></div>
					</div>
				</div>
				<div id="j-main-container" class="span10">
					<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
						<input type="hidden" name="view" value="ranks" />
						<input type="hidden" name="task" value="save" />
						<?php if ( $this->state->get('item.id') ): ?><input type="hidden" name="rankid" value="<?php echo $this->state->get('item.id') ?>" /><?php endif; ?>
						<?php echo JHtml::_( 'form.token' ); ?>

						<fieldset>
							<legend><?php echo !$this->state->get('item.id') ? JText::_('COM_KUNENA_NEW_RANK'): JText::_('COM_KUNENA_RANKS_EDIT'); ?></legend>
							<table class="table table-bordered table-striped">

								<tr>
									<td width="20%">
										<?php echo JText::_('COM_KUNENA_RANKS'); ?>
									</td>
									<td width="80%">
										<input class="post" type="text" name="rank_title" value="<?php echo isset($this->rank_selected) ? $this->rank_selected->rank_title : '' ?>" />
									</td>
								</tr>
								<tr>
									<td>
										<?php echo JText::_('COM_KUNENA_RANKSIMAGE'); ?>
									</td>
									<td>
										<?php echo $this->listranks; ?>
										<?php if ( !$this->state->get('item.id') ): ?>
											<img name="rank_image" src="" border="0" alt="" />
										<?php else: ?>
											<img name="rank_image" src="<?php echo $this->escape($this->ktemplate->getRankPath( $this->rank_selected->rank_image, true)); ?>" border="0" alt="" />
										<?php endif; ?>
									</td>
								</tr>
								<tr>
									<td>
										<?php echo JText::_('COM_KUNENA_RANKSMIN'); ?>
									</td>
									<td>
										<input class="post" type="text" name="rank_min" value="<?php echo isset($this->rank_selected) ? $this->rank_selected->rank_min : '1' ?>" />
									</td>
								</tr>
								<tr>
									<td>
										<?php echo JText::_('COM_KUNENA_RANKS_SPECIAL'); ?>
									</td>
									<td>
										<input type="checkbox" <?php echo isset($this->rank_selected) && $this->rank_selected->rank_special ? 'checked="checked"' : '' ?> name="rank_special" value="1" />
									</td>
								</tr>
							</table>
						</fieldset>
					</form>
				</div>
				<div class="pull-right small">
					<?php echo KunenaVersion::getLongVersionHTML (); ?>
				</div>
			</div>
		</div>
	</div>
</div>
