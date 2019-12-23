<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

if (KunenaConfig::getInstance()->lightbox != 1)
{
	return false;
}

$this->addStyleSheet('assets/css/fancybox.css');
$this->addScript('assets/js/fancybox-min.js');
