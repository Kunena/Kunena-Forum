<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers.Misc
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Credits;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutMiscDisplay
 *
 * @since   Kunena 6.1
 */
class Credits extends KunenaLayout
{
    public $output;

    public $user;

    public $headerText;
    
    public $pagination;
    
    public $config;
    
    public $logo;
    
    public $intro;
    
    public $memberList;
    
    public $thanks;
}
