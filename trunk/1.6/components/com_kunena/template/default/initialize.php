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
// On systems running < J1.5.19 this requires the mootools12 system plugin
JHTML::_ ( 'behavior.mootools' );

// We load smoothbox library
CKunenaTools::addScript( KUNENA_DIRECTURL . 'js/slimbox/slimbox-min.js' );

// New Kunena JS for default template
// TODO: Need to check if selected template has an override
CKunenaTools::addScript ( KUNENA_DIRECTURL . 'template/default/js/default-min.js' );

if (file_exists ( KUNENA_JTEMPLATEPATH .DS. 'css' .DS. 'kunena.forum-min.css' )) {
	// Load css from Joomla template
	CKunenaTools::addStyleSheet ( KUNENA_JTEMPLATEURL . 'css/kunena.forum-min.css' );
} else if (file_exists ( KUNENA_PATH_TEMPLATE .DS. 'css' .DS. 'kunena.forum-min.css' )){
	// Load css from the current template
	CKunenaTools::addStyleSheet ( KUNENA_TMPLTCSSURL );
} else {
	// Load css from default template
	CKunenaTools::addStyleSheet ( KUNENA_DIRECTURL . 'template/default/css/kunena.forum-min.css' );
}