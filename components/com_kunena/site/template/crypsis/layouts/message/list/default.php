<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Message
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$colspan = empty($this->actions) ? 4 : 5;
?>

<?php if (!empty($this->topics) && empty($this->subcategories)) : ?>
<div class="pagination pull-right">
	<?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $this->pagination->setDisplayedPages(4)); ?>
</div>
<?php endif; ?>

<?php if (!empty($this->embedded)) : ?>
<form action="<?php echo $this->escape(JUri::getInstance()->toString()); ?>" id="timeselect" name="timeselect"
      method="post" target="_self" class="timefilter pull-right">
	<?php $this->displayTimeFilter('sel'); ?>
</form>
<?php endif; ?>

<h3>
	<?php echo $this->escape($this->headerText); ?>
	<span class="badge badge-info"><?php echo $this->pagination->total; ?></span>
	<span class="badge badge-success"><?php // To Do:: echo $this->topics->count->unread; ?></span>
</h3>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics'); ?>" method="post"
      name="ktopicsform" id="ktopicsform">
	<?php echo JHtml::_('form.token'); ?>

	<table class="table table-striped table-bordered table-condensed">
		<?php if (empty($this->messages)) : ?>
		<tr>
			<td colspan="<?php echo $colspan; ?>">
				<?php echo JText::_('COM_KUNENA_NO_POSTS') ?>
			</td>
		</tr>
		<?php else : ?>
		<?php if (!empty($this->embedded)) : ?>
		<thead>
			<tr>
				<td colspan="6">
					<div class="pagination pull-right">
						<?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $this->pagination); ?>
					</div>
					<div class="clearfix"></div>
				</td>
			</tr>
		</thead>
		<?php endif; ?>
<thead>
			<tr>
				<td class="span1 center hidden-phone">
					<a id="forumtop"> </a>
					<a href="#forumbottom">
						<i class="icon-arrow-down hasTooltip"></i>
					</a>
				</td>
				<td class="span1">
				<?php echo JText::_('Message'); ?>
				</td>
				<td class="span1">
				<?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?>
				</td>
				<td class="span1 center hidden-phone">
				Author
				</td>
				<td class="span1">
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
		</thead>
		<?php if (!empty($this->actions)) : ?>
		<tfoot>
			<tr>
				<td colspan="<?php echo $colspan; ?>">
					<?php
					if (!empty($this->moreUri)) {
						echo JHtml::_('kunenaforum.link', $this->moreUri, JText::_('COM_KUNENA_MORE'), null, 'btn btn-primary', 'follow');
					}
					?>

					<?php if (!empty($this->actions)|| !empty($this->embedded))  : ?>
						<?php echo JHtml::_(
							'select.genericlist', $this->actions, 'task', 'class="inputbox kchecktask" size="1"',
							'value', 'text', 0, 'kchecktask'
						); ?>
						<input type="submit" name="kcheckgo" class="btn" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
					<?php endif; ?>
				</td>
			</tr>
		</tfoot>
		<?php endif; ?>

		<tbody>
			<?php
			foreach ($this->messages as $i => $message)
				echo $this->subLayout('Message/Row')
					->set('message', $message)
					->set('position', $i)
					->set('checkbox', !empty($this->actions));
			?>
		</tbody>
		<?php endif; ?>

	</table>
</form>
