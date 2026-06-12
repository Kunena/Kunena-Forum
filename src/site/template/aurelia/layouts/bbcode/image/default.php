<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Config\KunenaConfig;

$title    = $this->title;
$url      = $this->url;
$size     = $this->size;
$alt      = $this->alt;
$style    = $this->float ? " float:".$this->float.";" : '';
$canLink = isset($this->canLink) ? $this->canLink : true;

echo $this->subLayout('Widget/Lightbox');

$config = KunenaConfig::getInstance();

$attributesLink = $config->lightbox ? ' data-fancybox="gallery"' : '';
$width          = $size ? (int) $size . "px;" : 'auto ';
$attributesImg  = ' style="max-height: ' . (int) $config->imageHeight . 'px;' . ' max-width:' . $width . $style . '"';
$attributesImg  .= $alt ? ' alt="' . htmlspecialchars($alt) . '"' : '';
if (stripos(get_headers($url)[0],"200 OK")) {
?>
<div class="kmsgimage<?php echo ($style ? ' float' : ''); ?>">
    <?php if ($canLink) :
        ?>
    <a href="<?php echo $this->escape($url); ?>" data-bs-toggle="tooltip" title="<?php echo $alt; ?>" <?php echo $attributesLink; ?>>
    <?php endif; ?>

        <img loading=lazy src="<?php echo $this->escape($url); ?>" <?php echo $attributesImg; ?> alt="<?php echo $title; ?>"/>

        <?php if ($canLink) :
            ?>
    </a>
        <?php endif; ?>
</div> 
<?php }
    else
    echo 'image missing '.$url;