<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/*
  @var KunenaLayout $this */
// @var KunenaForumTopic $topic

$topic = $this->topic;
$topicPages = $topic->getPagination(null, KunenaConfig::getInstance()->messages_per_page, 3);
$userTopic = $topic->getUserTopic();
$author = $topic->getLastPostAuthor();
$this->ktemplate = KunenaFactory::getTemplate();
$avatar = $author->getAvatarImage($this->ktemplate->params->get('avatarType'), 'post');
$cols = empty($this->checkbox) ? 5 : 6;
$category = $this->topic->getCategory();
$config = KunenaConfig::getInstance();
$txt   = '';

if ($this->topic->ordering)
{
	$txt .= '-stickymsg';
}

if ($this->topic->getCategory()->class_sfx)
{
	if ($this->topic->ordering)
	{
		$txt .= '-stickymsg';
	}

	$txt .= $this->escape($this->topic->getCategory()->class_sfx);
}

if ($this->topic->hold == 1)
{
	$txt .= ' ' . 'unapproved';
}
else
{
	if ($this->topic->hold)
	{
		$txt .= ' ' . 'deleted';
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

<tr class="category<?php echo $this->escape($category->class_sfx) . $txt;?>">
	<?php if ($topic->unread) : ?>
	<td class="hidden-xs col-md-1 center topic-item-unread">
		<?php echo $this->getTopicLink($topic, 'unread', $topic->getIcon($topic->getCategory()->iconset), $this->escape($topic->subject), 'hasTooltip', $category, true, true); ?>
	<?php else :  ?>
	<td class="col-md-1 hidden-xs center">
		<?php echo $this->getTopicLink($topic, null, $topic->getIcon($topic->getCategory()->iconset), $this->escape($topic->subject), 'hasTooltip', $category, true, false); ?>
	<?php endif;?>
	<td class="col-md-<?php echo $cols; ?>">
		<div>
			<?php
			if ($this->ktemplate->params->get('labels') != 0)
			{
				echo $this->subLayout('Widget/Label')->set('topic', $this->topic)->setLayout('default');
			}

			if ($topic->unread)
			{
				echo $this->getTopicLink($topic, 'unread', $this->escape($topic->subject) . '<sup class="knewchar" dir="ltr">(' . (int) $topic->unread . ' ' .
					JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>', null, 'hasTooltip', $category, true, true);
			}
			else
			{
				echo $this->getTopicLink($topic, null, null, null, 'hasTooltip topictitle', $category, true, false);
			}
			echo $this->subLayout('Widget/Rating')->set('config', $config)->set('category', $category)->set('topic', $this->topic)->setLayout('default'); ?>
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

			<?php if ($this->topic->poll_id && $category->allow_polls) : ?>
				<i class="glyphicon glyphicon-stats hasTooltip" title="<?php echo JText::_('COM_KUNENA_ADMIN_POLLS'); ?>"></i>
			<?php endif; ?>
		</div>

		<div class="hidden-xs">
			<?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink($this->topic->getCategory())) ?>
			<br />
			<?php echo $topic->getFirstPostTime()->toKunena('config_post_dateformat'); ?>
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
			<span class="ktopic-category"> <?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink($this->topic->getCategory())) ?></span>
			<br />
			<?php echo JText::_('COM_KUNENA_GEN_LAST_POST')?>
			<?php echo  $topic->getLastPostTime()->toKunena('config_post_dateformat'); ?> <br>
			<?php echo JText::_('COM_KUNENA_BY') . ' ' . $this->topic->getLastPostAuthor()->getLink(null, null, '', '', null, $category->id);?>
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

	<td class="col-md-2 hidden-xs">
		<div class="container-fluid">
				<?php if ($config->avataroncat) : ?>
					<div class="col-md-3">
						<?php echo $author->getLink($avatar, null, '', '', null, $category->id); ?>
					</div>
					<div class="col-md-9">
				<?php else : ?>
					<div class="col-md-12">
				<?php endif; ?>
					<span><?php echo $this->getTopicLink($this->topic, 'last', JText::_('COM_KUNENA_GEN_LAST_POST'), null, 'hasTooltip', $category, false, true); ?>
						<?php echo ' ' . JText::_('COM_KUNENA_BY') . ' ' . $this->topic->getLastPostAuthor()->getLink(null, null, '', '', null, $category->id);?>
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
</tr>
