<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

$attachment = $this->attachment;

echo $this->subLayout('Widget/Lightbox');

$config = KunenaConfig::getInstance();

$attributesLink = $attachment->isImage() && $config->lightbox ? ' data-fancybox="gallery"' : '';
$attributesImg  = ' style="max-height: ' . (int) $config->thumbheight . 'px;"';
$name           = preg_replace('/.html/', '', $attachment->getUrl(false, false, true));

if (\Joomla\CMS\Application\CMSApplication::getInstance('site')->get('sef_suffix') && $config->attachment_protection)
{
	$name = preg_replace('/.html/', '', $attachment->getUrl(false, false, true));
}
else
{
	$name = $attachment->getUrl(false, false, true);
}

if ($attachment->isImage())
{
	if ($attachment->getPath()) :
	?>
	<a href="<?php echo $name; ?>"
	   title="<?php echo $attachment->getShortName($config->attach_start, $config->attach_end); ?>"<?php echo $attributesLink; ?>>
		<?php echo $config->display_filename_attachment ? $attachment->getShortName($config->attach_start, $config->attach_end) : ''; ?> <img src="<?php echo $name; ?>"<?php echo $attributesImg; ?> width="<?php echo $config->thumbwidth; ?>"
		     height="<?php echo $config->thumbheight; ?>" alt="<?php echo $attachment->getFilename(); ?>"/>
	</a>
	<?php
	else:
		echo KunenaIcons::picture();
	endif;
}
else
{
	?>
	<a href="<?php echo $attachment->getUrl(false, false, true); ?>"
	   title="<?php echo $attachment->getShortName($config->attach_start, $config->attach_end); ?>"<?php echo $attributesLink; ?>>
		<?php echo $config->display_filename_attachment ? $attachment->getShortName($config->attach_start, $config->attach_end) : ''; ?> <?php echo KunenaIcons::file(); ?>
	</a>
	<?php
}
