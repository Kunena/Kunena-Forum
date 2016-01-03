<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Message
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/** @var KunenaLayout $this */
/** @var KunenaForumMessage $message */
$message = $this->message;
$author = $message->getAuthor();
$topic = $message->getTopic();
$category = $message->getCategory();
$isReply = $message->id != $topic->first_post_id;
$category = $message->getCategory();
$avatar = $topic->getLastPostAuthor()->getAvatarImage('img-rounded', 'thumb');
$config = KunenaFactory::getConfig();
$cols = empty($this->checkbox) ? 5 : 6;
$txt   = '';

if ($topic->ordering)
{
	if ($topic->getCategory()->class_sfx)
	{
		$txt .= '';
	}
	else
	{
		$txt .= '-stickymsg';
	}
}

if ($topic->hold == 1 || $message->hold == 1)
{
	$txt .= ' '. 'unapproved';
}
else
{
	if ($topic->hold)
	{
		$txt .= ' '  . 'deleted';
	}
}

if ($topic->moved_id > 0)
{
	$txt .= ' ' . 'moved';
}
?>

<tr class="category<?php echo $this->escape($category->class_sfx).$txt; ?>">
	<?php if ($topic->unread) : ?>
	<td class="hidden-phone center topic-item-unread">
		<?php echo $this->getTopicLink($topic, 'unread', $topic->getIcon($topic->getCategory()->iconset)); ?>
	<?php else :  ?>
	<td class="hidden-phone span1 center">
		<?php echo $this->getTopicLink($topic, null, $topic->getIcon($topic->getCategory()->iconset)); ?>
	<?php endif;?>
	</td>
	<td class="span<?php echo $cols?>">
		<div>
			<?php
			if ($topic->unread) {
				echo $this->getTopicLink($topic, 'unread', ($isReply ? JText::_('COM_KUNENA_RE').' ' : '') . $message->displayField('subject') . '<sup class="knewchar" dir="ltr">(' . (int) $topic->unread
					. ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>');
			}
			else
			{
				echo $this->getTopicLink(
					$topic, $message, ($isReply ? JText::_('COM_KUNENA_RE').' ' : '') . $message->displayField('subject')
				);
			}

			if ($topic->getUserTopic()->favorite) {
				echo $this->getIcon ('kfavoritestar', JText::_('COM_KUNENA_FAVORITE'));
			}

			if ($topic->locked != 0) {
				echo $this->getIcon ('ktopiclocked', JText::_('COM_KUNENA_LOCKED_TOPIC'));
			}
			?>
		</div>
		<div>
			<?php echo $topic->getAuthor()->getLink(null, null, 'nofollow', '', null, $category->id); ?>,
			<?php echo $topic->getFirstPostTime()->toKunena('config_post_dateformat'); ?> <br />
			<?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink($topic->getCategory())); ?>
		</div>
	</td>
	<td class="span2 hidden-phone">
		<div class="replies"><?php echo JText::_('COM_KUNENA_GEN_REPLIES'); ?>:<span class="repliesnum"><?php echo $this->formatLargeNumber($topic->getReplies()); ?></span></div>
		<div class="views"><?php echo JText::_('COM_KUNENA_GEN_HITS');?>:<span class="viewsnum"><?php echo  $this->formatLargeNumber($topic->hits); ?></span></div>
	</td>
	<td class="span2">
		<div class="container-fluid">
			<div class="row-fluid">
			<?php if ($config->avataroncat) : ?>
				<div class="span3">
					<?php echo $avatar; ?>
				</div>
			<?php endif; ?>
				<div class="span9">
					<span>
						<?php echo $this->getTopicLink ( $topic, 'last', JText::_('COM_KUNENA_GEN_LAST_POST'), null, 'hasTooltip'); ?>
						<?php echo ' ' . JText::_('COM_KUNENA_BY') . ' ' . $topic->getLastPostAuthor()->getLink(null, null, 'nofollow', '', null, $category->id);?>
					</span>
					<br />
					<span><?php echo $topic->getLastPostTime()->toKunena('config_post_dateformat'); ?></span>
				</div>
			</div>
		</div>
	</td>

	<?php if (!empty($this->checkbox)) : ?>
		<td class="span1 center">
			<input class ="kcheck" type="checkbox" name="posts[<?php echo $message->id?>]" value="1" />
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
