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

use Joomla\CMS\Language\Text;
use function defined;

$attachment = $this->attachment;

if (!$attachment->getPath())
{
	return;
}

$config = \Kunena\Forum\Libraries\Config\KunenaConfig::getInstance();

$attributesLink = $attachment->isImage() && $config->lightbox ? ' data-fancybox="none"' : '';
?>

<a class="btn btn-outline-primary border btn-small" rel="popover" data-placement="bottom" data-trigger="hover"
   target="_blank"
   rel="noopener noreferrer"
   data-content="Filesize: <?php echo number_format($attachment->size / 1024, 0, '', ',') . Text::_('COM_KUNENA_USER_ATTACHMENT_FILE_WEIGHT'); ?>
" data-original-title="<?php echo $attachment->getShortName(); ?>"
   href="<?php echo $attachment->getUrl(false, false, true); ?>"
   title="<?php echo \Kunena\Forum\Libraries\Attachment\AttachmentHelper::shortenFileName($attachment->getFilename(), $config->attach_start, $config->attach_end); ?>" <?php echo $attributesLink; ?>>
	<?php echo \Kunena\Forum\Libraries\Icons\Icons::info(); ?>
</a>
