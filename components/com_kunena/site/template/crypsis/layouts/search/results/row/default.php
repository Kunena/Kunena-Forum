<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Search
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$message = $this->message;
$author = $message->getAuthor();
?>
<div class="row-fluid">
	<div class="span2 center">
		<div><?php echo $author->getLink($this->useravatar); ?></div>
		<div><?php echo $author->getLink(); ?></div>
	</div>

	<div class="span10">
		<h3>
			<?php echo $this->getTopicLink($this->topic, $this->message, $this->subjectHtml); ?>
			<small><?php echo KunenaDate::getInstance($this->message->time)->toSpan(); ?></small>
		</h3>

		<p>
			<?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink($this->category, $this->escape($this->category->name))); ?>
		</p>

		<div>
			<?php echo $this->messageHtml; ?>
		</div>
	</div>
</div>
