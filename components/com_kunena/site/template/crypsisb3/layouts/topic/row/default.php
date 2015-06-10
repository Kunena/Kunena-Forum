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

/** @var KunenaLayout $this */
/** @var KunenaForumTopic $topic */
$topic = $this->topic;
$userTopic = $topic->getUserTopic();
$topicPages = $topic->getPagination(null, KunenaConfig::getInstance()->messages_per_page, 3);
$author = $topic->getLastPostAuthor();
$avatar = $author->getAvatarImage('img-rounded', 'thumb');
$category = $this->topic->getCategory();
$cols = empty($this->checkbox) ? 5 : 6;
$category = $this->topic->getCategory();
$config = KunenaConfig::getInstance();

if (!empty($this->spacing)) : ?>
<tr>
	<td colspan="<?php echo $cols; ?>">&nbsp;</td>
</tr>
<?php endif; ?>

<tr class="category<?php echo $this->escape($category->class_sfx); ?>">
	<?php if ($topic->unread) : ?>
<td class="hidden-sm center topic-item-unread">
<?php else :  ?>
	<td class="hidden-sm col-md-1 center">
		<?php endif;?>
		<?php echo $this->getTopicLink($topic, 'unread', $topic->getIcon($topic->getCategory()->iconset)); ?>
	</td>
	<td class="col-md-<?php echo $cols?>">
		<div>
			<?php echo $this->getTopicLink($topic, null, null, null, 'hasTooltip topictitle'); ?>
			<?php
			if ($topic->unread)
			{
				echo $this->getTopicLink($topic, 'unread',
					'<sup class="knewchar" dir="ltr">(' . (int) $topic->unread . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>', null, 'hasTooltip');
			}
			?>
		</div>
		<div class="pull-right">
			<?php if ($userTopic->favorite) : ?>
				<i class="glyphicon glyphicon-star hasTooltip" title="<?php echo JText::_('COM_KUNENA_FAVORITE'); ?>"></i>
			<?php endif; ?>

			<?php if ($userTopic->posts) : ?>
				<i class="glyphicon glyphicon-flag hasTooltip" title="<?php echo JText::_('COM_KUNENA_MYPOSTS'); ?>"></i>
			<?php endif; ?>

			<?php if ($this->topic->attachments) : ?>
				<i class="glyphicon glyphicon-flag-2 hasTooltip" title="<?php echo JText::_('COM_KUNENA_ATTACH'); ?>"></i>
			<?php endif; ?>

			<?php if ($this->topic->poll_id) : ?>
				<i class="glyphicon glyphicon-bars hasTooltip" title="<?php echo JText::_('COM_KUNENA_ADMIN_POLLS'); ?>"></i>
			<?php endif; ?>
		</div>

		<div class="hidden-sm">
			<?php echo JText::_('COM_KUNENA_TOPIC_STARTED_ON')?>
			<?php echo $topic->getFirstPostTime()->toKunena('config_post_dateformat'); ?>,
			<?php echo JText::_('COM_KUNENA_BY') ?>
			<?php echo $topic->getAuthor()->getLink(); ?>
			<div class="pull-right">
				<?php /** TODO: New Feature - LABELS
				<span class="label label-info">
				<?php echo JText::_('COM_KUNENA_TOPIC_ROW_TABLE_LABEL_QUESTION'); ?>
				</span>	*/ ?>
				<?php if ($topic->locked != 0) : ?>
					<span class="label label-important">
						<i class="glyphicon glyphicon-locked"><?php JText::_('COM_KUNENA_LOCKED'); ?></i>
					</span>
				<?php endif; ?>
			</div>
		</div>

		<div class="visible-sm">
			<?php echo JText::_('COM_KUNENA_GEN_LAST_POST')?>
			<?php echo  $topic->getLastPostTime()->toKunena('config_post_dateformat'); ?> <br>
			<?php echo JText::_('COM_KUNENA_BY') . ' ' . $this->topic->getLastPostAuthor()->getLink();?>
			<div class="pull-right">
				<?php /** TODO: New Feature - LABELS
				<span class="label label-info">
				<?php echo JText::_('COM_KUNENA_TOPIC_ROW_TABLE_LABEL_QUESTION'); ?>
				</span>	*/ ?>
				<?php if ($topic->locked != 0) : ?>
					<span class="label label-important">
						<i class="glyphicon glyphicon-locked"><?php JText::_('COM_KUNENA_LOCKED'); ?></i>
					</span>
				<?php endif; ?>
			</div>
		</div>

		<div class="pull-left">
			<?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $topicPages)->setLayout('simple'); ?>
		</div>


	</td>

	<td class="hidden-sm">
		<div class="replies"><?php echo JText::_('COM_KUNENA_GEN_REPLIES'); ?>:<span class="repliesnum"><?php echo $this->formatLargeNumber($topic->getReplies()); ?></span></div>
		<div class="views"><?php echo JText::_('COM_KUNENA_GEN_HITS');?>:<span class="viewsnum"><?php echo  $this->formatLargeNumber($topic->hits); ?></span></div>
	</td>

	<td class="col-md-2 hidden-sm">
		<div class="container-fluid">
			<div class="row">
			<?php if ($config->avataroncat) : ?>
				<div class="col-md-3">
					<?php echo $author->getLink($avatar); ?>
				</div>
			<?php endif; ?>
				<div class="col-md-9">
					<span><?php echo $this->getTopicLink ( $this->topic, 'last', JText::_('COM_KUNENA_GEN_LAST_POST'), null, 'hasTooltip'); ?>
						<?php echo ' ' . JText::_('COM_KUNENA_BY') . ' ' . $this->topic->getLastPostAuthor()->getLink();?>
					</span>
					<br>
					<span><?php echo $topic->getLastPostTime()->toKunena('config_post_dateformat'); ?></span>
				</div>
			</div>
		</div>
	</td>

	<?php if (!empty($this->checkbox)) : ?>
		<td class="center">
			<label>
				<input class="kcheck" type="checkbox" name="topics[<?php echo $topic->displayField('id'); ?>]" value="1" />
			</label>
		</td>
	<?php endif; ?>

	<?php
	if (!empty($this->position))
		echo $this->subLayout('Widget/Module')
			->set('position', $this->position)
			->set('cols', $cols)
			->setLayout('table_row');
	?>
</tr>
