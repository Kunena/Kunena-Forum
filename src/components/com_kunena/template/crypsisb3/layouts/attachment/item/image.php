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

$data     = getimagesize($attachment->getPath());
$width    = $data[0];
$height   = $data[1];
$name     = preg_replace('/.html/', '', $attachment->getUrl());

if (!$attachment->isImage())
{
	return;
}

$config = KunenaFactory::getConfig();

// Load FancyBox library if enabled in configuration
if ($config->lightbox == 1)
{
	echo $this->subLayout('Widget/Lightbox');

	$config = KunenaConfig::getInstance();

	$attributesLink = $config->lightbox ? ' data-fancybox="gallery"' : '';
	$attributesImg  = ' style="max-height:' . (int) $config->imageheight . 'px;"';

	?>
	<a href="<?php echo $attachment->getUrl(); ?>"
	   title="<?php echo $attachment->getShortName($config->attach_start, $config->attach_end); ?>"<?php echo $attributesLink; ?>>
		<img src="<?php echo $attachment->getUrl(); ?>"<?php echo $attributesImg; ?> width="<?php echo $width; ?>"
		     height="<?php echo $height; ?>"
		     alt="<?php echo $attachment->getFilename(); ?>"/>
	</a>
	<?php
}
else
{
	?>
	<a href="<?php echo $name; ?>"
	   title="<?php echo $attachment->getShortName($config->attach_start, $config->attach_end); ?>"<?php echo $attributesLink; ?>>
		<img class="kmsimage" src="<?php echo $name; ?>"<?php echo $attributesImg; ?>
		     width="<?php echo $config->thumbwidth; ?>"
		     height="<?php echo $config->thumbheight; ?>" alt="<?php echo $attachment->getFilename(); ?>"/>
	</a>
	<?php
}
