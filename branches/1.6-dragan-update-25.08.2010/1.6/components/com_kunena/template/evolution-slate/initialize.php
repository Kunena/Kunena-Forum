<?php
/**
* @version $Id: initialize.php 2979 2010-07-10 14:03:27Z mahagr $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

$document = JFactory::getDocument();
$template = KunenaFactory::getTemplate();
$this->params = $template->params;
// Template requires Mootools 1.2 framework
// On systems running < J1.5.19 this requires the mootools12 system plugin
JHTML::_ ( 'behavior.mootools' );
$lang =& JFactory::getLanguage();
$lang->load( 'com_kunena.tpl_evolution-slate', KUNENA_PATH_TEMPLATE.DS.'evolution-slate/' );
// We load smoothbox library
CKunenaTools::addScript( KUNENA_DIRECTURL . 'js/slimbox/slimbox-min.js' );

// New Kunena JS for default template
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
$mediaurl = JURI::base() . "components/com_kunena/template/evolution-slate/media";
$imagesurl = JURI::base() . "components/com_kunena/template/evolution-slate/images";
$cssurl = JURI::base() . "components/com_kunena/template/evolution-slate/css/";
$styles = <<<EOF
#Kunena span.klatest-avatar {
	display: {$this->params->get('avatarcatShow')};
}
#Kunena .kfooter {
	display: {$this->params->get('timefooterShow')};
}
#Kunena .kicon-profile {
	background-image: url("{$mediaurl}/iconsets/profile/{$this->params->get('profileIconset')}/default.png");
}
#Kunena #kbbcode-toolbar li a,
#Kunena #kattachments a {
	background-image:url("{$mediaurl}/iconsets/editor/{$this->params->get('editorIconset')}/default.png");
}
#Kunena div#tk-topmenu ul.menu li.item{$this->params->get('homeID')},
#Kunena div#tk-topmenu ul.menu li.item{$this->params->get('userpanelID')},
#Kunena div#tk-topmenu ul.menu li.item{$this->params->get('favoritesID')},
#Kunena div#tk-topmenu ul.menu li.item{$this->params->get('helpID')},
#Kunena div#tk-topmenu ul.menu li.item{$this->params->get('playID')} {
	border-bottom: none!important;
	float: right;
	padding: 0 10px;
	height: 55px;
	padding: 1px 10px 2px 5px;
	margin: 10px 0 -20px 0;
	list-style: none;
	border: none;
}
#Kunena div#tk-topmenu ul.menu li .item {
	background: none!important;
}
#Kunena div#tk-topmenu ul.menu li.item{$this->params->get('homeID')} {
	background: url("{$imagesurl}/home.png") no-repeat 50% top !important;
}
#Kunena div#tk-topmenu ul.menu li.item{$this->params->get('userpanelID')} {
	background: url("{$imagesurl}/userpanel.png") no-repeat 50% top !important;
}
#Kunena div#tk-topmenu ul.menu li.item{$this->params->get('favoritesID')} {
	background: url("{$imagesurl}/favorite.png") no-repeat 50% top !important;
}
#Kunena div#tk-topmenu ul.menu li.item{$this->params->get('helpID')} {
	background: url("{$imagesurl}/help.png") no-repeat 50% top !important;
}
#Kunena div#tk-topmenu ul.menu li.item{$this->params->get('playID')} {
	background: url("{$imagesurl}/play.png") no-repeat 50% top !important;
}
#Kunena .kicon-button {
	background-image: url("{$mediaurl}/iconsets/buttons/{$this->params->get('buttonIconset')}/default.png") !important;
}
EOF;
$document->addStyleDeclaration($styles);
?>