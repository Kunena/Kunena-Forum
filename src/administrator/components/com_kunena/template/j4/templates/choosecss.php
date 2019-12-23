<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Template
 * @subpackage      Templates
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

?>
<div id="kunena" class="container-fluid">
	<div class="row">
		<div class="col-md-2 d-none d-md-block sidebar">
			<div id="sidebar">
				<nav class="sidebar-nav"><?php include KPATH_ADMIN . '/template/j4/common/menu.php'; ?></nav>
			</div>
		</div>
		<div id="j-main-container" class="col-md-10" role="main">
			<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=templates') ?>"
			      method="post" id="adminForm"
			      name="adminForm">
				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="id" value="<?php echo $this->escape($this->templatename); ?>"/>
				<input type="hidden" name="cid[]" value="<?php echo $this->escape($this->templatename); ?>"/>
				<input type="hidden" name="boxchecked" value="0"/>

				<fieldset>
					<legend><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_CHOOSE_CSS_TEMPLATE'); ?></legend>

					<table class="table table-striped">
						<thead>
						<tr>
							<th width="1%"></th>
							<th>
								<?php echo $this->escape($this->dir); ?>
							</th>
							<th>
								<?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_CSS_FILE_PERMISSION'); ?>
							</th>
						</tr>
						</thead>

						<?php foreach ($this->files as $id => $file)
							:
							?>
							<tr>
								<td>
									<input type="radio" id="cb<?php echo $id; ?>" name="filename"
									       value="<?php echo $this->escape($file); ?>"
									       onclick="Joomla.isChecked(this.checked);"/>
								</td>
								<td>
									<?php echo $this->escape($file); ?>
								</td>
								<td>
									<?php echo is_writable($this->dir . '/' . $file) ? '<span style="color:green;"> ' . Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_PARAMSWRITABLE', $this->escape($file)) . '</span>' : '<span style="color:red;"> ' . Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_PARAMSUNWRITABLE', $this->escape($file)) . '</span>' ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				</fieldset>
			</form>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
