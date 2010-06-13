<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

$document = JFactory::getDocument();

// Template requires Mootools 1.2 framework
// On systems running < J1.5.18 this requires the mootools12 system plugin
JHTML::_ ( 'behavior.framework' );

// We load smoothbox library
$document->addScript( KUNENA_DIRECTURL . 'js/slimbox/slimbox.js' );

// New Kunena JS for default template
// TODO: Need to check if selected template has an override
$document->addScript ( KUNENA_DIRECTURL . 'template/default/js/default.js' );

if (file_exists ( KUNENA_JTEMPLATEPATH .DS. 'css' .DS. 'kunena.forum.css' )) {
	// Load css from Joomla template
	$document->addStyleSheet ( KUNENA_JTEMPLATEURL . '/css/kunena.forum.css' );
} else if (file_exists ( KUNENA_PATH_TEMPLATE .DS. 'css' .DS. 'kunena.forum.css' )){
	// Load css from the current template
	$document->addStyleSheet ( KUNENA_TMPLTCSSURL );
} else {
	// Load css from default template
	$document->addStyleSheet ( KUNENA_DIRECTURL . '/template/default/css/kunena.forum.css' );
}