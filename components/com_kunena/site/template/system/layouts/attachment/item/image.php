<?php
/**
 * Kunena Component
* @package Kunena.Template.Crypsis
* @subpackage BBCode
*
* @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAttachment $attachment */
$attachment = $this->attachment;

if (!$attachment->isImage()) return;

$config = KunenaConfig::getInstance();

$attributesLink = $config->lightbox ? ' rel="lightbox[imagelink' . $attachment->mesid . ']"' : '';
$attributesImg = ' style="max-height:' . (int) $config->imageheight . 'px;"';
?>

<a href="<?php echo $attachment->getUrl(); ?>" title="<?php echo KunenaAttachmentHelper::shortenFileName($attachment->getFilename(), 0,7); ?>"<?php echo $attributesLink; ?>>
	<img src="<?php echo $attachment->getUrl(); ?>"<?php echo $attributesImg; ?> alt="" />
</a>
