<?php
/**
* @version $Id: initialize.php 2751 2010-06-16 15:46:57Z mahagr $
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
$imagesurl = JURI::base() . "components/com_kunena/template/".$template->name."/images/";
$iconsurl = JURI::base() . "media/kunena/icons";
$cssurl = JURI::base() . "components/com_kunena/template/".$template->name."/css";
?>
<!--[if lte IE 8]>
<link rel="stylesheet" href="<?php echo $cssurl; ?>/ie.css" type="text/css" />
<![endif]-->
<?php

if ($this->params->get('headerBorder') != '') {
$styles = <<<EOF
#kunena div#ktab-flat .moduletable,
#kunena li.header,
.tk-menu-special {
	border-bottom-color: {$this->params->get('headerBorder')};
	border-bottom-style: solid;
	border-bottom-width: 3px;
}
#kunena div#ktab-flat .moduletable {
	background-color: {$this->params->get('headerBorder')};
}

EOF;
}
if ($this->params->get('countcolumnShow') == '0') {
$styles .= <<<EOF
#kunena ul.topiclist dt { width: 65%; }
EOF;
}

if ($this->params->get('avborderColor') == '1') {
$styles .= <<<EOF
#kunena .kuser-admin img { border-color: {$this->params->get('userAdmin')} !important }
#kunena .kuser-globalmod img { border-color: {$this->params->get('userGlmoderator')} !important }
#kunena .kuser-moderator img { border-color: {$this->params->get('userModerator')} !important }
#kunena .kuser-user img { border-color: {$this->params->get('userUser')} !important }
#kunena .kuser-guest img { border-color: {$this->params->get('userGuest')} !important }
EOF;
}
if ($this->params->get('showStyle') == 'nopreset') :
$styles .= <<<EOF
#mbImage div.tk-mb-header-pm,
#mbImage div.tk-mb-header-pmread,
#mbImage div.tk-mb-header-logout,
#mbImage div.tk-mb-header-search,
#mbImage div.tk-mb-header-login,
#mbImage div.tk-mb-header-register {
	background: {$this->params->get('headerBg')};
	background: -webkit-gradient(linear, 0 0, 0 bottom, from({$this->params->get('headerBg')}), to({$this->params->get('headerBg')}));
	background: -moz-linear-gradient({$this->params->get('headerBg')}, {$this->params->get('headerBg')});
	background: linear-gradient({$this->params->get('headerBg')}, {$this->params->get('headerBg')});
EOF;
endif;

$styles .= <<<EOF
#kunena div.kmsgimage img,
#kunena div.kmsgsignature img {
	max-width:{$this->params->get('maxwidthPostimage')}px;
}
#kunena #ktab li:hover,
#kunena a.kicon-button:hover,
#kunena input.kbutton:hover,
#kunena span.tk-editlink:hover {
	cursor: pointer;
}
#kunena dd.tk-subcategories {
	border-top:1px dotted #DFDFDF/*borderColor*/;
}
#kunena img.klist-avatar,
#kunena img.kavatar {
	/*border:1px solid #DFDFDF*//*borderColor*/;
}
#kunena .ktopic-taglist {
    background-color:{$this->params->get('headerBorder')};
}
#kunena .ktopic-taglist li a {
    background-color:{$this->params->get('buttonsBg')};
}

#kunena .kuser-admin { color: {$this->params->get('userAdmin')} !important }
#kunena .kuser-globalmod { color: {$this->params->get('userGlmoderator')} !important }
#kunena .kuser-moderator { color: {$this->params->get('userModerator')} !important }
#kunena .kuser-user { color: {$this->params->get('userUser')} !important }
#kunena .kuser-guest { color: {$this->params->get('userGuest')} !important }

#kunena ul.forums li.sticky {
	background-color: {$this->params->get('stickyColor')} !important
}

#kunena li.row,
#kunena li.rowfull,
#kunena ul.forums li.sticky {
	margin:0;
}

#kunena div.tk-msgcontent {
	background-color:#FFFFFF/*msgBg*/;
	min-height:170px;
	padding:5px;
}
#kunena .tk-tools-options ul.tk-tools-container {
	background-color:#FFFFFF/*boxBg*/;
	border-color:#DFDFDF/*borderColor*/;
}

#kunena li.categorysuffix-{$this->params->get('category1ID')} { background-color: {$this->params->get('category1Color')} !important }
#kunena li.categorysuffix-{$this->params->get('category2ID')} { background-color: {$this->params->get('category2Color')} !important }
#kunena li.categorysuffix-{$this->params->get('category3ID')} { background-color: {$this->params->get('category3Color')} !important }
#kunena li.categorysuffix-{$this->params->get('category4ID')} { background-color: {$this->params->get('category4Color')} !important }
#kunena li.categorysuffix-{$this->params->get('category5ID')} { background-color: {$this->params->get('category5Color')} !important }
#kunena li.categorysuffix-{$this->params->get('category6ID')} { background-color: {$this->params->get('category6Color')} !important }
#kunena li.categorysuffix-{$this->params->get('category7ID')} { background-color: {$this->params->get('category7Color')} !important }
#kunena li.categorysuffix-{$this->params->get('category8ID')} { background-color: {$this->params->get('category8Color')} !important }
#kunena li.categorysuffix-{$this->params->get('category9ID')} { background-color: {$this->params->get('category9Color')} !important }
#kunena li.categorysuffix-{$this->params->get('category10ID')} { background-color: {$this->params->get('category10Color')} !important }

#kunena li.header dl.cat_{$this->params->get('section1ID')} { background-image: url('{$iconsurl}/{$this->params->get('section1icon')}'); }
#kunena li.header dl.cat_{$this->params->get('section2ID')} { background-image: url('{$iconsurl}/{$this->params->get('section2icon')}'); }
#kunena li.header dl.cat_{$this->params->get('section3ID')} { background-image: url('{$iconsurl}/{$this->params->get('section3icon')}'); }
#kunena li.header dl.cat_{$this->params->get('section4ID')} { background-image: url('{$iconsurl}/{$this->params->get('section4icon')}'); }
#kunena li.header dl.cat_{$this->params->get('section5ID')} { background-image: url('{$iconsurl}/{$this->params->get('section5icon')}'); }

#kunena div.sectionsuffix-{$this->params->get('section1ID')} {background-color: {$this->params->get('section1bgColor')}!important;}
#kunena div.sectionsuffix-{$this->params->get('section2ID')} {background-color: {$this->params->get('section2bgColor')}!important;}
#kunena div.sectionsuffix-{$this->params->get('section3ID')} {background-color: {$this->params->get('section3bgColor')}!important;}
#kunena div.sectionsuffix-{$this->params->get('section4ID')} {background-color: {$this->params->get('section4bgColor')}!important;}
#kunena div.sectionsuffix-{$this->params->get('section5ID')} {background-color: {$this->params->get('section5bgColor')}!important;}

#kunena span.solved { color: {$this->params->get('solvedTextcolor')}; background-color: {$this->params->get('solvedColor')};}
#kunena span.stickystatus { color: {$this->params->get('stickystatusTextcolor')}; background-color: {$this->params->get('stickystatusColor')};}
#kunena span.closed { color: {$this->params->get('closedTextcolor')}; background-color: {$this->params->get('closedColor')};}

#kunena div#ktab li.item{$this->params->get('topmenu1Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu1Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu1Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu2Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu2Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu2Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu3Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu3Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu3Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu4Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu4Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu4Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu5Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu5Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu5Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu6Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu6Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu6Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu7Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu7Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu7Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu8Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu8Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu8Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu9Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu9Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu9Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu10Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu10Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu10Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu11Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu11Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu11Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu12Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu12Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu12Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu13Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu13Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu13Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu14Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu14Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu14Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu15Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu15Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu15Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu16Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu16Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu16Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu17Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu17Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu17Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu18Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu18Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu18Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu19Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu19Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu19Itemidimage')}");
}
#kunena div#ktab li.item{$this->params->get('topmenu20Itemid')} span,#kunena div#ktab-flat li.item{$this->params->get('topmenu20Itemid')} span {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu20Itemidimage')}");
}
#kunena div#ktab li span,
#kunena div#ktab-flat li span {
	background-position: left center;
	background-repeat: no-repeat;
}

#kunena div.forumlist,
div.kbbcode-preview-right,
div.kbbcode-preview-bottom,
#kunena div.attachttitle,
#kunena div.kmsgattach h4,
#kunena div.tk-msgcontent div.kmsgtext-quote,
#kbbcode-colorpalette,
#kbbcode-image-options,
#kbbcode-link-options,
#kbbcode-video-options,
#kbbcode-map-options,
#kbbcode-code-options,
#kpoll-hide-not-allowed {
	background-color: #FFFFFF/*boxBg*/;
}
#kunena li.row:hover { background-color: #F4F4F4; }
#kunena div.forumlist,
.tk-login-special {
	border-width:1px;
	border-style:solid;
}
.pane-sliders .panel {
	overflow:hidden;
	height:auto;
}

#kunena div.inner-odd,
#kunena div.inner-even {
}

#kunena li.row,
#kunena li.rowfull {
	background-color: transparent/*rowBg*/;
}
#kunena span.tk-register-agree a,
#kunena span.tk-register-cancel a {
	text-shadow:0px -1px 0px {$this->params->get('buttonstextshadowColor')};
}
#kunena input.kbutton,
#kunena a.kicon-button span span {
	border:0;
	text-shadow:0px -1px 0px {$this->params->get('buttonstextshadowColor')};
	padding:0 7px 0 24px;
}
#kunena a.kicon-button span {
	text-shadow:1px 1px 1px {$this->params->get('buttonstextshadowColor')};
	padding:0;
	/*background-position: 2px center!important;*/
}

.pane-sliders .panel {
	background-color: #FFFFFF/*boxBg*/;
	border: 1px solid #DFDFDF/*borderColor*/;
}

#kunena .tk-pagination a,
#kunena .tk-pagination a:link,
#kunena .tk-pagination a:visited {
	border: 1px solid #DFDFDF/*borderColor*/;
}
#kunena .tk-pagination li.active {
	border: 1px solid {$this->params->get('buttonsBg')};
	background-color:{$this->params->get('buttonsBg')};
	color:{$this->params->get('buttonstextColor')};
}
#kunena .pagination span a,
#kunena .pagination span a:link,
#kunena .pagination span a:visited/*,
#kunena .pagination a*/ {
	border-color:#DFDFDF/*borderColor*/;
}
#kunena .pagination span a,
#kunena .pagination span a:link,
#kunena .pagination span a:visited,
#kunena .kpagination a {
	background-color:#FFFFFF/*rowBg*/;
}

#kunena span.tk-register-agree a,
#kunena span.tk-register-cancel a {
	padding:4px 5px 4px 22px !important;
}
#kunena a.kicon-button,
#kunena a.tk-insert-button,
#kunena a.tk-remove-button,
#kunena span.tk-register-agree a,
#kunena span.tk-register-cancel a,
#mbCenter input.tk-search-button,
#kunena input.tk-search-button,
#kunena .tk-submit-button,
/*#kunena input.tk-submit-button,*/
#kunena input.tk-save-button,
#kunena button.tk-save-button,
/*#kunena button.tk-submit-button,*/
#kunena button.tk-add-button,
#kunena input.tk-reset-button,
#kunena .tk-cancel-button,
/*#kunena button.tk-cancel-button,*/
#kunena input.tk-login-button,
#mbCenter input.tk-login-button,
#kunena input.tk-logout-button,
#mbCenter button.tk-logout-button,
#kunena input.tk-go-button,
#kunena input.tk-preview-button,
#kunena input.tk-add-button,
#kunena input.tk-markread-button/*,
#kunena div.tk-ann-links a*/ {
	vertical-align: middle;
	margin:0 0 0 2px;
	padding:0px 5px 0px 22px;
	color:{$this->params->get('buttonstextColor')};
	text-shadow: 0 1px 0{$this->params->get('buttonstextshadowColor')};
	background-color: {$this->params->get('buttonsBg')};
	background-repeat: no-repeat;
	background-position: 3px center;
	border:1px solid {$this->params->get('buttonsborderColor')};
	color:{$this->params->get('buttonstextColor')};
}

#mbCenter input.tk-login-button,
#mbCenter button.tk-logout-button,
#kunena input.tk-go-button,
#kunena input.tk-search-button,
#mbCenter input.tk-search-button,
#kunena input.tk-markread-button {
	background-image: url("{$imagesurl}icons/submit.png");
}
#kunena input.tk-cancel-button,
#mbCenter input.tk-cancel-button { background-image: url("{$imagesurl}icons/closelabel.png");}
#kunena input.tk-reset-button { background-image: url("{$imagesurl}icons/pill.png");}
#kunena input.tk-preview-button { background-image: url("{$imagesurl}icons/preview.png");}
#kunena input.tk-add-button,#kunena button.tk-add-button { background-image: url("{$imagesurl}icons/add.png");}
#kunena a.tk-insert-button { background-image: url("{$imagesurl}icons/insert.png");}
#kunena a.tk-remove-button { background-image: url("{$imagesurl}icons/remove.png");}

#kunena input.tk-go-button,
#kunena input.tk-search-button {
    float: right;
    font-size: 0.917em;
    margin: 1px 0 0 5px;
}

#kunena #ktab li a span {
color:#FFFFFF;
font-weight:normal;
text-shadow:0 -1px 0 rgba(0, 0, 0, 0.5);

}
#kunena a.tk-loginlink,
#kunena span.tk-editlink,
#kunena #ktab li:focus a,
#kunena #ktab li:hover a,
#kunena #ktab li.parent.active a,
#kunena #ktab li.haschild.active a,
#kunena #ktab li#current.active a,
#kunena #ktab li.active a,
#kunena #ktab div.moduletable ul.menu li:hover a {
	background-color: {$this->params->get('headerBorder')};
	margin:6px 3px;
	-moz-box-shadow:0 0 5px {$this->params->get('tabshadowColor')};
	-webkit-box-shadow:0 0 5px {$this->params->get('tabshadowColor')};
	box-shadow:0 0 5px {$this->params->get('tabshadowColor')};
	border-radius: {$this->params->get('tabRadius')}px {$this->params->get('tabRadius')}px 0px 0px;
	-webkit-border-top-right-radius: {$this->params->get('tabRadius')}px;
	-webkit-border-top-left-radius: {$this->params->get('tabRadius')}px;
	-webkit-border-bottom-right-radius: 0px;
	-webkit-border-bottom-left-radius: 0px;
	-moz-border-radius: {$this->params->get('tabRadius')}px {$this->params->get('tabRadius')}px 0px 0px;
}
#kunena #ktab-flat li.active a,
#kunena #ktab-flat li#current.active a,
#kunena #ktab-flat li:focus a,
#kunena #ktab-flat li:hover a {
	background-color: {$this->params->get('headerBorder')};
	margin:0px;
	-moz-box-shadow:0 0 5px {$this->params->get('subshadowColor')};
	-webkit-box-shadow:0 0 5px {$this->params->get('subshadowColor')};
	box-shadow:0 0 5px {$this->params->get('subshadowColor')};
	border-radius: {$this->params->get('tabRadius')}px {$this->params->get('tabRadius')}px {$this->params->get('tabRadius')}px {$this->params->get('tabRadius')}px;
	-webkit-border-top-right-radius: {$this->params->get('tabRadius')}px;
	-webkit-border-top-left-radius: {$this->params->get('tabRadius')}px;
	-webkit-border-bottom-right-radius: {$this->params->get('tabRadius')}px;
	-webkit-border-bottom-left-radius: {$this->params->get('tabRadius')}px;
	-moz-border-radius: {$this->params->get('tabRadius')}px {$this->params->get('tabRadius')}px {$this->params->get('tabRadius')}px {$this->params->get('tabRadius')}px;
}
#kunena div#ktab-flat,
#kunena div#ktab-flat .moduletable {
	border-left: 1px solid {$this->params->get('headerBorder')};
	border-right:1px solid {$this->params->get('headerBorder')};
}
#kunena #ktab-flat li.active a,
#kunena #ktab-flat li#current.active a,
#kunena #ktab-flat li:focus a,
#kunena #ktab-flat li:hover a
#kunena div#ktab-flat a {
	height:17px !important;
}

#kunena #ktab li {
background:none;
}

#kunena div.post ul.topiclist {
	margin: -7px -7px 10px;
}

/* Tooltips */
.tool-tip {
	color:#eee;
	float: left;
	background: #222 !important;
	border: 1px solid #333 !important;
	padding: 5px 10px !important;
	max-width: 400px !important;
	-moz-border-radius: 5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
	border-radius: 5px 5px 5px 5px;
	z-index:999;
}

.tool-title {
   padding: 0;
   margin: 0;
   font-size: 100%;
   font-weight: bold;
   margin-top: -15px !important;
   padding: 15px 0px 0 !important;
   background: none !important;
}

.tool-text {
   font-size: 100%;
   margin: 0;
}

span.tk-view-msgid a {
	color:#ffffff !important;
	letter-spacing: 1px;
}

/*		PM Popup 	*/
#mbOverlay {
    background-color: #000000;
}
#mbCenter {
	background-color: #111111;
    padding: 10px;
	-webkit-border-radius: 10px;
	border-radius: 10px;
	-webkit-box-shadow: 0 0 10px 1px #FFFFFF;
	-moz-box-shadow: 0 0 10px 1px #FFFFFF;
	box-shadow: 0 0 10px 1px #FFFFFF;
}
div.tk-mb-header-pm,
div.tk-mb-header-pmread,
div.tk-mb-header-logout,
div.tk-mb-header-login,
div.tk-mb-header-register,
div.tk-mb-header-search {
	border:0px;
	margin: -10px -10px 0;
	padding: 5px;
	color:#fff;
	font-weight:normal;
}
div.tk-mb-header-login span.tk-mb-first {
	background: url("{$imagesurl}icons/login-link.png") no-repeat left center;
	padding: 1px 1px 1px 25px;
}
div.tk-mb-header-logout span.tk-mb-first {
	background: url("{$imagesurl}icons/user_remove.png") no-repeat left center;
	padding: 1px 1px 1px 25px;
}
div.tk-mb-header-register span.tk-mb-first {
	background: url("{$imagesurl}icons/register-link.png") no-repeat left center;
	padding: 1px 1px 1px 25px;
}
div.tk-mb-header-search span.tk-mb-first {
	background: url("{$imagesurl}icons/search-link.png") no-repeat left center;
	padding: 1px 1px 1px 25px;
}
div.tk-mb-header-pmread span.tk-mb-first {
	background: url("{$imagesurl}icons/email_new.png") no-repeat left center;
	padding: 1px 1px 1px 25px;
}
div.tk-mb-header-pm span.tk-mb-first {
	padding: 1px 1px 1px 25px;
}
#mbBottom {
    background: none repeat scroll 0 0 {$this->params->get('headerBorder')};
    color: #000000 !important;
    padding: 8px 10px 2px;
}
#mbImage {
    background-color: #FFFFFF/*boxBg*/;
}
#mbImage div.tk-mb-header-pm,
#mbImage div.tk-mb-header-pmread,
#mbImage div.tk-mb-header-logout,
#mbImage div.tk-mb-header-search,
#mbImage div.tk-mb-header-login,
#mbImage div.tk-mb-header-register,
#mbImage div#mb_pmread,
#mbImage div#mb_logout,
#mbImage div#mb_login,
#mbImage div#mb_register,
#mbImage div#mb_search {
    display: block !important;
}
#mbImage div.tk-mb-header-pm,
#mbImage div.tk-mb-header-pmread,
#mbImage div.tk-mb-header-logout,
#mbImage div.tk-mb-header-search,
#mbImage div.tk-mb-header-login,
#mbImage div.tk-mb-header-register {
    border-bottom-color: {$this->params->get('headerBorder')};
    border-bottom-width: 3px;
    border-bottom-style: solid;
}

#mbCenter ul {
    padding: 0;
	margin:0;
}
#mbCenter ul li,
#mbCenter ul li input {
    padding: 0;
	margin:0;
}

a#mbCloseLink {
    background-image: url("{$imagesurl}icons/closelabel.png");
    width:16px;
    height:16px;
    display:block;
}
a#mbCloseLink big {
    display:none;
}
#mbImage ul.forums {
    padding: 0;
}
#mbImage ul.forums li {
    list-style-type: none;
    background:none;
    margin: 5px 0;
}
#mbImage ul.forums li.rowfull {
    background: url("{$imagesurl}members-login.png") no-repeat left center;
    padding:5px 0;
}
#mbImage ul.forums li.rowfull dd {
    padding-left:130px;
    margin:0;
    color:#666;
}
#mbImage div.tk-reg,
#mbImage p.information_td {
    color:#666;
}
#mbImage ul.forums li.tk-lostpwdlink,
#mbImage ul.forums li.tk-lostuserlink {
	background: url("{$imagesurl}icons/lightbulb.png") no-repeat left center;
    padding-left:25px;
}

#mbImage ul.forums li input.inputbox,
#mbImage div.tk-reg input.inputbox {
	border:1px solid #DFDFDF/*borderColor*/;
	padding:5px 2px 5px 20px;
}
#mbImage ul.forums li input[type=checkbox],
input[type=checkbox] {
	border:0;
}
#mbImage ul.forums li input.tk-username {
	background-image: url("{$imagesurl}icons/login-link.png");
    background-repeat: no-repeat;
    background-position: 3px center;
    background-color: #FFFFFF/*msgBg*/;
}
#mbImage ul.forums li input.tk-password {
	background-image: url("{$imagesurl}icons/lock.png");
    background-repeat: no-repeat;
    background-position: 3px center;
    background-color: #FFFFFF/*msgBg*/;
}
#mbImage ul.forums li input.tk-submit-button {
	float: right;
	margin-right: 33px;
}
/*		PM Popup 	*/

#mbBottom {
    color: #000000 !important;
    padding: 8px 10px 2px;
}
#mbImage {
    background-color: #FFFFFF/*boxBg*/;
}

a#mbCloseLink {
    background-image: url("{$imagesurl}icons/closelabel.png");
    width:16px;
    height:16px;
    display:block;
}
a.tk-mb-advsearchlink {
    background: url("{$imagesurl}icons/advsearch.png") no-repeat left center transparent;
    padding: 0 0 0 20px;
    min-height:16px;
    display:block;
    margin-top: 4px;
}
a#mbCloseLink big {
    display:none;
}
.tk-mb-avatar img.kavatar {
    border: 1px solid #dfdfdf;
    padding: 2px;
    max-width:50px !important;
    max-height:50px !important;
}
input.tk-mb-submit,
input.tk-mb-cancel {
    background-repeat: no-repeat;
    background-position: 5px center;
    background-color: {$this->params->get('buttonsBg')};
    padding:2px 5px 2px 22px !important;
    border: 1px solid {$this->params->get('buttonsborderColor')};
    color: {$this->params->get('buttonstextColor')};
    text-shadow: {$this->params->get('buttonstextshadowColor')};
    cursor:pointer;
}
input.tk-mb-cancel {
    background-image: url("{$imagesurl}icons/closelabel.png");
}
input.tk-mb-submit {
    background-image: url("{$imagesurl}icons/submit.png");
}
div.tk-mb-header {
	border:0px;
	margin: -10px -10px 0;
	padding: 4px 5px 6px 30px;
	color:#bbb;
	font-weight:bold;
}
div.tk-mb-result {
	padding: 30px 0 0 0
	background: url("{$imagesurl}info.png") no-repeat 5px center transparent;
	text-align:center;
	font-size:25px;
}
div.tk-mb-result span {
	padding: 50px 5px 5px 50px
	margin:30px 0 0 0;
}
a.tk-mb-profile-pmlink {
	float:left;
	width:18px;
	height:16px;
	display:block;
}
#kunena span.tk-pmlink {
	float: right;
	margin: 10px 15px 0 0;
	min-height: 16px;
	min-width: 70px;
}
#kunena span.tk-pmlink a {
	color: #ffffff !important;
	padding: 0 0 0 20px;
	float:right;
	min-height: 16px;
}


#kunena span.tk-pm {
	width:16px;
	height:16px;
	display:block;
}
#kunena span.tk-pm-nonew a {
	background: url({$imagesurl}icons/email_16.png) no-repeat left center transparent;
}
#kunena span.tk-pm-new a {
	background: url({$imagesurl}icons/email_new.png) no-repeat left center transparent;
}
#kunena span.tk-pm-new {
	background: url({$imagesurl}new.gif) no-repeat left center transparent;
}

/*		end PM Popup 	*/

select,
input.kfile-input-textbox,
input.kinputbox[type=text],
input.inputbox[type=text],
input.inputbox[type=password],
textarea,
#kunena ul.edituser dd input.inputbox,
#kunena ul.edituser dd input[type=text],
#kunena div.tk-reg input.inputbox,
#kunena div.tk-reg input[type=text] {
	background-color:#FFFFFF/*msgBg*/;
	border:1px solid #DFDFDF/*borderColor*/;
}

/* RocketTheme fix */
#maincontent-block {
    overflow: hidden;
}
#rt-mainbody ul li {
    background: none;
    margin-bottom: 0px;
}
#kunena ul.edituser dd input#email {
    background:#FFFFFF/*msgBg*/;
    font-size: 110%;
    height: 17px;
    line-height: 10px;
    margin: 0;
    padding-left: 0;
    padding-top: 0;
    width: 271px;
}
/*	End Fix	*/

#kunena div.tk-userlist-user-name h2,
#kunena dd.tk-userlist-content div.tk-pagecount,
#kunena div.tk-search div.tk-pagecount {
	background:#DFDFDF/*borderColor*/;
}
#kunena div.tk-userlist-user-avatar img {
	border: 1px solid #DFDFDF/*borderColor*/;
}
#kunena div.tk-profile-info-body,
#kunena span.kforumbottom,
#kunena span.kforumtop {
	border:1px solid {$this->params->get('buttonsborderColor')};
}
#kunena div.tk-profile-info-body span.tk-info-header,
.shadetabs li a.selected,
.shadetabs li a:hover {
	background-color:{$this->params->get('buttonsBg')};
	color: {$this->params->get('buttonstextColor')};
	text-shadow: 0 1px 0 {$this->params->get('buttonstextshadowColor')};
}
.shadetabs li a.selected,
.shadetabs li a:hover {
	padding-bottom: 10px;
}
.shadetabs li a.selected span,
.shadetabs li a:hover span {
font-size: 0px; line-height: 0%; width: 0px;
position: relative;top: 10px;
border-top: 10px solid {$this->params->get('buttonsBg')};
border-left: 45px solid #FFFFFF/*boxBg*/;
border-right: 45px solid #FFFFFF/*boxBg*/;
}
#kunena div.tk-bubble-left {
    border-right: 20px solid #FFFFFF/*msgBg*/;
    border-top: 20px solid #FFFFFF/*boxBg*/;
    border-bottom: 20px solid #FFFFFF/*boxBg*/;
    display: inline;
    float: left;
    font-size: 0;
    height: 0;
    line-height: 0;
    margin-bottom: -40px;
    position: relative;
    right: 31px;
    top: 40px;
    width: 20px;

}
.shadetabs li a {
	border:1px solid #FFFFFF/*boxBg*/;
}
#system-message dd.message {
	background-image:url("{$imagesurl}icons/info.png");
	background-repeat:no-repeat;
	background-position:5px center;
	background-color:transparent;
}
#kunena span.kforumbottom,
#kunena span.kforumtop {
	background-color: {$this->params->get('buttonsBg')};
	border: 1px solid {$this->params->get('buttonsborderColor')} !important;
}
#kunena .tk-widget-content {
    background-color: #DFDFDF/*borderColor*/;
}
.tk-widget-header {
    background-color: {$this->params->get('headerBorder')};
}
#kunena .kposthead {
    background-color: {$this->params->get('headerBorder')};
}
#kunena .kpostcategory-row, #kunena .kpostmessage-row, #kunena .keditprofile-row {
    border-bottom-color: #DFDFDF/*borderColor*/;
}
#kunena .krow-odd,
#kunena .krow-even {
    background-color: #FFFFFF/*boxBg*/;
}
#kunena li.row:hover span.krss-icon,
#kunena li.row:hover span.kmarkread-icon,
#kunena li.row:hover span.ksubs-icon {
    display:block !important;
}
EOF;
$document->addStyleDeclaration($styles);
