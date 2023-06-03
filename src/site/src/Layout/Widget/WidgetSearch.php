<?php

/**
 * Kunena Component
 * 
 * @package         Kunena.Site
 * @subpackage      Layout.widget
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Widget;

defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutTopicEditEditor
 *
 * @since  K6.1
 */
class WidgetSearch extends KunenaLayout
{
    public $catid;
    
    public $state;
    
    public $id;
    
    public $title;
}