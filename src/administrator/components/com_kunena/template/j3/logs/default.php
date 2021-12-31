<?php
/**
 * Kunena Component
 * @package       Kunena.Administrator.Template
 * @subpackage    Logs
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/** @var KunenaAdminViewLogs $this */

HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('dropdown.init');
HTMLHelper::_('bootstrap.popover');

$filterItem = $this->escape($this->state->get('item.id'));
?>

<script type="text/javascript">
	Joomla.orderTable = function () {
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
			<div class="sidebar-nav"><?php include KPATH_ADMIN . '/template/j3/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<div class="well well-small">
			<div class="module-title nav-header"><i
						class="icon-search"></i> <?php echo Text::_('COM_KUNENA_LOG_MANAGER') ?>
			</div>
			<hr class="hr-condensed">
			<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=logs'); ?>"
			      method="post" name="adminForm"
			      id="adminForm">
				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="boxchecked" value="1"/>
				<input type="hidden" name="filter_order" value="<?php echo $this->listOrdering; ?>"/>
				<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirection; ?>"/>
				<?php echo HTMLHelper::_('form.token'); ?>

				<div id="filter-bar" class="btn-toolbar">
					<div class="btn-group pull-left">
						<?php echo HTMLHelper::calendar($this->filterTimeStart, 'filter_time_start', 'filter_time_start', '%Y-%m-%d', array('class' => 'filter btn-wrapper', 'placeholder' => Text::_('COM_KUNENA_LOG_CALENDAR_PLACEHOLDER_START_DATE'))); ?>
						<?php echo HTMLHelper::calendar($this->filterTimeStop, 'filter_time_stop', 'filter_time_stop', '%Y-%m-%d', array('class' => 'filter wrapper', 'placeholder' => Text::_('COM_KUNENA_LOG_CALENDAR_PLACEHOLDER_END_DATE'))); ?>
					</div>
					<div class="btn-group pull-left">
						<button class="btn tip" type="submit"
						        title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT'); ?>"><i
									class="icon-search"></i> <?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>
						</button>
						<button class="btn tip" type="button"
						        title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>"
						        onclick="jQuery('.filter').val('');jQuery('#adminForm').submit();"><i
									class="icon-remove"></i> <?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>
						</button>
					</div>
					<div class="btn-group pull-right hidden-phone">
						<label for="limit"
						       class="element-invisible"><?php echo Text::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
						<?php echo KunenaLayout::factory('pagination/limitbox')->set('pagination', $this->pagination); ?>
					</div>
					<div class="btn-group pull-right hidden-phone">
						<label for="directionTable"
						       class="element-invisible"><?php echo Text::_('JFIELD_ORDERING_DESC'); ?></label>
						<select name="directionTable" id="directionTable" class="input-medium"
						        onchange="Joomla.orderTable()">
							<option value=""><?php echo Text::_('JFIELD_ORDERING_DESC'); ?></option>
							<?php echo HTMLHelper::_('select.options', $this->sortDirectionFields, 'value', 'text', $this->listDirection); ?>
						</select>
					</div>
					<div class="btn-group pull-right">
						<label for="sortTable"
						       class="element-invisible"><?php echo Text::_('JGLOBAL_SORT_BY'); ?></label>
						<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
							<option value=""><?php echo Text::_('JGLOBAL_SORT_BY'); ?></option>
							<?php echo HTMLHelper::_('select.options', $this->sortFields, 'value', 'text', $this->listOrdering); ?>
						</select>
					</div>
					<div class="btn-group pull-right">
						<label for="sortTable" class="element-invisible"><?php echo 'Filter users by:'; ?></label>
						<select name="filter_usertypes" id="filter_usertypes" class="input-medium filter"
						        onchange="Joomla.orderTable()">
							<option value=""><?php echo 'All'; ?></option>
							<?php echo HTMLHelper::_('select.options', $this->filterUserFields, 'value', 'text', $this->filterUsertypes); ?>
						</select>
					</div>
					<div class="clearfix"></div>
				</div>

				<table class="table table-striped" id="logList">
					<thead>
					<tr>
						<th class="nowrap center" width="1%">
							<?php echo !$this->group ? HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'id', $this->listDirection, $this->listOrdering) : 'Count'; ?>
						</th>
						<th class="nowrap center" width="1%" style="width: 130px;">
							<?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_LOG_TIME_SORT_LABEL', 'time', $this->listDirection, $this->listOrdering); ?>
						</th>
						<th class="nowrap" width="1%">
							<?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_LOG_TYPE_SORT_LABEL', 'type', $this->listDirection, $this->listOrdering); ?>
							<?php echo $this->getGroupCheckbox('type'); ?>
						</th>
						<th class="nowrap center">
							Operation
							<?php echo $this->getGroupCheckbox('operation'); ?>
						</th>
						<th class="nowrap">
							<?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_LOG_USER_SORT_LABEL', 'user', $this->listDirection, $this->listOrdering); ?>
							<?php echo $this->getGroupCheckbox('user'); ?>
						</th>
						<th class="nowrap">
							<?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_LOG_CATEGORY_SORT_LABEL', 'category', $this->listDirection, $this->listOrdering); ?>
							<?php echo $this->getGroupCheckbox('category'); ?>
						</th>
						<th class="nowrap">
							<?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_LOG_TOPIC_SORT_LABEL', 'topic', $this->listDirection, $this->listOrdering); ?>
							<?php echo $this->getGroupCheckbox('topic'); ?>
						</th>
						<th class="nowrap">
							<?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_LOG_TARGET_USER_SORT_LABEL', 'target_user', $this->listDirection, $this->listOrdering); ?>
							<?php echo $this->getGroupCheckbox('target_user'); ?>
						</th>
						<th class="nowrap center">
							IP
							<?php echo $this->getGroupCheckbox('ip'); ?>
						</th>
						<?php if (!$this->group)
							:
							?>
							<th class="nowrap center">
								<?php echo Text::_('COM_KUNENA_LOG_MANAGER') ?>
							</th>
						<?php endif; ?>
					</tr>
					<tr>
						<td>
						</td>
						<td>
						</td>
						<td>
							<label for="filter_type" class="element-invisible"><?php echo 'Type'; ?></label>
							<select name="filter_type" id="filter_type" class="select-filter filter"
							        onchange="Joomla.orderTable()">
								<option value=""><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></option>
								<?php echo HTMLHelper::_('select.options', $this->filterTypeFields, 'value', 'text', $this->filterType); ?>
							</select>
						</td>
						<td>
							<label for="filter_operation" class="element-invisible"><?php echo 'Type'; ?></label>
							<select name="filter_operation" id="filter_operation" class="filter"
							        onchange="Joomla.orderTable()">
								<option value=""><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></option>
								<?php echo HTMLHelper::_('select.options', $this->filterOperationFields, 'value', 'text', $this->filterOperation); ?>
							</select>
						</td>
						<td>
							<label for="filter_user"
							       class="element-invisible"><?php echo Text::_('COM_KUNENA_LOG_USER_FILTER_LABEL'); ?></label>
							<input class="input-block-level input-filter filter" type="text" name="filter_user"
							       id="filter_user"
							       placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
							       value="<?php echo $this->filterUser; ?>"
							       title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
						</td>
						<td>
							<label for="filter_category"
							       class="element-invisible"><?php echo Text::_('COM_KUNENA_LOG_CATEGORY_FILTER_LABEL'); ?></label>
							<input class="input-block-level input-filter filter" type="text" name="filter_category"
							       id="filter_category"
							       placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
							       value="<?php echo $this->filterCategory; ?>"
							       title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
						</td>
						<td>
							<label for="filter_topic"
							       class="element-invisible"><?php echo Text::_('COM_KUNENA_LOG_TOPIC_FILTER_LABEL'); ?></label>
							<input class="input-block-level input-filter filter" type="text" name="filter_topic"
							       id="filter_topic"
							       placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
							       value="<?php echo $this->filterTopic; ?>"
							       title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
						</td>
						<td>
							<label for="filter_target_user"
							       class="element-invisible"><?php echo Text::_('COM_KUNENA_LOG_TARGET_USER_FILTER_LABEL'); ?></label>
							<input class="input-block-level input-filter filter" type="text" name="filter_target_user"
							       id="filter_target_user"
							       placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
							       value="<?php echo $this->filterTargetUser; ?>"
							       title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
						</td>
						<td>
							<label for="filter_ip"
							       class="element-invisible"><?php echo Text::_('COM_KUNENA_LOG_IP_FILTER_LABEL'); ?></label>
							<input class="input-block-level input-filter filter" type="text" name="filter_ip"
							       id="filter_ip"
							       placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
							       value="<?php echo $this->filterIp; ?>"
							       title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
						</td>
						<?php if (!$this->group)
							:
							?>
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
					$i                = 0;

					if ($this->pagination->total > 0)
						:
						foreach ($this->items as $item)
							:
							$date = new KunenaDate($item->time);
							$user     = KunenaUserHelper::get($item->user_id);
							$category = KunenaForumCategoryHelper::get($item->category_id);
							$topic    = KunenaForumTopicHelper::get($item->topic_id);
							$target   = KunenaUserHelper::get($item->target_user);

							$document = Factory::getDocument();
							$document->addScriptDeclaration("window.addEvent('domready', function(){
									$('link_sel_all" . $item->id . "').addEvent('click', function(e){
										$('report_final" . $item->id . "').select();
										try {
											var successful = document.execCommand('copy');
											var msg = successful ? 'successful' : 'unsuccessful';
											console.log('Copying text command was ' + msg);
										}
										catch (err)
										{
											console.log('Oops, unable to copy');
										}
									});
								});");
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
									<?php echo !$this->group || isset($this->group['operation']) ? Text::_("COM_KUNENA_{$item->operation}") : ''; ?>
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
										<a href="#kerror<?php echo $item->id; ?>_form" role="button"
										   class="btn openmodal"
										   data-toggle="modal" data-target="#kerror<?php echo $item->id; ?>_form"
										   rel="nofollow">
											<?php if ($this->escape($this->getType($item->type)) != 'ACT') :?>
												<span class="icon-warning" aria-hidden="true"></span>
											<?php else: ?>
												<span class="icon-edit" aria-hidden="true"></span>
											<?php endif ;?>
											<?php echo !$this->group || isset($this->group['type']) ? $this->escape($this->getType($item->type)) : ''; ?>
										</a>
									</td>
								<?php endif; ?>
							</tr>
							<div class="modal fade" id="kerror<?php echo $item->id; ?>_form" role="dialog"
							     aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display:none;"
							     data-backdrop="false">
								<div class="modal-header">
									<button type="reset" class="close" data-dismiss="modal" aria-hidden="true">&times;
									</button>
									<h3>
										<?php if ($this->escape($this->getType($item->type)) != 'ACT') :?>
											<span class="icon-warning" aria-hidden="true"></span>
										<?php else: ?>
											<span class="icon-edit" aria-hidden="true"></span>
										<?php endif ;?>
										Kunena <?php echo !$this->group || isset($this->group['type']) ? $this->escape($this->getType($item->type)) : ''; ?>

										ID:<?php echo $item->id; ?>
									</h3>
								</div>
								<div class="modal-body">
									<div>
										<textarea style="margin-top: -3000px" id="report_final<?php echo $item->id; ?>"
										          for="report_final<?php echo $item->id; ?>"><?php echo KunenaHtmlParser::plainBBCode($item->data); ?></textarea>
										<pre><?php echo json_encode(json_decode($item->data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></pre>
									</div>
								</div>
								<div class="modal-footer">
									<a href="#" id="link_sel_all<?php echo $item->id; ?>"
									   name="link_sel_all<?php echo $item->id; ?>" type="button"
									   class="btn btn-small btn-primary"><i
												class="icon icon-signup"></i><?php echo Text::_('COM_KUNENA_REPORT_SELECT_ALL'); ?>
									</a>
									<button class="btn btn-danger" data-dismiss="modal"
									        aria-hidden="true"><?php echo Text::_('COM_KUNENA_EDITOR_MODAL_CLOSE_LABEL') ?></button>
								</div>
							</div>
							<?php
							$i++;
						endforeach;
					else:
						?>
						<tr>
							<td colspan="10">
								<div class="well center filter-state">
							<span><?php echo Text::_('COM_KUNENA_FILTERACTIVE'); ?>
								<?php
								if ($this->filterActive)
									:
									?>
									<button class="btn" type="button"
									        onclick="document.getElements('.filter').set('value', '');this.form.submit();"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></button>
								<?php endif; ?>
							</span>
								</div>
							</td>
						</tr>
					<?php endif; ?>
					</tbody>
				</table>
			</form>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaAdminVersion::getLongVersionHTML(); ?>
	</div>
</div>
