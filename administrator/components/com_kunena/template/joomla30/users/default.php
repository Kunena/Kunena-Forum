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
//JHtml::_('formbehavior.chosen', 'select');

$sortFields = array();
$sortFields[] = JHtml::_('select.option', 'username', JText::_('COM_KUNENA_USRL_USERNAME'));
$sortFields[] = JHtml::_('select.option', 'name', JText::_('COM_KUNENA_USRL_REALNAME'));
$sortFields[] = JHtml::_('select.option', 'moderator', JText::_('COM_KUNENA_VIEW_MODERATOR'));
$sortFields[] = JHtml::_('select.option', 'id', JText::_('JGRID_HEADING_ID'));

$sortDirection = array();
$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('JGLOBAL_ORDER_ASCENDING'));
$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('JGLOBAL_ORDER_DESCENDING'));

$user = JFactory::getUser();
$filterSearch = $this->escape($this->state->get('list.search'));
$filterState = $this->escape($this->state->get('list.filter_state'));
$filterUsername	= $this->escape($this->state->get('list.filter_username'));
$filterEmail = $this->escape($this->state->get('list.filter_email'));
$filterModerator = $this->escape($this->state->get('list.filter_moderator'));
$filterSignature = $this->escape($this->state->get('list.filter_signature'));
$filterLoggedin	= $this->escape($this->state->get('list.filter_loggedin'));
$filterBanned = $this->escape($this->state->get('list.filter_banned'));
$listOrdering = $this->escape($this->state->get('list.ordering'));
$listDirection = $this->escape($this->state->get('list.direction'));

$this->document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/layout.css' );

?>

<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrdering; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<div id="j-sidebar-container" class="span2">
	<div id="sidebar">
		<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
	</div>
</div>

<div id="j-main-container" class="span10">
	<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=users') ?>" method="post" id="adminForm" name="adminForm">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="type" value="list" />
		<input type="hidden" name="filter_order" value="<?php echo $this->escape ( $this->state->get('list.ordering') ) ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape ($this->state->get('list.direction')) ?>" />
		<input type="hidden" name="limitstart" value="<?php echo intval ( $this->navigation->limitstart ) ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_( 'form.token' ); ?>

		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo 'Search in';?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo 'Search Users'; ?>" value="<?php echo $filterSearch; ?>" title="<?php echo 'Search Users'; ?>" />
			</div>
			<div class="btn-group pull-left">
				<button class="btn tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn tip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->navigation->getListFooter(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<?php echo JHtml::_('select.options', $sortDirection, 'value', 'text', $listDirection);?>
					</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrdering);?>
				</select>
			</div>
			<div class="clearfix"></div>
		</div>

		<table class="table table-striped">
			<thead>
				<tr>
					<th width="1%" class="nowrap center"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" /></th>
					<th width="5%" class="nowrap center hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_ENABLED', 'status', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th width="5%" class="nowrap hidden-phone"><?php echo JText::_('COM_KUNENA_USRL_AVATAR'); ?></th>
					<th><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_USERNAME', 'username', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th class="hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_GEN_EMAIL', 'email', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<!--<th class="center hidden-phone hidden-tablet"><?php //echo JHtml::_('grid.sort', 'COM_KUNENA_GEN_USERGROUP', 'usergroup', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>-->
					<th width="5%" class="nowrap center hidden-phone hidden-tablet"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_VIEW_MODERATOR', 'moderator', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th width="5%" class="nowrap center hidden-phone hidden-tablet"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_GEN_SIGNATURE', 'signature', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th width="5%" class="nowrap center hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_LOGGEDIN', 'loggedin', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th width="5%" class="nowrap center hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_BANNED', 'banned', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th width="1%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_ANN_ID', 'id', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
				</tr>
				<tr>
					<td class="hidden-phone">
					</td>
					<td class="nowrap center">
						<label for="filter_state" class="element-invisible"><?php echo JText::_('All');?></label>
						<select name="filter_state" id="filter_state" class="select-filter" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('All');?></option>
							<?php echo JHtml::_('select.options', $this->stateOptions(), 'value', 'text', $filterState, true); ?>
						</select>
					</td>
					<td class="nowrap"></td>
					<td class="nowrap">
						<label for="filter_username" class="element-invisible"><?php echo 'Search in';?></label>
						<input class="input-block-level input-filter" type="text" name="filter_username" id="filter_username" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterUsername; ?>" title="<?php echo 'Filter'; ?>" />
					</td>
					<td class="nowrap">
						<label for="filter_email" class="element-invisible"><?php echo 'Search in';?></label>
						<input class="input-block-level input-filter" type="text" name="filter_email" id="filter_email" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterEmail; ?>" title="<?php echo 'Filter'; ?>" />
					</td>
					<td class="nowrap center">
						<label for="filter_moderator" class="element-invisible"><?php echo JText::_('All');?></label>
						<select name="filter_moderator" id="filter_moderator" class="select-filter" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('All');?></option>
							<?php echo JHtml::_('select.options', $this->moderatorOptions(), 'value', 'text', $filterModerator); ?>
						</select>
					</td>
					<td class="nowrap center hidden-phone">
						<label for="filter_signature" class="element-invisible"><?php echo JText::_('All');?></label>
						<select name="filter_signature" id="filter_signature" class="select-filter" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('All');?></option>
							<?php echo JHtml::_('select.options', $this->signatureOptions(), 'value', 'text', $filterSignature); ?>
						</select>
					</td>
					<td class="nowrap center hidden-phone">
						<label for="filter_loggedin" class="element-invisible"><?php echo JText::_('All');?></label>
						<select name="filter_loggedin" id="filter_loggedin" class="select-filter" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('All');?></option>
							<?php echo JHtml::_('select.options', $this->loggedinOptions(), 'value', 'text', $filterLoggedin); ?>
						</select>
					</td>
					<td class="nowrap center">
						<label for="filter_banned" class="element-invisible"><?php echo JText::_('All');?></label>
						<select name="filter_banned" id="filter_banned" class="select-filter" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('All');?></option>
							<?php echo JHtml::_('select.options', $this->bannedOptions(), 'value', 'text', $filterBanned); ?>
						</select>
					</td>
					<td class="nowrap center hidden-phone">
					</td>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="12">
						<?php echo $this->navigation->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<?php
				if (!empty($this->users)) :
					$i = 0;
					foreach($this->users as $user) :
						$kunena_user = KunenaFactory::getUser($user->id);
						$userLogged = $kunena_user->isOnline() ? '<img src="components/com_kunena/images/tick.png" width="16" height="16" border="0" alt="" />': '';
						$userEnabled = $kunena_user->isBlocked() ? 'publish_x.png' : 'tick.png';
						$altUserEnabled = $kunena_user->isBlocked() ? JText::_( 'Blocked' ) : JText::_( 'Enabled' );
						$userBlockTask = $kunena_user->isBlocked() ? 'unblock' : 'block';
						$userbanned = $kunena_user->isBanned() ? 'tick.png' : 'publish_x.png';

						$userBannedTask = $kunena_user->isBanned() ? 'ban' : 'ban';
						$altUserBanned = $kunena_user->isBanned() ? JText::_( 'Banned' ) : JText::_( 'Not banned' );
					?>
			<tr>
				<td>
					<?php echo JHtml::_('grid.id', $i, intval($user->id)) ?>
				</td>
				<td class="center hidden-phone">
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $userBlockTask; ?>')">
						<i class="icon-checkmark"></i>
					</a>
				</td>
				<td class="hidden-phone"><?php echo $kunena_user->getAvatarImage('kavatar', 36, 36); ?></td>
				<td>
					<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $this->escape($kunena_user->username); ?></a>
					<small>
						<?php echo JText::sprintf('(Name: %s)', $this->escape($kunena_user->name));?>
					</small>
				</td>
				<td class="hidden-phone"><?php echo $this->escape($kunena_user->email); ?></td>
				<!--<td class="center hidden-phone hidden-tablet"><?php //echo $this->escape($kunena_user->group_id);?></td>-->
				<td class="center hidden-phone hidden-tablet">
					<?php echo $kunena_user->moderator ? JText::_('COM_KUNENA_YES') : JText::_('COM_KUNENA_NO'); ?>
				</td>
				<td class="center hidden-phone hidden-tablet"><?php echo $this->escape ( $kunena_user->signature ); ?></td>
				<td class="center hidden-phone"><?php echo $userLogged; ?></td>
				<td class="center hidden-phone">
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $userBannedTask; ?>')">
						<i class="icon-cancel"></i>
					</a>
				</td>
				<td class="center"><?php echo $this->escape($kunena_user->userid); ?></td>
			</tr>
		<?php $i++; endforeach; else : ?>
			<tr>
				<td colspan="12"><?php echo JText::_('COM_KUNENA_NOUSERSFOUND') ?></td>
			</tr>
		<?php endif; ?>
		</table>
	</form>
</div>

<div class="pull-right small">
	<?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>
