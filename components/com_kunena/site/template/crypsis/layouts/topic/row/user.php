<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/** @var KunenaLayout $this */
/** @var KunenaForumTopic $topic */
$topic = $this->topic;
$topicPages = $topic->getPagination(null, KunenaConfig::getInstance()->messages_per_page, 3);
$userTopic = $topic->getUserTopic();
$avatar = $topic->getAuthor()->getAvatarImage('img-rounded', 48);
$cols = empty($this->checkbox) ? 5 : 6;

if (!empty($this->spacing)) : ?>
<tr>
	<td colspan="<?php echo $cols; ?>">&nbsp;</td>
</tr>
<?php endif; ?>

<tr>

	<td class="span1 center hidden-phone">
		<?php echo $this->getTopicLink($topic, 'unread', $topic->getIcon()); ?>
	</td>
	<td class="span6">
		
		<div>
			<?php echo $this->getTopicLink($topic); ?>

			<?php
			if ($topic->getUserTopic()->favorite) {
				echo $this->getIcon('kfavoritestar', JText::_('COM_KUNENA_FAVORITE'));
			}

			if ($topic->unread) {
				echo $this->getTopicLink($topic, 'unread', '<sup dir="ltr" class="knewchar">('
					. (int) $topic->unread . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>');
			}

			if ($topic->locked != 0) {
				echo $this->getIcon('ktopiclocked', JText::_('COM_KUNENA_LOCKED_TOPIC'));
			}
			?>
		<?php if ($topic->attachments) echo $this->getIcon('icon-flag-2', JText::_('COM_KUNENA_ATTACH')); ?>
		<?php if ($topic->poll_id) echo $this->getIcon('icon-bars', JText::_('COM_KUNENA_ADMIN_POLLS')); ?>
		</div>
		<div>
			<?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink($topic->getCategory())); ?>
		</div>

		<div class="pull-right">
			<?php echo $this->subLayout('Pagination/List')->set('pagination', $topicPages)->setLayout('simple'); ?>
		</div>
	</td>
	
	<td class="span1 center hidden-phone">
		<?php echo $topic->getAuthor()->getLink(); ?>
	</td>
	
	<td class="span1 center hidden-phone">
		<?php echo $this->formatLargeNumber($topic->hits); ?>
	</td>
	<td class="span1 center hidden-phone">
		<?php echo $this->formatLargeNumber($topic->getReplies()); ?>
	</td>
	
	<td class="span3">
		<?php if ($avatar) : ?>
						<div class="pull-left hidden-phone" style="padding-left:3%;">
							<?php echo $avatar; ?>
						</div>
						<div class="last-post-message">
						<?php else :	?>
						<div>
					<?php endif; ?>
			<div class="ktopic-latest-post">
			<?php echo $this->getTopicLink ( $this->topic, JText::_('COM_KUNENA_GEN_LAST_POST'), 'Post'); ?>

			<?php echo ' ' . JText::_('COM_KUNENA_BY') . ' ' . $this->topic->getLastPostAuthor()->getLink();?>
			<br>
			<?php echo $topic->getLastPostTime()->toKunena('config_post_dateformat'); ?>
			</div>
		</div>
		</div>
	</td>

	<?php if (!empty($this->checkbox)) : ?>
	<td class="span1 center">
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