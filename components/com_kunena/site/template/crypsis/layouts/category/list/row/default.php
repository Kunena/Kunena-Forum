<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$topic = $this->category->getLastTopic();
$avatar = $this->config->avataroncat > 0 ? $topic->getAuthor()->getAvatarImage() : null;
?>

<tr>
	<td>
		<h3>
			<?php echo $this->getCategoryLink($this->category, null, null, 'ktopic-title km'); ?>
			<small class="hidden-phone">
				(<?php echo JText::plural('COM_KUNENA_X_TOPICS', $this->formatLargeNumber($this->category->getTopics())); ?>)
			</small>
		</h3>
	</td>
	<?php if (!$topic->exists()) : ?>
		<td>
			<?php echo JText::_('COM_KUNENA_NO_POSTS'); ?>
		</td>
	<?php else : ?>
		<td class="center">
			<?php if ($this->config->avataroncat > 0) : ?>
				<span class="hidden-phone">
			<?php echo $topic->getLastPostAuthor()->getLink($avatar); ?>
		</span>
			<?php endif; ?>
		</td>
		<td>
		<!-- TODO : Missing topic tags (keywords), topic hits, icons (favorite, locked) -->
		<div>
			<?php echo $this->getTopicLink($topic, 'last', KunenaHtmlParser::parseText($topic->subject, 30)) ?>
		</div>
		<div>
			<?php echo $topic->getLastPostAuthor()->getLink(); ?>
		</div>
		<div>
			<?php echo KunenaDate::getInstance($topic->last_post_time)->toSpan('config_post_dateformat', 'config_post_dateformat_hover'); ?>
		</div>
	</td>
	<?php endif; ?>
	<?php if ($this->checkbox) : ?>
	<td class="center">
		<input type="checkbox" name="categories[<?php echo $this->category->id?>]" value="1" />
	</td>
	<?php endif; ?>
</tr>
