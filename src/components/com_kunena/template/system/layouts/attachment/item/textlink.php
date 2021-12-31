<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsis
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();
use Joomla\CMS\Language\Text;

$attachment = $this->attachment;

$config = KunenaConfig::getInstance();

$attributesLink = $attachment->isImage() && $config->lightbox ? ' data-fancybox="gallery"' : '';
?>

<a class="btn btn-small" rel="popover" data-placement="bottom" data-trigger="hover" target="_blank"
   rel="noopener noreferrer"
   data-content="Filesize: <?php echo number_format($attachment->size / 1024, 0, '', ',') . Text::_('COM_KUNENA_USER_ATTACHMENT_FILE_WEIGHT'); ?>
" data-original-title="<?php echo $attachment->getShortName(); ?>" href="<?php echo $attachment->getUrl(); ?>"
   title="<?php echo KunenaAttachmentHelper::shortenFileName($attachment->getFilename(), $config->attach_start, $config->attach_end); ?>">
	<?php echo KunenaIcons::info(); ?>
</a>
