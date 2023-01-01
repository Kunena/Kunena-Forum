<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Icons\KunenaSvgIcons;

$this->ktemplate = KunenaFactory::getTemplate();
$icon            = $this->ktemplate->getTopicLabel($this->topic);
$topicicontype   = $this->ktemplate->params->get('topicicontype');
$class           = ' class="badge me-1 bg-' . $icon->labeltype . '"';

if ($topicicontype == 'fa') {
    $icons = '<i class="fa fa-' . $icon->fa . '" aria-hidden="true"></i>';
} elseif ($topicicontype == 'svg') {
    $icons = KunenaSvgIcons::loadsvg($icon->svg);
} else {
    $icons = '';
}
?>
<span <?php echo $class; ?> >
    <?php
    if ($topicicontype !== 0) :
        ?>
        <?php echo $icons ?>
    <?php endif; ?>
    <span class="sr-only"></span><?php echo Text::_($icon->name); ?>
</span>
