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
//$sortFields[] = JHtml::_('select.option', 'name', JText::_('COM_KUNENA_USRL_REALNAME'));
$sortFields[] = JHtml::_('select.option', 'email', JText::_('COM_KUNENA_USRL_EMAIL'));
$sortFields[] = JHtml::_('select.option', 'signature', JText::_('COM_KUNENA_GEN_SIGNATURE'));
$sortFields[] = JHtml::_('select.option', 'enabled', JText::_('COM_KUNENA_USRL_ENABLED'));
$sortFields[] = JHtml::_('select.option', 'banned', JText::_('COM_KUNENA_USRL_BANNED'));
$sortFields[] = JHtml::_('select.option', 'moderator', JText::_('COM_KUNENA_VIEW_MODERATOR'));
$sortFields[] = JHtml::_('select.option', 'id', JText::_('JGRID_HEADING_ID'));

$sortDirection = array();
$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('JGLOBAL_ORDER_ASCENDING'));
$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('JGLOBAL_ORDER_DESCENDING'));

$filterSearch = $this->escape($this->state->get('filter.search'));
$filterUsername	= $this->escape($this->state->get('filter.username'));
$filterEmail = $this->escape($this->state->get('filter.email'));
$filterSignature = $this->escape($this->state->get('filter.signature'));
$filterBlock = $this->escape($this->state->get('filter.block'));
$filterBanned = $this->escape($this->state->get('filter.banned'));
$filterModerator = $this->escape($this->state->get('filter.moderator'));
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
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_( 'form.token' ); ?>

		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo 'Search in';?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo 'Search Users'; ?>" value="<?php echo $filterSearch; ?>" title="<?php echo 'Search Users'; ?>" />
			</div>
			<div class="btn-group pull-left">
				<button class="btn tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i> Filter</button>
				<button class="btn tip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="jQuery('.filter').val('');jQuery('#adminForm').submit();"><i class="icon-remove"></i> Clear</button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getListFooter(); ?>
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

		<table class="table table-striped adminlist" id="userList">
			<thead>
				<tr>
					<th width="1%" class="nowrap center"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" /></th>
					<th><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_USERNAME', 'username', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th class="hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_GEN_EMAIL', 'email', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th width="5%" class="nowrap center hidden-phone hidden-tablet"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_GEN_SIGNATURE', 'signature', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th width="5%" class="nowrap center hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_ENABLED', 'enabled', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th width="5%" class="nowrap center hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_BANNED', 'banned', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th width="5%" class="nowrap center hidden-phone hidden-tablet"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_VIEW_MODERATOR', 'moderator', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th width="1%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_ANN_ID', 'id', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
				</tr>
				<tr>
					<td class="hidden-phone">
					</td>
					<td class="nowrap">
						<label for="filter_username" class="element-invisible"><?php echo 'Search in';?></label>
						<input class="input-block-level input-filter filter" type="text" name="filter_username" id="filter_username" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterUsername; ?>" title="<?php echo 'Filter'; ?>" />
					</td>
					<td class="nowrap">
						<label for="filter_email" class="element-invisible"><?php echo 'Search in';?></label>
						<input class="input-block-level input-filter filter" type="text" name="filter_email" id="filter_email" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterEmail; ?>" title="<?php echo 'Filter'; ?>" />
					</td>
					<td class="nowrap center hidden-phone">
						<label for="filter_signature" class="element-invisible"><?php echo JText::_('All');?></label>
						<select name="filter_signature" id="filter_signature" class="select-filter filter" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('All');?></option>
							<?php echo JHtml::_('select.options', $this->signatureOptions(), 'value', 'text', $filterSignature); ?>
						</select>
					</td>
					<td class="nowrap center">
						<label for="filter_block" class="element-invisible"><?php echo JText::_('All');?></label>
						<select name="filter_block" id="filter_block" class="select-filter filter" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('All');?></option>
							<?php echo JHtml::_('select.options', $this->blockOptions(), 'value', 'text', $filterBlock, true); ?>
						</select>
					</td>
					<td class="nowrap center">
						<label for="filter_banned" class="element-invisible"><?php echo JText::_('All');?></label>
						<select name="filter_banned" id="filter_banned" class="select-filter filter" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('All');?></option>
							<?php echo JHtml::_('select.options', $this->bannedOptions(), 'value', 'text', $filterBanned); ?>
						</select>
					</td>
					<td class="nowrap center">
						<label for="filter_moderator" class="element-invisible"><?php echo JText::_('All');?></label>
						<select name="filter_moderator" id="filter_moderator" class="select-filter filter" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('All');?></option>
							<?php echo JHtml::_('select.options', $this->moderatorOptions(), 'value', 'text', $filterModerator); ?>
						</select>
					</td>
					<td class="nowrap center hidden-phone">
					</td>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="8">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<?php
				$img_no = '<i class="icon-cancel"></i>';
				$img_yes = '<i class="icon-checkmark"></i>';
				if (!empty($this->items)) :
					$i = 0;
					foreach($this->items as $item) :
						$kunena_user = KunenaFactory::getUser($item->id);
						$userBlockTask = $kunena_user->isBlocked() ? 'unblock' : 'block';
						$userBannedTask = $kunena_user->isBanned() ? 'unban' : 'ban';
					?>
			<tr>
				<td>
					<?php echo JHtml::_('grid.id', $i, intval($item->id)) ?>
				</td>
				<td>
					<?php echo $kunena_user->getAvatarImage('kavatar', 36, 36); ?>
					<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $this->escape($item->username); ?></a>
					<small>
						<?php echo JText::sprintf('(Name: %s)', $this->escape($item->name));?>
					</small>
				</td>
				<td class="hidden-phone"><?php echo $this->escape($item->email); ?></td>
				<td class="center hidden-phone hidden-tablet">
					<?php echo $this->escape ( $kunena_user->signature ); ?>
				</td>
				<td class="center hidden-phone">
					<a class ="btn btn-micro <?php echo (!$item->block ? 'active':''); ?>" href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $userBlockTask ?>')">
						<?php echo (!$item->block ? $img_yes : $img_no); ?>
					</a>
				</td>
				<td class="center hidden-phone">
					<a class ="btn btn-micro <?php echo ($kunena_user->isBanned() ? 'active':''); ?>" href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $userBannedTask ?>')">
						<?php echo ($kunena_user->isBanned() ? $img_yes : $img_no); ?>
					</a>
				</td>
				<td class="center hidden-phone hidden-tablet">
					<span class ="btn btn-micro <?php echo ($kunena_user->moderator ? 'active':''); ?>">
						<?php echo ($kunena_user->moderator ? $img_yes : $img_no); ?>
					</span>
				</td>
				<td class="center"><?php echo $this->escape($item->id); ?></td>
			</tr>
		<?php $i++; endforeach; else : ?>
			<tr>
				<td colspan="8"><?php echo JText::_('COM_KUNENA_NOUSERSFOUND') ?></td>
			</tr>
		<?php endif; ?>
		</table>
	</form>
</div>

<div class="pull-right small">
	<?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>
