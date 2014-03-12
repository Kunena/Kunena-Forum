<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Category
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$categoryActions = $this->getCategoryActions();
$colspan = empty($this->actions) ? 5 : 6;
?>

<?php if ($this->category->headerdesc) : ?>
<div class="alert alert-info kfrontend">
	<a class="close" data-dismiss="alert" href="#">&times;</a>
	<?php echo $this->category->displayField('headerdesc'); ?>
</div>
<?php endif; ?>

<?php if (!$this->category->isSection()) : ?>
<h2>
	<a><?php echo $this->escape($this->headerText); ?></a>
	<span class="pull-right">
		<?php echo $this->subLayout('Widget/Search')->set('catid', $this->category->id); ?>
	</span>
</h2>

<form id="ktopicsapprove" action="<?php echo KunenaRoute::_('index.php?option=com_kunena&task=approvetopicsincategories'); ?>" method="post">
	<input type="hidden" name="view" value="topics" />
	<?php echo JHtml::_( 'form.token' ); ?>

	<table class="table table-striped table-bordered table-hover table-condensed">
		<thead>
			<tr>
				<ul class="inline no-margin">
					<?php if ($categoryActions) : ?>
						<li class="hidden-phone">
							<?php echo implode($categoryActions); ?>
						</li>
						<?php endif; ?>
				</ul>
			</tr>
			<tr>
				<td class="span1 center hidden-phone">
					<a id="forumtop"> </a>
					<a href="#forumbottom">
						<i class="icon-arrow-down hasTooltip"></i>
					</a>
				</td>
				<td class="span1">
				<?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?>
				</td>
				<td class="span1 center hidden-phone">
				<?php echo JText::_('COM_KUNENA_GEN_AUTHOR'); ?>
				</td>
				<td class="span1 center hidden-phone">
				<?php echo JText::_('COM_KUNENA_GEN_HITS');?>
				</td>
				<td class="span1 center hidden-phone">
				<?php echo JText::_('COM_KUNENA_GEN_REPLIES'); ?>
				</td>
				<td class="span1">
				<?php echo JText::_('COM_KUNENA_GEN_LAST_POST'); ?>
				</td>
				<?php if (!empty($this->topicActions)) : ?>
				<td class="span1 center">
					<label>
						<input class="kcheckall" type="checkbox" name="toggle" value="" />
					</label>
				</td>
				<?php endif; ?>
			</tr>
		</thead>
		<?php if (empty($this->topics)) : ?>
		<div class="alert">
			<?php echo JText::_('COM_KUNENA_VIEW_NO_TOPICS') ?>
		</div>

		<?php else :

		/** @var KunenaForumTopic $previous */
		$previous = null;

		foreach ($this->topics as $position => $topic) {
			echo $this->subLayout('Topic/Row')
				->set('topic', $topic)
				->set('spacing', $previous && $previous->ordering != $topic->ordering)
				->set('position', 'kunena_topic_' . $position)
				->set('checkbox', !empty($this->topicActions))
				->setLayout('category');
			$previous = $topic;
		}

		?>

</form>
		<tfoot>
		<tr>
			<td class="center hidden-phone">
				<a id="forumbottom"> </a>
				<a href="#forumtop" rel="nofollow">
					<span class="divider"></span>
					<i class="icon-arrow-up hasTooltip"></i>
				</a>
				<?php // FIXME: $this->displayCategoryActions() ?>
			</td>

			<td colspan="6">
				<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics'); ?>" method="post">

				<?php if (!empty($this->topicActions) || !empty($this->embedded)) : ?>

				<?php if (!empty($this->moreUri)) echo JHtml::_('kunenaforum.link', $this->moreUri,
						JText::_('COM_KUNENA_MORE'), null, null, 'follow'); ?>

				<?php if (!empty($this->topicActions)) : ?>
				<?php echo JHtml::_('select.genericlist', $this->topicActions, 'task',
							'class="inputbox kchecktask" size="1"', 'value', 'text', 0, 'kchecktask'); ?>

				<?php if ($this->actionMove) :
								$options = array (
									JHtml::_('select.option', '0', JText::_('COM_KUNENA_BULK_CHOOSE_DESTINATION'))
								);
								echo JHtml::_('kunenaforum.categorylist', 'target', 0, $options, array(),
									'size="1" disabled="disabled"', 'value', 'text', 0,
									'kchecktarget');
							?>
				<button class="btn" name="kcheckgo" type="submit"><?php echo JText::_('COM_KUNENA_GO') ?></button>
				<?php endif; ?>

				<?php endif; ?>

				<?php endif; ?>


				<?php echo JHtml::_( 'form.token' ); ?>
				</form>
			</td>
		</tr>
		</tfoot>
		<?php endif; ?>
	</table>


<div class="pull-right">
	<?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $this->pagination); ?>
</div>

<?php
if (!empty($this->moderators))
	echo $this->subLayout('Category/Moderators')->set('moderators', $this->moderators);
?>

<?php endif; ?>
<div class="clearfix"></div>
