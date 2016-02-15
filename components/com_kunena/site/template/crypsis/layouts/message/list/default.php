<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Message
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

$colspan = !empty($this->actions) ? 4 : 3;
$cols    = empty($this->checkbox) ? 4 : 5;

?>

<div class="row-fluid">
	<div class="span12">
		<div class="pull-left">
			<h2>
				<?php echo $this->escape($this->headerText); ?>
				<small class="hidden-sm">
					(<?php echo (JText::plural('COM_KUNENA_X_TOPICS', $this->formatLargeNumber($this->pagination->total))); ?>)
				</small>

				<?php // ToDo:: <span class="badge badge-success"> <?php echo $this->topics->count->unread; ?/></span> ?>
			</h2>
		</div>

		<div class="filter-time pull-right">
			<div class="filter-sel">
				<form action="<?php echo $this->escape(JUri::getInstance()->toString()); ?>" id="timeselect" name="timeselect"
					method="post" target="_self" class="form-inline hidden-phone">
					<div>
						<?php $this->displayTimeFilter('sel'); ?>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="pull-right">
	<?php echo $this->subLayout('Widget/Search')
		->set('catid', 'all')
		->setLayout('topic'); ?>
</div>

<div class="pull-left">
	<?php echo $this->subLayout('Widget/Pagination/List')
		->set('pagination', $this->pagination->setDisplayedPages(4))
		->set('display', true); ?>
</div>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics'); ?>" method="post"
	name="ktopicsform" id="ktopicsform">
	<?php echo JHtml::_('form.token'); ?>

	<table class="table table-bordered table-condensed">
		<thead>
			<?php if (empty($this->messages)) : ?>
				<tr>
					<td colspan="<?php echo $colspan; ?>">
						<?php echo JText::_('COM_KUNENA_NO_POSTS') ?>
					</td>
				</tr>
			<?php else : ?>
				<tr class="category">
					<td class="span1 center hidden-phone">
						<a id="forumtop"> </a>
						<a href="#forumbottom">
							<i class="icon-arrow-down hasTooltip"></i>
						</a>
					</td>
					<td class="span<?php echo $cols; ?>">
						<?php echo JText::_('COM_KUNENA_GEN_MESSAGE'); ?> / <?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?>
					</td>
					<td class="span2">
						<?php echo JText::_('COM_KUNENA_GEN_REPLIES'); ?> / <?php echo JText::_('COM_KUNENA_GEN_HITS'); ?>
					</td>
					<td class="span3">
						<?php echo JText::_('COM_KUNENA_GEN_LAST_POST'); ?>
					</td>
					<?php if (!empty($this->actions)) : ?>
						<td class="span1 center">
							<label>
								<input class="kcheckall" type="checkbox" name="toggle" value="" />
							</label>
						</td>
					<?php endif; ?>
				</tr>
			<?php endif; ?>
		</thead>
		<tfoot>
			<?php if (!empty($this->messages)) : ?>
				<tr>
					<td class="center hidden-phone">
						<a id="forumbottom"> </a>
						<a href="#forumtop" rel="nofollow">
							<i class="icon-arrow-up hasTooltip"></i>
						</a>
						<?php // FIXME: $this->displayCategoryActions() ?>
					</td>
					<td colspan="<?php echo $colspan; ?>">
						<div class="input-append">
							<?php if (!empty($this->moreUri))
							{
								echo JHtml::_('kunenaforum.link', $this->moreUri, JText::_('COM_KUNENA_MORE'), null, 'btn btn-primary', 'nofollow');
							} ?>
							<?php if (!empty($this->actions)) : ?>
								<?php echo JHtml::_('select.genericlist', $this->actions, 'task', 'class="inputbox kchecktask" ', 'value', 'text', 0, 'kchecktask'); ?>
								<?php if (isset($this->actions['move'])) :
									$options = array(JHtml::_('select.option', '0', JText::_('COM_KUNENA_BULK_CHOOSE_DESTINATION')));
									echo JHtml::_('kunenaforum.categorylist', 'target', 0, $options, array(), 'class="inputbox fbs" disabled="disabled"', 'value', 'text', 0, 'kchecktarget');
								endif; ?>
								<input type="submit" name="kcheckgo" class="btn" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
							<?php endif; ?>
						</div>
					</td>
				</tr>
			<?php endif; ?>
		</tfoot>

		<tbody>
		<?php
		foreach ($this->messages as $i => $message)
			echo $this->subLayout('Message/Row')
				->set('message', $message)
				->set('position', $i)
				->set('checkbox', !empty($this->actions));
		?>
		</tbody>

	</table>
</form>

<div class="pagination pull-left">
	<?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $this->pagination->setDisplayedPages(4))->set('display', true); ?>
</div>

<form action="<?php echo $this->escape(JUri::getInstance()->toString()); ?>" id="timeselect" name="timeselect"
	method="post" target="_self" class="timefilter pull-right">
	<?php $this->displayTimeFilter('sel'); ?>
</form>

<div class="clearfix"></div>
