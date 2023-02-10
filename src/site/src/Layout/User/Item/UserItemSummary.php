<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\User\Item;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use stdClass;

/**
 * KunenaLayoutUserItem
 *
 * @since   Kunena 6.1
 */
class UserItemSummary extends KunenaLayout
{
    public $profile;
    
    public $config;
    
    public $candisplaymail;
    
    public $me;
    
    public $private;
    
    public $points;
    
    public $medals;
    
    public $socials;
    
    public $avatar;
    
    public $banInfo;
}
