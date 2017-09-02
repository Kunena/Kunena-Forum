<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

if (KunenaConfig::getInstance()->lightbox == 0)
{
	return false;
}

$template = KunenaTemplate::getInstance();

if ($template->params->get('lightboxColor') == 'white')
{
	$this->addStyleSheet('assets/css/fancybox.white.css');
}
else
{
	$this->addStyleSheet('assets/css/fancybox.black.css');
}

$this->addScript('assets/js/fancybox.js');
$this->addScript('assets/js/fancybox.settings.js');

?>
