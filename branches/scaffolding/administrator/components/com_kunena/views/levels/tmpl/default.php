<?php
/**
 * @version		$Id$
 * @package		JXtended.Members
 * @subpackage	com_members
 * @copyright	(C) 2008 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die('Invalid Request.');

JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHTML::stylesheet('default.css', 'administrator/components/com_members/media/css/');
JHTML::script('checkall.js', 'administrator/components/com_members/media/js/');
JHTML::_('behavior.tooltip');
$type = $this->state->get('list.group_type');
?>
<form action="<?php echo JRoute::_('index.php?option=com_members&view=groups');?>" method="post" name="adminForm">
	<fieldset class="filter clearfix">
		<div class="left">
			<label for="search"><?php echo JText::_('JX Search'); ?>:</label>
			<input type="text" name="filter_search" id="search" value="<?php echo $this->state->get('filter.search'); ?>" size="60" title="<?php echo JText::_('JX Search in title'); ?>" />
			<button type="submit"><?php echo JText::_('JX Search Go'); ?></button>
			<button type="button" onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_('JX Search Clear'); ?></button>
		</div>
		<input type="hidden" name="group_type" value="<?php echo $type;?>" />
	</fieldset>
	<table class="adminlist">
		<thead>
			<tr>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(this)" />
				</th>
				<th class="left">
					<?php echo JText::_('JX Col Asset Group Name'); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('JX Col Asset Section'); ?>
				</th>
				<th width="20%">
					<?php echo JText::_('JX Col View User Groups'); ?>
				</th>
				<th width="50%">
					&nbsp;
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
			foreach ($this->items as $item) : ?>
			<tr class="row<?php echo $i++ % 2; ?>">
				<td style="text-align:center">
					<?php echo JHTML::_('grid.id', $item->id, $item->id); ?>
				</td>
				<td style="padding-left:<?php echo intval(($item->level-2)*15)+4; ?>px">
					<a href="<?php echo JRoute::_('index.php?option=com_kunena&task=rule.edit&name='.$item->rule_name);?>">
						<?php echo $item->name; ?></a>
				</td>
				<td align="center">
					<?php echo $item->section_id ? $item->section_name : JText::_('Global'); ?>
				</td>
				<td>
					<?php if ($userGroups = $item->references->getAroGroups()) : ?>
						<ol>
						<?php foreach ($item->references->getAroGroups() as $group) : ?>
							<li>
								<?php echo $group; ?>
							</li>
						<?php endforeach; ?>
						</ol>
					<?php endif; ?>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />

</form>
