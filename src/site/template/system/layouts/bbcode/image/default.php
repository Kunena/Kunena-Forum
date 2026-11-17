<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.System
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Html\KunenaLightbox;

$url      = $this->url;
$size     = $this->size;
$alt      = $this->alt;

$canLink = isset($this->canLink) ? $this->canLink : true;

echo $this->subLayout('Widget/Lightbox');

$config = KunenaConfig::getInstance();

$attributesLink = KunenaLightbox::attributes();
$width          = $size ? (int) $size . "px;" : 'auto ';
// The float of [img float=...]: left or right, nothing else reaches the style.
$float          = \in_array($this->float, ['left', 'right'], true) ? $this->float : '';
$style          = $float ? ' float:' . $float . ';' : '';
$attributesImg  = ' style="max-height: ' . (int) $config->imageHeight . 'px;' . ' max-width:' . $width . '"';
// The words of alt=... as the text of the image and its tooltip; without them the text of the image is the title of the layout.
$altText        = $alt ? (string) $alt : (string) $this->title;
?>
<div class="kmsgimage">
    <?php if ($canLink) :
        ?>
    <a href="<?php echo $this->escape($url); ?>" data-bs-toggle="tooltip"<?php echo $alt ? ' title="' . $this->escape((string) $alt) . '"' : ''; ?><?php echo $attributesLink; ?>>
    <?php endif; ?>

        <img loading=lazy src="<?php echo $this->escape($url); ?>"<?php echo $attributesImg; ?> alt="<?php echo $this->escape($altText); ?>"/>

        <?php if ($canLink) :
            ?>
    </a>
        <?php endif; ?>
</div>
