<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Search
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/** @var KunenaForumMessage $message */
$message = $this->message;
$topic = $message->getTopic();
$category = $topic->getCategory();
$author = $message->getAuthor();
$isReply = $message->id != $topic->first_post_id;
$config = KunenaFactory::getConfig();
$me = isset($this->me) ? $this->me : KunenaUserHelper::getMyself();
?>
<div id="kunena_search_results" class="row-fluid">
	<div class="span2 center">
		<ul class="unstyled center profilebox">
			<li><strong><?php echo $author->getLink(null, null, 'nofollow', '', null, $topic->getCategory()->id); ?></strong></li>
			<li><?php echo $author->getLink($author->getAvatarImage('img-polaroid', 'post')); ?></li>
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
				<?php  if (!$isReply) :
					echo $message->displayField('message');
 				else :
					echo (!$me->userid && $config->teaser) ? JText::_('COM_KUNENA_TEASER_TEXT') : $this->message->displayField('message');
 			endif;?>
			</div>
		</div>
	</div>
</div>
