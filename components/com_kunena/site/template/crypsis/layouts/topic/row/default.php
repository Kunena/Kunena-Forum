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
$avatar = $topic->getAuthor()->getAvatarImage();

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

			<?php
			if ($topic->unread) {
				echo $this->getTopicLink($topic, 'unread',
					'<sup dir="ltr">(' . (int) $topic->unread . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>');
			}
			?>
		</div>

		<div class="pull-right">
			<?php echo $this->subLayout('Pagination/List')->set('pagination', $topicPages); ?>
		</div>

		<div>
			<span class="label label-info">
				<?php echo JText::_('COM_KUNENA_TOPIC_ROW_TABLE_LABEL_QUESTION'); ?>
			</span>

			<?php if ($topic->locked != 0) : ?>
			<span class="label label-important">
				<i class="icon-locked"><?php JText::_('COM_KUNENA_LOCKED'); ?></i>
			</span>
			<?php endif; ?>

			in <?php echo $this->getCategoryLink($topic->getCategory(), null, null, 'hasTooltip'); ?>
		</div>
	</td>
	<td class="span1 hidden-phone">
		<span>
			<?php echo JText::_('COM_KUNENA_GEN_HITS') . ':' . $this->formatLargeNumber($topic->hits); ?>
		</span>
		<span>
			<?php echo JText::_('COM_KUNENA_GEN_REPLIES') . ':' . $this->formatLargeNumber($topic->getReplies()); ?>
		</span>
	</td>
	<td class="span1 center">

		<?php if ($avatar) : ?>
		<span class="hidden-phone">
			<?php echo $topic->getLastPostAuthor()->getLink($avatar); ?>
		</span>
		<?php endif; ?>

	</td>
	<td class="span2">
		<span class="hasTooltip" title="<?php echo $topic->getLastPostAuthor()->getName(); ?>">
			<?php echo $topic->getLastPostAuthor()->getLink(); ?>
		</span>
		<br />
		<span class="hasTooltip" title="<?php echo $topic->getLastPostTime()->toKunena('config_post_dateformat_hover'); ?>">
			<?php echo $this->getTopicLink($topic, 'last', $topic->getLastPostTime()->toKunena('config_post_dateformat')); ?>
		</span>
	</td>

	<?php if (!empty($this->checkbox)) : ?>
	<td class="span1">
		<label>
			<input class="kcheck" type="checkbox" name="topics[<?php echo $topic->displayField('id'); ?>]" value="1" />
		</label>
	</td>
	<?php endif; ?>

	<?php
	if (!empty($this->position))
		echo $this->subLayout('Page/Module')
			->set('position', $this->position)
			->set('cols', $cols)
			->setLayout('table_row');
	?>
</tr>
