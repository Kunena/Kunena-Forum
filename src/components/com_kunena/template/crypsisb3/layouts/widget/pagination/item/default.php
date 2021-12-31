<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.Pagination
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

$item = $this->item;

if ($item->base !== null)
{
	// Check if the item can be clicked.
	$limit = 'limitstart.value=' . (int) $item->base;
	echo '<li><a ' . KunenaTemplate::getInstance()->tooltips(true) . ' href="' . $item->link . '" title="' . Text::_('COM_KUNENA_PAGE') . $item->text . '">' . $item->text . '</a></li>';
}
elseif (!empty($item->active))
{
	// Check if the item is the active (or current) page.
	echo '<li class="active"><a>' . $item->text . '</a></li>';
}
else
{
	// Doesn't match any other condition, render disabled item.
	echo '<li><a class="disabled">' . $item->text . '</a></li>';
}
