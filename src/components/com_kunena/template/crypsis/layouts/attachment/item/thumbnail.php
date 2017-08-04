<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsis
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

// @var KunenaAttachment $attachment

$attachment = $this->attachment;

echo $this->subLayout('Widget/Lightbox');

$config = KunenaConfig::getInstance();

$attributesLink = $attachment->isImage() && $config->lightbox ? ' class="fancybox-button" rel="fancybox-button"' : '';
$attributesImg  = ' style="max-height: ' . (int) $config->thumbheight . 'px;"';
$name           = preg_replace('/.html/', '', $attachment->getUrl());

if (\Joomla\CMS\Application\CMSApplication::getInstance('site')->get('sef_suffix') && $config->attachment_protection)
{
	$name = preg_replace('/.html/', '', $attachment->getUrl());
}
else
{
	$name = $attachment->getUrl();
}

if ($attachment->isImage())
{
	if ($config->lazyload)
	{
		?>
		<a href="<?php echo $name; ?>"
		   title="<?php echo $attachment->getShortName($config->attach_start, $config->attach_end); ?>"<?php echo $attributesLink; ?>>
			<img class="lazy" data-original="<?php echo $name; ?>"<?php echo $attributesImg; ?> width="<?php echo $config->thumbheight; ?>"
			     height="<?php echo $config->thumbheight; ?>" alt="<?php echo $attachment->getFilename(); ?>"/>
		</a>
		<?php
	}
	else
	{
		?>
		<a href="<?php echo $name; ?>"
		   title="<?php echo $attachment->getShortName($config->attach_start, $config->attach_end); ?>"<?php echo $attributesLink; ?>>
			<img src="<?php echo $name; ?>"<?php echo $attributesImg; ?> width="<?php echo $config->thumbheight; ?>"
			     height="<?php echo $config->thumbheight; ?>" alt="<?php echo $attachment->getFilename(); ?>"/>
		</a>
		<?php
	}
}
else
{
	?>
	<a href="<?php echo $attachment->getUrl(); ?>" title="<?php echo $attachment->getShortName($config->attach_start, $config->attach_end); ?>"<?php echo $attributesLink; ?>>
		<?php echo KunenaIcons::file(); ?>
	</a>
	<?php
}
