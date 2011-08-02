<?php
$document = JFactory::getDocument();
$template = KunenaFactory::getTemplate();
$this->params = $template->params;
$images = JURI::base() . "components/com_kunena/template/".$template->name."/images/";
if ($this->params->get('showStyle') == 'orange') : $pos = '-90'; $bgcolor = '#ce6815'; $bordercolor = '#eaded5';
elseif ($this->params->get('showStyle') == 'purple') : $pos= '-135'; $bgcolor = '#8a4590'; $bordercolor = '#F9DEFB';
elseif ($this->params->get('showStyle') == 'green') : $pos = '0'; $bgcolor = '#547345'; $bordercolor = '#e3e9c7';
elseif ($this->params->get('showStyle') == 'grey') : $pos = '-45'; $bgcolor = '#547345'; $bordercolor = '#DFDFDF';
elseif ($this->params->get('showStyle') == 'sky') : $pos = '-180'; $bgcolor = '#238DB4'; $bordercolor = '#E8EDF0';
else : $pos = '50'; $bgcolor = $this->params->get('headerBg'); $bordercolor = '#DFDFDF';
endif;

$messagecolor = '#FFFFFF';

$styles = <<<EOF
#kunena a {
	color: {$bgcolor};
	/*font-family: Trebuchet MS;*/
	font-weight: normal;
}
#kunena li.header {
	color:#FFFFFF !important;
	font-size:110%;
	margin:0;
	padding:5px 15px 3px;
	margin:0;
	text-shadow:0 -1px 0 rgba(0, 0, 0, 0.6);
	line-height:1.2em;

}
#kunena li.header dt a {
	color:#FFFFFF !important;
}

#kunena li.header,
#kunena div.tk-menu {
	background-image: url("{$images}header-bg.png");
	background-position: left {$pos}px;
	background-repeat: repeat-x;
	background-color:{$bgcolor};
}
div.tk-mb-header-pm,
div.tk-mb-header-pmread,
div.tk-mb-header-logout,
div.tk-mb-header-search,
div.tk-mb-header-login,
div.tk-mb-header-register {
	background-image: url("{$images}header-bg.png") !important;
	background-position: left {$pos}px;
	background-repeat: repeat-x;
	background-color:{$bgcolor};
}
#kunena .postbody h3,
#kunena .postbody-right h3,
#kunena .postbody-topbot h3 {
	color: {$bgcolor};
}
#kunena li.row,
#kunena .kedit-user-information-row {
	border-bottom: 1px dotted {$bordercolor};
}
#kunena div.tk-msgcontent,
#kunena img.klist-avatar,
#kunena img.kavatar {
	border: 1px solid {$bordercolor};
}
#kunena li.row dd,
#kunena .postbackground-top,
#kunena .kpostbuttonset, .kpostbuttons {
	border-color: {$bordercolor};
	/*line-height: 120%;*/
}
#kunena div.tksection-desc {
	background: {$bordercolor};
}
#kunena div.forumlist,
#kunena #profilebox span.avatar img,
#kunena li.rowfull dd,
#kunena span.tkchild-title,
#kunena .wholegend,
#kunena .whoonline,
#kunena img.usl_avatar,
#kunena li.rowfull:hover dd,
input.kbutton, li.rowbulk input.kbutton,
#kunena input,#kunena select,#kunena textarea,#kunena .inputbox,
#kunena div.post div.inner,
dl#profilebox-post img,
div.avatar-lg img.kavatar,
#kunena span.kavatar img,
#kunena img.klist-avatar,
#kbbcode-toolbar li a,
div.kbbcode-preview-right,
div.kbbcode-preview-bottom,
#kunena .kreview-quote,
#kunena div.msgsignature,
#kunena div.tk-profile-personal-text,
/*#kunena .kpagination a,*/
#kunena div.attachttitle,
#kunena div.kmsgattach h4,
#kunena div.kmsgattach,
#kunena div.tk-msgcontent div.kmsgtext-quote,
#kunena .kattachment-remove,
#kunena .kattachment-insert,
#kunena input.kinputbox,
#kunena textarea.ktxtarea,
#kunena .kpostmessage .ktxtarea,
#kunena .kpostmessage select {
	border-color: {$bordercolor};
}


#kunena div.inner-odd,
#kunena div.inner-even {
	background: {$messagecolor};
}

EOF;
$document->addStyleDeclaration($styles);

?>