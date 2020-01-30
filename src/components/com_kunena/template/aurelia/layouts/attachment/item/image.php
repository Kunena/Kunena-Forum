<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

defined('_JEXEC') or die();

use function defined;

$attachment = $this->attachment;

$name = preg_replace('/.html/', '', $attachment->getUrl());

if (!$attachment->isImage())
{
	return;
}

$config = \Kunena\Forum\Libraries\Factory\KunenaFactory::getConfig();

// Load FancyBox library if enabled in configuration
if ($config->lightbox == 1)
{
	echo $this->subLayout('Widget/Lightbox');

	$config = \Kunena\Forum\Libraries\Config\KunenaConfig::getInstance();

	$attributesLink = $config->lightbox ? ' data-fancybox="gallery"' : '';
	$attributesImg  = ' style="max-height:' . (int) $config->imageheight . 'px;"';

	?>
	<a href="<?php echo $attachment->getUrl(); ?>"
	   title="<?php echo $attachment->getShortName($config->attach_start, $config->attach_end); ?>"<?php echo $attributesLink; ?>>
		<img src="<?php echo $attachment->getUrl(); ?>"<?php echo $attributesImg; ?>
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
	   title="<?php echo $attachment->getShortName($config->attach_start, $config->attach_end); ?>">
		<img class="kmsimage" src="<?php echo $name; ?>"
			 width="<?php echo $config->thumbwidth; ?>"
			 height="<?php echo $config->thumbheight; ?>" alt="<?php echo $attachment->getFilename(); ?>"/>
	</a>
	<?php
}
