<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Email
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

// Report moderator email (HTML)
$this->mail->isHtml(true);

$user = $this->message->getAuthor();
?>

<h2><?php echo JText::_('COM_KUNENA_REPORT_RSENDER') . " {$this->me->username} ({$this->me->name})"; ?></h2>
<div><?php echo JText::_('COM_KUNENA_REPORT_RREASON') . " " . $this->title; ?></div>
<div><?php echo JText::_('COM_KUNENA_REPORT_RMESSAGE') . " " . $this->content; ?></div>

<div><?php echo JText::_('COM_KUNENA_REPORT_POST_POSTER') . " {$user->username} ({$user->name})";?></div>
<div><?php echo JText::_('COM_KUNENA_REPORT_POST_SUBJECT') . ": " . $this->message->getTopic()->subject; ?></div>
<div><?php echo JText::_('COM_KUNENA_REPORT_POST_MESSAGE'); ?></div>
<hr />
<div><?php echo $this->message->displayField('message'); ?></div>
<hr />

<div>
	<?php echo JText::_('COM_KUNENA_REPORT_POST_LINK'); ?>
	<a href="<?php echo $this->messageLink; ?>"><?php echo $this->messageLink; ?></a>
</div>

<?php
// Email as plain text:

$alt = <<<EOS
{$this->text('COM_KUNENA_REPORT_RSENDER')} {$this->me->username} ({$this->me->name})
{$this->text('COM_KUNENA_REPORT_RREASON')} {$this->title}
{$this->text('COM_KUNENA_REPORT_RMESSAGE')} {$this->content}

{$this->text('COM_KUNENA_REPORT_POST_POSTER')} {$user->username} ({$user->name})
{$this->text('COM_KUNENA_REPORT_POST_SUBJECT')}: {$this->message->getTopic()->subject}

{$this->text('COM_KUNENA_REPORT_POST_MESSAGE')}
-----
{$this->message->displayField('message', false)}
-----

{$this->text('COM_KUNENA_REPORT_POST_LINK')} {$this->messageLink}
EOS;
$this->mail->AltBody = $alt;
