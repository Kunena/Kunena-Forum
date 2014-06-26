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

$config = KunenaFactory::getConfig();
$cols = empty($this->checkbox) ? 4 : 5;
?>
<tr class="category<?php echo $this->escape($category->class_sfx); ?>">
	<td class="span2 hidden-phone center">
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
			<?php echo JText::_('COM_KUNENA_GEN_MESSAGE'); ?>:
			<?php echo $this->getTopicLink(
				$topic, $message, ($isReply ? JText::_('COM_KUNENA_RE').' ' : '') . $message->displayField('subject')
			); ?>
		</div>
		<div>
			<?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?>:
			<?php
			echo $this->getTopicLink($topic);

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
	</td>
	<td class="span3">
		<div>
			<?php echo JText::_('COM_KUNENA_GEN_AUTHOR'); ?>: <?php echo $author->getLink(); ?>
		</div>
		<div>
			<?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink($topic->getCategory())); ?>
		</div>
	</td>
	<td class="span3">
		<div>
			<span title="<?php echo KunenaDate::getInstance($message->time)->toKunena('config_post_dateformat_hover'); ?>">
				<?php echo JText::_('COM_KUNENA_POSTED_AT')
					. ' ' . KunenaDate::getInstance($message->time)->toKunena('config_post_dateformat'); ?>
			</span>

			<?php if ($message->userid) : ?>
			<span><?php echo JText::_('COM_KUNENA_BY') . ' ' . $message->getAuthor()->getLink(); ?></span>
			<?php endif; ?>

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
