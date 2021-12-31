<?php
/**
 * Kunena Component
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

?>
<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN . '/template/j3/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=templates') ?>"
			  method="post" id="adminForm"
			  name="adminForm">
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="id" value="<?php echo $this->escape($this->templatename); ?>"/>
			<input type="hidden" name="cid[]" value="<?php echo $this->escape($this->templatename); ?>"/>
			<input type="hidden" name="filename" value="<?php echo $this->escape($this->filename); ?>"/>
			<?php echo HTMLHelper::_('form.token'); ?>

			<?php // TODO: redo FTP protection fields ?>
			<fieldset>
				<legend><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_EDIT_CSS_TEMPLATE'); ?></legend>
				<table class="table table-bordered table-striped">
					<tr>
						<th>
							<?php echo $this->escape($this->css_path); ?>
						</th>
					</tr>
					<tr>
						<td>
							<textarea class="input-xxlarge" cols="110" rows="25"
									  name="filecontent"><?php echo $this->content; ?></textarea>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
	<div class="pull-right small">
		<?php echo KunenaAdminVersion::getLongVersionHTML(); ?>
	</div>
</div>
