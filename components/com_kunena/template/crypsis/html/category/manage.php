<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
jimport( 'joomla.html.html.grid' );

?>

<div class="well well-small">
  <h2 class="page-header"><?php echo $this->header; ?></h2>
  <div class="row-fluid column-row">
    <div class="span12 column-item">
      <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=category&layout=manage') ?>" method="post" id="adminForm" name="adminForm">
        <input type="hidden" name="view" value="category" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="filter_order" value="<?php echo intval ( $this->state->get('list.ordering') ) ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo intval ( $this->state->get('list.direction') ) ?>" />
        <input type="hidden" name="limitstart" value="<?php echo intval ( $this->navigation->limitstart ) ?>" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_( 'form.token' ); ?>
        <table class="adminlist table table-striped">
          <tr>
            <td>
              <div class="pull-left"> <?php echo JText::_( 'COM_KUNENA_FILTER' ); ?>:
                <input type="text" name="search" value="<?php echo $this->escape ( $this->state->get('list.search') );?>" class="text_area" onchange="document.adminForm.submit();" />
              </div>
              <div class="btn-group pull-left">
                <button class="btn" onclick="this.form.submit();"><i class="icon-chevron-right "></i><?php echo JText::_( 'COM_KUNENA_GO' ); ?></button>
                <button class="btn" onclick="document.getElementById('search').value='';this.form.submit();"><i class="icon-arrow-left"></i><?php echo JText::_( 'COM_KUNENA_RESET' ); ?></button>
              </div>
              <div class="btn-group pull-right">
                <button class="btn" onclick="javascript: submitbutton('add')"> <i class="icon-plus"></i> <?php echo JText::_( 'COM_KUNENA_NEW' ); ?></button>
                <button class="btn" onclick="javascript: submitbutton('edit')"> <i class="icon-edit"></i> <?php echo JText::_( 'COM_KUNENA_EDIT' ); ?></button>
                <button class="btn" onclick="javascript: submitbutton('remove')"> <i class="icon-remove"></i> <?php echo JText::_( 'COM_KUNENA_DELETE' ); ?></button>
              </div>
            </td>
          </tr>
        </table>
        <table class="adminlist table table-striped">
          <thead>
            <tr>
              <th class="kcenter span1">#</th>
              <th class="span1">
                <input type="checkbox" name="toggle" onclick="checkAll(<?php echo $this->state->get('list.count.admin'); ?>);" />
              </th>
              <th class="span1"><?php echo JHtml::_('grid.sort', JText::_('COM_KUNENA_CATEGORY'), 'name', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
              <th class="span1"><small><?php echo JHtml::_('grid.sort', JText::_('COM_KUNENA_CATID'), 'id', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></small></th>
              <th class="center nowrap span1"><small> <?php echo JHtml::_('grid.sort', JText::_('COM_KUNENA_REORDER'), 'ordering', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?> <?php echo JHtml::_('grid.order', $this->state->get('list.count.admin') ); ?></small> </th>
              <th class="span1 hidden-phone hidden-tablet"><small><?php echo JText::_('COM_KUNENA_LOCKED'); ?></small></th>
              <th class="span1 hidden-phone hidden-tablet"><small><?php echo JText::_('COM_KUNENA_REVIEW'); ?></small></th>
              <th class="span1 hidden-phone hidden-tablet "><small><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS'); ?></small></th>
              <th class="span1 hidden-phone hidden-tablet"><small><?php echo JText::_('COM_KUNENA_ADMIN_POLLS'); ?></small></th>
              <th class="span1 hidden-phone hidden-tablet"><small><?php echo JText::_('COM_KUNENA_PUBLISHED'); ?></small></th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <td colspan="14">
                <div class="pagination">
                  <div class="pull-left"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?> <?php echo $this->navigation->getLimitBox (). $this->navigation->getResultsCounter (); ?></div>
                  <div class="pull-right"><?php echo $this->navigation->getPagesLinks (); ?></div>
                </div>
              </td>
            </tr>
          </tfoot>
          <?php
		$k = 0;
		$i = -1;
		$j = 0;
		$n = count($this->categories);
		$img_yes = "<i class='icon-ok'></i>";
		$img_no = "<i class='icon-remove'></i>";
		foreach($this->categories as $category) {
	?>
          <tr <?php echo 'class = "row' . $k . '"';?>>
            <td class="span1 kright"><?php echo $j + $this->navigation->limitstart + 1; ?></td>
            <?php if (!$category->authorise('admin')): ?>
            <td></td>
            <td><?php echo str_repeat  ( '...', $category->level  ).' '.$this->escape($category->name); ?></td>
            <?php else : ?>
            <td><?php echo JHtml::_('grid.id', ++$i, intval($category->id)) ?></td>
            <td class="kleft span1" width="70%"><a href="#edit" onclick="return listItemTask('cb<?php echo $i ?>','edit')"><?php echo str_repeat  ( '...', $category->level  ).' '.$category->name; ?></a></td>
            <?php endif; ?>
            <td class="span1"><?php echo intval($category->id); ?></td>
            <?php if (!$category->authorise('admin')): ?>
            <td colspan="6"></td>
            <?php elseif ($category->isSection()): ?>
            <td class="span1 right nowrap"> <span><?php echo $this->navigation->orderUpIcon ( $i, $category->up, 'orderup', 'Move Up', 1 ); ?></span> <span><?php echo $this->navigation->orderDownIcon ( $i, $n, $category->down, 'orderdown', 'Move Down', 1 ); ?></span>
              <input type="text" name="order[<?php echo intval($category->id) ?>]" size="5" value="<?php echo intval($category->ordering); ?>" class="text_area center" />
            </td>
            <td class="span1 hidden-phone hidden-tablet"> <a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->locked ? 'un':'').'lock'; ?>')"> <?php echo ($category->locked == 1 ? $img_yes : $img_no); ?> </a> </td>
            <td colspan="3" class="span1 hidden-phone hidden-tablet"><?php echo JText::_('COM_KUNENA_SECTION') ?></td>
            <?php else: ?>
            <td class=" span1 right nowrap"> <span><?php echo $this->navigation->orderUpIcon ( $i, $category->up, 'orderup', 'Move Up', 1 ); ?></span> <span><?php echo $this->navigation->orderDownIcon ( $i, $n, $category->down, 'orderdown', 'Move Down', 1 ); ?></span>
              <?php if ($category->reorder) : ?>
              <input type="text" name="order[<?php echo intval($category->id) ?>]" size="5" value="<?php echo $this->escape ( $category->ordering ); ?>" class="text_area" style="text-align: center" />
              <?php endif; ?>
            </td>
            <td class="span1 hidden-phone hidden-tablet"> <a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->locked ? 'un':'').'lock'; ?>')"> <?php echo ($category->locked == 1 ? $img_yes : $img_no); ?> </a> </td>
            <td class="span1 hidden-phone hidden-tablet"> <a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->review ? 'un':'').'review'; ?>')"> <?php echo ($category->review == 1 ? $img_yes : $img_no); ?> </a> </td>
            <td class="span1 hidden-phone hidden-tablet"> <a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->allow_anonymous ? 'deny':'allow').'_anonymous'; ?>')"> <?php echo ($category->allow_anonymous == 1 ? $img_yes : $img_no); ?> </a> </td>
            <td class="span1 hidden-phone hidden-tablet"> <a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->allow_polls ? 'deny':'allow').'_polls'; ?>')"> <?php echo ($category->allow_polls == 1 ? $img_yes : $img_no); ?> </a> </td>
            <?php endif; ?>
            <td class="span1 hidden-phone hidden-tablet"><?php echo JHtml::_('jgrid.published', $i, $category); ?></td>
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
