<?php
/**
* Kunena Component
* @package Kunena.Template.Blue_Eagle
*
* @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/
defined( '_JEXEC' ) or die();

$app = JFactory::getApplication();
$document = JFactory::getDocument();
$template = KunenaFactory::getTemplate();

// Template requires Mootools 1.2 framework
$template->loadMootools();

// We load mediaxboxadvanced library only if configuration setting allow it
if ( KunenaFactory::getConfig()->lightbox == 1 ) {
	$template->addStyleSheet ( 'css/mediaboxAdv.css');
	$template->addScript( 'js/mediaboxAdv.js' );
}

// New Kunena JS for default template
$template->addScript ( 'js/default.js' );

$rtl = JFactory::getLanguage()->isRTL();
$skinner = $template->params->get('enableSkinner', 0);

if (file_exists ( JPATH_ROOT . "/templates/{$app->getTemplate()}/css/kunena.forum.css" )) {
	// Load css from Joomla template
	CKunenaTools::addStyleSheet ( JUri::root(true). "/templates/{$app->getTemplate()}/css/kunena.forum.css" );
	if ($skinner && file_exists ( JPATH_ROOT. "/templates/{$app->getTemplate()}/css/kunena.skinner.css" )){
		CKunenaTools::addStyleSheet ( JUri::root(true). "/templates/{$app->getTemplate()}/css/kunena.skinner.css" );
	} elseif (!$skinner && file_exists ( JPATH_ROOT. "/templates/{$app->getTemplate()}/css/kunena.default.css" )) {
		CKunenaTools::addStyleSheet ( JUri::root(true). "/templates/{$app->getTemplate()}/css/kunena.default.css" );
	}
} else {
	$loadResponsiveCSS = $template->params->get('loadResponsiveCSS', 1);
	// Load css from default template
	$template->addStyleSheet ( 'css/kunena.forum.css' );
	if ($loadResponsiveCSS) $template->addStyleSheet ( 'css/kunena.responsive.css' );
	if ($skinner) {
		$template->addStyleSheet ( 'css/kunena.skinner.css' );
	} else {
		$template->addStyleSheet ( 'css/kunena.default.css' );
	}
}
$cssurl = JUri::root(true) . '/components/com_kunena/template/blue_eagle/css';
?>
<!--[if lte IE 7]>
<link rel="stylesheet" href="<?php echo $cssurl; ?>/kunena.forum.ie7.css" type="text/css" />
<![endif]-->
<?php
$mediaurl = JUri::root(true) . "/components/com_kunena/template/{$template->name}/media";

$styles = <<<EOF
	/* Kunena Custom CSS */
EOF;

if ($skinner){
		$forumHeader = ' ';
	} elseif (!$skinner) {
		$forumHeader = $template->params->get('forumHeadercolor');
	}

if ($forumHeader) {
	$styles .= <<<EOF
	#Kunena div.kblock > div.kheader,#Kunena .kblock div.kheader { background: {$forumHeader} !important; }
	#Kunena #ktop { border-color: {$forumHeader}; }
	#Kunena #ktop span.ktoggler { background: {$forumHeader}; }
	#Kunena #ktab a:hover,
	#Kunena #ktab li.Kunena-item-active a	{ background-color: {$forumHeader}; }
	#Kunena #ktab ul.menu li.active a { background-color: {$forumHeader}; }
	#Kunena a:link,
	#Kunena a:visited,
	#Kunena a:active {color: {$forumHeader};}
	#Kunena a:focus {outline: none;}
	#Kunena a:hover {color: #FF0000;}
EOF;
}

if ($skinner) {
		$forumLink = ' ';
	} elseif (!$skinner) {
		$forumLink = $template->params->get('forumlinkcolor');
	}

if ($forumLink) {
	$styles .= <<<EOF
	#Kunena a:link,
	#Kunena a:visited,
	#Kunena a:active {color: {$forumLink};}
	#Kunena a:focus {outline: none;}
EOF;
}

if ($skinner){
		$announcementHeader = ' ';
	} elseif (!$skinner) {
		$announcementHeader = $template->params->get('announcementHeadercolor');
	}

if ($announcementHeader) {
	$styles .= <<<EOF
	#Kunena div.kannouncement div.kheader { background: {$announcementHeader} !important; }
EOF;
}

if ($skinner){
		$announcementBox = ' ';
	} elseif (!$skinner) {
		$announcementBox = $template->params->get('announcementBoxbgcolor');
	}

if ($announcementBox) {
	$styles .= <<<EOF
	#Kunena div#kannouncement .kanndesc { background: {$announcementBox}; }
EOF;
}

if ($skinner){
		$frontStatsHeader = ' ';
	} elseif (!$skinner) {
		$frontStatsHeader = $template->params->get('frontstatsHeadercolor');
	}

if ($frontStatsHeader) {
	$styles .= <<<EOF
	#Kunena div.kfrontstats div.kheader { background: {$frontStatsHeader} !important; }
EOF;
}

if ($skinner){
		$onlineHeader = '';
	} elseif (!$skinner) {
		$onlineHeader = $template->params->get('whoisonlineHeadercolor');
	}

if ($onlineHeader) {
	$styles .= <<<EOF
	#Kunena div.kwhoisonline div.kheader { background: {$onlineHeader} !important; }
EOF;
}

if ($skinner){
		$inactiveTab = '';
	} elseif (!$skinner) {
		$inactiveTab = $template->params->get('inactiveTabcolor');
	}

if ($inactiveTab) {
	$styles .= <<<EOF
	#Kunena #ktab a { background-color: {$inactiveTab} !important; }
EOF;
}

if ($skinner){
		$activeTab = '';
	} elseif (!$skinner) {
		$activeTab = $template->params->get('activeTabcolor');
	}

if ($activeTab) {
	$styles .= <<<EOF
	#Kunena #ktab ul.menu li.active a,#Kunena #ktab li#current.selected a { background-color: {$activeTab} !important; }
EOF;
}

if ($skinner){
		$hoverTab = '';
	} elseif (!$skinner) {
		$hoverTab = $template->params->get('hoverTabcolor');
	}

if ($hoverTab) {
	$styles .= <<<EOF
	#Kunena #ktab a:hover { background-color: {$hoverTab} !important; }
EOF;
}

if ($skinner){
		$topBorder = '';
	} elseif (!$skinner) {
		$topBorder = $template->params->get('topBordercolor');
	}

if ($topBorder) {
	$styles .= <<<EOF
	#Kunena #ktop { border-color: {$topBorder} !important; }
EOF;
}

if ($skinner){
		$inactiveFont = '';
	} elseif (!$skinner) {
		$inactiveFont = $template->params->get('inactiveFontcolor');
	}

if ($inactiveFont) {
	$styles .= <<<EOF
	#Kunena #ktab a span { color: {$inactiveFont} !important; }
EOF;
}

if ($skinner){
		$activeFont = '';
	} elseif (!$skinner) {
		$activeFont = $template->params->get('activeFontcolor');
	}

if ($activeFont) {
	$styles .= <<<EOF
	#Kunena #ktab #current a span { color: {$activeFont} !important; }
EOF;
}

if ($skinner){
		$toggleButton = '';
	} elseif (!$skinner) {
		$toggleButton = $template->params->get('toggleButtoncolor');
	}

if ($toggleButton) {
	$styles .= <<<EOF
	#Kunena #ktop span.ktoggler { background-color: {$toggleButton} !important; }
EOF;
}

$profileIcons = $template->getFile("media/iconsets/profile/{$template->params->get('profileIconset', 'default')}/default.png", true);
$buttonIcons = $template->getFile("media/iconsets/buttons/{$template->params->get('buttonIconset', 'default')}/default.png", true);
$editorIcons = $template->getFile("media/iconsets/editor/{$template->params->get('editorIconset', 'default')}/default.png", true);

$styles .= <<<EOF
	#Kunena .kicon-profile { background-image: url("{$profileIcons}"); }
	#Kunena .kicon-button { background-image: url("{$buttonIcons}") !important; }
	#Kunena #kbbcode-toolbar li a,#Kunena #kattachments a { background-image:url("{$editorIcons}"); }
	/* End of Kunena Custom CSS */
EOF;

$document->addStyleDeclaration($styles);
