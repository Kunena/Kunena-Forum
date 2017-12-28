<?php
/**
 * Kunena Component
 * @package     Kunena.Administrator.Template
 * @subpackage  Users
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

// @var KunenaAdminViewUsers $this

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');

?>

<script type="text/javascript">
	Joomla.orderTable = function()
	{
		var table = document.getElementById("sortTable");
		var direction = document.getElementById("directionTable");
		var order = table.options[table.selectedIndex].value;

		if (order != '<?php echo $this->listOrdering; ?>')
		{
			var dirn = 'asc';
		}
		else
		{
			var dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN . '/template/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=users') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="type" value="list" />
			<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->state->get('list.ordering')) ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->state->get('list.direction')) ?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_('form.token'); ?>

			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_INPUT_SEARCH') ?></label>
					<input type="text" name="filter_search" id="filter_search" class="filter" placeholder="<?php echo JText::_('COM_KUNENA_FIELD_INPUT_SEARCH'); ?>" value="<?php echo $this->filterSearch; ?>" title="<?php echo JText::_('COM_KUNENA_FIELD_INPUT_SEARCH'); ?>" />
				</div>
				<div class="btn-group pull-left">
					<button class="btn tip" type="submit" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT'); ?>"><i class="icon-search"></i> <?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?></button>
					<button class="btn tip" type="button" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>" onclick="jQuery('.filter').val('');jQuery('#adminForm').submit();"><i class="icon-remove"></i> <?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?></button>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
					<?php echo KunenaLayout::factory('pagination/limitbox')->set('pagination', $this->pagination); ?>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
					<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
						<?php echo JHtml::_('select.options', $this->sortDirectionFields, 'value', 'text', $this->listDirection);?>
						</select>
				</div>
				<div class="btn-group pull-right">
					<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
					<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
						<?php echo JHtml::_('select.options', $this->sortFields, 'value', 'text', $this->listOrdering);?>
					</select>
				</div>
				<div class="clearfix"></div>
			</div>

			<table class="table table-striped" id="userList">
				<thead>
					<tr>
						<th width="1%" class="nowrap center"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" /></th>
						<th><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_USERNAME', 'username', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
						<th class="hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_GEN_EMAIL', 'email', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
						<th width="5%" class="hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_GEN_IP', 'ip', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
						<th width="10%" class="nowrap hidden-phone hidden-tablet"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_A_RANKS', 'rank', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
						<th width="5%" class="nowrap center hidden-phone hidden-tablet"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_GEN_SIGNATURE', 'signature', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
						<th width="5%" class="nowrap center hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_ENABLED', 'enabled', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
						<th width="5%" class="nowrap center hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_BANNED', 'banned', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
						<th width="5%" class="nowrap center hidden-phone hidden-tablet"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_VIEW_MODERATOR', 'moderator', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
						<th width="1%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_ANN_ID', 'id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
					</tr>
					<tr>
						<td class="hidden-phone">
						</td>
						<td class="nowrap">
							<label for="filter_username" class="element-invisible"><?php echo JText::_('COM_KUNENA_USERS_FIELD_INPUT_SEARCHUSERS');?></label>
							<input class="input-block-level input-filter filter" type="text" name="filter_username" id="filter_username" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterUsername; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
						</td>
						<td class="nowrap">
							<label for="filter_email" class="element-invisible"><?php echo JText::_('COM_KUNENA_USERS_FIELD_INPUT_SEARCHUSERS');?></label>
							<input class="input-block-level input-filter filter" type="text" name="filter_email" id="filter_email" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterEmail; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
						</td>
						<td class="nowrap">
							<label for="filter_ip" class="element-invisible"><?php echo JText::_('COM_KUNENA_USERS_FIELD_INPUT_SEARCHUSERS');?></label>
							<input class="input-block-level input-filter filter" type="text" name="filter_ip" id="filter_ip" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterIp; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
						</td>
						<td class="nowrap">
							<label for="filter_rank" class="element-invisible"><?php echo JText::_('COM_KUNENA_USERS_FIELD_INPUT_SEARCHUSERS');?></label>
							<select name="filter_rank" id="filter_rank" class="select-filter filter" onchange="Joomla.orderTable()">
								<option value=""><?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT');?></option>
								<?php echo JHtml::_('select.options', $this->ranksOptions(), 'value', 'text', $this->filterRank); ?>
							</select>
						</td>
						<td class="nowrap center hidden-phone">
							<label for="filter_signature" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></label>
							<select name="filter_signature" id="filter_signature" class="select-filter filter" onchange="Joomla.orderTable()">
								<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
								<?php echo JHtml::_('select.options', $this->signatureOptions(), 'value', 'text', $this->filterSignature); ?>
							</select>
						</td>
						<td class="nowrap center">
							<label for="filter_block" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></label>
							<select name="filter_block" id="filter_block" class="select-filter filter" onchange="Joomla.orderTable()">
								<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
								<?php echo JHtml::_('select.options', $this->blockOptions(), 'value', 'text', $this->filterBlock, true); ?>
							</select>
						</td>
						<td class="nowrap center">
							<label for="filter_banned" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></label>
							<select name="filter_banned" id="filter_banned" class="select-filter filter" onchange="Joomla.orderTable()">
								<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
								<?php echo JHtml::_('select.options', $this->bannedOptions(), 'value', 'text', $this->filterBanned); ?>
							</select>
						</td>
						<td class="nowrap center">
							<label for="filter_moderator" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></label>
							<select name="filter_moderator" id="filter_moderator" class="select-filter filter" onchange="Joomla.orderTable()">
								<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
								<?php echo JHtml::_('select.options', $this->moderatorOptions(), 'value', 'text', $this->filterModerator); ?>
							</select>
						</td>
						<td class="nowrap center hidden-phone">
						</td>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="8">
							<?php echo KunenaLayout::factory('pagination/footer')->set('pagination', $this->pagination); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php
				$i = 0;
				$img_no = '<i class="icon-cancel"></i>';
				$img_yes = '<i class="icon-checkmark"></i>';

				if ($this->pagination->total > 0) :
				foreach ($this->users as $user) :
				$userBlockTask = $user->isBlocked() ? 'unblock' : 'block';
				$userBannedTask = $user->isBanned() ? 'unban' : 'ban';
				?>
					<tr>
						<td>
							<?php echo JHtml::_('grid.id', $i, intval($user->userid)) ?>
						</td>
						<td>
							<span class="editlinktip hasTip" title="<?php echo $this->escape($user->username . '::' . $user->getAvatarImage('kavatar', 128, 128)); ?> ">
								<?php echo $user->getAvatarImage('kavatar', 24, 24); ?>
								<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $this->escape($user->username); ?></a>
								<small>
									(<?php echo JText::sprintf('COM_KUNENA_LABEL_USER_NAME', $this->escape($user->name));?>)
								</small>
							</span>
						</td>
						<td>
							<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $this->escape($user->email); ?></a>
						</td>
						<td>
							<a href="#tab6" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $this->escape($user->ip); ?></a>
						</td>
						<td class="hidden-phone hidden-tablet">
							<a href="#tab7" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $this->escape($user->getRank(0, 'title')); ?></a>
						</td>
						<td class="center hidden-phone hidden-tablet">
							<span class="editlinktip <?php echo ($user->signature ? 'hasTip' : ''); ?>" title="<?php echo $this->escape($user->signature); ?> ">
								<?php if ($user->signature) { ?>
									<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo JText::_('COM_KUNENA_YES'); ?></a>
								<?php } else { ?>
									<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo JText::_('COM_KUNENA_NO'); ?></a>
								<?php } ?>
							</span>
						</td>
						<td class="center hidden-phone">
							<a class ="btn btn-micro <?php echo (!$user->isBlocked() ? 'active' : ''); ?>" href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $userBlockTask ?>')">
								<?php echo (!$user->isBlocked() ? $img_yes : $img_no); ?>
							</a>
						</td>
						<td class="center hidden-phone">
							<a class ="btn btn-micro <?php echo ($user->isBanned() ? 'active' : ''); ?>" href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $userBannedTask ?>')">
								<?php echo ($user->isBanned() ? $img_yes : $img_no); ?>
							</a>
						</td>
						<td class="center hidden-phone hidden-tablet">
							<span class ="btn btn-micro <?php echo ($user->moderator ? 'active' : ''); ?>">
								<?php echo ($user->moderator ? $img_yes : $img_no); ?>
							</span>
						</td>
						<td class="center"><?php echo $this->escape($user->userid); ?></td>
					</tr>
				<?php $i++;
				endforeach;
				else : ?>
					<tr>
						<td colspan="10">
							<div class="well center filter-state">
								<span><?php echo JText::_('COM_KUNENA_FILTERACTIVE'); ?>
									<button class="btn" type="button"  onclick="document.getElements('.filter').set('value', '');this.form.submit();"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></button>
								</span>
							</div>
						</td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>
			<?php echo $this->loadTemplateFile('moderators'); ?>
		</form>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
