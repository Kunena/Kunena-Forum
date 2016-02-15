<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Templates
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewTemplates $this */

JHtml::_('behavior.tooltip');
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
					<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=templates'); ?>" method="post" id="adminForm" name="adminForm">
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="templatename" value="<?php echo $this->escape($this->templatename); ?>">
						<?php echo JHtml::_( 'form.token' ); ?>

						<div class="row-fluid">
							<fieldset class="span4">
								<legend><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_DETAILS' ); ?></legend>
								<table class="table table-bordered table-striped">
									<tr>
										<td colspan="2"><h1><?php echo JText::_($this->details->name); ?></h1></td>
									</tr>
									<tr>
										<td><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR' ); ?>:</td>
										<td><strong><?php echo JText::_($this->details->author); ?></strong></td>
									</tr>
									<tr>
										<td><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_DESCRIPTION' ); ?>:</td>
										<td><?php $path = KPATH_SITE.'/template/'.$this->templatename. '/images/template_thumbnail.png';
											if (is_file($path)) : ?>
												<div><img src ="<?php echo JUri::root(true); ?>/components/com_kunena/template/<?php echo $this->escape($this->templatename); ?>/images/template_thumbnail.png" alt="" /></div>
											<?php endif; ?>
											<div><?php echo JText::_($this->details->description); ?></div>
										</td>
									</tr>
								</table>
							</fieldset>

							<fieldset class="span8">
								<legend><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_PARAMETERS' ); ?></legend>
								<table class="table table-bordered table-striped">
									<tr>
										<td colspan="2">
											<?php
											$templatefile = KPATH_SITE.'/template/'.$this->templatename.'/params.ini';
											echo is_writable($templatefile) ? JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_PARAMSWRITABLE', $this->escape($templatefile)):JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_PARAMSUNWRITABLE', $this->escape($templatefile));
											?>
										</td>
									</tr>
									<tr>
										<td>
											<ul class="adminformlist">
											</ul>
											<?php if ($this->form !== false && count($this->form->getFieldset())) : ?>
												<table class="table table-bordered table-striped">
													<?php foreach($this->form->getFieldset() as $field) : if (!$field->hidden) : ?>
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
										</td>
									</tr>
								</table>
							</fieldset>
						</div>
					</form>
				</div>
				<div class="pull-right small">
					<?php echo KunenaVersion::getLongVersionHTML(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
