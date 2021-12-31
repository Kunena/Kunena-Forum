<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.Message
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

$this->addStyleSheet('assets/css/rating.css');

$message         = $this->message;
$author          = $message->getAuthor();
$topic           = $message->getTopic();
$category        = $message->getCategory();
$isReply         = $message->id != $topic->first_post_id;
$category        = $message->getCategory();
$this->ktemplate = KunenaFactory::getTemplate();
$config          = KunenaFactory::getConfig();
$avatar          = $config->avataroncat ? $topic->getLastPostAuthor()->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType'), 'thumb') : null;
$cols            = empty($this->checkbox) ? 5 : 6;
$txt             = $topic->getActions();

?>
<tr class="category<?php echo $this->escape($category->class_sfx) . $txt; ?>">
	<?php
	if ($topic->unread)
	:
	?>
<td class="hidden-xs col-md-1 center topic-item-unread">
<?php echo $this->getTopicLink($topic, 'unread', $topic->getIcon($topic->getCategory()->iconset), '', null, $category, true, true); ?>
<?php else

:
?>
	<td class="col-md-1 hidden-xs center">
		<?php echo $this->getTopicLink($topic, $this->message, $topic->getIcon($topic->getCategory()->iconset), '', null, $category, true, false); ?>
		<?php endif; ?>
	<td class="col-md-<?php echo $cols ?>">
		<div class="krow">
			<?php
			if ($topic->unread)
			{
				echo $this->getTopicLink($topic, 'unread', $this->escape($topic->subject) . '<sup class="knewchar" dir="ltr">(' . (int) $topic->unread .
					' ' . Text::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>', null, KunenaTemplate::getInstance()->tooltips() . ' topictitle', $category, true, true
				);
			}
			else
			{
				echo $this->getTopicLink($topic, $this->message, null, null, KunenaTemplate::getInstance()->tooltips() . ' topictitle', $category, true, false);
			}
			?>
			<?php echo $this->subLayout('Widget/Rating')->set('config', $config)->set('category', $category)->set('topic', $topic)->setLayout('default'); ?>
		</div>
		<div class="pull-right">
			<?php if ($topic->getUserTopic()->favorite)
				:
				?>
				<span <?php echo KunenaTemplate::getInstance()->tooltips(true); ?>
						title="<?php echo Text::_('COM_KUNENA_FAVORITE'); ?>"><?php echo KunenaIcons::star(); ?></span>
			<?php endif; ?>

			<?php if ($topic->getUserTopic()->posts)
				:
				?>
				<span <?php echo KunenaTemplate::getInstance()->tooltips(true); ?>
						title="<?php echo Text::_('COM_KUNENA_MYPOSTS'); ?>"><?php echo KunenaIcons::flag(); ?></span>
			<?php endif; ?>

			<?php if ($topic->attachments)
				:
				?>
				<span <?php echo KunenaTemplate::getInstance()->tooltips(true); ?>
						title="<?php echo Text::_('COM_KUNENA_ATTACH'); ?>"><?php echo KunenaIcons::attach(); ?></span>
			<?php endif; ?>

			<?php if ($topic->poll_id)
				:
				?>
				<span <?php echo KunenaTemplate::getInstance()->tooltips(true); ?>
						title="<?php echo Text::_('COM_KUNENA_ADMIN_POLLS'); ?>"><?php echo KunenaIcons::poll(); ?></span>
			<?php endif; ?>
		</div>
		<div class="hidden-xs">
			<span class="ktopic-category"><?php echo Text::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink($topic->getCategory(), null, null, KunenaTemplate::getInstance()->tooltips())); ?></span>
			<br>
			<?php echo Text::_('COM_KUNENA_TOPIC_STARTED_ON') ?>
			<?php if ($config->post_dateformat != 'none')
				:
				?>
				<?php echo $topic->getFirstPostTime()->toKunena('config_post_dateformat'); ?>
				<?php echo Text::_('COM_KUNENA_BY') ?>
				<?php echo $topic->getAuthor()->getLink(null, Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $topic->getAuthor()->getName()), '', '', KunenaTemplate::getInstance()->tooltips(), $category->id); ?>
			<?php endif; ?>
			<div class="pull-right">
				<?php /** TODO: New Feature - LABELS
				 * <span class="label label-info">
				 * <?php echo Text::_('COM_KUNENA_TOPIC_ROW_TABLE_LABEL_QUESTION'); ?>
				 * </span>    */ ?>
				<?php if ($topic->locked != 0)
					:
					?>
					<span class="label label-warning">
						<?php echo KunenaIcons::lock(); ?><?php Text::_('COM_KUNENA_LOCKED'); ?>
					</span>
				<?php endif; ?>
			</div>
		</div>
	</td>

	<td class="col-md-2 hidden-xs">
		<div class="replies"><?php echo Text::_('COM_KUNENA_GEN_REPLIES'); ?>:<span
					class="repliesnum"><?php echo $this->formatLargeNumber($topic->getReplies()); ?></span></div>
		<div class="views"><?php echo Text::_('COM_KUNENA_GEN_HITS'); ?>:<span
					class="viewsnum"><?php echo $this->formatLargeNumber($topic->hits); ?></span></div>
	</td>

	<td class="col-md-2">
		<div class="container-fluid">
			<div class="row-fluid">
				<?php if ($config->avataroncat) : ?>
					<div class="col-md-3">
						<?php echo $topic->getLastPostAuthor()->getLink($avatar, Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $topic->getLastPostAuthor()->getName()), '', '', KunenaTemplate::getInstance()->tooltips(), $category->id); ?>
					</div>
				<?php endif; ?>
				<div class="col-md-9">
					<span class="datepost"><?php echo $this->getTopicLink($topic, 'last', Text::_('COM_KUNENA_GEN_LAST_POST'), null, KunenaTemplate::getInstance()->tooltips(), $category, false, true); ?>
						<?php echo ' ' . Text::_('COM_KUNENA_BY') . ' ' . $topic->getLastPostAuthor()->getLink(null, Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $topic->getLastPostAuthor()->getName()), '', '', KunenaTemplate::getInstance()->tooltips(), $category->id); ?>
					</span>
					<br/>
					<span class="datepost"><?php echo $topic->getLastPostTime()->toKunena('config_post_dateformat'); ?></span>
				</div>
			</div>
		</div>
	</td>

	<?php if (!empty($this->checkbox))
		:
		?>
		<td class="col-md-1 center">
			<input class="kcheck" type="checkbox" name="posts[<?php echo $message->id ?>]" value="1"/>
		</td>
	<?php endif; ?>

	<?php
	if (!empty($this->position))
	{
		echo $this->subLayout('Widget/Module')
			->set('position', $this->position)
			->set('cols', $cols)
			->setLayout('table_row');
	}
	?>
</tr>
