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

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Icons\KunenaIcons;

$karma = '';

if ($this->karmatype == 'karmadown') {
    $url       = 'index.php?option=com_kunena&view=user&task=karmadown&userid=' . $this->userid . '&' . Session::getFormToken() . '=1';
    $karmaIcon = KunenaIcons::minus(Text::_('COM_KUNENA_KARMA_SMITE'));
} else {
    $url       = 'index.php?option=com_kunena&view=user&task=karmaup&userid=' . $this->userid . '&' . Session::getFormToken() . '=1';
    $karmaIcon = KunenaIcons::plus(Text::_('COM_KUNENA_KARMA_APPLAUD'));
}

$karma .= ' ' . HTMLHelper::_('kunenaforum.link', $url, $karmaIcon);

echo $karma;
