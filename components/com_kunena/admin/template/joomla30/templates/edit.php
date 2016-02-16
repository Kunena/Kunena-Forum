<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator.Template.Joomla30
 * @subpackage    Templates
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die ();

/** @var KunenaAdminViewTemplates $this */

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
?>

<div id="edittemplates" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN . '/template/joomla30/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=templates'); ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="templatename" value="<?php echo $this->escape($this->templatename); ?>">
			<?php echo JHtml::_('form.token'); ?>

			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<h1 style="text-transform: capitalize;"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE'); ?>: <?php echo $this->escape($this->templatename); ?></h1>
						<div class="tabbable-panel">
							<div class="tabbable-line">
								<ul class="nav nav-tabs ">
									<li class="active">
										<a href="#tab_info" data-toggle="tab">
											<?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_INFO'); ?> </a>
									</li>
									<li>
										<a href="#tab_basic" data-toggle="tab">
											<?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_BASIC'); ?> </a>
									</li>
									<?php if ($this->details->version >= '4.0' && $this->details->name !== 'Blue Eagle') : ?>
									<li>
										<a href="#tab_features" data-toggle="tab">
											<?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_FEATURES'); ?> </a>
									</li>
									<li>
										<a href="#tab_bbcode" data-toggle="tab">
											<?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_BBCODE'); ?> </a>
									</li>
									<li>
										<a href="#tab_colors" data-toggle="tab">
											<?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_COLORS'); ?> </a>
									</li>
									<!--<li>
										<a href="#tab_avatars" data-toggle="tab">
											<?php /*echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_AVATARS'); */?> </a>
									</li>-->
									<?php endif; ?>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="tab_info">
										<table class="table table-bordered table-striped">
											<tr>
												<td><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR'); ?>:</td>
												<td><strong><?php echo JText::_($this->details->author); ?></strong></td>
											</tr>
											<tr>
												<td><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_VERSION'); ?>:</td>
												<td><?php echo JText::_($this->details->version); ?></td>
											</tr>
											<tr>
												<td><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_DATE'); ?>:</td>
												<td><?php echo JText::_($this->details->creationdate); ?></td>
											</tr>
											<tr>
												<td><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_DESCRIPTION'); ?>:</td>
												<td><?php $path = KPATH_SITE . '/template/' . $this->templatename . '/images/template_thumbnail.png';
													if (is_file($path)) : ?>
														<div><img src ="<?php echo JUri::root(true); ?>/components/com_kunena/template/<?php echo $this->escape($this->templatename); ?>/images/template_thumbnail.png" alt="" /></div>
													<?php endif; ?>
													<div><?php echo JText::_($this->details->description); ?></div>
												</td>
											</tr>
										</table>
									</div>
									<div class="tab-pane" id="tab_basic">
										<?php if ($this->form !== false && count($this->form->getFieldset())) : ?>
											<table class="table table-bordered table-striped">
												<?php foreach($this->form->getFieldset('advanced') as $field) : if (!$field->hidden) : ?>
													<tr>
														<td width="40%" class="paramlist_key"><?php echo $field->label; ?></td>
														<td class="paramlist_value"><?php echo $field->input; ?></td>
													</tr>
												<?php endif; endforeach; ?>
											</table>
										<?php
										else :
											echo '<em>' . JText :: _('COM_KUNENA_A_TEMPLATE_MANAGER_NO_PARAMETERS') . '</em>';
										endif;
										?>
									</div>
									<div class="tab-pane" id="tab_features">
										<?php if ($this->form !== false && count($this->form->getFieldset())) : ?>
											<table class="table table-bordered table-striped">
												<?php foreach($this->form->getFieldset('features') as $field) : if (!$field->hidden) : ?>
													<tr>
														<td width="40%" class="paramlist_key"><?php echo $field->label; ?></td>
														<td class="paramlist_value"><?php echo $field->input; ?></td>
													</tr>
												<?php endif; endforeach; ?>
											</table>
										<?php
										else :
											echo '<em>' . JText :: _('COM_KUNENA_A_TEMPLATE_MANAGER_NO_PARAMETERS') . '</em>';
										endif;
										?>
									</div>
									<div class="tab-pane" id="tab_bbcode">
										<?php if ($this->form !== false && count($this->form->getFieldset())) : ?>
											<table class="table table-bordered table-striped">
												<?php foreach($this->form->getFieldset('bbcode') as $field) : if (!$field->hidden) : ?>
													<tr>
														<td width="20%" class="paramlist_key"><?php echo $field->label; ?></td>
														<td class="paramlist_value"><?php echo $field->input; ?></td>
													</tr>
												<?php endif; endforeach; ?>
											</table>
										<?php
										else :
											echo '<em>' . JText :: _('COM_KUNENA_A_TEMPLATE_MANAGER_NO_PARAMETERS') . '</em>';
										endif;
										?>
									</div>
									<div class="tab-pane" id="tab_colors">
										<?php if ($this->form !== false && count($this->form->getFieldset())) : ?>
											<table class="table table-bordered table-striped">
												<?php foreach($this->form->getFieldset('colors') as $field) : if (!$field->hidden) : ?>
													<tr>
														<td width="40%" class="paramlist_key"><?php echo $field->label; ?></td>
														<td class="paramlist_value"><?php echo $field->input; ?></td>
													</tr>
												<?php endif; endforeach; ?>
											</table>
										<?php
										else :
											echo '<em>' . JText :: _('COM_KUNENA_A_TEMPLATE_MANAGER_NO_PARAMETERS') . '</em>';
										endif;
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
