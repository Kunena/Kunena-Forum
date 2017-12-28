<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Category
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$categoryActions = $this->getCategoryActions();
$cols            = empty($this->checkbox) ? 7 : 6;
$this->addStyleSheet('assets/css/rating.css');
?>

<?php if ($this->category->headerdesc) : ?>
	<div class="alert alert-info kfrontend">
		<h1>
			<a class="close" data-dismiss="alert" href="#"></a>
		</h1>
		<?php echo $this->category->displayField('headerdesc'); ?>
	</div>
<?php endif; ?>

<?php if (!$this->category->isSection()) : ?>

<?php if (!empty($this->topics)) : ?>
<div class="row-fluid">
	<div class="span12">
		<h2 class="pull-right">
			<?php echo $this->subLayout('Widget/Search')
	->set('catid', $this->category->id)
	->setLayout('topic'); ?>
		</h2>
		<div class="pull-left">
			<?php echo $this->subLayout('Widget/Pagination/List')
	->set('pagination', $this->pagination)
	->set('display', true); ?>
		</div>
	</div>
</div>
<?php endif; ?>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena'); ?>" method="post" id="ktopicsform">
	<input type="hidden" name="view" value="topics" />
	<?php echo JHtml::_('form.token'); ?>
	<div>
		<ul class="inline">
			<?php if ($categoryActions) : ?>
				<li>
					<?php echo implode($categoryActions); ?>
				</li>
			<?php endif; ?>
		</ul>
	</div>
	<?php if ($this->topics) : ?>
	<table class="table<?php echo KunenaTemplate::getInstance()->borderless();?>">
		<thead>
		<tr>
			<td class="span1 center hidden-phone">
				<a id="forumtop"> </a>
				<a href="#forumbottom" rel="nofollow">
					<?php echo KunenaIcons::arrowdown();?>
				</a>
			</td>
			<td class="span<?php echo $cols ?>">
				<?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?>
			</td>
			<td class="span2 hidden-phone">
				<?php echo JText::_('COM_KUNENA_GEN_REPLIES'); ?> / <?php echo JText::_('COM_KUNENA_GEN_HITS'); ?>
			</td>
			<td class="span3 hidden-phone">
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
		<?php endif; ?>
		<?php
			/** @var KunenaForumTopic $previous */
			$previous = null;

			foreach ($this->topics as $position => $topic)
			{
				echo $this->subLayout('Topic/Row')
					->set('topic', $topic)
					->set('spacing', $previous && $previous->ordering != $topic->ordering)
					->set('position', 'kunena_topic_' . $position)
					->set('checkbox', !empty($this->topicActions))
					->setLayout('category');
				$previous = $topic;
			}
		?>
		<tfoot>
		<?php if ($this->topics) : ?>
		<tr>
			<td class="center hidden-phone">
				<a id="forumbottom"> </a>
				<a href="#forumtop" rel="nofollow">
					<span class="divider"></span>
					<?php echo KunenaIcons::arrowup();?>
				</a>
				<?php // FIXME: $this->displayCategoryActions() ?>
			</td>
			<td colspan="6" class="hidden-phone">
				<div class="input-append">

					<?php if (!empty($this->moreUri))
					{
						echo JHtml::_(
       'kunenaforum.link', $this->moreUri,
	  JText::_('COM_KUNENA_MORE'), null, null, 'follow');
					} ?>

					<?php if (!empty($this->topicActions)) : ?>
						<?php echo JHtml::_(
	'select.genericlist', $this->topicActions, 'task',
'class="inputbox kchecktask"', 'value', 'text', 0, 'kchecktask'); ?>

						<?php if ($this->actionMove) : ?>
							<?php
							$options = array(JHtml::_('select.option', '0', JText::_('COM_KUNENA_BULK_CHOOSE_DESTINATION')));
							echo JHtml::_(
								'kunenaforum.categorylist', 'target', 0, $options, array(),
								' disabled="disabled"', 'value', 'text', 0,
								'kchecktarget'
							);
							?>
							<button class="btn" name="kcheckgo" type="submit"><?php echo JText::_('COM_KUNENA_GO') ?></button>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</td>
		</tr>
		<?php else: 
			echo JText::_('COM_KUNENA_VIEW_NO_TOPICS');
		endif; ?>
		</tfoot>
		<?php endif; ?>
	</table>
</form>

<?php if ($this->topics) : ?>
	<div class="pull-left">
		<?php echo $this->subLayout('Widget/Pagination/List')
	->set('pagination', $this->pagination)
	->set('display', true); ?>
	</div>
<?php endif; ?>
<div class="clearfix"></div>

<?php if (!empty($this->moderators))
{
	echo $this->subLayout('Category/Moderators')
		->set('moderators', $this->moderators);
}
?>

<?php if ($this->ktemplate->params->get('writeaccess')) : ?>
<div><?php echo $this->subLayout('Widget/Writeaccess')->set('id', $this->category->id); ?></div>
<?php endif; ?>
