<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Config\KunenaConfig;

$url      = $this->url;
$size     = $this->size;
$alt      = $this->alt;

$canLink = isset($this->canLink) ? $this->canLink : true;

echo $this->subLayout('Widget/Lightbox');

$config = KunenaConfig::getInstance();

$attributesLink = $config->lightbox ? ' data-fancybox="gallery"' : '';
$width          = $size ? (int) $size . "px;" : 'auto ';
$attributesImg  = ' style="max-height: ' . (int) $config->imageHeight . 'px;' . ' max-width:' . $width . '"';
$attributesImg  .= $alt ? ' alt="' . htmlspecialchars($alt) . '"' : '';
?>
<div class="kmsgimage">
    <?php if ($canLink) :
        ?>
    <a href="<?php echo $this->escape($url); ?>" data-bs-toggle="tooltip" title="<?php echo $alt; ?>" <?php echo $attributesLink; ?>>
    <?php endif; ?>

        <img loading=lazy src="<?php echo $this->escape($url); ?>" <?php echo $attributesImg; ?> alt="<?php echo $alt; ?>"/>

        <?php if ($canLink) :
            ?>
    </a>
        <?php endif; ?>
</div>
