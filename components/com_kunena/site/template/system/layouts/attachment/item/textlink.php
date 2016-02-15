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

/** @var KunenaAttachment $attachment */
$attachment = $this->attachment;

$config = KunenaConfig::getInstance();

$attributesLink = $attachment->isImage() && $config->lightbox ? ' rel="lightbox[simple' . $attachment->mesid . ']"' : '';
?>
<a href="<?php echo $attachment->getUrl(); ?>" target="_blank" title="<?php echo $attachment->getShortName(0,7); ?>" <?php echo $attributesLink ?>>
	<?php echo $attachment->getShortName(); ?>
</a>
(<?php echo number_format($attachment->size / 1024, 0, '', ',') . JText::_('COM_KUNENA_USER_ATTACHMENT_FILE_WEIGHT'); ?>)
