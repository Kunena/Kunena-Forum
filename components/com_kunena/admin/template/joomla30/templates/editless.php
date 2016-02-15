<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template.Joomla30
 * @subpackage Templates
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewTemplates $this */
?>
<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=templates') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="id" value="<?php echo $this->escape($this->templatename); ?>" />
			<input type="hidden" name="cid[]" value="<?php echo $this->escape($this->templatename); ?>" />
			<input type="hidden" name="filename" value="<?php echo $this->escape($this->filename); ?>" />
			<?php echo JHtml::_( 'form.token' ); ?>

			<?php // TODO: redo FTP protection fields ?>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_EDIT_LESS_TEMPLATE'); ?></legend>
				<table class="table table-bordered table-striped">
					<tr>
						<th>
							<?php echo $this->escape($this->less_path); ?>
						</th>
					</tr>
					<tr>
						<td>
							<textarea class="input-xxlarge" cols="110" rows="25" name="filecontent"><?php echo $this->content; ?></textarea>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
