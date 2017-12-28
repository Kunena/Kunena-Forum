<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Category
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;
?>
<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=category') ?>" method="post" name="kcategoryform" id="kcategoryform">
	<?php echo JHtml::_('form.token'); ?>

	<h3>
		<?php echo $this->escape($this->headerText); ?>
		<span class="badge badge-info"><?php echo (int) $this->pagination->total; ?></span>

		<?php if (!empty($this->actions) && !empty($this->categories)) : ?>
			<div class="input-append pull-right">
				<?php echo JHtml::_('select.genericlist', $this->actions, 'task', 'size="1"', 'value', 'text', 0,
					'kchecktask'); ?>
				<input type="submit" name="kcheckgo" class="btn" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
			</div>
		<?php endif; ?>

		<?php if (!empty($this->embedded)) : ?>
		<div class="pull-right">
			<?php echo $this->subLayout('Widget/Pagination/List')
				->set('pagination', $this->pagination)
				->set('display', true); ?>
		</div>
		<?php endif; ?>
	</h3>

	<table class="table table-striped<?php echo KunenaTemplate::getInstance()->borderless();?>">

		<?php if (!empty($this->actions) && !empty($this->categories)) : ?>
			<thead>
				<tr>
					<th colspan="3"></th>
					<th class="center">
						<input class="kcheckallcategories" type="checkbox" name="toggle" value="" />
					</th>
				</tr>
			</thead>
		<?php endif; ?>

		<?php if (empty($this->categories)) : ?>
			<tbody>
				<tr>
					<td>
						<?php echo JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS_NONE') ?>
					</td>
				</tr>
			</tbody>
		<?php else : ?>
			<tbody>
				<?php
				foreach ($this->categories as $this->category)
				{
					echo $this->subLayout('Category/List/Row')
						->set('category', $this->category)
						->set('config', $this->config)
						->set('checkbox', !empty($this->actions));
				}
				?>
			</tbody>
		<?php endif; ?>

	</table>
</form>
