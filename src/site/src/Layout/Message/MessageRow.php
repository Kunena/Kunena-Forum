<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Message;

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutMessageList
 *
 * @since   Kunena 6.1
 */
class MessageRow extends KunenaLayout
{
    public $position;

    public $message;

    public $checkbox;

    public $ktemplate;
}
