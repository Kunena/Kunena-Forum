<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/*
  @var KunenaLayout $this */
// @var KunenaForumTopic $topic

$topic = $this->topic;
$category = $topic->getCategory();
$userTopic = $topic->getUserTopic();
$topicPages = $topic->getPagination(null, KunenaConfig::getInstance()->messages_per_page, 3);
$author = $topic->getLastPostAuthor();
$this->ktemplate = KunenaFactory::getTemplate();
$avatar = $author->getAvatarImage($this->ktemplate->params->get('avatarType'), 'post');
$config = KunenaConfig::getInstance();
$cols = empty($this->checkbox) ? 5 : 6;
$txt   = '';

if ($this->topic->ordering)
{
	$txt = $this->topic->getCategory()->class_sfx ? $txt . '' : $txt . '-stickymsg';
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
	<tr class="kcontenttablespacer">
		<td colspan="<?php echo $cols; ?>">&nbsp;</td>
	</tr>
<?php endif; ?>

<tr class="category<?php echo $this->escape($category->class_sfx) . $txt;?>">
	<?php if ($topic->unread) : ?>
	<td class="hidden-xs col-md-1 center topic-item-unread">
		<?php echo $this->getTopicLink($topic, 'unread', $topic->getIcon($topic->getCategory()->iconset), '', null, $category, true, true); ?>
	<?php else :  ?>
	<td class="col-md-1 hidden-xs center">
		<?php echo $this->getTopicLink($topic, null, $topic->getIcon($topic->getCategory()->iconset), '', null, $category, true, false); ?>
	<?php endif;?>
	<td class="col-md-<?php echo $cols?>">
		<div>
			<?php
			if ($this->ktemplate->params->get('labels') != 0)
			{
				echo $this->subLayout('Widget/Label')->set('topic', $this->topic)->setLayout('default');
			}

			if ($topic->unread)
			{
				echo $this->getTopicLink($topic, 'unread', $this->escape($topic->subject) .
					'<sup class="knewchar" dir="ltr">(' . (int) $topic->unread . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>', null, KunenaTemplate::getInstance()->tooltips() . ' topictitle', $category, true, true);
			}
			else
			{
				echo $this->getTopicLink($topic, null, null, null, KunenaTemplate::getInstance()->tooltips() . ' topictitle', $category, true, false);
			}

			echo $this->subLayout('Widget/Rating')->set('config', $config)->set('category', $category)->set('topic', $this->topic)->setLayout('default'); ?>
		</div>
		<div class="pull-right">
			<?php if ($userTopic->favorite) : ?>
			<span <?php echo KunenaTemplate::getInstance()->tooltips(true);?> title="<?php echo JText::_('COM_KUNENA_FAVORITE'); ?>"><?php echo KunenaIcons::star(); ?></span>
			<?php endif; ?>

			<?php if ($userTopic->posts) : ?>
				<span <?php echo KunenaTemplate::getInstance()->tooltips(true);?> title="<?php echo JText::_('COM_KUNENA_MYPOSTS'); ?>"><?php echo KunenaIcons::flag(); ?></span>
			<?php endif; ?>

			<?php if ($this->topic->attachments) : ?>
				<span <?php echo KunenaTemplate::getInstance()->tooltips(true);?> title="<?php echo JText::_('COM_KUNENA_ATTACH'); ?>"><?php echo KunenaIcons::attach(); ?></span>
			<?php endif; ?>

			<?php if ($this->topic->poll_id && $category->allow_polls) : ?>
				<span <?php echo KunenaTemplate::getInstance()->tooltips(true);?> title="<?php echo JText::_('COM_KUNENA_ADMIN_POLLS'); ?>"><?php echo KunenaIcons::poll(); ?></span>
			<?php endif; ?>
		</div>

		<div class="started">
			<?php echo JText::_('COM_KUNENA_TOPIC_STARTED_ON')?>
			<?php if ($config->post_dateformat != 'none') : ?>
				<?php echo $topic->getFirstPostTime()->toKunena('config_post_dateformat'); ?>,
			<?php endif; ?>
			<?php echo JText::_('COM_KUNENA_BY') ?>
			<?php echo $topic->getAuthor()->getLink(null, JText::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $this->topic->getFirstPostAuthor()->getName()), '', '', KunenaTemplate::getInstance()->tooltips(), $category->id); ?>
			<div class="pull-right">
				<?php /** TODO: New Feature - LABELS
				<span class="label label-info">
				<?php echo JText::_('COM_KUNENA_TOPIC_ROW_TABLE_LABEL_QUESTION'); ?>
				</span>	*/ ?>
				<?php if ($topic->locked != 0) : ?>
					<span class="label label-warning">
						<?php echo KunenaIcons::lock(); ?> <?php echo JText::_('COM_KUNENA_LOCKED'); ?>
					</span>
				<?php endif; ?>
			</div>
		</div>

		<div id="klastpostphone" class="visible-xs">
			<?php echo $this->getTopicLink($this->topic, 'last', JText::_('COM_KUNENA_GEN_LAST_POST'), null, null, $category, false, true); ?>
			<?php if ($config->post_dateformat != 'none') : ?>
				<?php echo  $topic->getLastPostTime()->toKunena('config_post_dateformat'); ?> <br>
			<?php endif; ?>
			<?php echo JText::_('COM_KUNENA_BY') . ' ' . $this->topic->getLastPostAuthor()->getLink(null, null, '', '', null, $category->id);?>
			<div class="pull-right">
				<?php /** TODO: New Feature - LABELS
				<span class="label label-info">
				<?php echo JText::_('COM_KUNENA_TOPIC_ROW_TABLE_LABEL_QUESTION'); ?>
				</span>	*/ ?>
				<?php if ($topic->locked != 0) : ?>
					<span class="label label-warning">
						<?php echo KunenaIcons::lock(); ?> <?php echo JText::_('COM_KUNENA_LOCKED'); ?>
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
						<?php echo $author->getLink($avatar, JText::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $this->topic->getLastPostAuthor()->getName()), '', '', KunenaTemplate::getInstance()->tooltips(), $category->id); ?>
					</div>
					<div class="col-md-9">
				<?php else : ?>
					<div class="col-md-12">
					<?php endif; ?>
					<span class="lastpostlink"><?php echo $this->getTopicLink($this->topic, 'last', JText::_('COM_KUNENA_GEN_LAST_POST'), null, KunenaTemplate::getInstance()->tooltips(), $category, false, true); ?>
							<?php echo ' ' . JText::_('COM_KUNENA_BY') . ' ' . $this->topic->getLastPostAuthor()->getLink(null, JText::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $this->topic->getLastPostAuthor()->getName()), '', '', KunenaTemplate::getInstance()->tooltips(), $category->id);?>
					</span>
					<br>
					<span class="datepost"><?php echo $topic->getLastPostTime()->toKunena('config_post_dateformat'); ?></span>
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
