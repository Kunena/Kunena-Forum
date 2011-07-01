<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHTML::_('behavior.formvalidation');
JHTML::_('behavior.tooltip');
?>
<form enctype="multipart/form-data" name="adminForm" method="post" id="kcategoryform" class="adminForm form-validate" action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=category&layout=manage') ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering') ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction') ?>" />
	<input type="hidden" name="limitstart" value="<?php echo $this->navigation->limitstart ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>
	<div class="ksection">
		<h2 class="kheader"><a rel="ksection-categories" title="<?php echo $this->header ?>"><?php echo $this->header ?></a></h2>
			<table class="kgrid kgrid-categories" id="kcategories-manager">
				<thead>
					<tr>
						<th class="kgrid-num">#</th>
						<th class="kgrid-name"><?php echo JHTML::_('kunenagrid.sort', JText::_('COM_KUNENA_CATEGORY'), 'name', $this->state->get('list.direction'), $this->state->get('list.ordering') ) ?></th>
						<th class="kgrid-id"><?php echo JHTML::_('kunenagrid.sort', JText::_('COM_KUNENA_CATID'), 'id', $this->state->get('list.direction'), $this->state->get('list.ordering') ) ?></th>
						<th class="kgrid-order">
							<?php echo JHTML::_('kunenagrid.sort', JText::_('COM_KUNENA_REORDER'), 'ordering', $this->state->get('list.direction'), $this->state->get('list.ordering') ) ?>
							<?php echo JHTML::_('kunenagrid.order', $this->state->get('list.count.admin') ) ?>
						</th>
						<th class="kgrid-lock"><?php echo JText::_('COM_KUNENA_LOCKED') ?></th>
						<th class="kgrid-moderate"><?php echo JText::_('COM_KUNENA_MODERATED') ?></th>
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
						<span><?php echo JHTML::_('kunenagrid.orderUp', $i, 'orderup', $category->up ) ?></span>
						<span><?php echo JHTML::_('kunenagrid.orderDown', $i, 'orderdown', $category->down ) ?></span>
						<input type="text" name="order[<?php echo $category->id ?>]" size="4" value="<?php echo $category->ordering ?>" class="hasTip" title="Order :: Category Order" />
					</td>
					<td class="kgrid-lock"><?php echo JHTML::_('kunenagrid.boolean', $i, $category->locked, 'lock', 'unlock') ?></td>

					<?php if ($category->isSection()): ?>

					<td class="kgrid-section" colspan="4"><?php echo JText::_('COM_KUNENA_SECTION') ?></td>

					<?php else : // !isSection ?>

					<td class="kgrid-moderate"><?php echo JHTML::_('kunenagrid.boolean', $i, $category->moderated, 'moderate', 'unmoderate') ?></td>
					<td class="kgrid-review"><?php echo JHTML::_('kunenagrid.boolean', $i, $category->review, 'review', 'unreview') ?></td>
					<td class="kgrid-anonymous"><?php echo JHTML::_('kunenagrid.boolean', $i, $category->allow_anonymous, 'allow_anonymous', 'deny_anonymous') ?></td>
					<td class="kgrid-polls"><?php echo JHTML::_('kunenagrid.boolean', $i, $category->allow_polls, 'allow_polls', 'deny_polls') ?></td>

					<?php endif // isSection ?>

					<td class="kgrid-publish"><?php echo JHTML::_('kunenagrid.published', $i, $category) ?></td>
					<td class="kgrid-access">
						<ul>
						<?php echo $category->pub_group ? '<li>'.$this->escape ( $category->pub_group ).'</li>' : '' ?>
						<?php echo $category->admin_group ? '<li>'.$this->escape ( $category->admin_group ).'</li>' : '' ?>
						</ul>
					</td>
					<td class="kgrid-checkbox"><?php echo JHTML::_('kunenagrid.checkedOut', $category, $i, 'id') ?></td>

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
		<select name="batch" id="kmoderate-select" class="kinputbox" size="1">
			<option value=""><?php echo JText::_('COM_KUNENA_SELECT_BATCH_OPTION') ?></option>
			<option value="publish"><?php echo JText::_('COM_KUNENA_PUBLISH') ?></option>
			<option value="unpublish"><?php echo JText::_('COM_KUNENA_UNPUBLISH') ?></option>
			<option value="lock"><?php echo JText::_('COM_KUNENA_LOCK') ?></option>
			<option value="unlock"><?php echo JText::_('COM_KUNENA_UNLOCK') ?></option>
			<option value="review"><?php echo JText::_('COM_KUNENA_ENABLE_REVIEW') ?></option>
			<option value="unreview"><?php echo JText::_('COM_KUNENA_DISABLE_REVIEW') ?></option>
			<option value="allow_anomymous"><?php echo JText::_('COM_KUNENA_ALLOW_ANONYMOUS') ?></option>
			<option value="deny_anonymous"><?php echo JText::_('COM_KUNENA_DISALLOW_ANONYMOUS') ?></option>
			<option value="allow_polls"><?php echo JText::_('COM_KUNENA_ALLOW_POLLS') ?></option>
			<option value="deny_polls"><?php echo JText::_('COM_KUNENA_DISALLOW_POLLS') ?></option>
			<option value="delete"><?php echo JText::_('COM_KUNENA_DELETE') ?></option>
		</select>
		<button class="kbutton" onclick="javascript: submitbutton(this.form.batch.value)"><?php echo JText::_('COM_KUNENA_SUBMIT') ?></button>
	</div>
</form>