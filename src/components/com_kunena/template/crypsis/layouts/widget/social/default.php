<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

if ($this->me->socialshare == 0 && $this->me->exists())
{
	return false;
}

$socialtheme     = $this->ktemplate->params->get('socialtheme');
$this->addStyleSheet('jssocials.css');
$this->addStyleSheet('jssocials-theme-' . $socialtheme . '.css');
$this->addScript('jssocials.js');
?>

<div id="share"></div>
