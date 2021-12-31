<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Templates
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('dropdown.init');
?>

<div id="edittemplates" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN . '/template/j3/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=templates'); ?>"
			  method="post" id="adminForm"
			  name="adminForm">
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="templatename" value="<?php echo $this->escape($this->templatename); ?>">
			<?php echo HTMLHelper::_('form.token'); ?>

			<div class="container-fluid">
				<div class="row">
					<div class="span12">
						<h1 style="text-transform: capitalize;"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE'); ?>
							: <?php echo $this->escape($this->templatename); ?></h1>
						<div class="tabbable-panel">
							<div class="tabbable-line">
								<ul class="nav nav-tabs ">
									<li class="active">
										<a href="#tab_info" data-toggle="tab">
											<?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_INFO'); ?> </a>
									</li>
									<?php foreach ($this->form->getFieldsets() as $fieldset)
									:
	?>
										<?php if ($fieldset->name != 'template')
										:
	?>
											<li>
												<a href="#tab_<?php echo $fieldset->name; ?>"
												   data-toggle="tab"><?php echo Text::_($fieldset->name); ?></a>
											</li>
										<?php endif; ?>
									<?php endforeach; ?>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="tab_info">
										<table class="table table-bordered table-striped">
											<tr>
												<td><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR'); ?>:
												</td>
												<td><strong><?php echo Text::_($this->details->author); ?></strong>
												</td>
											</tr>
											<tr>
												<td><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_VERSION'); ?>:
												</td>
												<td><?php echo Text::_($this->details->version); ?></td>
											</tr>
											<tr>
												<td><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DATE'); ?>:</td>
												<td><?php echo Text::_($this->details->creationdate); ?></td>
											</tr>
											<tr>
												<td><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DESCRIPTION'); ?>
													:
												</td>
												<td><?php $path = KPATH_SITE . '/template/' . $this->templatename . '/assets/images/template_thumbnail.png';

												if (is_file($path))
												:
	?>
														<div>
															<img
																	src="<?php echo Uri::root(true); ?>/components/com_kunena/template/<?php echo $this->escape($this->templatename); ?>/assets/images/template_thumbnail.png"
																	alt="<?php echo $this->escape($this->templatename); ?>"/>
														</div>
												<?php endif; ?>
													<div><?php echo Text::_($this->details->description); ?></div>
												</td>
											</tr>
										</table>
									</div>

									<?php foreach ($this->form->getFieldsets() as $fieldset)
									:
	?>
										<div class="tab-pane" id="tab_<?php echo $fieldset->name; ?>">
											<table class="table table-bordered table-striped">
												<?php foreach ($this->form->getFieldset($fieldset->name) as $field)
												:
	?>
													<?php if ($field->hidden)
													:
	?>
														<tr style="display: none">
															<td class="paramlist_key"><?php echo $field->label; ?></td>
															<td class="paramlist_value"><?php echo $field->input; ?></td>
														</tr>
													<?php else

		:
	?>
														<tr>
															<td width="40%"
																class="paramlist_key"><?php echo $field->label; ?></td>
															<td class="paramlist_value"><?php echo $field->input; ?></td>
														</tr>
													<?php endif; ?>
												<?php endforeach; ?>
											</table>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
		</form>
	</div>
	<div class="pull-right small">
		<?php echo KunenaAdminVersion::getLongVersionHTML(); ?>
	</div>
</div>
