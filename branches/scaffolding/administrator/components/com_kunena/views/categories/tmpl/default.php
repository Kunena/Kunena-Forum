<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

// Include the component HTML helpers.
JHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHTML::_('behavior.tooltip');
JHTML::script('checkall.js', 'administrator/components/com_kunena/media/js/');

// Load the default stylesheet.
JHTML::stylesheet('default.css', 'administrator/components/com_kunena/media/css/');

// Build the toolbar.
$this->buildDefaultToolBar();
?>
<form action="<?php echo JRoute::_('index.php?option=com_kunena&view=categories');?>" method="post" name="adminForm">
	<fieldset class="filter clearfix">
		<div class="left">
			<label for="search"><?php echo JText::_('Search'); ?>:</label>
			<input type="text" name="filter_search" id="search" value="<?php echo $this->state->get('filter.search'); ?>" size="60" title="<?php echo JText::_('KUNENA_CATEGORY_SEARCH_IN_TITLE'); ?>" />
			<button type="submit"><?php echo JText::_('Go'); ?></button>
			<button type="button" onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_('Clear'); ?></button>
		</div>
		<div class="right">
			<ol>
				<li>
					<label for="published">
						<?php echo JText::_('KUNENA_SHOW_CATEGORIES_THAT_ARE'); ?>:
					</label>
					<select name="filter_state" id="published" class="inputbox" onchange="this.form.submit()">
					<?php echo JHTML::_('select.options', $this->filter_state, 'value', 'text', $this->state->get('filter.state'));
					?>
					</select>
				</li>
			</ol>
		</div>
	</fieldset>
	<table class="adminlist">
		<thead>
			<tr>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(this)" />
				</th>
				<th class="left">
					<?php echo JHTML::_('grid.sort', 'FB Col Title', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="5%">
					<?php echo JHTML::_('grid.sort', 'FB Col Review', 'a.review', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="5%">
					<?php echo JHTML::_('grid.sort', 'FB Col Moderated', 'a.moderated', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="5%">
					<?php echo JHTML::_('grid.sort', 'FB Col Locked', 'a.locked', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="5%">
					<?php echo JHTML::_('grid.sort', 'FB Col Published', 'a.published', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="5%">
					<?php echo JHTML::_('grid.sort', 'FB Col Access', 'a.access', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="10%">
					<?php echo JHTML::_('grid.sort', 'FB Col Threads', 'a.total_threads', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
					/ <?php echo JHTML::_('grid.sort', 'FB Col Posts', 'a.total_posts', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
	$i = 0;
	foreach ($this->items as $item) :
?>
			<tr class="row<?php echo $i++ % 2; ?>">
				<td style="text-align:center">
					<?php echo JHTML::_('grid.id', $item->id, $item->id); ?>
				</td>
				<td style="padding-left:<?php echo intval($item->level*15)+4; ?>px">
					<?php if ($item->checked_out) : ?>
						<?php echo JHTML::_('KGrid.checkedout', $item->editor, $item->checked_out_time); ?>
					<?php endif; ?>
					<a href="<?php echo JRoute::_('index.php?option=com_kunena&task=category.edit&cid[]='.$item->id);?>">
						<?php echo $item->title; ?></a>
					<?php echo JHTML::_('KGrid.orderUpIcon', $item->id, $item->ordering, $this->pagination, 'category.orderup', 'Move Up', ($item->ordering != 0)); ?>
					<?php echo JHTML::_('KGrid.orderdownIcon', $item->id, $item->ordering, $this->pagination, 'category.orderdown', 'Move Down', (!empty($this->items[$i]) && ($item->level == $this->items[$i]->level))); ?>
				</td>
				<td align="center">
					<?php echo JHTML::_('KGrid.boolean', $item->id, $item->review, 'category.reviewed', 'category.unreviewed'); ?>
				</td>
				<td align="center">
					<?php echo JHTML::_('KGrid.boolean', $item->id, $item->moderated, 'category.moderated', 'category.unmoderated'); ?>
				</td>
				<td align="center">
					<?php echo JHTML::_('KGrid.boolean', $item->id, $item->locked, 'category.lock', 'category.unlock'); ?>
				</td>
				<td align="center">
					<?php echo JHTML::_('KGrid.published', $item->id, $item->published, 'category.'); ?>
				</td>
				<td align="center">
					<?php
						//echo JHTML::_('kunenagrid.access', $item->id, $item->access, $item->access_name, 'category.');
					?>
				</td>
				<td align="center">
					<?php echo $item->total_threads; ?>
					/ <?php echo $item->total_posts; ?>
				</td>
			</tr>
<?php
	endforeach;
?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
