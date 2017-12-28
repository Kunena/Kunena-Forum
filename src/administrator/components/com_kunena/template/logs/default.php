<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Logs
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined('_JEXEC') or die();

/** @var KunenaAdminViewLogs $this */

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('bootstrap.popover');

$filterItem = $this->escape($this->state->get('item.id'));
?>

<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $this->listOrdering; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<div id="kunena" class="admin override">
<div id="j-sidebar-container" class="span2">
	<div id="sidebar">
		<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/common/menu.php'; ?></div>
	</div>
</div>
<div id="j-main-container" class="span10">
<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=logs'); ?>" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="1" />
<input type="hidden" name="filter_order" value="<?php echo $this->listOrdering; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirection; ?>" />
<?php echo JHtml::_('form.token'); ?>

<div id="filter-bar" class="btn-toolbar">
	<div class="btn-group pull-left">
		<?php echo JHtml::calendar($this->filterTimeStart, 'filter_time_start', 'filter_time_start', '%Y-%m-%d', array('class' => 'filter btn-wrapper', 'placeholder' => JText::_('COM_KUNENA_LOG_CALENDAR_PLACEHOLDER_START_DATE'))); ?>
		<?php echo JHtml::calendar($this->filterTimeStop, 'filter_time_stop', 'filter_time_stop', '%Y-%m-%d', array('class' => 'filter btn-wrapper', 'placeholder' => JText::_('COM_KUNENA_LOG_CALENDAR_PLACEHOLDER_END_DATE'))); ?>
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
	<div class="btn-group pull-right">
		<label for="sortTable" class="element-invisible"><?php echo 'Filter users by:';?></label>
		<select name="filter_usertypes" id="filter_usertypes" class="input-medium filter" onchange="Joomla.orderTable()">
			<option value=""><?php echo 'All';?></option>
			<?php echo JHtml::_('select.options', $this->filterUserFields, 'value', 'text', $this->filterUsertypes);?>
		</select>
	</div>
	<div class="clearfix"></div>
</div>

<table class="table table-striped" id="logList">
	<thead>
		<tr>
			<th class="nowrap center" width="1%">
				<?php echo !$this->group ? JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $this->listDirection, $this->listOrdering) : 'Count'; ?>
			</th>
			<th class="nowrap center" width="1%" style="width: 130px;">
				<?php echo JHtml::_('grid.sort', 'COM_KUNENA_LOG_TIME_SORT_LABEL', 'time', $this->listDirection, $this->listOrdering); ?>
			</th>
			<th class="nowrap" width="1%">
				<?php echo JHtml::_('grid.sort', 'COM_KUNENA_LOG_TYPE_SORT_LABEL', 'type', $this->listDirection, $this->listOrdering); ?>
				<?php echo $this->getGroupCheckbox('type'); ?>
			</th>
			<th class="nowrap center">
				Operation
				<?php echo $this->getGroupCheckbox('operation'); ?>
			</th>
			<th class="nowrap">
				<?php echo JHtml::_('grid.sort', 'COM_KUNENA_LOG_USER_SORT_LABEL', 'user', $this->listDirection, $this->listOrdering); ?>
				<?php echo $this->getGroupCheckbox('user'); ?>
			</th>
			<th class="nowrap">
				<?php echo JHtml::_('grid.sort', 'COM_KUNENA_LOG_CATEGORY_SORT_LABEL', 'category', $this->listDirection, $this->listOrdering); ?>
				<?php echo $this->getGroupCheckbox('category'); ?>
			</th>
			<th class="nowrap">
				<?php echo JHtml::_('grid.sort', 'COM_KUNENA_LOG_TOPIC_SORT_LABEL', 'topic', $this->listDirection, $this->listOrdering); ?>
				<?php echo $this->getGroupCheckbox('topic'); ?>
			</th>
			<th class="nowrap">
				<?php echo JHtml::_('grid.sort', 'COM_KUNENA_LOG_TARGET_USER_SORT_LABEL', 'target_user', $this->listDirection, $this->listOrdering); ?>
				<?php echo $this->getGroupCheckbox('target_user'); ?>
			</th>
			<th class="nowrap center">
				IP
				<?php echo $this->getGroupCheckbox('ip'); ?>
			</th>
			<?php if (!$this->group) : ?>
			<th class="nowrap center">
				Data
			</th>
			<?php endif; ?>
		</tr>
		<tr>
			<td>
			</td>
			<td>
			</td>
			<td>
				<label for="filter_type" class="element-invisible"><?php echo 'Type';?></label>
				<select name="filter_type" id="filter_type" class="select-filter filter" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
					<?php echo JHtml::_('select.options', $this->filterTypeFields, 'value', 'text', $this->filterType); ?>
				</select>
			</td>
			<td>
				<label for="filter_operation" class="element-invisible"><?php echo 'Type';?></label>
				<select name="filter_operation" id="filter_operation" class="filter" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
					<?php echo JHtml::_('select.options', $this->filterOperationFields, 'value', 'text', $this->filterOperation); ?>
				</select>
			</td>
			<td>
				<label for="filter_user" class="element-invisible"><?php echo JText::_('COM_KUNENA_LOG_USER_FILTER_LABEL');?></label>
				<input class="input-block-level input-filter filter" type="text" name="filter_user" id="filter_user" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterUser; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
			</td>
			<td>
				<label for="filter_category" class="element-invisible"><?php echo JText::_('COM_KUNENA_LOG_CATEGORY_FILTER_LABEL');?></label>
				<input class="input-block-level input-filter filter" type="text" name="filter_category" id="filter_category" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterCategory; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
			</td>
			<td>
				<label for="filter_topic" class="element-invisible"><?php echo JText::_('COM_KUNENA_LOG_TOPIC_FILTER_LABEL');?></label>
				<input class="input-block-level input-filter filter" type="text" name="filter_topic" id="filter_topic" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterTopic; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
			</td>
			<td>
				<label for="filter_target_user" class="element-invisible"><?php echo JText::_('COM_KUNENA_LOG_TARGET_USER_FILTER_LABEL');?></label>
				<input class="input-block-level input-filter filter" type="text" name="filter_target_user" id="filter_target_user" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterTargetUser; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
			</td>
			<td>
				<label for="filter_ip" class="element-invisible"><?php echo JText::_('COM_KUNENA_LOG_IP_FILTER_LABEL');?></label>
				<input class="input-block-level input-filter filter" type="text" name="filter_ip" id="filter_ip" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterIp; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
			</td>
			<?php if (!$this->group) : ?>
			<td>
			</td>
			<?php endif; ?>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="10">
				<?php echo KunenaLayout::factory('pagination/footer')->set('pagination', $this->pagination); ?>
			</td>
		</tr>
	</tfoot>
<tbody>
<?php
$i = 0;
if($this->pagination->total > 0) :
	foreach($this->items as $item) :
		$date = new KunenaDate($item->time);
		$user = KunenaUserHelper::get($item->user_id);
		$category = KunenaForumCategoryHelper::get($item->category_id);
		$topic = KunenaForumTopicHelper::get($item->topic_id);
		$target = KunenaUserHelper::get($item->target_user);
		?>
		<tr>
			<td class="center">
				<?php echo !$this->group ? $this->escape($item->id) : (int) $item->count; ?>
			</td>
			<td class="center">
				<?php echo $date->toSql(); ?>
			</td>
			<td class="center">
				<?php echo !$this->group || isset($this->group['type']) ? $this->escape($this->getType($item->type)) : ''; ?>
			</td>
			<td class="center">
				<?php echo !$this->group || isset($this->group['operation']) ? JText::_("COM_KUNENA_{$item->operation}") : ''; ?>
			</td>
			<td>
				<?php echo !$this->group || isset($this->group['user']) ? ($user->id ? $this->escape($user->username) . ' <small>(' . $this->escape($item->user_id) . ')</small>' . '<br />' . $this->escape($user->name) : '') : ''; ?>
			</td>
			<td>
				<?php echo !$this->group || isset($this->group['category']) ? ($category->exists() ? $category->displayField('name') . ' <small>(' . $this->escape($item->category_id) . ')</small>' : '') : ''; ?>
			</td>
			<td>
				<?php echo !$this->group || isset($this->group['topic']) ? ($topic->exists() ? $topic->displayField('subject') . ' <small>(' . $this->escape($item->topic_id) . ')</small>' : '') : ''; ?>
			</td>
			<td>
				<?php echo !$this->group || isset($this->group['target_user']) ? ($target->id ? $this->escape($target->username) . ' <small>(' . $this->escape($item->target_user) . ')</small>' . '<br />' . $this->escape($target->name) : '') : ''; ?>
			</td>
			<td class="center">
				<?php echo !$this->group || isset($this->group['ip']) ? $this->escape($item->ip) : ''; ?>
			</td>
			<?php if (!$this->group) : ?>
			<td>
				<a class="btn hasPopover" title="Data" data-content="<?php echo
				$this->escape("<pre>{$this->escape(json_encode(json_decode($item->data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))}</pre>"); ?>"
				   data-placement="left" href="#">Data</a>

			</td>
			<?php endif; ?>
		</tr>
		<?php
		$i++;
	endforeach;
else : ?>
		<tr>
			<td colspan="10">
				<div class="well center filter-state">
					<span><?php echo JText::_('COM_KUNENA_FILTERACTIVE'); ?>
						<?php if($this->filterActive) : ?>
							<button class="btn" type="button"  onclick="document.getElements('.filter').set('value', '');this.form.submit();"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></button>
						<?php endif; ?>
					</span>
				</div>
			</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
</form>
</div>
<div class="pull-right small">
	<?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>
</div>
