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

// Report email (plain text)

$user = $this->message->getAuthor();
?>
<?php echo JText::_('COM_KUNENA_REPORT_RSENDER') . " {$this->me->username} ({$this->me->name})"; ?>
<?php echo JText::_('COM_KUNENA_REPORT_RREASON') . " " . $this->title; ?>
<?php echo JText::_('COM_KUNENA_REPORT_RMESSAGE') . " " . $this->content; ?>

<?php echo JText::_('COM_KUNENA_REPORT_POST_POSTER') . " {$user->username} ({$user->name})";?>
<?php echo JText::_('COM_KUNENA_REPORT_POST_SUBJECT') . ": " . $this->message->getTopic()->subject; ?>
<?php echo JText::_('COM_KUNENA_REPORT_POST_MESSAGE'); ?>
-----
<?php echo KunenaHtmlParser::stripBBCode($this->message->message, 0, false); ?>
-----

<?php echo JText::_('COM_KUNENA_REPORT_POST_LINK') . " " . $this->messageLink; ?>
