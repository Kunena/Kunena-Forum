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
<form action="<?php echo JRoute::_('index.php?option=com_kunena&view=members');?>" method="post" name="adminForm">
	<fieldset class="filter clearfix">
		<div class="left">
			<label for="search"><?php echo JText::_('Search'); ?>:</label>
			<input type="text" name="filter_search" id="search" value="<?php echo $this->state->get('filter.search'); ?>" size="60" title="<?php echo JText::_('KUNENA_MEMBERS_SEARCH_IN_NAME'); ?>" />
			<button type="submit"><?php echo JText::_('Go'); ?></button>
			<button type="button" onclick="$('search').value='';this.form.submit();"><?php echo JText::_('Clear'); ?></button>
		</div>
		<div class="right">
			<ol>
				<li>
					<label for="published">
						<?php echo JText::_('KUNENA_SHOW_MEMBERS_THAT_ARE'); ?>:
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
					<?php echo JHTML::_('grid.sort', 'Login Name', 'a.username', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="20%">
					<?php echo JHTML::_('grid.sort', 'Name', 'a.name', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="20%">
					<?php echo JHTML::_('grid.sort', 'Email', 'a.email', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="15%">
					<?php echo JHTML::_('grid.sort', 'Active', 'a.block', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
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
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_kunena&task=member.edit&cid[]='.$item->id);?>">
						<?php echo $item->username; ?></a>
				</td>
				<td align="center">
					<?php echo $item->name; ?>
				</td>
				<td align="center">
					<?php echo $item->email; ?>
				</td>
				<td align="center">
					<?php echo JHTML::_('KGrid.boolean', $item->id, !$item->block, 'member.activate', 'member.block'); ?>
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
