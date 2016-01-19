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

// New post email for subscribers (HTML)

$this->mail->isHtml(true);

$config = KunenaConfig::getInstance();
$subject = $this->message->subject ? $this->message->subject : $this->message->getTopic()->subject;
$author = $this->message->getAuthor();
$more = ($this->once ?
	JText::_($this->message->parent? 'COM_KUNENA_POST_EMAIL_NOTIFICATION_MORE_READ' :
		'COM_KUNENA_POST_EMAIL_NOTIFICATION_MORE_SUBSCRIBE'). "\n" : '');
?>

<h2><?php echo JText::_('COM_KUNENA_POST_EMAIL_MOD1') . " " . $config->board_title; ?></h2>

<div><?php echo JText::_('COM_KUNENA_MESSAGE_SUBJECT') . " : " . $subject; ?></div>
<div><?php echo JText::_('COM_KUNENA_CATEGORY') . " : " . $this->message->getCategory()->name; ?></div>
<div><?php echo JText::_('COM_KUNENA_VIEW_POSTED') . " : " . $author->getName('???', false); ?></div>

<p>URL : <a href="<?php echo $this->messageUrl; ?>"><b><?php echo $this->messageUrl; ?></b></a></p>

<?php if ($config->mailfull == 1) : echo JText::_('COM_KUNENA_MESSAGE'); ?>:
<hr />
<div><?php echo $this->message->displayField('message'); ?></div>
<hr />
<?php endif; ?>
<div><?php echo JText::_('COM_KUNENA_POST_EMAIL_MOD2'); ?></div>

<div><?php echo JText::_('COM_KUNENA_POST_EMAIL_NOTIFICATION3'); ?></div>

<?php
// Email as plain text:

$full = !$config->mailfull ? '' : <<<EOS
{$this->text('COM_KUNENA_MESSAGE')}
-----
{$this->message->displayField('message', false)}
-----

EOS;

$alt = <<<EOS
{$this->text('COM_KUNENA_POST_EMAIL_MOD1')} {$config->board_title}

{$this->text('COM_KUNENA_MESSAGE_SUBJECT')} : {$subject}
{$this->text('COM_KUNENA_CATEGORY')} : {$this->message->getCategory()->name}
{$this->text('COM_KUNENA_VIEW_POSTED')} : {$author->getName('???', false)}

URL : {$this->messageUrl}

{$full}{$this->text('COM_KUNENA_POST_EMAIL_MOD2')}{$more}

{$this->text('COM_KUNENA_POST_EMAIL_NOTIFICATION3')}
EOS;
$this->mail->AltBody = $alt;
