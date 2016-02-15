<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$this->document->addScript ( 'includes/js/joomla.javascript.js' );
$this->addStyleSheet ( 'css/kunena.manage.css' );
?>
<div class="kblock kmanage">
	<div class="kheader">
		<h2><?php echo $this->header; ?></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=category&layout=manage') ?>" method="post" id="adminForm" name="adminForm">
	<input type="hidden" name="view" value="category" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo intval ( $this->state->get('list.ordering') ) ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo intval ( $this->state->get('list.direction') ) ?>" />
	<input type="hidden" name="limitstart" value="<?php echo intval ( $this->navigation->limitstart ) ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_( 'form.token' ); ?>
	<table class="kadmin-sort">
		<tr>
			<td class="kleft">
				<?php echo JText::_( 'COM_KUNENA_FILTER' ); ?>:
				<input type="text" name="search" value="<?php echo $this->escape ( $this->state->get('list.search') );?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'COM_KUNENA_GO' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_KUNENA_RESET' ); ?></button>
			</td>
			<td class="kright">
				<button onclick="submitbutton('add')"><?php echo JText::_( 'COM_KUNENA_NEW' ); ?></button>
				<button onclick="submitbutton('edit')"><?php echo JText::_( 'COM_KUNENA_EDIT' ); ?></button>
				<button onclick="submitbutton('remove')"><?php echo JText::_( 'COM_KUNENA_DELETE' ); ?></button>
			</td>
		</tr>
	</table>
	<table class="adminlist table table-striped">
		<thead>
			<tr>
				<th class="kcenter" width="5">#</th>
				<th width="5"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
				<th class="title"><?php echo JHtml::_('grid.sort', JText::_('COM_KUNENA_CATEGORY'), 'name', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
				<th><small><?php echo JHtml::_('grid.sort', JText::_('COM_KUNENA_CATID'), 'id', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></small></th>
				<th width="100" class="center nowrap"><small>
					<?php echo JHtml::_('grid.sort', JText::_('COM_KUNENA_REORDER'), 'ordering', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?>
					<?php echo JHtml::_('grid.order', $this->state->get('list.count.admin') ); ?></small>
				</th>
				<th class="kcenter"><small><?php echo JText::_('COM_KUNENA_LOCKED'); ?></small></th>
				<th class="kcenter"><small><?php echo JText::_('COM_KUNENA_REVIEW'); ?></small></th>
				<th class="kcenter"><small><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS'); ?></small></th>
				<th class="kcenter"><small><?php echo JText::_('COM_KUNENA_ADMIN_POLLS'); ?></small></th>
				<th class="kcenter"><small><?php echo JText::_('COM_KUNENA_PUBLISHED'); ?></small></th>
				<th class="kcenter"><small><?php echo JText::_('COM_KUNENA_PUBLICACCESS'); ?></small></th>
				<th class="kcenter"><small><?php echo JText::_('COM_KUNENA_CHECKEDOUT'); ?></small></th>
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
		$i = -1;
		$j = 0;
		$n = count($this->categories);
		$img_yes = '<img src="'.$this->ktemplate->getImagePath('tick.png').'" alt="'.JText::_('COM_KUNENA_YES').'" />';
		$img_no = '<img src="'.$this->ktemplate->getImagePath('publish_x.png').'" alt="'.JText::_('COM_KUNENA_NO').'" />';
		foreach($this->categories as $category) {
	?>
		<tr <?php echo 'class = "row' . $k . '"';?>>
			<td class="kright"><?php echo $j + $this->navigation->limitstart + 1; ?></td>
			<?php if (!$category->authorise('admin')): ?>

			<td></td>
			<td><?php echo str_repeat  ( '...', $category->level  ).' '.$this->escape($category->name); ?></td>

			<?php else : ?>

			<td><?php echo JHtml::_('grid.id', ++$i, intval($category->id)) ?></td>
			<td class="kleft" width="70%"><a href="#edit" onclick="return listItemTask('cb<?php echo $i ?>','edit')"><?php echo str_repeat  ( '...', $category->level  ).' '.$category->name; ?></a></td>

			<?php endif; ?>

			<td class="kcenter"><?php echo intval($category->id); ?></td>

			<?php if (!$category->authorise('admin')): ?>

			<td colspan="6"></td>

			<?php elseif ($category->isSection()): ?>

			<td class="right nowrap">
				<span><?php echo $this->navigation->orderUpIcon ( $i, $category->up, 'orderup', 'Move Up', 1 ); ?></span>
				<span><?php echo $this->navigation->orderDownIcon ( $i, $n, $category->down, 'orderdown', 'Move Down', 1 ); ?></span>
				<input type="text" name="order[<?php echo intval($category->id) ?>]" size="5" value="<?php echo intval($category->ordering); ?>" class="text_area center" />
			</td>
			<td class="kcenter">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->locked ? 'un':'').'lock'; ?>')">
					<?php echo ($category->locked == 1 ? $img_yes : $img_no); ?>
				</a>
			</td>
			<td colspan="3" class="kcenter"><?php echo JText::_('COM_KUNENA_SECTION') ?></td>

			<?php else: ?>

			<td class="right nowrap">
				<span><?php echo $this->navigation->orderUpIcon ( $i, $category->up, 'orderup', 'Move Up', 1 ); ?></span>
				<span><?php echo $this->navigation->orderDownIcon ( $i, $n, $category->down, 'orderdown', 'Move Down', 1 ); ?></span>
				<?php if ($category->reorder) : ?>
				<input type="text" name="order[<?php echo intval($category->id) ?>]" size="5" value="<?php echo $this->escape ( $category->ordering ); ?>" class="text_area" style="text-align: center" />
				<?php endif; ?>
			</td>
			<td class="kcenter">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->locked ? 'un':'').'lock'; ?>')">
					<?php echo ($category->locked == 1 ? $img_yes : $img_no); ?>
				</a>
			</td>
			<td class="kcenter">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->review ? 'un':'').'review'; ?>')">
					<?php echo ($category->review == 1 ? $img_yes : $img_no); ?>
				</a>
			</td>
			<td class="kcenter">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->allow_anonymous ? 'deny':'allow').'_anonymous'; ?>')">
					<?php echo ($category->allow_anonymous == 1 ? $img_yes : $img_no); ?>
				</a>
			</td>
			<td class="kcenter">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->allow_polls ? 'deny':'allow').'_polls'; ?>')">
					<?php echo ($category->allow_polls == 1 ? $img_yes : $img_no); ?>
				</a>
			</td>

			<?php endif; ?>

			<?php if (!$category->authorise('admin')): ?>
			<td></td>
			<td></td>
			<?php else: ?>
			<td class="kcenter"><?php echo JHtml::_('grid.published', $category, $i) ?></td>
			<td class="kcenter"><?php echo $this->escape ( $category->accessname ); ?></td>
			<td width="15%" class="kcenter"><?php echo $this->escape ( $category->editor ); ?></td>

			<?php endif; ?>
		</tr>
		<?php
		$j++;
		$k = 1 - $k;
		}
		?>
	</table>
</form>
</div>
</div>
</div>
