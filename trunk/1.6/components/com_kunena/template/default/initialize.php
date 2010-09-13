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
defined( '_JEXEC' ) or die();

$document = JFactory::getDocument();
$template = KunenaFactory::getTemplate();
$this->params = $template->params;
// Template requires Mootools 1.2 framework
// On systems running < J1.5.19 this requires the mootools12 system plugin
JHTML::_ ( 'behavior.mootools' );

// We load smoothbox library
CKunenaTools::addScript( KUNENA_DIRECTURL . 'js/slimbox/slimbox-min.js' );

// New Kunena JS for default template
// TODO: Need to check if selected template has an override
CKunenaTools::addScript ( KUNENA_DIRECTURL . 'template/default/js/default-min.js' );

if (file_exists ( KUNENA_JTEMPLATEPATH .DS. 'css' .DS. 'kunena.forum.css' )) {
	// Load css from Joomla template
	CKunenaTools::addStyleSheet ( KUNENA_JTEMPLATEURL . 'css/kunena.forum-min.css' );
} else if (file_exists ( KUNENA_ABSTMPLTPATH .DS. 'css' .DS. 'kunena.forum.css' )){
	// Load css from the current template
	CKunenaTools::addStyleSheet ( KUNENA_TMPLTCSSURL );
} else {
	// Load css from default template
	CKunenaTools::addStyleSheet ( KUNENA_DIRECTURL . 'template/default/css/kunena.forum-min.css' );
}
$cssurl = JURI::base() . "components/com_kunena/template/default/css";
?>
<!--[if IE 7]>
<link rel="stylesheet" href="<?php echo $cssurl; ?>/kunena.forum.ie7.css" type="text/css" />
<![endif]-->
<?php
$mediaurl = JURI::base() . "components/com_kunena/template/default/media";
$styles = <<<EOF
	/* Kunena Custom CSS */
	#Kunena div.kheader { background: {$this->params->get('forumHeadercolor', '#5388B4')} }
	#Kunena div.kannouncement div.kheader { background: {$this->params->get('announcementHeadercolor', '#5388B4')} }
	#Kunena div#kannouncement .kanndesc { background: {$this->params->get('announcementBoxbgcolor', '#ffffff')} }
	#Kunena div.kfrontstats div.kheader { background: {$this->params->get('frontstatsHeadercolor', '#5388B4')} }
	#Kunena div.kwhoisonline div.kheader { background: {$this->params->get('whoisonlineHeadercolor', '#5388B4')} }
	#Kunena .kicon-profile { background-image: url("{$mediaurl}/iconsets/profile/{$this->params->get('profileIconset', 'default')}/default.png"); }
	#Kunena .kicon-button { background-image: url("{$mediaurl}/iconsets/buttons/{$this->params->get('buttonIconset', 'default')}/default.png"); }
	#Kunena #kbbcode-toolbar li a,#Kunena #kattachments a { background-image:url("{$mediaurl}/iconsets/editor/{$this->params->get('editorIconset', 'default')}/default.png"); }
	/* End of Kunena Custom CSS */
EOF;

$document->addStyleDeclaration($styles);