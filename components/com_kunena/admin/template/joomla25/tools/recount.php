<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage SyncUsers
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewTools $this */
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
					<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" id="adminForm" name="adminForm">
						<input type="hidden" name="task" value="recount" />
						<?php echo JHtml::_( 'form.token' ); ?>

						<fieldset>
							<legend><?php echo JText::_('COM_KUNENA_A_RECOUNT'); ?></legend>
							<table class="table table-bordered table-striped">
								<tr>
									<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_TOPICS'); ?></td>
									<td><input type="checkbox" checked="checked" name="topics" value="1" /></td>
									<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_TOPICS_DESC'); ?></td>
								</tr>
								<tr>
									<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_USERTOPICS'); ?></td>
									<td><input type="checkbox" checked="checked" name="usertopics" value="1" /></td>
									<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_USERTOPICS_DESC'); ?></td>
								</tr>
								<tr>
									<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_CATEGORIES'); ?></td>
									<td><input type="checkbox" checked="checked" name="categories" value="1" /></td>
									<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_CATEGORIES_DESC'); ?></td>
								</tr>
								<tr>
									<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_USERS'); ?></td>
									<td><input type="checkbox" checked="checked" name="users" value="1" /></td>
									<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_USERS_DESC'); ?></td>
								</tr>
							</table>
						</fieldset>
					</form>
				</div>

				<div class="pull-right small">
					<?php echo KunenaVersion::getLongVersionHTML(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
