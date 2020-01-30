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

namespace Kunena\Forum\Site;

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use function defined;

$karma = '';

if ($this->topicicontype == 'B3')
{
	$karmaMinusIcon = '<span class="glyphicon-karma glyphicon glyphicon-minus-sign text-danger" title="' . Text::_('COM_KUNENA_KARMA_SMITE') . '"></span>';
}
elseif ($this->topicicontype == 'fa')
{
	$karmaMinusIcon = '<i class="fa fa-minus-circle" title="' . Text::_('COM_KUNENA_KARMA_SMITE') . '"></i>';
}
elseif ($this->topicicontype == 'B2')
{
	$karmaMinusIcon = '<span class="icon-karma icon icon-minus text-error" title="' . Text::_('COM_KUNENA_KARMA_SMITE') . '"></span>';
}
else
{
	$karmaMinusIcon = '<span class="kicon-profile kicon-profile-minus" title="' . Text::_('COM_KUNENA_KARMA_SMITE') . '"></span>';
}

$karma .= ' ' . HTMLHelper::_('kunenaforum.link', 'index.php?option=com_kunena&view=user&task=karmadown&userid=' . $this->userid . '&' . Session::getFormToken() . '=1', $karmaMinusIcon);

echo $karma;
?>
