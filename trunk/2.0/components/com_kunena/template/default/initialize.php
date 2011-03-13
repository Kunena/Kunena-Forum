<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2011 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/
defined( '_JEXEC' ) or die();

$app = JFactory::getApplication();
$document = JFactory::getDocument();
$template = KunenaFactory::getTemplate();

// Template requires Mootools 1.2 framework
$template->loadMootools();

// Toggler language strings
$document->addScriptDeclaration('// <![CDATA[
var kunena_toggler_close = "'.JText::_('COM_KUNENA_TOGGLER_COLLAPSE').'";
var kunena_toggler_open = "'.JText::_('COM_KUNENA_TOGGLER_EXPAND').'";
// ]]>');

// We load mediaxboxadvanced library
$template->addStyleSheet ( 'css/mediaboxAdv-min.css');
$template->addScript( 'js/mediaboxAdv-min.js' );

// New Kunena JS for default template
$template->addScript ( 'js/default-min.js' );

$skinner = $template->params->get('enableSkinner', 0);

if (file_exists ( JPATH_ROOT .DS. "templates" .DS. $app->getTemplate() .DS. 'css' .DS. 'kunena.forum.css' )) {
	// Load css from Joomla template
	KunenaAddStyleSheet ( JURI::root(true). "templates/".$app->getTemplate().'css/kunena.forum-min.css' );
	if ($skinner && file_exists ( JPATH_ROOT. "templates/".$app->getTemplate().'css/kunena.skinner.css' )){
		KunenaAddStyleSheet ( JURI::root(true). "templates/".$app->getTemplate().'css/kunena.skinner-min.css' );
	} elseif (!$skinner && file_exists ( JPATH_ROOT. "templates/".$app->getTemplate().'css/kunena.default.css' )) {
		KunenaAddStyleSheet ( JURI::root(true). "templates/".$app->getTemplate().'css/kunena.default-min.css' );
	}
} else {
	// Load css from default template
	$template->addStyleSheet ( 'css/kunena.forum-min.css' );
	if ($skinner) {
		$template->addStyleSheet ( 'css/kunena.skinner-min.css' );
	} else {
		$template->addStyleSheet ( 'css/kunena.default-min.css' );
	}
}
$cssurl = JURI::root(true) . '/components/com_kunena/template/default/css';
?>
<!--[if lte IE 7]>
<link rel="stylesheet" href="<?php echo $cssurl; ?>/kunena.forum.ie7.css" type="text/css" />
<![endif]-->
<?php
$mediaurl = JURI::root(true) . '/components/com_kunena/template/default/media';

$styles = <<<EOF
	/* Kunena Custom CSS */
EOF;

$forumHeader = $template->params->get('forumHeadercolor', $skinner ? '' : '#5388B4');

if ($forumHeader) {
	$styles .= <<<EOF

	#Kunena div.kblock > div.kheader { background: {$forumHeader} !important; }
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
	#Kunena #ktab ul.menu li.active a { background-color: {$activeTab} !important; }
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


$styles .= <<<EOF
	#Kunena .kicon-profile { background-image: url("{$mediaurl}/iconsets/profile/{$template->params->get('profileIconset', 'default')}/default.png"); }
	#Kunena .kicon-button { background-image: url("{$mediaurl}/iconsets/buttons/{$template->params->get('buttonIconset', 'default')}/default.png"); }
	#Kunena #kbbcode-toolbar li a,#Kunena #kattachments a { background-image:url("{$mediaurl}/iconsets/editor/{$template->params->get('editorIconset', 'default')}/default.png"); }
	/* End of Kunena Custom CSS */
EOF;

$document->addStyleDeclaration($styles);

/**
 * Wrapper to addStyleSheet
 *
 */
function KunenaAddStyleSheet($filename) {

	$document = JFactory::getDocument ();
	$config = KunenaFactory::getConfig ();

	if (JDEBUG || $config->debug || KunenaForum::isSvn()) {
		// If we are in debug more, make sure we load the unpacked css
		$filename = preg_replace ( '/\-min\./u', '.', $filename );
	}

	return $document->addStyleSheet ( $filename );
}
