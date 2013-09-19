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
<?php if ($this->category->headerdesc) : ?>
<div class="alert alert-info">
	<a class="close" data-dismiss="alert" href="#">&times;</a>
	<?php echo $this->category->displayField('headerdesc'); ?>
</div>
<?php endif; ?>

<?php if (!$this->category->isSection()) : ?>
<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena'); ?>" method="post">
	<input type="hidden" name="view" value="topics" />
	<?php echo JHtml::_( 'form.token' ); ?>

	<?php if (!empty($this->topicActions)) : ?>
	<span class="pull-right">
		<input class="kcheckall" type="checkbox" name="toggle" value="" />
	</span>
	<?php endif; ?>

	<h2>
		<a><?php echo $this->escape($this->headerText); ?></a>
	</h2>

	<?php if (!empty($this->categoryButtons)) : ?>
	<div class="hidden-phone">
		<?php echo implode($this->categoryButtons); // TODO <- ?>
	</div>
	<?php endif; ?>

	<?php if (empty($this->topics) && !$this->category->isSection()) : ?>
	<div class="alert">
		<?php echo JText::_('COM_KUNENA_VIEW_NO_TOPICS') ?>
	</div>
	<?php else : ?>
	<table class="table table-striped table-bordered table-hover table-condensed">
		<thead>
			<tr>
				<td colspan="1" class="center">
					<a id="forumtop"> </a>
					<a href="#forumbottom"><span class="divider"></span><i class="icon-arrow-down hasTooltip"></i></a>
				</td>
				<td colspan="3">
					<div class="pull-right">
						<?php echo $this->subLayout('Pagination/List')->set('pagination', $this->pagination); ?>
					</div>
				</td>
			</tr>
		</thead>
		<?php
		$previous = null;
		foreach ($this->topics as $position=>$topic) {
		echo $this->subLayout('Topic/Row')
			->set('topic', $topic)
			->set('spacing', $previous && $previous->ordering != $topic->ordering)
			->set('position', 'kunena_topic_' . $position)
			->set('checkbox', !empty($this->topicActions))
			->setLayout('table_icons');
		$previous = $topic;
		}
		?>
		<tfoot>
		<tr>
			<td colspan="1" class="center">
				<a id="forumbottom"> </a>
				<a href="#forumtop" rel="nofollow"><span class="divider"></span><i class="icon-arrow-up hasTooltip"></i></a>
				<?php //$this->displayCategoryActions() ?>
			</td>
			<td colspan="3">
				<?php if (!empty($this->topicActions) || !empty($this->embedded)) : ?>
				<!-- Bulk Actions -->
				<?php if (!empty($this->moreUri)) echo JHtml::_('kunenaforum.link', $this->moreUri, JText::_('COM_KUNENA_MORE'), null, null, 'follow'); ?>
				<?php if (!empty($this->topicActions)) : ?>
				<?php echo JHtml::_('select.genericlist', $this->topicActions, 'task', 'class="inputbox kchecktask" size="1"', 'value', 'text', 0, 'kchecktask'); ?>
				<?php if ($this->actionMove) :
								$options = array (JHtml::_ ( 'select.option', '0', JText::_('COM_KUNENA_BULK_CHOOSE_DESTINATION') ));
								echo JHtml::_('kunenaforum.categorylist', 'target', 0, $options, array(), 'class="inputbox fbs" size="1" disabled="disabled"', 'value', 'text', 0, 'kchecktarget');
							?>
				<button class="btn" name="kcheckgo" type="submit"><?php echo JText::_('COM_KUNENA_GO') ?></button>
				<?php endif;?>
				<?php endif; ?>
				<!-- /Bulk Actions -->
				<?php endif; ?>
				<div class="pull-right">
					<?php echo $this->subLayout('Pagination/List')->set('pagination', $this->pagination); ?>
				</div>
			</td>
		</tr>
		</tfoot>
	</table>
	<?php endif; ?>
</form>

<?php if (!empty($this->moderators)) echo $this->subLayout('Category/Moderators')->set('moderators', $this->moderators); ?>
<?php endif; ?>
