<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Users
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewUser $this */
?>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="view" value="users" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="1" />
			<?php echo JHtml::_( 'form.token' ); ?>

			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_MOVE_USERMESSAGES'); ?></legend>
				<table class="table table-striped">
					<thead>
						<tr>
							<th width="25%">Ttitle</th>
							<th width="25%">Opiton</th>
							<th>Description</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?php echo JText::_('COM_KUNENA_CATEGORY_TARGET'); ?>
							</td>
							<td>
								<?php
									echo $this->catslist;
								?>
							</td>
							<td>
								<strong><?php echo JText::_('COM_KUNENA_MOVEUSERMESSAGES_USERS_CURRENT'); ?></strong>
								<ol>
								<?php
								foreach($this->users as $user) {
									echo '<li>'.$this->escape($user->username).' ('.JText::_('COM_KUNENA_TRASH_AUTHOR_USERID').' '.$this->escape($user->id).')</li> ';
								}
								?>
								</ol>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</form>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
