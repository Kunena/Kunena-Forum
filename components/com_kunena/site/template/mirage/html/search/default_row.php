<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Search
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<li class="kposts-row [K=ROW:krow-]">
	<table summary="List of all forum categories with posts and replies of each">
		<tbody>
			<tr>
				<td class="kposts-body">
					<ul>
						<li class="kpost-title">
							<h3><?php echo $this->getTopicLink($this->topic, $this->message, $this->subjectHtml) ?></h3>
							<div class="clr"></div>
						</li>
						<li class="kpost-smavatar"><?php echo $this->author->getLink($this->author->getAvatarImage('kavatar', 'list')) ?></li>
						<li class="kpost-details kauthor"><?php echo JText::_('COM_KUNENA_BY').' '.$this->author->getLink() ?></li>
						<li class="kpost-details kdate"><?php echo JText::sprintf('COM_KUNENA_ON_DATE', "[K=DATE:{$this->message->time}]") ?></li>
					</ul>
				</td>
				<td class="ktopic-icon"><?php echo $this->getTopicLink ( $this->topic, 'unread', '[K=TOPIC_ICON]' ) ?></td>
				<td class="kpost-topic">
					<ul>
						<li class="ktopic-title">
							<h3><?php echo $this->getTopicLink($this->topic, null, null) ?></h3>
							<ul class="ktopic-actions">
								<li><?php if ($this->topic->locked) echo $this->getIcon ( 'klocked-icon', JText::_('COM_KUNENA_TOPIC_IS_LOCKED') ) ?></li>
							</ul>
							<div class="clr"></div>
						</li>
						<?php if (!empty($this->categoryLink)) : ?>
						<li class="ktopic-category"><?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->categoryLink) ?></li>
						<?php endif ?>
						<li class="ktopic-details"><?php echo JText::sprintf('COM_KUNENA_TOPIC_STARTED_ON_DATE_BY_USER', "[K=DATE:{$this->topicTime}]", $this->topicAuthor->getLink()) ?></li>
						<li>
							<div class="kpagination-topic">
								<?php echo $this->topic->getPagination(false, $this->config->messages_per_page, 3)->getPagesLinks() ?>
							</div>
						</li>
					</ul>
				</td>
			</tr>
			<tr>
				<td class="kposts-message" colspan="3">
					<?php echo $this->messageHtml ?>
				</td>
			</tr>
		</tbody>
	</table>
</li>