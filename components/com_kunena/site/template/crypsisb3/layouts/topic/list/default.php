<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$cols = !empty($this->actions) ? 5 : 6;
$colspan = !empty($this->actions) ? 4 : 3;
?>
<div class="row">
	<div class="col-md-12">
		<div class="pull-left">
			<h2>
				<?php echo $this->escape($this->headerText); ?>
				<small class="hidden-xs">
					(<?php echo (JText::plural('COM_KUNENA_X_TOPICS', $this->formatLargeNumber($this->pagination->total))); ?>)
				</small>

				<?php // ToDo:: <span class="badge badge-success"> <?php echo $this->topics->count->unread; ?/></span> ?>
			</h2>
		</div>

		<div class="pull-right" id="filter-time">
			<div class="filter-sel pull-right">
				<form action="<?php echo $this->escape(JUri::getInstance()->toString()); ?>" id="timeselect" name="timeselect"
					method="post" target="_self" class="form-inline hidden-xs">
						<?php $this->displayTimeFilter('sel'); ?>
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
		->set('display', true);	?>
</div>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics'); ?>" method="post" name="ktopicsform" id="ktopicsform">
	<?php echo JHtml::_('form.token'); ?>
	<table class="table table-bordered">
		<thead>
		<tr>
			<td class="col-md-1 center hidden-xs">
				<a id="forumtop"> </a>
				<a href="#forumbottom">
					<i class="glyphicon glyphicon-arrow-down hasTooltip "></i>
				</a>
			</td>
			<td class="col-md-<?php echo $cols ?>">
				<?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?>
			</td>
			<td class="col-md-2 hidden-xs">
				<?php echo JText::_('COM_KUNENA_GEN_REPLIES'); ?> / <?php echo JText::_('COM_KUNENA_GEN_HITS');?>
			</td>
			<td class="col-md-3 hidden-xs">
				<?php echo JText::_('COM_KUNENA_GEN_LAST_POST'); ?>
			</td>
			<?php if (!empty($this->actions)) : ?>
				<td class="col-md-1 center">
					<label>
						<input class="kcheckall" type="checkbox" name="toggle" value="" />
					</label>
				</td>
			<?php endif; ?>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<td class="center hidden-xs">
				<a id="forumbottom"> </a>
				<a href="#forumtop" rel="nofollow">
					<i class="glyphicon glyphicon-arrow-up hasTooltip"></i>
				</a>
			</td>
			<?php if (empty($this->actions)) : ?>
			<td colspan="<?php echo $colspan; ?>" class="hidden-xs">
			<?php else : ?>
			<td colspan="<?php echo $colspan; ?>">
			<?php endif; ?>
				<?php if (!empty($this->actions) || !empty($this->moreUri)) : ?>
				<div class="form-group">
					<label>
					<?php if (!empty($this->topics) && !empty($this->moreUri)) echo JHtml::_('kunenaforum.link', $this->moreUri, JText::_('COM_KUNENA_MORE'), null, 'btn btn-primary', 'follow'); ?>
					<?php if (!empty($this->actions)) : ?>
						<?php echo JHtml::_('select.genericlist', $this->actions, 'task', 'class="form-control kchecktask" ', 'value', 'text', 0, 'kchecktask'); ?>
						<?php if (isset($this->actions['move'])) :
							$options = array (JHtml::_ ( 'select.option', '0', JText::_('COM_KUNENA_BULK_CHOOSE_DESTINATION') ));
							echo JHtml::_('kunenaforum.categorylist', 'target', 0, $options, array(), 'class="form-control fbs" disabled="disabled"', 'value', 'text', 0, 'kchecktarget');
						endif;?>
						<input type="submit" name="kcheckgo" class="btn btn-default" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
					<?php endif; ?>
						</label>
				</div>
				<?php endif; ?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php if (empty($this->topics) && empty($this->subcategories)) : ?>
			<tr>
				<td colspan="4" class="center"><?php echo JText::_('COM_KUNENA_VIEW_NO_TOPICS') ?></td>
			</tr>
		<?php else : ?>
			<?php foreach ($this->topics as $i => $topic)
			{
				echo $this->subLayout('Topic/Row')
					->set('topic', $topic)
					->set('position', 'kunena_topic_' . $i)
					->set('checkbox', !empty($this->actions));
			} ?>
		<?php endif; ?>
		</tbody>
	</table>
</form>

<div class="pull-left">
	<?php echo $this->subLayout('Widget/Pagination/List')
		->set('pagination', $this->pagination->setDisplayedPages(4))
		->set('display', true); ?>
</div>

<div class="clearfix"></div>

