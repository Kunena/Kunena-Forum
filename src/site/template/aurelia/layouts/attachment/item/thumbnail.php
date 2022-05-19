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

use Joomla\CMS\Application\CMSApplication;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Icons\KunenaIcons;

$attachment = $this->attachment;

echo $this->subLayout('Widget/Lightbox');

$config = KunenaConfig::getInstance();

$attributesLink = $attachment->isImage() && $config->lightbox ? ' data-fancybox="gallery"' : '';
$attributesImg  = ' style="max-height: ' . (int) $config->thumbHeight . 'px;"';

if (CMSApplication::getInstance('site')->get('sef_suffix') && $config->attachmentProtection)
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
           data-bs-toggle="tooltip" title="<?php echo $attachment->getShortName($config->attachStart, $config->attachEnd); ?>"<?php echo $attributesLink; ?>>
			<?php echo $config->display_filename_attachment ? $attachment->getShortName($config->attachStart, $config->attachEnd) : ''; ?>
            <img loading=lazy src="<?php echo $name; ?>"<?php echo $attributesImg; ?>
                 width="<?php echo $config->thumbWidth; ?>"
                 height="<?php echo $config->thumbHeight; ?>" alt="<?php echo $attachment->getFilename(); ?>"/>
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
       data-bs-toggle="tooltip" title="<?php echo $attachment->getShortName($config->attachStart, $config->attachEnd); ?>"<?php echo $attributesLink; ?>>
		<?php echo $config->display_filename_attachment ? $attachment->getShortName($config->attachStart, $config->attachEnd) : ''; ?><?php echo KunenaIcons::file(); ?>
    </a>
	<?php
}
