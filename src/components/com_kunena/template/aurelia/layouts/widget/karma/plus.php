<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

$karma = '';

if ($this->topicicontype == 'B3')
{
	$karmaPlusIcon  = '<span class="glyphicon-karma glyphicon glyphicon-plus-sign text-success" title="' . Text::_('COM_KUNENA_KARMA_APPLAUD') . '"></span>';
}
elseif ($this->topicicontype == 'fa')
{
	$karmaPlusIcon  = '<i class="fa fa-plus-circle" title="' . Text::_('COM_KUNENA_KARMA_APPLAUD') . '"></i>';
}
elseif ($this->topicicontype == 'B2')
{
 	$karmaPlusIcon  = '<span class="icon-karma icon icon-plus text-success" title="' . Text::_('COM_KUNENA_KARMA_APPLAUD') . '"></span>';
}
else
{
	$karmaPlusIcon  = '<span class="kicon-profile kicon-profile-plus" title="' . Text::_('COM_KUNENA_KARMA_APPLAUD') . '"></span>';
}

$karma .= ' ' . HTMLHelper::_('kunenaforum.link', 'index.php?option=com_kunena&view=user&task=karmaup&userid=' . $this->userid . '&' . Session::getFormToken() . '=1', $karmaPlusIcon);

echo $karma;
?>