<?php
/**
 * Kunena Component
* @package Kunena.Template.Crypsis
* @subpackage BBCode
*
* @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link https://www.kunena.org
**/
defined ( '_JEXEC' ) or die ();

$title = $this->title;
$url = $this->url;
$filename = $this->filename;
$size = $this->size;
?>
<div class="kmsgattach">
	<h4>
		<?php echo $title; ?>
	</h4>

	<?php if ($url) : ?>

	<?php echo JText::_('COM_KUNENA_FILENAME'); ?>
	<a href="<?php echo $url; ?>" title="<?php echo $this->escape($filename); ?>">
		<?php echo $this->escape(KunenaAttachmentHelper::shortenFilename($filename)); ?>
	</a>

	<br />

	<?php echo JText::_('COM_KUNENA_FILESIZE') . number_format($size / 1024, 0, '', ',') . ' ' .
		JText::_('COM_KUNENA_USER_ATTACHMENT_FILE_WEIGHT'); ?>

	<?php endif; ?>
</div>
