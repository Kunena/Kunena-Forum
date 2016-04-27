<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$this->ktemplate = KunenaFactory::getTemplate();
$socialtheme = $this->ktemplate->params->get('socialtheme');
$this->addStyleSheet('css/jssocials.css');
$this->addStyleSheet('css/jssocials-theme-'.$socialtheme.'.css');
$this->addScript('js/jssocials.js');
?>

<div id="share"></div>
