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

$cols = empty($this->checkbox) ? 4 : 5;

if (!empty($this->spacing)) : ?>
<tr>
	<td colspan="<?php echo $cols; ?>">&nbsp;</td>
</tr>
<?php endif; ?>

<tr class="<?php echo $topic->ordering ? 'warning' : ''; ?>">
	<td class="span1 center">
		<?php echo $this->getTopicLink($topic, 'unread', $topic->getIcon()); ?>
	</td>
	<td class="span7">
		<h4>
			<?php echo $this->getTopicLink($topic); ?>
			<small class="hidden-phone">
				(<?php echo $this->formatLargeNumber($topic->getReplies()) . ' ' . JText::_('COM_KUNENA_GEN_REPLIES'); ?>)
			</small>
			<?php
			if ($topic->unread) {
				echo $this->getTopicLink($topic, 'unread', '<sup dir="ltr" class="knewchar">(' . (int) $topic->unread
					. ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>');
			}
			?>
		</h4>

		<div class="pull-right">
			<?php echo $this->subLayout('Pagination/List')->set('pagination', $topicPages); ?>
		</div>

		<ul class="inline hidden-phone">
			<li>
				<i class="icon-user"></i>
				Started by <?php echo $topic->getFirstPostAuthor()->getLink(); ?>
			</li>
			<li title="<?php echo $topic->getFirstPostTime()->toKunena('config_post_dateformat_hover'); ?>">
				<i class="icon-calendar"></i>
				<?php echo $topic->getFirstPostTime()->toKunena('config_post_dateformat'); ?>
			</li>
		</ul>
	</td>
	<td class="span1 center hidden-phone">
			<?php echo $topic->getLastPostAuthor()->getLink(
				$topic->getLastPostAuthor()->getAvatarImage('img-polaroid', 48)
			); ?>
	</td>
	<td class="span3 hidden-phone">
		<div>
			<?php echo $this->getTopicLink($topic, 'last'); ?>
		</div>
		<div>
			<?php echo $topic->getLastPostAuthor()->getLink(); ?>
		</div>
		<div>
			<span class="ktopic-date hasTooltip" title="<?php echo $topic->getLastPostTime()
				->toKunena('config_post_dateformat_hover'); ?>">
				<?php echo $topic->getLastPostTime()->toKunena('config_post_dateformat'); ?>
			</span>
		</div>
	</td>
	<?php if (!empty($this->checkbox)) : ?>
	<td class="span1">
		<input class ="kcheck" type="checkbox" name="topics[<?php echo $topic->displayField('id'); ?>]" value="1" />
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
