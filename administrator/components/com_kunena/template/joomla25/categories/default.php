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

$document = JFactory::getDocument();
$document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/admin.css' );
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/admin.rtl.css' );
$changeOrder 	= ($this->state->get('list.ordering') == 'ordering' && $this->state->get('list.direction') == 'asc');
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-adminforum"><?php echo JText::_('COM_KUNENA_ADMIN'); ?></div>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=categories') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="filter_order" value="<?php echo intval ( $this->state->get('list.ordering') ) ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape ($this->state->get('list.direction')) ?>" />
			<input type="hidden" name="limitstart" value="<?php echo intval ( $this->navigation->limitstart ) ?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_( 'form.token' ); ?>

			<fieldset id="filter-bar">
				<div class="filter-search fltlft">
					<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('COM_KUNENA_FILTER'); ?>:</label>
					<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('list.search')); ?>" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />

					<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
					<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
				</div>
				<div class="filter-select fltrt">
					<select name="filter_published" id="filter_published" class="inputbox" onchange="this.form.submit()">
						<option value="">-<?php echo JText::_('COM_KUNENA_CATEGORIES_FIELD_LABEL_PUBLISHED');?>-</option>
						<?php echo JHtml::_('select.options', $this->publishedOptions(), 'value', 'text', $this->filterPublished, true); ?>
					</select>

					<select name="filter_access" id="filter_access" class="inputbox" onchange="this.form.submit()">
						<option value="">-<?php echo JText::_('COM_KUNENA_CATEGORIES_FIELD_LABEL_ACCESS');?>-</option>
						<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $$this->filterAccess); ?>
					</select>

					<select name="filter_locked" id="filter_locked" class="inputbox" onchange="this.form.submit()">
							<option value="">-<?php echo JText::_('COM_KUNENA_CATEGORIES_FIELD_LABEL_LOCKED');?>-</option>
							<?php echo JHtml::_('select.options', $this->lockOptions(), 'value', 'text', $this->filterLocked); ?>
					</select>

					<select name="filter_review" id="filter_review" class="inputbox" onchange="this.form.submit()">
						<option value="">-<?php echo JText::_('COM_KUNENA_CATEGORIES_FIELD_LABEL_REVIEW');?>-</option>
						<?php echo JHtml::_('select.options', $this->reviewOptions(), 'value', 'text', $this->filterReview); ?>
					</select>

					<select name="filter_anonymous" id="filter_anonymous" class="inputbox" onchange="this.form.submit()">
						<option value="">-<?php echo JText::_('COM_KUNENA_CATEGORIES_FIELD_LABEL_ANONYMOUS');?>-</option>
						<?php echo JHtml::_('select.options', $this->anonymousOptions(), 'value', 'text', $this->filterAnonymous); ?>
					</select>
				</div>
				</fieldset>
			<div class="clr"> </div>

			<table class="adminlist table table-striped">
				<thead>
					<tr>
						<th align="center" width="5">#</th>
						<th width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->categories ); ?>);" /></th>
						<th width="5%">
							<?php echo JHtml::_('grid.sort', 'JSTATUS', 'p.published', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
						</th>
						<th>
							<small>
								<?php echo JHtml::_('grid.sort', 'COM_KUNENA_REORDER', 'ordering', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?>
								<?php echo JHtml::_('grid.order',  $this->categories, 'filesave.png', 'saveorder' ); ?>
							</small>
						</th>
						<th class="title">
							<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'p.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
						</th>
						<th width="7%" class="center">
							<?php echo JHTML::_('grid.sort', 'COM_KUNENA_CATEGORIES_LABEL_ACCESS', 'p.access', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
						</th>
						<th width="5%" class="center">
							<?php echo JHtml::_('grid.sort', 'COM_KUNENA_LOCKED', 'p.locked', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
						</th>
						<th width="5%" class="center">
							<?php echo JHtml::_('grid.sort', 'COM_KUNENA_REVIEW', 'p.review', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
						</th>
						<th width="5%" class="center">
							<?php echo JHtml::_('grid.sort', 'COM_KUNENA_CATEGORY_ANONYMOUS', 'p.anonymous', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
						</th>
						<th width="1%" class="center hidden-phone">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'p.id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="14">
							<div class="pagination">
								<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?> <?php echo $this->navigation->getLimitBox (); ?></div>
								<?php echo $this->navigation->getPagesLinks (); ?>
								<div class="limit"><?php echo $this->navigation->getResultsCounter (); ?></div>
							</div>
						</td>
					</tr>
				</tfoot>
		<?php
			$k = 0;
			$i = 0;
			$n = count($this->categories);
			$img_yes = '<img src="'.JUri::base(true).'/components/com_kunena/images/tick.png" alt="'.JText::_('COM_KUNENA_A_YES').'" />';
			$img_no = '<img src="'.JUri::base(true).'/components/com_kunena/images/publish_x.png" alt="'.JText::_('COM_KUNENA_A_NO').'" />';
			foreach($this->categories as $category) {
		?>
			<tr <?php echo 'class = "row' . $k . '"';?>>
				<td class="right"><?php echo $i + $this->navigation->limitstart + 1; ?></td>
				<td><?php echo JHtml::_('grid.id', $i, intval($category->id)) ?></td>
				<td class="center"><?php echo JHtml::_('grid.published', $category, $i) ?></td>

				<?php if ($category->isSection()): ?>

				<td class="right nowrap">
					<?php if ($changeOrder) : ?>
					<span><?php echo $this->navigation->orderUpIcon ( $i, $category->up, 'orderup', 'Move Up', 1 ); ?></span>
					<span><?php echo $this->navigation->orderDownIcon ( $i, $n, $category->down, 'orderdown', 'Move Down', 1 ); ?></span>
					<?php endif ?>
					<input type="text" name="order[<?php echo intval($category->id) ?>]" size="5" value="<?php echo intval($category->ordering); ?>" class="text_area center" />
				</td>
				<td class="left" width="70%"><a href="#edit" onclick="return listItemTask('cb<?php echo $i ?>','edit')"><?php echo str_repeat  ( '...', count($category->indent)-1 ).' '.$category->name; ?> </a></td>
				<td width="" align="center"><?php echo $this->escape ( $category->accessname ); ?></td>
				<td class="center">
					<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->locked ? 'un':'').'lock'; ?>')">
						<?php echo ($category->locked == 1 ? $img_yes : $img_no); ?>
					</a>
				</td>
				<td colspan="2" class="center"><?php echo JText::_('COM_KUNENA_SECTION') ?></td>

				<?php else: ?>

				<td class="right nowrap">
					<?php if ($changeOrder) : ?>
					<span><?php echo $this->navigation->orderUpIcon ( $i, $category->up, 'orderup', 'Move Up', 1 ); ?></span>
					<span><?php echo $this->navigation->orderDownIcon ( $i, $n, $category->down, 'orderdown', 'Move Down', 1 ); ?></span>
					<?php endif ?>
					<input type="text" name="order[<?php echo intval($category->id) ?>]" size="5" value="<?php echo $this->escape ( $category->ordering ); ?>" class="text_area" style="text-align: center" />
				</td>
				<td class="left" width="70%"><a href="#edit" onclick="return listItemTask('cb<?php echo $i ?>','edit')"><?php echo str_repeat  ( '...', count($category->indent)-1 ).' '.$category->name; ?> </a></td>
				<td width="" align="center"><?php echo $this->escape ( $category->accessname ); ?></td>
				<td class="center">
					<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->locked ? 'un':'').'lock'; ?>')">
						<?php echo ($category->locked == 1 ? $img_yes : $img_no); ?>
					</a>
				</td>
				<td class="center">
					<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->review ? 'un':'').'review'; ?>')">
						<?php echo ($category->review == 1 ? $img_yes : $img_no); ?>
					</a>
				</td>
				<td class="center">
					<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->allow_anonymous ? 'deny':'allow').'_anonymous'; ?>')">
						<?php echo ($category->allow_anonymous == 1 ? $img_yes : $img_no); ?>
					</a>
				</td>

				<?php endif; ?>

				<td width="15%" align="center"><?php echo $this->escape ( $category->id ); ?></td>
			</tr>
				<?php
				$i++;
				$k = 1 - $k;
				}
				?>
		</table>

		</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>