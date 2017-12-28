<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$this->ktemplate = KunenaFactory::getTemplate();
$socialtheme = $this->ktemplate->params->get('socialtheme');
$this->addStyleSheet('assets/css/jssocials.css');
$this->addStyleSheet('assets/css/jssocials-theme-'.$socialtheme.'.css');
$this->addScript('assets/js/jssocials.js');
?>

<div id="share"></div>
