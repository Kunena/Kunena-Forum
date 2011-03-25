<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$document = JFactory::getDocument();
$template = KunenaFactory::getTemplate();
$template->categoryIcons = array('knonew', 'knew');

// Template requires Mootools 1.2 framework
$template->loadMootools();

// New Kunena JS for default template
$template->addScript ( 'js/default.js' );

// We load mediaxboxadvanced library
$template->addScript( 'js/mediaboxAdv.js' );
$template->addStyleSheet ( 'css/mediaboxAdv.css');

// Load css from default template
$template->addStyleSheet ( 'css/global.css' );
$template->addStyleSheet ( 'css/design.css' );
