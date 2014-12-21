<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Message
 *
 * @copyright   (C) 2008 - 2014 Kunena Team. All rights reserved.
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
$avatar = $topic->getLastPostAuthor()->getAvatarImage('img-thumbnail', 48);
$config = KunenaFactory::getConfig();
$cols = empty($this->checkbox) ? 4 : 5;
?>
<tr class="category<?php echo $this->escape($category->class_sfx); ?>">
	<td class="span1 hidden-phone center">
		<?php echo $this->getTopicLink($topic, 'unread', $topic->getIcon()); ?>
	</td>
	<td class="span5">
		<?php
		// FIXME:
		/*if ($message->attachments) {
			echo $this->getIcon('ktopicattach', JText::_('COM_KUNENA_ATTACH'));
		}*/
		?>
		<div>
			<?php echo $this->getTopicLink(
				$topic, $message, ($isReply ? JText::_('COM_KUNENA_RE').' ' : '') . $message->displayField('subject')
			); ?>
			<?php

			if ($topic->getUserTopic()->favorite) {
				echo $this->getIcon ('kfavoritestar', JText::_('COM_KUNENA_FAVORITE'));
			}

			if ($topic->unread) {
				echo $this->getTopicLink($topic, 'unread', '<sup class="knewchar" dir="ltr">(' . (int) $topic->unread
					. ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>');
			}

			if ($topic->locked != 0) {
				echo $this->getIcon ('ktopiclocked', JText::_('COM_KUNENA_LOCKED_TOPIC'));
			}
			?>
		</div>
		<div>
			<?php echo $topic->getAuthor()->getLink(); ?>,
			<?php echo $topic->getFirstPostTime()->toKunena('config_post_dateformat'); ?> <br />
			<?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink($topic->getCategory())); ?>
		</div>
	</td>
	<td class="span2 hidden-phone">
		<table cellpadding="0" cellspacing="0">
			<tbody>
			<tr>
				<td style="border: 0 none;" class="labels">
					<div class="replies"><strong><?php echo JText::_('COM_KUNENA_GEN_REPLIES'); ?>:</strong></div>
					<div class="views"><?php echo JText::_('COM_KUNENA_GEN_HITS');?>:</div>
				</td>
				<td style="width:100%;text-align:right;border: 0 none;" class="numbers">
					<div class="repliesnum"><strong><?php echo $this->formatLargeNumber($topic->getReplies()); ?></strong></div>
					<div class="viewsnum"><?php echo  $this->formatLargeNumber($topic->hits); ?></div>

				</td>
			</tr>
			</tbody>
		</table>
	</td>
	<td class="span2" id="recent-topics">
		<?php if ($config->avataroncat) : ?>
			<div class="span2">
				<?php echo $avatar; ?>
			</div>
		<?php endif; ?>
		<div class="span9 last-posts">
			<?php echo $this->getTopicLink ( $topic, JText::_('COM_KUNENA_GEN_LAST_POST'), 'Last Post'); ?>
			<?php if ($message->userid) : ?>
				<span><?php echo JText::_('COM_KUNENA_BY') . ' ' . $message->getAuthor()->getLink(); ?></span>
			<?php endif; ?>
			<br />
			<?php echo $topic->getLastPostTime()->toKunena('config_post_dateformat'); ?>
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
