<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Config\KunenaConfig;

$attachment = $this->attachment;

$name = preg_replace('/.html/', '', $attachment->getUrl());

if (!$attachment->isImage())
{
	return;
}

$config = KunenaConfig::getInstance();

// Load FancyBox library if enabled in configuration
if ($config->lightbox == 1)
{
	echo $this->subLayout('Widget/Lightbox');

	$attributesLink = $config->lightbox ? ' data-fancybox="gallery"' : '';
	$attributesImg  = ' style="max-height:' . (int) $config->imageHeight . 'px;"';
	?>
	<a href="<?php echo $attachment->getUrl(); ?>"
	   data-bs-toggle="tooltip" title="<?php echo $attachment->getShortName($config->attachStart, $config->attachEnd); ?>"<?php echo $attributesLink; ?>>
		<img loading=lazy src="<?php echo $attachment->getUrl(); ?>"<?php echo $attributesImg; ?>
			 width="<?php echo $attachment->width; ?>"
			 height="<?php echo $attachment->height; ?>"
			 alt="<?php echo $attachment->getFilename(); ?>"/>
	</a>
	<?php
}
else
{
	?>
	<a href="<?php echo $name; ?>"
	   data-bs-toggle="tooltip" title="<?php echo $attachment->getShortName($config->attachStart, $config->attachEnd); ?>">
		<img loading=lazy class="kmsimage" src="<?php echo $name; ?>"
			 width="<?php echo $config->thumbWidth; ?>"
			 height="<?php echo $config->thumbHeight; ?>" alt="<?php echo $attachment->getFilename(); ?>"/>
	</a>
	<?php
}
