<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Email
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// New post email for subscribers (plain text)

$author = $this->message->getAuthor();
$config = KunenaConfig::getInstance();
$subject = $this->massage->subject ? $this->message->subject : $this->message->getTopic()->subject;

$msg1 = $this->message->parent ? JText::_('COM_KUNENA_POST_EMAIL_NOTIFICATION1') : JText::_
	('COM_KUNENA_POST_EMAIL_NOTIFICATION1_CAT');
$msg2 = $this->message->parent ? JText::_('COM_KUNENA_POST_EMAIL_NOTIFICATION2') : JText::_
	('COM_KUNENA_POST_EMAIL_NOTIFICATION2_CAT');
?>
<?php echo $msg1 . " " . $config->board_title; ?>

<?php echo JText::_('COM_KUNENA_MESSAGE_SUBJECT') . " : " . $subject; ?>
<?php echo JText::_('COM_KUNENA_CATEGORY') . " : " . $this->message->getCategory()->name; ?>
<?php echo JText::_('COM_KUNENA_VIEW_POSTED') . " : " . $author->getName('???', false); ?>

URL : <?php echo $this->messageUrl; ?>

<?php if ($config->mailfull == 1) : echo JText::_('COM_KUNENA_MESSAGE'); ?>:
-----
<?php echo KunenaHtmlParser::stripBBCode($this->message, 0, false); ?>
-----
<?php endif; ?>
<?php echo $msg2; ?>
<?php if ($this->once) {
	if ($this->message->parent) {
		echo JText::_('COM_KUNENA_POST_EMAIL_NOTIFICATION_MORE_READ');
	} else {
		echo JText::_('COM_KUNENA_POST_EMAIL_NOTIFICATION_MORE_SUBSCRIBE');
	}
} ?>

<?php echo JText::_('COM_KUNENA_POST_EMAIL_NOTIFICATION3'); ?>
