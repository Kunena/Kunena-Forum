<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
?>
<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=category&layout=manage') ?>" enctype="multipart/form-data" id="adminForm" name="adminForm" method="post" class="adminForm form-validate">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering') ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction') ?>" />
	<input type="hidden" name="limitstart" value="<?php echo $this->navigation->limitstart ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_( 'form.token' ); ?>

<div class="kmodule category-manage">
	<div class="kbox-wrapper kbox-full">
		<div class="category-manage-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><a rel="ksection-categories" title="<?php echo $this->header ?>"><?php echo $this->header ?></a></h2>
			<table class="kgrid kgrid-categories" id="kcategories-manager">
				<thead>
					<tr>
						<th class="kgrid-num">#</th>
						<th class="kgrid-name"><?php echo JHtml::_('kunenagrid.sort', JText::_('COM_KUNENA_CATEGORY'), 'name', $this->state->get('list.direction'), $this->state->get('list.ordering') ) ?></th>
						<th class="kgrid-id"><?php echo JHtml::_('kunenagrid.sort', JText::_('COM_KUNENA_CATID'), 'id', $this->state->get('list.direction'), $this->state->get('list.ordering') ) ?></th>
						<th class="kgrid-order">
							<?php echo JHtml::_('kunenagrid.sort', JText::_('COM_KUNENA_REORDER'), 'ordering', $this->state->get('list.direction'), $this->state->get('list.ordering') ) ?>
							<?php echo JHtml::_('kunenagrid.order', $this->state->get('list.count.admin') ) ?>
						</th>
						<th class="kgrid-lock"><?php echo JText::_('COM_KUNENA_LOCKED') ?></th>
						<th class="kgrid-review"><?php echo JText::_('COM_KUNENA_REVIEW') ?></th>
						<th class="kgrid-anonymous"><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS') ?></th>
						<th class="kgrid-polls"><?php echo JText::_('COM_KUNENA_ADMIN_POLLS') ?></th>
						<th class="kgrid-publish"><?php echo JText::_('COM_KUNENA_PUBLISHED') ?></th>
						<th class="kgrid-access"><?php echo JText::_('COM_KUNENA_ACCESS') ?></th>
						<th class="kgrid-checkbox"><input type="checkbox" class="kcatmanage-checkall check-all-toggle" name="" value="0" /></th>
					</tr>
				</thead>
				<?php
				$n = count($this->categories);
				foreach($this->categories as $i=>$category) : ?>
				<tr class="krow-<?php echo $i & 1 ? 'odd' : 'even' ?>">
					<td class="kgrid-num"><?php echo $this->navigation->limitstart + $i + 1;?></td>

					<td class="kgrid-name">
						<span class="ktree ktree-<?php echo implode('"></span><span class="ktree ktree-', $category->indent)?>"></span>
					<?php if (!$category->authorise('admin')): ?>
						<?php echo $this->escape($category->name); ?>
					<?php else : ?>
						<a class="grid_action hasTip" href="#edit" rel="{id:'cb<?php echo $i ?>', task:'edit'}" title="<?php echo JText::_('COM_KUNENA_EDIT_CATEGORY') ?> :: <?php echo $this->escape($category->name) ?>">
							<?php echo $this->escape($category->name) ?>
						</a>
					<?php endif; ?>
					</td>

					<td class="kgrid-id"><?php echo $category->id ?></td>

					<?php if (!$category->authorise('admin')): ?>

					<td class="kgrid-disabled" colspan="10"></td>

					<?php else : // authorise ?>

					<td class="kgrid-order">
						<span><?php echo JHtml::_('kunenagrid.orderUp', $i, 'orderup', $category->up ) ?></span>
						<span><?php echo JHtml::_('kunenagrid.orderDown', $i, 'orderdown', $category->down ) ?></span>
						<input type="text" name="order[<?php echo $category->id ?>]" size="4" value="<?php echo $category->ordering ?>" class="hasTip" title="Order :: Category Order" />
					</td>
					<td class="kgrid-lock"><?php echo JHtml::_('kunenagrid.boolean', $i, $category->locked, 'lock', 'unlock') ?></td>

					<?php if ($category->isSection()): ?>

					<td class="kgrid-section" colspan="3"><?php echo JText::_('COM_KUNENA_SECTION') ?></td>

					<?php else : // !isSection ?>

					<td class="kgrid-review"><?php echo JHtml::_('kunenagrid.boolean', $i, $category->review, 'review', 'unreview') ?></td>
					<td class="kgrid-anonymous"><?php echo JHtml::_('kunenagrid.boolean', $i, $category->allow_anonymous, 'allow_anonymous', 'deny_anonymous') ?></td>
					<td class="kgrid-polls"><?php echo JHtml::_('kunenagrid.boolean', $i, $category->allow_polls, 'allow_polls', 'deny_polls') ?></td>

					<?php endif // isSection ?>

					<td class="kgrid-publish"><?php echo JHtml::_('kunenagrid.published', $i, $category) ?></td>
					<td class="kgrid-access"><?php echo $this->escape ( $category->accessname ) ?></td>
					<td class="kgrid-checkbox"><?php echo JHtml::_('kunenagrid.checkedOut', $category, $i, 'id') ?></td>

					<?php endif // authorise ?>

				</tr>
				<?php endforeach // categories ?>
			</table>
		<div class="kpost-buttons">
			<button class="kbutton" onclick="javascript: submitbutton('add')" title="<?php echo JText::_('COM_KUNENA_NEW_CATEGORY_LONG') ?>"><?php echo JText::_('COM_KUNENA_NEW_CATEGORY') ?></button>
		</div>
	</div>

	<div class="ksection-pagination">
		<div class="kpagination-limitbox"><?php echo JText::_('COM_KUNENA_A_DISPLAY') ?> <?php echo $this->navigation->getLimitBox () ?></div>
		<?php echo $this->navigation->getPagesLinks () ?>
		<div class="kpagination-count"><?php echo $this->navigation->getResultsCounter () ?></div>
	</div>

	<div id="ksection-modbox">
		<?php echo $this->displayManageActions('class="kinputbox" size="1"', 'kmoderate-select') ?>
		<button class="button" onclick="javascript: submitbutton(this.form.batch.value)"><?php echo JText::_('COM_KUNENA_SUBMIT') ?></button>
	</div>
	</div>
	</div>
	</div>
	</div>
</form>