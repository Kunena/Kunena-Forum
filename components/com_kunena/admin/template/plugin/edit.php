<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$this->fieldsets = $this->form->getFieldsets('params');
?>
<script type="text/javascript">
	Joomla.submitbutton = function (task) {
		if (task == 'plugin.cancel' || document.formvalidator.isValid(document.id('style-form'))) {
			Joomla.submitform(task, document.getElementById('style-form'));
		}
	}
</script>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN . '/template/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=plugin&layout=edit&extension_id=' . (int) $this->item->extension_id); ?>" method="post" name="adminForm" id="style-form" class="form-validate form-horizontal">
			<fieldset>
				<div class="tabbable-panel">
					<div class="tabbable-line">
						<ul class="nav nav-tabs">
							<li class="active">
								<a href="#details" data-toggle="tab"><?php echo JText::_('JDETAILS'); ?></a></li>
							<?php if (count($this->fieldsets)) : ?>
								<?php foreach ($this->fieldsets as $fieldset) : ?>
									<?php $label = !empty($fieldset->label) ? JText::_($fieldset->label) : JText::_('COM_PLUGINS_' . $fieldset->name . '_FIELDSET_LABEL'); ?>
									<li>
										<a href="#options-<?php echo $fieldset->name; ?>" data-toggle="tab"><?php echo $label ?></a>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>

						<div class="tab-content">
							<div class="tab-pane active" id="details">
								<table class="table table-striped">
									<thead>
									<tr>
										<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
										<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
									</tr>
									</thead>
									<tbody>
									<tr>
										<td>
											<?php echo $this->form->getLabel('name'); ?>
										</td>
										<td>
											<?php echo $this->form->getInput('name'); ?>
											<span class="readonly plg-name"><?php echo JText::_($this->item->name); ?></span>
										</td>
									</tr>
									<tr>
										<td>
											<?php echo $this->form->getLabel('enabled'); ?>
										</td>
										<td>
											<?php echo $this->form->getInput('enabled'); ?>
										</td>
									</tr>
									<tr>
										<td>
											<?php echo $this->form->getLabel('access'); ?>
										</td>
										<td>
											<?php echo $this->form->getInput('access'); ?>
										</td>
									</tr>
									<tr>
										<td>
											<?php echo $this->form->getLabel('ordering'); ?>
										</td>
										<td>
											<?php echo $this->form->getInput('ordering'); ?>
										</td>
									</tr>
									<tr>
										<td>
											<?php echo $this->form->getLabel('folder'); ?>
										</td>
										<td>
											<?php echo $this->form->getInput('folder'); ?>
										</td>
									</tr>
									<tr>
										<td>
											<?php echo $this->form->getLabel('element'); ?>
										</td>
										<td>
											<?php echo $this->form->getInput('element'); ?>
										</td>
									</tr>
									<?php if ($this->item->extension_id) : ?>
										<tr>
											<td>
												<?php echo $this->form->getLabel('extension_id'); ?>
											</td>
											<td>
												<?php echo $this->form->getInput('extension_id'); ?>
											</td>
										</tr>
									<?php endif; ?>
									<!-- Plugin metadata -->
									<?php if ($this->item->xml) : ?>
										<?php if (($text = trim($this->item->xml->description))) : ?>
											<tr>
												<td>
													<?php echo JText::_('JGLOBAL_DESCRIPTION'); ?>
												</td>
												<td>
													<?php echo JText::_($text); ?>
												</td>
											</tr>
										<?php endif; ?>
									<?php else : ?>
										<div class="alert alert-error">
											<?php echo JText::_('COM_PLUGINS_XML_ERR'); ?>
										</div>
									<?php endif; ?>
									</tbody>
								</table>
							</div>
							<?php echo $this->loadTemplate('options'); ?>
						</div>
					</div>
				</div>
			</fieldset>
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>
