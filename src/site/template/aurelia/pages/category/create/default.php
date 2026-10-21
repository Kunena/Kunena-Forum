<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Pages.Category
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;

$content = $this->execute('Category/Manage');

// The breadcrumb path to the parent category, and the new category.
$parentId = (int) $content->category->parentid;

if ($parentId) {
    $parents   = KunenaCategoryHelper::getParents($parentId);
    $parents[] = KunenaCategoryHelper::get($parentId);

    foreach ($parents as $parent) {
        $this->addBreadcrumb(
            $parent->displayField('name'),
            $parent->getUri()
        );
    }
}

$this->addBreadcrumb(
    Text::_($parentId ? 'COM_KUNENA_CATEGORY_NEW_FRONTEND' : 'COM_KUNENA_CATEGORY_NEW_SECTION'),
    'index.php?option=com_kunena&view=category&layout=create'
);

echo $content;
