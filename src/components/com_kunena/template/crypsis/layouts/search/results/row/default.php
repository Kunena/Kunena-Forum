<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Search
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$message  = $this->message;
$topic    = $message->getTopic();
$category = $topic->getCategory();
$author   = $message->getAuthor();
$isReply  = $message->id != $topic->first_post_id;
$config   = KunenaFactory::getConfig();
$name     = $config->username ? $author->username : $author->name;
$me       = isset($this->me) ? $this->me : KunenaUserHelper::getMyself();
?>
<div id="kunena_search_results" class="row-fluid">
	<div class="span2 center">
		<ul class="unstyled center profilebox">
			<li>
				<strong><?php echo $author->getLink(null, null, 'nofollow', '', null, $topic->getCategory()->id); ?></strong>
			</li>
			<li><?php echo $author->getLink($author->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType'), 'post')); ?></li>
		</ul>
	</div>

	<div class="span10">
		<small class="text-muted pull-right hidden-phone" style="margin-top:-5px;">
			<?php echo KunenaIcons::clock(); ?> <?php echo $message->getTime()->toSpan(); ?>
			<?php
			if ($message->modified_time)
				:
				?> - <?php echo KunenaIcons::edit() . ' ' . $message->getModifiedTime()->toSpan();
			endif; ?>
		</small>
		<div class="badger-left badger-info khistory">
			<div class="mykmsg-header">
				<?php
				$subject = $message->displayField('subject');
				$msg     = $isReply ? 'COM_KUNENA_MESSAGE_REPLIED_NEW' : 'COM_KUNENA_MESSAGE_CREATED_NEW';
				echo Text::sprintf($msg, $message->getAuthor()->getLink(), $this->getTopicLink($topic, $message, null, null, KunenaTemplate::getInstance()->tooltips() . ' topictitle', $category, true, false));
				?>
			</div>
			<h3>
				<?php echo $this->getTopicLink($topic, $message, null, null, 'hasTooltip'); ?>
			</h3>

			<p>
				<?php echo Text::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink($category, null, null, 'hasTooltip')); ?>
			</p>

			<div class="kmessage">
				<?php if (!$isReply)
					:
					echo $message->displayField('message');
				else

					:
					echo (!$me->userid && $config->teaser) ? Text::_('COM_KUNENA_TEASER_TEXT') : $this->message->displayField('message');
				endif; ?>
			</div>
		</div>
	</div>
</div>
