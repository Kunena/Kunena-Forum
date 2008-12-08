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

// Include the JXtended HTML helpers.
JHTML::addIncludePath(JPATH_ROOT.'/plugins/system/jxtended/html/html');

// Load the tooltip behavior.
JHTML::_('behavior.tooltip');

// Load the default stylesheet.
JHTML::stylesheet('default.css', 'administrator/components/com_kunena/media/css/');

// Build the toolbar.
$this->buildDefaultToolBar();
?>
<form action="<?php echo JRoute::_('index.php?option=com_kunena&view=rules');?>" method="post" name="adminForm">
	<fieldset class="filter">
		<div class="left">
			<label for="search"><?php echo JText::_('Search'); ?>:</label>
			<input type="text" name="fitler_search" id="search" value="<?php echo $this->state->get('filter.serach'); ?>" size="40" title="<?php echo JText::_('Search in note'); ?>" />
			<button type="submit"><?php echo JText::_('Go'); ?></button>
			<button type="button" onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_('Clear'); ?></button>
		</div>
	</fieldset>
	<table class="adminlist">
		<thead>
			<tr>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items);?>)" />
				</th>
				<th class="left">
					<?php echo JHTML::_('grid.sort', 'Note', 'a.note', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th nowrap="nowrap" align="center">
					<?php echo JText::_('User Groups'); ?>
				</th>
				<th nowrap="nowrap" align="center">
					<?php echo JText::_('Permissions'); ?>
				</th>
				<th nowrap="nowrap" align="center">
					<?php echo JText::_('Applies to Items'); ?>
				</th>
				<th width="5%">
					<?php echo JHTML::_('grid.sort', 'Allow/Deny', 'a.allow', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th nowrap="nowrap" width="5%">
					<?php echo JHTML::_('grid.sort', 'Enabled', 'a.enabled', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
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
			$i = -1;
			foreach ($this->items as $item) : ?>
			<tr class="row<?php echo ++$i % 2; ?>">
				<td style="text-align:center">
					<?php echo JHTML::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_kunena&task=rule.edit&rule_id='.$item->id);?>">
						<?php echo $item->note; ?></a>
					<br /><?php echo JText::sprintf('Rule Type %s', $item->acl_type); ?>
				</td>
				<td align="left" valign="top">
					<div class="scroll" style="height: 75px;">
				<?php
					if (isset($item->aros)) :
						foreach ($item->aros as $section => $aros) : ?>
							<strong><?php echo $section;?></strong>
							<?php if (count($aros)) : ?>
								<ul>
									<?php foreach ($aros as $name) : ?>
									<li>
										<?php echo $name; ?>
									</li>
									<?php endforeach; ?>
								</ul>
							<?php endif;
						endforeach;
					endif;

					if (isset($item->aroGroups) && count($item->aroGroups)) : ?>
						<ul>
							<?php foreach ($item->aroGroups as $name) : ?>
							<li>
								<?php echo $name; ?>
							</li>
							<?php endforeach; ?>
						</ul>
					<?php
					endif;
				?>
					</div>
				</td>
				<td align="left" valign="top">
				<?php if (isset($item->acos)) : ?>
					<div class="scroll" style="height: 75px;">
					<?php foreach ($item->acos as $section => $acos) : ?>
							<?php if (count($acos)) : ?>
								<ul>
									<?php foreach ($acos as $name) : ?>
									<li>
										<?php echo $name; ?>
									</li>
									<?php endforeach; ?>
								</ul>
							<?php endif;
						endforeach; ?>
					</div>
				<?php endif; ?>
				</td>

				<td align="left" valign="top">
				<?php if (isset($item->axos)) : ?>
					<div class="scroll" style="height: 75px;">
					<?php foreach ($item->axos as $section => $axos) : ?>
							<?php if ($n = count($axos)) : ?>
								<ul>
									<?php foreach ($axos as $name) : ?>
									<li>
										<?php echo $name; ?>
									</li>
									<?php endforeach; ?>
								</ul>
							<?php endif;
						endforeach; ?>
					</div>
					<?php
					endif;

					if (isset($item->axoGroups) && count($item->axoGroups)) : ?>
						<strong><?php echo JText::_('JX Item Groups');?></strong>
						<ol>
							<?php foreach ($item->axoGroups as $name) : ?>
							<li>
								<?php echo $name; ?>
							</li>
							<?php endforeach; ?>
						</ol>
					<?php
					endif;
				?>
				</td>
				<td align="center">
					<?php echo JHTML::_('jxgrid.allowed', $item->allow, $i); ?>
				</td>
				<td align="center">
					<?php echo JHTML::_('jxgrid.enabled', $item->enabled, $i); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
