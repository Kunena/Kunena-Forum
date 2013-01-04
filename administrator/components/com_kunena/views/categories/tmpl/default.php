<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Categories
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
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
 <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=categories'); ?>" method="post" name="adminForm" id="adminForm">
 <!-- Main page container -->
<div class="container-fluid">
<div class="row-fluid">
 <div class="span2">
	<div><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
    <div class="clr">&nbsp;</div>
    <div><?php echo $this->sidebar; ?></div>
		<!-- Right side -->
 </div>
 <div class="span10">
 <div class="well well-small" style="min-height:120px;">
                       <div class="nav-header">Categories</div>
                         <div class="row-striped">
                         <br />			
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo 'Search in';?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo 'Search categorie'; ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo 'Search categorie'; ?>" />
			</div>
			<div class="btn-group pull-left">
				<button class="btn" rel="tooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-search"></i></button>
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
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
                    <option value="type" <?php if ($listDirn == 'type') echo 'selected="selected"'; ?>><?php echo JText::_('Type');?></option>
				</select>
			</div>
		
        
		<table class="table table-striped adminlist">
			<thead>
				<tr>
					<th width="20" class="nowrap center hidden-phone">
						<?php echo JText::_( 'Num' ); ?>
					</th>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="checkAll(<?php echo count ( $this->categories ); ?>);" />
					</th>
					<th width="5%" class="nowrap center">
						<?php echo JHTML::_('grid.sort', JText::_('JSTATUS'), 'p.published', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?>
					</th>
					<th class="nowrap">
						<?php echo JHTML::_('grid.sort', JText::_('JGLOBAL_TITLE'), 'p.title', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?>
					</th>
                    <th width="10%" class="nowrap hidden-phone">
						<?php echo JHTML::_('grid.sort', 'Type', 'p.type', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo JHTML::_('grid.sort', 'Access', 'p.access', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?>
					</th>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHTML::_('grid.sort', JText::_('JGRID_HEADING_ID'), 'p.id', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="6">
                    
								<div class="pagination"><?php echo $this->navigation->getPagesLinks (); ?></div>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php
			$k = 0;
			$i = 0;
			foreach($this->categories as $category) {
		?>
				<tr <?php echo 'class = "row' . $k . '"';?>>
					<td class="center hidden-phone">
						<?php echo $i + $this->navigation->limitstart + 1; ?>
					</td>
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, intval($category->id)) ?>
					</td>
					<td class="center">
						<?php echo JHtml::_('jgrid.published', $category->published, $i, 'publish','cb'); ?>
					</td>
					<td class="has-context">
						<div class="pull-left">
							
								<a href="#edit" onclick="return listItemTask('cb<?php echo $i ?>','edit')">
									<?php echo str_repeat  ( '...', count($category->indent)-1 ).' '.$category->name; ?></a>
							(Alias: <?php echo $category->alias  ?>)
						</div>
						<div class="pull-left">
							<?php
								// Create dropdown items
								if ($category) :
									JHtml::_('dropdown.edit','cb' . $i, '&view=categories#edit');
									JHtml::_('dropdown.divider');
								endif;

								if( $category ) :
									if ($category->published) :
										JHtml::_('dropdown.unpublish', 'cb' . $i, 'list.');
									else :
										JHtml::_('dropdown.publish', 'cb' . $i, 'list.');
									endif;
								endif;								

								// Render dropdown list
								echo JHtml::_('dropdown.render');
							?>
						</div>
					</td>
					<td class="hidden-phone">
                    <?php if ($category->isSection()): ?>
						<?php echo JText::_('COM_KUNENA_SECTION') ?>
                        <?php else: ?>
                        <?php echo JText::_('...Categorie') ?>
                        <?php endif; ?>
					</td>
					<td class="hidden-phone">
						<?php echo $this->escape ( $category->accessname ); ?>
					</td>
					<td class="center hidden-phone">
						<?php echo intval($category->id); ?>
					</td>
				</tr>
<?php
				$i++;
				$k = 1 - $k;
				}
				?>
                </tbody>
		</table>
		
	</div>
    <input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
</form>
	</div>

	</div>

	<div class="kadmin-footer center">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
    </div>
    </div>
</div>
