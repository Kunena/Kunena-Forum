<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Site\Layout\Widget\WidgetBreadcrumb;

$pathway  = WidgetBreadcrumb::withoutDefaultPageCrumb($this->breadcrumb->getPathway(), Factory::getApplication()->getMenu());
$item     = array_shift($pathway);
$position = 2;
// An error page keeps its breadcrumb, without the structured data (a missing page has no place in the site for the search engines).
$schema   = !WidgetBreadcrumb::isErrorPage(Factory::getApplication()->getHeaders());

if ($item) :
    ?>
    <nav class="pagination-container-links pt-4" aria-label="breadcrumbs">
        <ol class="mod-kunena-breadcrumbs breadcrumb"<?php echo $schema ? ' itemtype="https://schema.org/BreadcrumbList" itemscope=""' : ''; ?>>
            <li class="mod-kunena-breadcrumbs__item breadcrumb-item active" aria-current="page"<?php echo $schema
                ? ' itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"' : ''; ?>>
                <?php echo KunenaIcons::home(); ?>
                <a<?php echo $schema ? ' itemprop="item"' : ''; ?> href="<?php echo $item->link; ?>"><span<?php echo $schema ? ' itemprop="name"' : ''; ?>><?php
                    echo $this->escape($item->name); ?></span></a>
                <?php if ($schema) : ?>
                    <meta itemprop="position" content="1"/>
                <?php endif; ?>
            </li>

            <?php foreach ($pathway as $item) :
                ?>
                <li class="mod-kunena-breadcrumbs__item breadcrumb-item"<?php echo $schema
                    ? ' itemtype="https://schema.org/ListItem" itemscope="" itemprop="itemListElement"' : ''; ?>>
                    <a<?php echo $schema ? ' itemprop="item"' : ''; ?> href="<?php echo $item->link; ?>"><span<?php echo $schema ? ' itemprop="name"' : ''; ?>><?php
                        echo $this->escape($item->name); ?></span></a>
                    <?php if ($schema) : ?>
                        <meta itemprop="position" content="<?php echo $position++; ?>"/>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>
<?php endif;
