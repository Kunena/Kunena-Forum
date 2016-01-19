<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Pagination
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$item = $this->item;

if (!is_null($item->base))
{
	// Check if the item can be clicked.
	$limit = 'limitstart.value=' . (int) $item->base;
	echo '<li><a href="' . $item->link . '" title="' . $item->text . '">' . $item->text . '</a></li>';
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
