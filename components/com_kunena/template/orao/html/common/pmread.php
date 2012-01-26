<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div id="mb_pmread" style="display: none; color:#999;background:#fff;position:relative;">
	<div class="tk-mb-header-pmread">
		<span class="tk-mb-first"><?php echo JText::_('COM_KUNENA_PMS_INBOX'); ?></span>
	</div>
	<div class="tk-mb-pmread" style="text-align:center;color:#666;">
		<span><br /><?php echo JText::_('COM_KUNENA_TEMPLATE_YOU_HAVE'); ?> <?php if ($this->unreadprivatemessages == 0) echo JText::_('COM_KUNENA_TEMPLATE_YOU_HAVE_NOT'); else echo ' '.$this->unreadprivatemessages; ?>
		<?php echo JText::_('COM_KUNENA_TEMPLATE_UNREAD_PRIVATE'); ?> <?php if (empty($this->privateMessagesLink)) echo JText::_('COM_KUNENA_TEMPLATE_MESSAGES'); elseif ($this->unreadprivatemessages == 1) echo JText::_('COM_KUNENA_TEMPLATE_MESSAGE'); elseif ($this->unreadprivatemessages > 1) echo JText::_('COM_KUNENA_TEMPLATE_MESSAGES'); ?></span>
	</div>
	<?php if (!empty($this->privateMessagesLink) && $this->unreadprivatemessages == 0) :?>
	<div class="tk-mb-inboxlink">
		<a class="tk-mb-inboxlink" href="<?php JURI::base() ?>index.php?option=com_uddeim&task=inbox"><?php echo JText::_('COM_KUNENA_TEMPLATE_READ_NOW'); ?></a>
	</div>
	<?php elseif (!empty($this->privateMessagesLink) && $this->unreadprivatemessages != 0) :?>
	<div class="tk-mb-inboxlink">
		<a class="tk-mb-inboxlink" href="<?php JURI::base() ?>index.php?option=com_uddeim&task=inbox"><?php echo JText::_('COM_KUNENA_TEMPLATE_GO_TO_INBOX'); ?></a>
	</div>
	<?php endif; ?>
</div>