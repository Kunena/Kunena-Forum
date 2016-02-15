<?php
/**
 * Kunena Component
* @package Kunena.Template.Crypsis
* @subpackage BBCode
*
* @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link https://www.kunena.org
**/
defined ( '_JEXEC' ) or die ();

$title = $this->title;
$url = $this->url;
$filename = $this->filename;
$size = $this->size;
/** @var  bool  $canLink  False if image is inside a link: [url=http://www.domain.com][img]image.jpg[/img][/url] */
$canLink = isset($this->canLink) ? $this->canLink : true;

$config = KunenaConfig::getInstance();

$attributesLink = $config->lightbox ? ' rel="lightbox[gallery]"' : '';
$attributesImg = ' style="max-height: '. (int) $config->imageheight . 'px;"';
$attributesImg .= $size ? ' width="' . (int) $size . '"' : '';
?>

<div class="kmsgimage">
	<?php if ($canLink) : ?>
	<a href="<?php echo $this->escape($url); ?>" title=""<?php echo $attributesLink; ?>>
	<?php endif; ?>

		<img src="<?php echo $this->escape($url); ?>"<?php echo $attributesImg; ?> alt="" />

	<?php if ($canLink) : ?>
	</a>
	<?php endif; ?>
</div>
