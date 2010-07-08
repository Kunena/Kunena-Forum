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
	$template = KunenaFactory::getTemplate();
	$this->params = $template->params;
// Template requires Mootools 1.2 framework
// On systems running < J1.5.18 this requires the mootools12 system plugin
JHTML::_ ( 'behavior.framework' );

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
?>
<style type="text/css">
#Kunena h1,
#Kunena h2 {
	background: <?php echo $this->params->get('forumHeadercolor')?>;
}
#Kunena div.kannouncement h2 {
	background: <?php echo $this->params->get('announcementHeadercolor')?>;
}
#Kunena div#kannouncement .kanndesc {
	background: <?php echo $this->params->get('announcementBoxbgcolor')?>;
}
#Kunena div.kfrontstats h2 {
	background: <?php echo $this->params->get('frontstatsHeadercolor')?>;
}
#Kunena div.kwhoisonline h2 {
	background: <?php echo $this->params->get('whoisonlineHeadercolor')?>;
}
#Kunena div.kiconrow span,
#Kunena div.kiconprofile span {
	background-image: url("components/com_kunena/template/default/media/iconsets/profile/<?php echo $this->params->get('profileIconset')?>/default.png");
}
#Kunena a.kbuttoncomm span.reply, 
#Kunena a.kbuttoncomm span.quote,
#Kunena a.kbuttoncomm span.newtopic,
#Kunena a.kbuttonuser span.thankyou,
#Kunena a.kbuttonuser span.favorite,
#Kunena a.kbuttonuser span.subscribe,
#Kunena a.kbuttonuser span.markread,
#Kunena a.kbuttonuser span.report,
#Kunena a.kbuttonmod span.merge,
#Kunena a.kbuttonmod span.edit,
#Kunena a.kbuttonmod span.delete,
#Kunena a.kbuttonmod span.permdelete,
#Kunena a.kbuttonmod span.undelete,
#Kunena a.kbuttonmod span.move,
#Kunena a.kbuttonmod span.sticky,
#Kunena a.kbuttonmod span.lock,
#Kunena a.kbuttonmod span.split,
#Kunena a.kbuttonmod span.approve,
#Kunena a.kbuttonmod span.moderate,
#Kunena a.kbuttonmod span span,
#Kunena a.kbuttonuser span span,
#Kunena a.kbuttongen span span,
#Kunena a.kbuttoncomm span span,
#Kunena a.kbuttonuser,
#Kunena a.kbuttoncomm,
#Kunena a.kbuttonmod,
#Kunena a.kbuttongen {
	background-image: url("components/com_kunena/template/default/media/iconsets/buttons/<?php echo $this->params->get('buttonIconset')?>/default.png");
}
#Kunena #kbbcode-toolbar li a,
#Kunena #kattachments a {
	background-image:url("components/com_kunena/template/default/media/iconsets/editor/<?php echo $this->params->get('editorIconset')?>/default.png");
}
</style>