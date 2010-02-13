<?php
defined( '_JEXEC' ) or die( 'Restricted index access' );

jimport('joomla.filesystem.file');

// remove mootools.js and caption.js if set in params
$headerjs = $this->getHeadData();
reset($headerjs['scripts']);
foreach ($headerjs['scripts'] as $script=>$type) {
    if (($mootools_enabled == "false" and strpos($script,'mootools.js')) or ($caption_enabled == "false" and strpos($script,'caption.js'))) {
        unset($headerjs['scripts'][$script]);
    }
}
$this->setHeadData($headerjs);

//Are we in edit mode
$editmode = false;
if (JRequest::getCmd('task') == 'edit' ) :
	$editmode = true;
endif;

$leftcolumn_width = ($this->countModules('left')>0) ? $leftcolumn_width : 0;
$rightcolumn_width = (!$editmode and $this->countModules('right')>0) ? $rightcolumn_width : 0;

$col_mode = "s-c-s";
if ($leftcolumn_width==0 and $rightcolumn_width>0) $col_mode = "x-c-s";
if ($leftcolumn_width>0 and $rightcolumn_width==0) $col_mode = "s-c-x";
if ($leftcolumn_width==0 and $rightcolumn_width==0) $col_mode = "x-c-x";

$template_width = 'margin: 0 auto; width: ' . $template_width . 'px;';

$mainmod_count = ($this->countModules('user1')>0) + ($this->countModules('user2')>0) + ($this->countModules('user3')>0);
$mainmod_width = $mainmod_count > 0 ? ' w' . floor(99 / $mainmod_count) : '';
$mainmod2_count = ($this->countModules('user4')>0) + ($this->countModules('user5')>0) + ($this->countModules('user6')>0);
$mainmod2_width = $mainmod2_count > 0 ? ' w' . floor(99 / $mainmod2_count) : '';
$mainmod3_count = ($this->countModules('user7')>0) + ($this->countModules('user8')>0) + ($this->countModules('user9')>0);
$mainmod3_width = $mainmod3_count > 0 ? ' w' . floor(99 / $mainmod3_count) : '';

$inlinestyle = "
	#wrapper { ".$template_width."padding:0;}
	.s-c-s #colmid { left:".$leftcolumn_width."px;}
	.s-c-s #colright { margin-left:-".($leftcolumn_width + $rightcolumn_width)."px;}
	.s-c-s #col1pad { margin-left:".($leftcolumn_width + $rightcolumn_width)."px;}
	.s-c-s #col2 { left:".$rightcolumn_width."px;width:".$leftcolumn_width."px;}
	.s-c-s #col3 { width:".$rightcolumn_width."px;}
	
	.s-c-x #colright { left:".$leftcolumn_width."px;}
	.s-c-x #col1wrap { right:".$leftcolumn_width."px;}
	.s-c-x #col1 { margin-left:".$leftcolumn_width."px;}
	.s-c-x #col2 { right:".$leftcolumn_width."px;width:".$leftcolumn_width."px;}
	
	.x-c-s #colright { margin-left:-".$rightcolumn_width."px;}
	.x-c-s #col1 { margin-left:".$rightcolumn_width."px;}
	.x-c-s #col3 { left:".$rightcolumn_width."px;width:".$rightcolumn_width."px;}";
$this->addStyleDeclaration($inlinestyle);

?>