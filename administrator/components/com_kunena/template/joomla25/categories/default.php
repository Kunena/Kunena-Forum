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

			<table class="kadmin-sort">
				<tr>
					<td class="left" width="90%">
						<?php echo JText::_( 'COM_KUNENA_FILTER' ); ?>:
						<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape ( $this->state->get('filter.search') );?>" class="text_area" onchange="document.adminForm.submit();" />
						<button onclick="this.form.submit();"><?php echo JText::_( 'COM_KUNENA_GO' ); ?></button>
						<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_KUNENA_RESET' ); ?></button>
					</td>
				</tr>
			</table>
			<table class="adminlist table table-striped">
				<thead>
					<tr>
						<th align="center" width="5">#</th>
						<th width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->categories ); ?>);" /></th>
						<th class="title"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_CATEGORY', 'name', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
						<th><small><?php echo JHtml::_('grid.sort', 'COM_KUNENA_CATID', 'catid', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></small></th>
						<th width="100" class="center nowrap">
						<small>
							<?php echo JHtml::_('grid.sort', 'COM_KUNENA_REORDER', 'ordering', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?>
							<?php echo JHtml::_('grid.order',  $this->categories, 'filesave.png', 'saveorder' ); ?></small>
						</th>
						<th class="center"><small><?php echo JText::_('COM_KUNENA_LOCKED'); ?></small></th>
						<th class="center"><small><?php echo JText::_('COM_KUNENA_REVIEW'); ?></small></th>
						<th class="center"><small><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS'); ?></small></th>
						<th class="center"><small><?php echo JText::_('COM_KUNENA_ADMIN_POLLS'); ?></small></th>
						<th class="center"><small><?php echo JText::_('COM_KUNENA_PUBLISHED'); ?></small></th>
						<th class="center"><small><?php echo JText::_('COM_KUNENA_ACCESS'); ?></small></th>
						<th class="center"><small><?php echo JText::_('COM_KUNENA_CHECKEDOUT'); ?></small></th>
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
				<td class="left" width="70%"><a href="#edit" onclick="return listItemTask('cb<?php echo $i ?>','edit')"><?php echo str_repeat  ( '...', count($category->indent)-1 ).' '.$category->name; ?> </a></td>
				<td class="center"><?php echo intval($category->id); ?></td>

				<?php if ($category->isSection()): ?>

				<td class="right nowrap">
					<?php if ($changeOrder) : ?>
					<span><?php echo $this->navigation->orderUpIcon ( $i, $category->up, 'orderup', 'Move Up', 1 ); ?></span>
					<span><?php echo $this->navigation->orderDownIcon ( $i, $n, $category->down, 'orderdown', 'Move Down', 1 ); ?></span>
					<?php endif ?>
					<input type="text" name="order[<?php echo intval($category->id) ?>]" size="5" value="<?php echo intval($category->ordering); ?>" class="text_area center" />
				</td>
				<td class="center">
					<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->locked ? 'un':'').'lock'; ?>')">
						<?php echo ($category->locked == 1 ? $img_yes : $img_no); ?>
					</a>
				</td>
				<td colspan="3" class="center"><?php echo JText::_('COM_KUNENA_SECTION') ?></td>

				<?php else: ?>

				<td class="right nowrap">
					<?php if ($changeOrder) : ?>
					<span><?php echo $this->navigation->orderUpIcon ( $i, $category->up, 'orderup', 'Move Up', 1 ); ?></span>
					<span><?php echo $this->navigation->orderDownIcon ( $i, $n, $category->down, 'orderdown', 'Move Down', 1 ); ?></span>
					<?php endif ?>
					<input type="text" name="order[<?php echo intval($category->id) ?>]" size="5" value="<?php echo $this->escape ( $category->ordering ); ?>" class="text_area" style="text-align: center" />
				</td>
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
				<td class="center">
					<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->allow_polls ? 'deny':'allow').'_polls'; ?>')">
						<?php echo ($category->allow_polls == 1 ? $img_yes : $img_no); ?>
					</a>
				</td>

				<?php endif; ?>

				<td class="center"><?php echo JHtml::_('grid.published', $category, $i) ?></td>
				<td width="" align="center"><?php echo $this->escape ( $category->accessname ); ?></td>
				<td width="15%" align="center"><?php echo $this->escape ( $category->editor ); ?></td>
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