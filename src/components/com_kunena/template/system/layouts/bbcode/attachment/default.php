<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  BBCode
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

// @var KunenaAttachment $attachment

$attachment = $this->attachment;
?>
<div class="kmsgattach">
	<h4>
		<?php echo JText::_('COM_KUNENA_FILEATTACH'); ?>
	</h4>

	<?php echo JText::_('COM_KUNENA_FILENAME'); ?>
	<?php echo $this->subLayout('Attachment/Item')->set('attachment', $attachment); ?>

	<br />

	<?php echo JText::_('COM_KUNENA_FILESIZE') . number_format($attachment->size / 1024, 0, '', ',') . ' ' .
		JText::_('COM_KUNENA_USER_ATTACHMENT_FILE_WEIGHT'); ?>
</div>
