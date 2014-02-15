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
$userTopic = $topic->getUserTopic();
$topicPages = $topic->getPagination(null, KunenaConfig::getInstance()->messages_per_page, 3);
$avatar = $topic->getAuthor()->getAvatarImage('img-rounded', 48);

$cols = empty($this->checkbox) ? 5 : 6;

if (!empty($this->spacing)) : ?>
<tr>
	<td colspan="<?php echo $cols; ?>">&nbsp;</td>
</tr>
<?php endif; ?>

<tr>
	<td class="hidden-phone span1 center">
		<?php echo $this->getTopicLink($topic, 'unread', $topic->getIcon()); ?>
	</td>
	<td class="span6">
		<div>
			<?php echo $this->getTopicLink($topic, null, null, null, 'hasTooltip'); ?>

			<?php if ($userTopic->favorite) : ?>
				<i class="icon-star hasTooltip"><?php JText::_('COM_KUNENA_FAVORITE'); ?></i>
			<?php endif; ?>

			<?php if ($userTopic->posts) : ?>
				<i class="icon-flag hasTooltip"><?php JText::_('COM_KUNENA_MYPOSTS'); ?></i>
			<?php endif; ?>
			
			<?php if ($this->topic->attachments) : ?>
				<i class="icon-flag-2 hasTooltip"><?php JText::_('COM_KUNENA_ATTACH'); ?></i>
			<?php endif; ?>
			
			<?php if ($this->topic->poll_id) : ?>
				<i class="icon-bars hasTooltip"><?php JText::_('COM_KUNENA_ADMIN_POLLS'); ?></i>
			<?php endif; ?>

			<?php
			if ($topic->unread) {
				echo $this->getTopicLink($topic, 'unread',
					'<sup class="knewchar" dir="ltr">(' . (int) $topic->unread . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>');
			}
			?>
		</div>

		<div class="pull-right">
			<?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $topicPages)->setLayout('simple'); ?>
		</div>

		<div>
			<?php /** TODO: New Feature - LABELS
			<span class="label label-info">
				<?php echo JText::_('COM_KUNENA_TOPIC_ROW_TABLE_LABEL_QUESTION'); ?>
			</span>	*/ ?>
			<?php if ($topic->locked != 0) : ?>
			<span class="label label-important">
				<i class="icon-locked"><?php JText::_('COM_KUNENA_LOCKED'); ?></i>
			</span>
			<?php endif; ?>
			<span class="ktopic-category"> <?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink ( $this->topic->getCategory() ) ) ?></span>
		</div>
	</td>
	
	<td class="span1 center hidden-phone">
		<?php if ($avatar) : ?>
		<span>
			<?php echo $topic->getAuthor()->getLink(); ?>
		</span>
		<?php endif; ?>
	</td>
	
	<td class="span1 hidden-phone center">
			<?php echo  $this->formatLargeNumber($topic->hits); ?>
		</td>
	<td class="span1 hidden-phone center">
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
