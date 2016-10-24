<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Template.Crypsis
 * @subpackage  BBCode
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

$document   = JFactory::getDocument();
$attachment = $this->attachment;
$doctype    = $document->getType();

$location = JUri::root() . $attachment->getUrl();
$data = getimagesize($location);
$width = $data[0];
$height = $data[1];

if (!$attachment->isImage())
{
	return;
}

// Only render for HTML output
if ($doctype == 'html')
{
	$document->addCustomTag('<link rel="image_src" href="' . JURI::base() . $attachment->getUrl() . '">');
	$document->addCustomTag ('<meta property="og:image" content="' . JURI::base() . $attachment->getUrl() . '" />');
	$document->addCustomTag ('<meta name="twitter:image:src" content="' . JURI::base() . $attachment->getUrl() . '" />');
}

echo $this->subLayout('Widget/Lightbox');

$config = KunenaConfig::getInstance();

$attributesLink = $config->lightbox ? ' class="fancybox-button" rel="fancybox-button"' : '';
$attributesImg  = ' style="max-height:' . (int) $config->imageheight . 'px;"';
?>

<a href="<?php echo $attachment->getUrl(); ?>" title="<?php echo $attachment->getShortName($config->attach_start, $config->attach_end); ?>"<?php echo $attributesLink; ?>>
	<img src="<?php echo $attachment->getUrl(); ?>"<?php echo $attributesImg; ?> width="<?php echo $width ;?>" height="<?php echo $height ;?>" alt="<?php echo $attachment->getFilename();?>" />
</a>
