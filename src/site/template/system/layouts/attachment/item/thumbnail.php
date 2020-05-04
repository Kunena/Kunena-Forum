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

use Joomla\CMS\Application\CMSApplication;
use function defined;

$attachment = $this->attachment;

echo $this->subLayout('Widget/Lightbox');

$config = \Kunena\Forum\Libraries\Config\KunenaConfig::getInstance();

$attributesLink = $attachment->isImage() && $config->lightbox ? ' data-fancybox="gallery"' : '';
$attributesImg  = ' style="max-height: ' . (int) $config->thumbheight . 'px;"';
$name           = preg_replace('/.html/', '', $attachment->getUrl());

if (CMSApplication::getInstance('site')->get('sef_suffix') && $config->attachment_protection)
{
	$name = preg_replace('/.html/', '', $attachment->getUrl());
}
else
{
	$name = $attachment->getUrl();
}

if ($attachment->isImage())
{
	?>
	<a href="<?php echo $name; ?>"
	   title="<?php echo $attachment->getShortName($config->attach_start, $config->attach_end); ?>"<?php echo $attributesLink; ?>>
		<img loading="lazy" src="<?php echo $name; ?>"<?php echo $attributesImg; ?> width="<?php echo $config->thumbwidth; ?>"
			 height="<?php echo $config->thumbheight; ?>" alt="<?php echo $attachment->getFilename(); ?>"/>
	</a>
	<?php
}
else
{
	?>
	<a href="<?php echo $attachment->getUrl(); ?>"
	   title="<?php echo $attachment->getShortName($config->attach_start, $config->attach_end); ?>"<?php echo $attributesLink; ?>>
		<?php echo \Kunena\Forum\Libraries\Icons\Icons::file(); ?>
	</a>
	<?php
}
