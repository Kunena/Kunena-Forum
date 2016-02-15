<?php
/**
* Kunena Component
* @package Kunena.Template.Blue_Eagle
*
* @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link https://www.kunena.org
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
if ($rtl) {
	$template->addStyleSheet ( 'css/kunena.forum.rtl.css');
}

$skinner = $template->params->get('enableSkinner', 0);

if (is_file(JPATH_ROOT . "/templates/{$app->getTemplate()}/css/kunena.forum.css")) {
	// Load css from Joomla template
	CKunenaTools::addStyleSheet ( JUri::root(true). "/templates/{$app->getTemplate()}/css/kunena.forum.css" );
	if ($skinner && is_file(JPATH_ROOT. "/templates/{$app->getTemplate()}/css/kunena.skinner.css")){
		CKunenaTools::addStyleSheet ( JUri::root(true). "/templates/{$app->getTemplate()}/css/kunena.skinner.css" );
	} elseif (!$skinner && is_file(JPATH_ROOT. "/templates/{$app->getTemplate()}/css/kunena.default.css")) {
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

$forumHeader = $template->params->get('forumHeadercolor', $skinner ? '' : '#5388B4');

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

$forumLink = $template->params->get('forumLinkcolor', $skinner ? '' : '#5388B4');

if ($forumLink) {
	$styles .= <<<EOF
	#Kunena a:link,
	#Kunena a:visited,
	#Kunena a:active {color: {$forumLink};}
	#Kunena a:focus {outline: none;}
EOF;
}

$announcementHeader = $template->params->get('announcementHeadercolor', $skinner ? '' : '#5388B4');

if ($announcementHeader) {
	$styles .= <<<EOF
	#Kunena div.kannouncement div.kheader { background: {$announcementHeader} !important; }
EOF;
}

$announcementBox = $template->params->get('announcementBoxbgcolor', $skinner ? '' : '#FFFFFF');

if ($announcementBox) {
	$styles .= <<<EOF
	#Kunena div#kannouncement .kanndesc { background: {$announcementBox}; }
EOF;
}

$frontStatsHeader = $template->params->get('frontstatsHeadercolor', $skinner ? '' : '#5388B4');

if ($frontStatsHeader) {
	$styles .= <<<EOF
	#Kunena div.kfrontstats div.kheader { background: {$frontStatsHeader} !important; }
EOF;
}

$onlineHeader = $template->params->get('whoisonlineHeadercolor', $skinner ? '' : '#5388B4');

if ($onlineHeader) {
	$styles .= <<<EOF
	#Kunena div.kwhoisonline div.kheader { background: {$onlineHeader} !important; }
EOF;
}

$inactiveTab = $template->params->get('inactiveTabcolor', $skinner ? '' : '#737373');

if ($inactiveTab) {
	$styles .= <<<EOF
	#Kunena #ktab a { background-color: {$inactiveTab} !important; }
EOF;
}

$activeTab = $template->params->get('activeTabcolor', $skinner ? '' : '#5388B4');

if ($activeTab) {
	$styles .= <<<EOF
	#Kunena #ktab ul.menu li.active a,#Kunena #ktab li#current.selected a { background-color: {$activeTab} !important; }
EOF;
}

$hoverTab = $template->params->get('hoverTabcolor', $skinner ? '' : '#5388B4');

if ($hoverTab) {
	$styles .= <<<EOF
	#Kunena #ktab a:hover { background-color: {$hoverTab} !important; }
EOF;
}

$topBorder = $template->params->get('topBordercolor', $skinner ? '' : '#5388B4');

if ($topBorder) {
	$styles .= <<<EOF
	#Kunena #ktop { border-color: {$topBorder} !important; }
EOF;
}

$inactiveFont = $template->params->get('inactiveFontcolor', $skinner ? '' : '#FFFFFF');

if ($inactiveFont) {
	$styles .= <<<EOF
	#Kunena #ktab a span { color: {$inactiveFont} !important; }
EOF;
}
$activeFont = $template->params->get('activeFontcolor', $skinner ? '' : '#FFFFFF');

if ($activeFont) {
	$styles .= <<<EOF
	#Kunena #ktab #current a span { color: {$activeFont} !important; }
EOF;
}

$toggleButton = $template->params->get('toggleButtoncolor', $skinner ? '' : '#5388B4');

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
