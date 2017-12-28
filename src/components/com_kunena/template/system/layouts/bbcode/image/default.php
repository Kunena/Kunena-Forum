<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  BBCode
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

$title = $this->title;
$url = $this->url;
$filename = $this->filename;
$size = $this->size;
$alt = $this->alt;
// @var  bool  $canLink  False if image is inside a link: [url=http://www.domain.com][img]image.jpg[/img][/url]

$canLink = isset($this->canLink) ? $this->canLink : true;

$config = KunenaConfig::getInstance();

$attributesLink = $config->lightbox ? ' class="fancybox-button" rel="fancybox-button"' : '';
$width = $size ? (int) $size . "px;"  : 'auto ';
$attributesImg  = ' style="max-height: ' . (int) $config->imageheight . 'px;' . ' max-width:' . $width . '"';
$attributesImg .= $alt ? ' alt="' . htmlspecialchars($alt) . '"' : '';
?>

<div class="kmsgimage">
	<?php if ($canLink) : ?>
	<a href="<?php echo $this->escape($url); ?>" title=""<?php echo $attributesLink; ?>>
	<?php endif; ?>

		<img src="<?php echo $this->escape($url); ?>"<?php echo $attributesImg; ?> />

	<?php if ($canLink) : ?>
	</a>
	<?php endif; ?>
</div>
