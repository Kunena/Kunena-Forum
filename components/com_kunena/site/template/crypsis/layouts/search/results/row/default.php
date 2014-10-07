<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Search
 *
 * @copyright   (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/** @var KunenaForumMessage $message */
$message = $this->message;
$topic = $message->getTopic();
$category = $topic->getCategory();
$author = $message->getAuthor();
$isReply = $message->id != $message->getTopic('first');
?>
<div id="kunena_search_results" class="row-fluid">
	<div class="span2 center">
		<ul class="unstyled center profilebox">
			<li><strong><?php echo $author->getLink(); ?></strong></li>
			<li><?php echo $author->getLink($author->getAvatarImage('img-polaroid', 128, 128)); ?></li>
		</ul>
	</div>

	<div class="span10">
		<small class="text-muted pull-right hidden-phone" style="margin-top:-5px;"> <span class="icon icon-clock"></span> <?php echo $message->getTime()->toSpan(); ?></small>
		<?php //TODO: Find a better way for inline elements like this can displayField contain HTML which would not be valid inside the attribute. ?>
		<div class="badger-left badger-info khistory" data-badger="<?php echo (!$isReply) ? $author->username . ' created the topic: ' : $author->username. ' replied the topic: '; ?><?php echo $message->displayField('subject'); ?>">
			<h3>
				<?php echo $this->getTopicLink($topic, $message); ?>
			</h3>

			<p>
				<?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink($category)); ?>
			</p>

			<div class="kmessage">
				<?php echo $message->displayField('message'); ?>
			</div>
		</div>
	</div>
</div>
