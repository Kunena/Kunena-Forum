<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

$topic      = $this->topic;
$userTopic  = $topic->getUserTopic();
$template   = KunenaFactory::getTemplate();
$topicPages = $topic->getPagination(null, KunenaConfig::getInstance()->messages_per_page, 3);
$author     = $topic->getLastPostAuthor();
$avatar     = $author->getAvatarImage($template->params->get('avatarType'), 'thumb');
$category   = $this->topic->getCategory();
$cols       = empty($this->checkbox) ? 5 : 6;
$config     = KunenaConfig::getInstance();
$txt        = $this->topic->getActions();

if (!empty($this->spacing)) : ?>
	<tr>
		<td colspan="<?php echo $cols; ?>">&nbsp;</td>
	</tr>
<?php endif; ?>

<tr class="category<?php echo $this->escape($category->class_sfx) . $txt; ?>">
	<?php if ($topic->unread) : ?>
<td class="hidden-phone span1 center topic-item-unread">
<?php echo $this->getTopicLink($topic, 'unread', KunenaTemplate::getInstance()->getTopicIcon($topic), '', null, $category, true, true); ?>
<?php else : ?>
	<td class="hidden-phone span1 center">
		<?php echo $this->getTopicLink($topic, null, KunenaTemplate::getInstance()->getTopicIcon($topic), '', null, $category, true, false); ?>
		<?php endif; ?>
	</td>
	<td class="span<?php echo $cols ?>">
		<div class="krow">
			<?php
			if ($template->params->get('labels') != 0)
			{
				echo $this->subLayout('Widget/Label')->set('topic', $this->topic)->setLayout('default');
			}

			if ($topic->unread)
			{
				echo $this->getTopicLink($topic, 'unread', $this->escape($topic->subject) . '<sup class="knewchar" dir="ltr">(' . (int) $topic->unread .
					' ' . Text::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>', null, KunenaTemplate::getInstance()->tooltips() . ' topictitle', $category, true, true);
			}
			else
			{
				echo $this->getTopicLink($topic, null, null, null, KunenaTemplate::getInstance()->tooltips() . ' topictitle', $category, true, false);
			}
			?>
			<?php echo $this->subLayout('Widget/Rating')->set('config', $config)->set('category', $category)->set('topic', $this->topic)->set('reviewCount', $this->topic->getReviewCount())->setLayout('default'); ?>
		</div>
		<div class="pull-right">
			<?php if ($userTopic->favorite) : ?>
				<span <?php echo KunenaTemplate::getInstance()->tooltips(true); ?>
						title="<?php echo Text::_('COM_KUNENA_FAVORITE'); ?>"><?php echo KunenaIcons::star(); ?></span>
			<?php endif; ?>

			<?php if ($userTopic->posts) : ?>
				<span <?php echo KunenaTemplate::getInstance()->tooltips(true); ?>
						title="<?php echo Text::_('COM_KUNENA_MYPOSTS'); ?>"><?php echo KunenaIcons::flag(); ?></span>
			<?php endif; ?>

			<?php if ($this->topic->attachments) : ?>
				<span <?php echo KunenaTemplate::getInstance()->tooltips(true); ?>
						title="<?php echo Text::_('COM_KUNENA_ATTACH'); ?>"><?php echo KunenaIcons::attach(); ?></span>
			<?php endif; ?>

			<?php if ($this->topic->poll_id && $category->allow_polls) : ?>
				<span <?php echo KunenaTemplate::getInstance()->tooltips(true); ?>
						title="<?php echo Text::_('COM_KUNENA_ADMIN_POLLS'); ?>"><?php echo KunenaIcons::poll(); ?></span>
			<?php endif; ?>
		</div>

		<div class="started">
			<span class="ktopic-category"> <?php echo Text::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink($this->topic->getCategory(), null, $this->topic->getCategory()->description, KunenaTemplate::getInstance()->tooltips())) ?></span>
			<br/>
			<?php echo Text::_('COM_KUNENA_TOPIC_STARTED_ON') ?>
			<?php echo $topic->getFirstPostTime()->toKunena('config_post_dateformat'); ?>,
			<?php echo Text::_('COM_KUNENA_BY') ?>
			<?php echo $topic->getAuthor()->getLink(null, Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $this->topic->getFirstPostAuthor()->getName()), '', '', KunenaTemplate::getInstance()->tooltips(), $category->id); ?>
			<div class="pull-right hidden-phone">
				<?php /** TODO: New Feature - LABELS
				 * <span class="label label-info">
				 * <?php echo Text::_('COM_KUNENA_TOPIC_ROW_TABLE_LABEL_QUESTION'); ?>
				 * </span>    */ ?>
				<?php if ($topic->locked != 0) : ?>
					<span class="label label-important">
						<?php echo KunenaIcons::lock(); ?><?php echo Text::_('COM_KUNENA_LOCKED'); ?>
					</span>
				<?php endif; ?>
			</div>
		</div>

		<div id="klastpostphone" class="visible-phone">
			<?php echo $this->getTopicLink($this->topic, 'last', Text::_('COM_KUNENA_GEN_LAST_POST'), null, null, $category, false, true); ?>
			<?php echo $topic->getLastPostTime()->toKunena('config_post_dateformat'); ?> <br>
			<?php echo Text::_('COM_KUNENA_BY') . ' ' . $this->topic->getLastPostAuthor()->getLink(null, null, '', '', null, $category->id); ?>
			<div class="pull-right">
				<?php /** TODO: New Feature - LABELS
				 * <span class="label label-info">
				 * <?php echo Text::_('COM_KUNENA_TOPIC_ROW_TABLE_LABEL_QUESTION'); ?>
				 * </span>    */ ?>
				<?php if ($topic->locked != 0) : ?>
					<span class="label label-important">
						<?php echo KunenaIcons::lock(); ?><?php echo Text::_('COM_KUNENA_LOCKED'); ?>
					</span>
				<?php endif; ?>
			</div>
		</div>

		<div class="pull-left">
			<?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $topicPages)->setLayout('simple'); ?>
		</div>


	</td>

	<td class="hidden-phone">
		<div class="replies"><?php echo Text::_('COM_KUNENA_GEN_REPLIES'); ?>:<span
					class="repliesnum"><?php echo $this->formatLargeNumber($topic->getReplies()); ?></span></div>
		<div class="views"><?php echo Text::_('COM_KUNENA_GEN_HITS'); ?>:<span
					class="viewsnum"><?php echo $this->formatLargeNumber($topic->hits); ?></span></div>
	</td>

	<td class="span2 hidden-phone">
		<div class="container-fluid">
			<div class="row-fluid">
				<?php if ($config->avataroncat) : ?>
				<div class="span3">
					<?php echo $author->getLink($avatar, Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $this->topic->getLastPostAuthor()->getName()), '', '', KunenaTemplate::getInstance()->tooltips(), $category->id, $config->avataredit); ?>
				</div>
				<div class="span9">
					<?php else : ?>
					<div class="span12">
						<?php endif; ?>
						<span class="lastpostlink"><?php echo $this->getTopicLink($this->topic, 'last', Text::_('COM_KUNENA_GEN_LAST_POST'), null, KunenaTemplate::getInstance()->tooltips(), $category, false, true); ?>
							<?php echo ' ' . Text::_('COM_KUNENA_BY') . ' ' . $this->topic->getLastPostAuthor()->getLink(null, Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $this->topic->getLastPostAuthor()->getName()), '', '', KunenaTemplate::getInstance()->tooltips(), $category->id); ?>
					</span>
						<br>
						<span class="datepost"><?php echo $topic->getLastPostTime()->toKunena('config_post_dateformat'); ?></span>
					</div>
				</div>
			</div>
	</td>

	<?php if (!empty($this->checkbox)) : ?>
		<td class="center">
			<label>
				<input class="kcheck" type="checkbox" name="topics[<?php echo $topic->displayField('id'); ?>]"
				       value="1"/>
			</label>
		</td>
	<?php endif; ?>
</tr>
