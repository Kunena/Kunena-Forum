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
$this->ktemplate = KunenaFactory::getTemplate();
$avatar = $author->getAvatarImage($this->ktemplate->params->get('avatarType'), 'post');
$category = $this->topic->getCategory();
$cols = empty($this->checkbox) ? 4 : 5;
$category = $this->topic->getCategory();
$config = KunenaConfig::getInstance();
$txt   = '';

if ($this->topic->ordering)
{
	$txt = $this->topic->getCategory()->class_sfx ? $txt . '' : $txt . '-stickymsg';
}

if ($this->topic->hold == 1)
{
	$txt .= ' '. 'unapproved';
}
else
{
	if ($this->topic->hold)
	{
		$txt .= ' '  . 'deleted';
	}
}
if ($this->topic->moved_id > 0)
{
	$txt .= ' ' . 'moved';
}


if (!empty($this->spacing)) : ?>
<tr>
	<td colspan="<?php echo $cols; ?>">&nbsp;</td>
</tr>
<?php endif; ?>

<tr class="category<?php echo $this->escape($category->class_sfx).$txt;?>">
	<?php if ($topic->unread) : ?>
	<td class="hidden-xs center col-md-1 topic-item-unread">
		<?php echo $this->getTopicLink($topic, 'unread', $topic->getIcon($topic->getCategory()->iconset)); ?>
	<?php else :  ?>
	<td class="col-md-1 hidden-xs center">
		<?php echo $this->getTopicLink($topic, null, $topic->getIcon($topic->getCategory()->iconset)); ?>
	<?php endif;?>
	<td class="col-md-<?php echo $cols?>">
		<div>
			<?php
			if ($topic->unread)
			{
				echo $this->getTopicLink($topic, 'unread',
					$topic->subject . '<sup class="knewchar" dir="ltr">(' . (int) $topic->unread . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>');
			}
			else
			{
				echo $this->getTopicLink($topic, null, null, null, 'hasTooltip topictitle');
			}
			$labels = $this->ktemplate->params->get('labels');
			if ($labels) {
				if ($this->topic->locked != 0) { ?>
					<span class="label label-default">CLOSED</span>
				<?php }

				if ($this->topic->ordering != 0)  { ?>
					<span class="label label-info"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only"></span>STICKY</span>
				<?php }

				if ($this->topic->icon_id == 1)  { ?>
					<span class="label label-danger"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only"></span>IMPORTANT</span>
				<?php }

				if ($this->topic->icon_id == 2) { ?>
					<span class="label label-primary"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
					<span class="sr-only"></span>QUESTION</span>
				<?php }

				$str_counts = substr_count($this->topic->subject, 'solved');
				if ($this->topic->icon_id == 8 || $str_counts) { ?>
				<a href="#"><span class="label label-success"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
				   <span class="sr-only"></span>SOLVED</span></a>
			   <?php }

				if ($this->topic->icon_id == 10) { ?>
					<span class="label label-danger"><span class="glyphicon glyphicon-bell" aria-hidden="true"></span>
					<span class="sr-only"></span>BUG</span>
				<?php }
			}?>
		</div>
		<div class="pull-right">
			<?php if ($userTopic->favorite) : ?>
				<i class="glyphicon glyphicon-star hasTooltip" title="<?php echo JText::_('COM_KUNENA_FAVORITE'); ?>"></i>
			<?php endif; ?>

			<?php if ($userTopic->posts) : ?>
				<i class="glyphicon glyphicon-flag hasTooltip" title="<?php echo JText::_('COM_KUNENA_MYPOSTS'); ?>"></i>
			<?php endif; ?>

			<?php if ($this->topic->attachments) : ?>
				<i class="glyphicon glyphicon-paperclip hasTooltip" title="<?php echo JText::_('COM_KUNENA_ATTACH'); ?>"></i>
			<?php endif; ?>

			<?php if ($this->topic->poll_id) : ?>
				<i class="glyphicon glyphicon-stats hasTooltip" title="<?php echo JText::_('COM_KUNENA_ADMIN_POLLS'); ?>"></i>
			<?php endif; ?>
		</div>

		<div class="hidden-xs">
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

	<td class="col-md-2 hidden-xs">
		<div class="replies"><?php echo JText::_('COM_KUNENA_GEN_REPLIES'); ?>:<span class="repliesnum"><?php echo $this->formatLargeNumber($topic->getReplies()); ?></span></div>
		<div class="views"><?php echo JText::_('COM_KUNENA_GEN_HITS');?>:<span class="viewsnum"><?php echo  $this->formatLargeNumber($topic->hits); ?></span></div>
	</td>

	<td class="col-md-3 hidden-xs">
		<div class="container-fluid">
			<?php if ($config->avataroncat) : ?>
				<div class="col-md-3">
					<?php echo $author->getLink($avatar); ?>
				</div>
			<?php endif; ?>
				<div class="col-md-9">
					<span><?php echo $this->getTopicLink ( $this->topic, 'last', JText::_('COM_KUNENA_GEN_LAST_POST'), null, 'hasTooltip'); ?>
						<?php echo ' ' . JText::_('COM_KUNENA_BY') . ' ' . $this->topic->getLastPostAuthor()->getLink(null, null, 'nofollow', '', null, $category->id);?>
					</span>
					<br>
					<span><?php echo $topic->getLastPostTime()->toKunena('config_post_dateformat'); ?></span>
				</div>
		</div>
	</td>

	<?php if (!empty($this->checkbox)) : ?>
		<td class="col-md-1 center">
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
