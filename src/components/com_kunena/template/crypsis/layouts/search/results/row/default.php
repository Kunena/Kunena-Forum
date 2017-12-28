<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Search
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

// @var KunenaForumMessage $message

$message = $this->message;
$topic = $message->getTopic();
$category = $topic->getCategory();
$author = $message->getAuthor();
$isReply = $message->id != $topic->first_post_id;
$config = KunenaFactory::getConfig();
$name = $config->username ? $author->username : $author->name;
$me = isset($this->me) ? $this->me : KunenaUserHelper::getMyself();
?>
<div id="kunena_search_results" class="row-fluid">
	<div class="span2 center">
		<ul class="unstyled center profilebox">
			<li><strong><?php echo $author->getLink(null, null, 'nofollow', '', null, $topic->getCategory()->id); ?></strong></li>
			<li><?php echo $author->getLink($author->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType'), 'post')); ?></li>
		</ul>
	</div>

	<div class="span10">
		<small class="text-muted pull-right hidden-phone" style="margin-top:-5px;"> 
			<?php echo KunenaIcons::clock();?> <?php echo $message->getTime()->toSpan(); ?>
			<?php if ($message->modified_time) :?> - <?php echo KunenaIcons::edit() . ' ' . $message->getModifiedTime()->toSpan(); endif;?>
		</small>
		<?php //TODO: Find a better way for inline elements like this can displayField contain HTML which would not be valid inside the attribute. ?>
		<div class="badger-left badger-info khistory">
		    <div class="mykmsg-header">
			    <?php echo (!$isReply) ? $name . ' ' . JText::_('COM_KUNENA_MESSAGE_CREATED') : $name . ' ' . JText::_('COM_KUNENA_MESSAGE_REPLIED'); ?><?php echo $message->displayField('subject'); ?>  
            </div>
			<h3>
				<?php echo $this->getTopicLink($topic, $message, null, null, 'hasTooltip'); ?>
			</h3>

			<p>
				<?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink($category, null, null, 'hasTooltip')); ?>
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
