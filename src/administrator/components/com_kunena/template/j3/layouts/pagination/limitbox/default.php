<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Template
 * @subpackage      Layouts.Pagination
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$app    = Factory::getApplication();
$limits = array();

// Make the option list.
for ($i = 5; $i <= 30; $i += 5)
{
	$limits[] = HTMLHelper::_('select.option', "$i");
}

$limits[] = HTMLHelper::_('select.option', '50', Text::_('J50'));
$limits[] = HTMLHelper::_('select.option', '100', Text::_('J100'));
$limits[] = HTMLHelper::_('select.option', '0', Text::_('JALL'));

$selected = $this->pagination->limit == $this->pagination->total ? 0 : $this->pagination->limit;

// Build the select list.
echo HTMLHelper::_(
	'select.genericlist',
	$limits,
	$this->pagination->prefix . 'limit',
	'class="inputbox input-mini" size="1" onchange="Joomla.submitform();"',
	'value',
	'text',
	$selected
);
