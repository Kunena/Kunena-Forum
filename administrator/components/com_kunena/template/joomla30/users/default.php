<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Users
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$changeOrder 	= ($this->state->get('list.ordering') == 'ordering' && $this->state->get('list.direction') == 'asc');

?>
<!-- Main page container -->
<div class="container-fluid">
<div class="row-fluid">
 <div class="span2">
	<div><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
		</div>
		<!-- Right side -->
			<div class="span10">	
             <div class="well well-small" style="min-height:120px;">
                       <div class="nav-header">Users</div>
                         <div class="row-striped">
                         <br />
				
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=users') ?>" method="post" id="adminForm" name="adminForm">
		<input type="hidden" name="view" value="users" />
		<input type="hidden" name="task" value="" />
        <input type="hidden" name="type" value="list" />
		<input type="hidden" name="filter_order" value="<?php echo $this->escape ( $this->state->get('list.ordering') ) ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape ($this->state->get('list.direction')) ?>" />
		<input type="hidden" name="limitstart" value="<?php echo intval ( $this->navigation->limitstart ) ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_( 'form.token' ); ?>
             <article class="data-block">
                <div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo 'Search in';?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo 'Search user'; ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo 'Search user'; ?>" />
			</div>
			<div class="btn-group pull-left">
				<button class="btn" rel="tooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn" rel="tooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo  $this->navigation->getLimitBox (); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo  $this->navigation->getLimitBox (); ?>
				</select>
			</div>
		<div class="clr">&nbsp;</div>
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th align="center" width="5">#</th>
					<th align="center" width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->users ); ?>);" /></th>
					<th align="center" class="hidden-phone"><?php echo JText::_('COM_KUNENA_USRL_AVATAR'); ?></th>
					<th class="title" align="center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_ANN_ID', 'id', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th align="left"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_USERNAME', 'username', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th align="left"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_REALNAME', 'name', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th align="center" class="hidden-phone"><?php echo JText::_('COM_KUNENA_USRL_LOGGEDIN'); ?></th>
					<th align="center" class="hidden-phone"><?php echo JText::_('COM_KUNENA_USRL_ENABLED'); ?></th>
					<th align="center" class="hidden-phone"><?php echo JText::_('COM_KUNENA_USRL_BANNED'); ?></th>
					<th align="left" class="hidden-phone"><?php echo JText::_('COM_KUNENA_GEN_EMAIL'); ?></th>
					<th align="left" class="hidden-phone hidden-tablet"><?php echo JText::_('COM_KUNENA_GEN_USERGROUP'); ?></th>
					<th align="left" class="hidden-phone hidden-tablet"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_VIEW_MODERATOR', 'moderator', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th align="left" class="hidden-phone hidden-tablet"><?php echo JText::_('COM_KUNENA_GEN_SIGNATURE'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="14">
							<div class="pagination">
								<div class="center"><?php echo $this->navigation->getPagesLinks (); ?></div>
						    </div>
						</td>
				</tr>
			</tfoot>
			<?php
			if (!empty($this->users)) {
					$k = 1;
					//foreach ($profileList as $pl)
					$i = 0;
					foreach($this->users as $user) {
						$kunena_user = KunenaFactory::getUser($user->id);
						$k = 1 - $k;
						$userLogged = $kunena_user->isOnline() ? '<img src="components/com_kunena/images/tick.png" width="16" height="16" border="0" alt="" />': '';
						$userEnabled = $kunena_user->isBlocked() ? 'publish_x.png' : 'tick.png';
						$altUserEnabled = $kunena_user->isBlocked() ? JText::_( 'Blocked' ) : JText::_( 'Enabled' );
						$userBlockTask =  $kunena_user->isBlocked() ? 'unblock' : 'block';
						$userbanned = $kunena_user->isBanned() ? 'tick.png' : 'publish_x.png';

						$userBannedTask = $kunena_user->isBanned() ? 'ban' : 'ban';
						$altUserBanned = $kunena_user->isBanned() ? JText::_( 'Banned' ) : JText::_( 'Not banned' );
					?>
			<tr class="row<?php echo $k; ?>">
			<td class="right"><?php echo $i + $this->navigation->limitstart + 1; ?></td>
				<td align="center">
					<?php echo JHtml::_('grid.id', $i, intval($user->id)) ?>
				</td>
				<td align="center" width="1%" class="hidden-phone"><?php echo $kunena_user->getAvatarImage('kavatar', 36, 36); ?></td>
				<td align="center" width="1%"><?php echo $this->escape($kunena_user->userid); ?></td>
				<td>
					<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $this->escape($kunena_user->username); ?></a>
				</td>
				<td>
					<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $this->escape($kunena_user->name); ?></a></td>
				<td class="hidden-phone" align="center"><?php echo $userLogged; ?></td>
				<td class="hidden-phone" align="center">
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $userBlockTask; ?>')">
						<i class="icon-checkmark"></i>
					</a></td>
				<td class="hidden-phone" align="center">
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $userBannedTask; ?>')">
						<i class="icon-cancel"></i>
					</a>
				</td>
				<td class="hidden-phone" width="100"><?php echo $this->escape($kunena_user->email);
						?>&nbsp;</td>
                <td class="hidden-phone hidden-tablet" align="center" >
                <?php echo $this->escape($kunena_user->group_id);?>
                </td>
				<td class="hidden-phone hidden-tablet" align="center">
					<?php
					if ($kunena_user->moderator) {
						echo JText::_('COM_KUNENA_YES');
					} else {
						echo JText::_('COM_KUNENA_NO');
					}
					?>
				</td>
				<td class="hidden-phone hidden-tablet" width="*"><?php echo $this->escape ( $kunena_user->signature ); ?></td>
			</tr>
		<?php $i++; }
		} else { ?>
			<tr><td colspan="11"><?php echo JText::_('COM_KUNENA_NOUSERSFOUND') ?></td></tr>
		<?php } ?>
		</table>
        </article>
		</form>
	</div>
	</div>
	<div class="kadmin-footer center">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
    </div>
</div>