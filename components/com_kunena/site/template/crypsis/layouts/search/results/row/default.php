<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Search
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/** @var KunenaForumMessage $message */
$message = $this->message;
$topic = $message->getTopic();
$category = $topic->getCategory();
$author = $message->getAuthor();
?>
<div class="row-fluid">
	<div class="span2 center thumbnail">
		<div><?php echo $author->getLink($author->getAvatarImage('', 120)); ?></div>
		<div><?php echo $author->getLink(); ?></div>
	</div>

	<div class="span10 well">
		<h3>
			<?php echo $this->getTopicLink($topic, $message); ?>
			<small><?php echo $message->getTime()->toSpan(); ?></small>
		</h3>

		<p>
			<?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink($category)); ?>
		</p>

		<div>
			<?php echo $message->displayField('message'); ?>
		</div>
	</div>
</div>
