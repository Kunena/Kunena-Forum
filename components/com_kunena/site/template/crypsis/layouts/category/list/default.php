<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="kcategoryform" id="kcategoryform">
	<input type="hidden" name="view" value="category" />
	<?php echo JHtml::_( 'form.token' ); ?>

	<h2>
		<?php echo $this->escape($this->header); ?>
		<?php if (!empty($this->actions)) : ?>
		<div class="input-append pull-right">
			<?php echo JHtml::_('select.genericlist', $this->actions, 'task', 'size="1"', 'value', 'text', 0, 'kchecktask'); ?>
			<input type="submit" name="kcheckgo" class="btn" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
		</div>
		<?php endif; ?>
	</h2>

	<table class="table table-striped table-bordered" id="kflattable">
		<?php if (empty($this->categories)) : ?>
			<tbody>
				<tr>
					<td>
						<?php echo JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS_NONE') ?>
					</td>
				</tr>
			</tbody>
		<?php else : ?>
			<?php if (!empty($this->actions)) : ?>
			<thead>
				<th>
					<td colspan="2"></td>
					<td class="center">
						<input class="kcheckall" type="checkbox" name="toggle" value="" />
					</td>
				</th>
			</thead>
			<?php endif; ?>
			<tbody>
				<?php
				foreach ($this->categories as $this->category) {
					echo $this->subLayout('Category/List/Row')
						->set('category', $this->category)
						->set('config', $this->config)
						->set('checkbox', !empty($this->actions))
						->setLayout('flat');
				} ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="<?php echo empty($this->actions) ? 3 : 4 ?>">
						<div class="pull-right">
							<?php echo $this->subLayout('Pagination/List')->set('pagination', $this->pagination); ?>
						</div>
					</td>
				</tr>
			</tfoot>
		<?php endif; ?>
	</table>
</form>
