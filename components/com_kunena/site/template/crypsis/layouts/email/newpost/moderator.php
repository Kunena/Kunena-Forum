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

// New post email for moderators (plain text)

$config = KunenaConfig::getInstance();
$subject = $this->massage->subject ? $this->message->subject : $this->message->getTopic()->subject;
$author = $this->message->getAuthor();
?>
<?php echo JText::_('COM_KUNENA_POST_EMAIL_MOD1') . " " . $config->board_title; ?>

<?php echo JText::_('COM_KUNENA_MESSAGE_SUBJECT') . " : " . $subject; ?>
<?php echo JText::_('COM_KUNENA_CATEGORY') . " : " . $this->massage->getCategory()->name; ?>
<?php echo JText::_('COM_KUNENA_VIEW_POSTED') . " : " . $author->getName('???', false); ?>

URL : <?php echo $this->messageUrl; ?>

<?php if ($config->mailfull == 1) : echo JText::_('COM_KUNENA_MESSAGE'); ?>:
-----
<?php echo KunenaHtmlParser::stripBBCode($this->message->message, 0, false); ?>
-----
<?php endif; ?>
<?php echo JText::_('COM_KUNENA_POST_EMAIL_MOD2'); ?>

<?php echo JText::_('COM_KUNENA_POST_EMAIL_NOTIFICATION3'); ?>
