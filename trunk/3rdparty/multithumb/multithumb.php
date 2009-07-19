<?php
#error_reporting(E_ALL);
/**
* @version $Id: multithumb.php, v 2.0 alpha 3 for Joomla 1.5 2008/8/27 15:08:21 marlar Exp $
* @package Joomla
* @copyright (C) 2007-2008 Martin Larsen; with modifications from Erich N. Pekarek and René-C. Kerner
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//import library dependencies
jimport('joomla.event.plugin');
jimport('joomla.document.document');

class plgContentMultithumb extends JPlugin {
 
 function plgContentMultithumb ( &$subject ) {
	 global $plgContentMultithumb_live_site;
     $plgContentMultithumb_live_site = JURI :: base();
     if(substr($plgContentMultithumb_live_site, -1, 1)=='/')
        $plgContentMultithumb_live_site = substr($plgContentMultithumb_live_site, 0, -1);
     
 	 parent::__construct( $subject );  
	 $this->_plugin = JPluginHelper::getPlugin( 'content', 'multithumb' );
	 $this->_params = new JParameter( $this->_plugin->params );
     $this->_live_site = $plgContentMultithumb_live_site;
  }
  
  function onPrepareContent ( &$row, &$params, $page=0 ) {
	$published	= JPluginHelper::isEnabled('content','multithumb');
    if (!$published ) {
        $row->text = preg_replace('#{(no)?multithumb}#i', '', $row->text);
        return true;
    }

	global $mainframe;
	global $multithumbMessage;
	global $multithumbVersion;
	
	$document 	= &JFactory::getDocument();
	
	$multithumbMessage = '';
   	$multithumbVersion = 'Multithumb 2.0  alpha 1.0 for Joomla 1.5';
	$jpath_site=JPATH_SITE;

   global $regex;
	global $mosConfig_lang;
   
   global $mt_thumbnail_count;
   global $mt_gallery_count;
   global $botmtversion, $multithumb_msg, $isfirstimage;
   global $botMtParams, $botMtLinkText, $botMtLinkOn;
   $mt_thumbnail_count = array();
   $mt_gallery_count = 0;
   static $bot_mt_screenres_header_added = 0;
   
   $isfirstimage = true;   
   $botmtversion = 'Multithumb 2.1 alpha 3';
   #$botMtLinkText = @$row->link_text;
   #$botMtLinkOn = @$row->link_on;
   $botMtLinkText = 'Read more';
   $botMtLinkOn = "$this->_live_site/index.php?option=com_content&view=article&id=$row->slug&catid=$row->catslug";
   
   $multithumb_msg = '';

   $botparam=$this->_params;
   
   $only_cats = $botparam->get('only_cats', '');
   $ignore_cats = $botparam->get('ignore_cats', '');
   @$preg_cat = '/(;|^) *' . preg_quote(trim($row->category)) . ' *(;|$)/i';
   if($only_cats && !preg_match($preg_cat, $only_cats)) return;
   if($ignore_cats && preg_match($preg_cat, $ignore_cats)) return;
    global $botMtGlobals, $botMtGlobalsDef;
    $botMtGlobals['memory_limit'] = $botparam->get('memory_limit', 'default');
    $botMtGlobals['time_limit'] = $botparam->get('time_limit', '');

    #if(is_object($params) && $params->get('intro_only'))
    if($row->readmore)
        $botMtGlobals['blog_mode'] = $botparam->get('blog_mode', 'link');
    elseif($botparam->get('full_mode', 'normal') == 'disabled')
         return true;
   else
        $botMtGlobals['blog_mode'] = 'popup';
        
   
   $botMtGlobals['only_classes'] = $botparam->get('only_classes', '');
   $botMtGlobals['thumbclass'] = $botparam->get('thumbclass', 'multithumb');
   $botMtGlobals['only_tagged'] = $botparam->get('only_tagged', 0);
   $botMtGlobals['exclude_tagged'] = $botparam->get('exclude_tagged', 0);
   $botMtGlobals['enable_thumbs'] = $botparam->get('enable_thumbs', 1);
   $botMtGlobals['thumb_width'] = $botparam->get('thumb_width',150);
   $botMtGlobals['thumb_height'] = $botparam->get('thumb_height',100);
   $botMtGlobals['thumb_proportions'] = $botparam->get('thumb_proportions','bestfit');
   $botMtGlobals['full_width'] = $botparam->get('full_width',800);
   $botMtGlobals['full_height'] = $botparam->get('full_height',600);
   $botMtGlobals['image_proportions'] = $botparam->get('image_proportions','bestfit');
   if(isset($_COOKIE['botmtscreenres']))
    $botMtGlobals['screenres'] = explode('x', $_COOKIE['botmtscreenres']);
   elseif(strpos($botMtGlobals['full_width'], '%') || strpos($botMtGlobals['full_height'], '%')) {
    if(!$bot_mt_screenres_header_added) {
        $bot_mt_screenres_header_added = 1;
        $header = '<script type="text/javascript" language="javascript">document.cookie= "botmtscreenres=" + screen.width+"x"+screen.height;</script>';
        $document->addCustomTag($header);
    }
   }    
   $botMtGlobals['popup_type'] = $botparam->get('popup_type', 'lightbox');
   $botMtGlobals['max_thumbnails'] = $botparam->get('max_thumbnails', 0);
   $botMtGlobals['thumb_bg'] = $botparam->get('thumb_bg', '#FFFFFF');
   $botMtGlobals['num_cols'] = $botparam->get('num_cols', 3);
   $botMtGlobals['image_bg'] = $botparam->get('image_bg', '#000000');
   $botMtGlobals['resize'] = $botparam->get('resize',0);
   $botMtGlobals['allow_img_toolbar'] = $botparam->get('allow_img_toolbar',0);
   $botMtGlobals['caption_pos'] = $botparam->get('caption_pos', 'disabled');
   $botMtGlobals['caption_type'] = $botparam->get('caption_type', 'title');
   $botMtGlobals['clear_cache'] = $botparam->get('clear_cache', 0);
   $botMtGlobals['scramble'] = $botparam->get('scramble', 'off');
   $botMtGlobals['quality'] = $botparam->get('quality', 80);
   $botMtGlobals['watermark'] = $botparam->get('watermark', 0);
   $botMtGlobals['watermark_file'] = $botparam->get('watermark_file', '');
   if(strpos($botMtGlobals['watermark_file'], '://')) { // It's a url
	   $botMtGlobals['watermark_file'] = str_replace($this->_live_site, $jpath_site, $botMtGlobals['watermark_file']);
   }

   $botMtGlobals['watermark_left'] = $botparam->get('watermark_left', '');
   $botMtGlobals['watermark_top'] = $botparam->get('watermark_top', '');
   $botMtGlobals['transparency_type'] = $botparam->get('transparency_type', 'alpha');
   $botMtGlobals['transparent_color'] = hexdec($botparam->get('transparent_color', '#000000'));
   $botMtGlobals['transparency'] = $botparam->get('transparency', '25');
   $botMtGlobals['add_headers'] = $botparam->get('add_headers', 'auto');
   $botMtGlobals['error_msg'] = $botparam->get('error_msg', 'popup');
   $botMtGlobals['language'] = strtolower($botparam->get('language', ''));
   if(!$botMtGlobals['language']) $botMtGlobals['language']  = strtolower($mosConfig_lang);
   $botMtGlobals['border_size'] = $botparam->get('border_size', '2px');
   $botMtGlobals['border_color'] = $botparam->get('border_color', '#000000');
   $botMtGlobals['border_style'] = $botparam->get('border_style', 'solid');
   $botMtGlobals['css'] = $botparam->get('css', "/*
The comments below are to help you understanding and modifying the look and feel of thumbnails. Borders can be set using the border fields above. You can safely delete these comments.
*/

/*
Styles for the DIV surrounding the image.
*/
div.mtImgBoxStyle {
 margin:5px;
}

/* 
Styles for the caption box below/above the image.
Change font family and text color etc. here.
*/
div.mtCapStyle {
 font-weight: bold;
 color: black;
 background-color: #ddd;
 padding: 2px;
 text-align:center;
 overflow:hidden;
}
/* 
Styles for the table based Multithumb gallery
*/
table.multithumb {
 width: auto;
}");
   $botMtGlobalsDef = $botMtGlobals;
   if($botMtGlobals['clear_cache']) {
	  $par = mysql_real_escape_string(str_replace('clear_cache=1','clear_cache=0',$botparam->_raw));
	  $query = "update #__mambots set params = '$par' WHERE id=$id";
	  $database->setQuery( $query );
	  $database->query();
	  foreach(glob("$jpath_site/plugins/content/multithumb/thumbs/{*.gif,*.jpg,*.png,*.GIF,*.JPG,*.PNG}", GLOB_BRACE) as $fn) unlink($fn);
      foreach(glob("$jpath_site/plugins/content/multithumb/images/{*.gif,*.jpg,*.png,*.GIF,*.JPG,*.PNG}", GLOB_BRACE) as $fn) unlink($fn);
   }
   $donothing = ($botMtGlobals['exclude_tagged'] && stristr($row->text, '{nomultithumb}')!==false) || (preg_match('/{multithumb([^}]*)}/is', $row->text, $botMtParams)==0 && $botMtGlobals['only_tagged']);
   if($donothing) {
        $row->text = preg_replace('/{(no)?multithumb([^}]*)}/i', '', $row->text);
        return true;
   }
   if($botMtGlobals['only_classes'])
	  $regex = '#<img(?=[^>]*class=["\'](?:'.$botMtGlobals['only_classes'].')["\'])[^>]*src=(["\'])([^"\']*)\1[^>]*>';
   else
	  $regex = '#<img[^>]*src=(["\'])([^"\']*)\1[^>]*>';
    $regex .= '|{multithumb([^}]*)}#is';
   $row->text = preg_replace_callback($regex, array($this,'bot_mt_image_replacer'), $row->text);
   
   if($multithumb_msg)
      switch($botMtGlobals['error_msg']) {
         case 'popup':
            $row->text .= "<script type='text/javascript' language='javascript'>alert('Multithumb found errors on this page:\\n\\n$multithumb_msg')</script>";
            break;
         case 'text':
            $multithumb_msg = str_replace('\\n', "\n", $multithumb_msg);
            $row->text = "<pre style='border:2px solid black; padding: 10px; background-color: white; font-weight: bold; color: red;'>Multithumb found errors on this page:\n\n$multithumb_msg</pre>" . $row->text;
      }
   $this->botAddMultiThumbHeader('style');
   return true;
}

function create_watermark($sourcefile_id, $watermarkfile, $x, $y, $transcol = false, $transparency = 100) {
    global $multithumb_msg;
    static $disable_wm_ext_warning, $disable_wm_load_warning, $disable_alpha_warning;
    
    //Get the resource ids of the pictures
    $fileType = strtolower(substr($watermarkfile, strlen($watermarkfile)-3));
    switch($fileType) {
        case 'png':
            $watermarkfile_id = @imagecreatefrompng($watermarkfile);
            break;
        case 'gif':
            $watermarkfile_id = @imagecreatefromgif($watermarkfile);
            break;
        case 'jpg':
            $watermarkfile_id = @imagecreatefromjpeg($watermarkfile);
            break;
        default:
            $watermarkfile = basename($watermarkfile);
            if(!$disable_wm_ext_warning) $multithumb_msg .= "You cannot use a .$fileType file ($watermarkfile) as a watermark\\n";
            $disable_wm_ext_warning = true;
            return false;
    }
    if(!$watermarkfile_id) {
        if(!$disable_wm_load_warning) $multithumb_msg .= "There was a problem loading the watermark $watermarkfile\\n";
        $disable_wm_load_warning = true;
        return false;
    }
    
    @imageAlphaBlending($watermarkfile_id, false);
    $result = @imageSaveAlpha($watermarkfile_id, true);
    if(!$result) {
        if(!$disable_alpha_warning) $multithumb_msg .= "Watermark problem: your server does not support alpha blending (requires GD 2.0.1+)\\n";
        $disable_alpha_warning = true;
        imagedestroy($watermarkfile_id);
        return false;
    }

    //Get the sizes of both pix  
  $sourcefile_width=imageSX($sourcefile_id);
  $sourcefile_height=imageSY($sourcefile_id);
  $watermarkfile_width=imageSX($watermarkfile_id);
  $watermarkfile_height=imageSY($watermarkfile_id);
  if(!$x)
    $dest_x = ( $sourcefile_width / 2 ) - ( $watermarkfile_width / 2 );
  elseif($x>0)
    $dest_x = $x;
  else
    $dest_x = $sourcefile_width - $watermarkfile_width + $x;
    
  if(!$y)
    $dest_y = ( $sourcefile_height / 2 ) - ( $watermarkfile_height / 2 );
  elseif($y>0)
    $dest_y = $y;
  else
    $dest_y = $sourcefile_height - $watermarkfile_height + $y;      
    
   
    // if a gif, we have to upsample it to a truecolor image
    if($fileType == 'gif') {
        // create an empty truecolor container
        $tempimage = imagecreatetruecolor($sourcefile_width, $sourcefile_height);
       
        // copy the 8-bit gif into the truecolor image
        imagecopy($tempimage, $sourcefile_id, 0, 0, 0, 0, $sourcefile_width, $sourcefile_height);
       
        // copy the source_id int
        $sourcefile_id = $tempimage;
    }

    if($transcol!==false) {
        imagecolortransparent($watermarkfile_id, $transcol); // use transparent color
        imagecopymerge($sourcefile_id, $watermarkfile_id, $dest_x, $dest_y, 0, 0, $watermarkfile_width, $watermarkfile_height, $transparency);
    }
    else
        imagecopy($sourcefile_id, $watermarkfile_id, $dest_x, $dest_y, 0, 0, $watermarkfile_width, $watermarkfile_height); // True alphablend

    imagedestroy($watermarkfile_id);
   
}

function botmt_thumbnail($filename, $dest_folder, &$width, &$height, $proportion='bestfit', $bgcolor = 0xFFFFFF, $watermarkfile = '') {
    global $mainframe, $botMtGlobals, $multithumb_msg, $jpath_site;
    static $disablegifwarning, $disablepngwarning, $disablejpgwarning, $disablepermissionwarning;
   $jpath_site=JPATH_SITE;
    $temp = pathinfo($filename);
    $ext = $temp['extension']; //todo: check filename here
    $alt_filename = '';
    if($dest_folder=='thumbs') {
      $alt_filename = substr($filename, 0, -strlen($ext)) . 'thumb.' . $ext;
      if(file_exists($alt_filename))
         $filename = $alt_filename;
    }
    
   $size = @getimagesize($filename);
   if(!$size) {
       $multithumb_msg .= "There was a problem loading image $filename\\n";
       return false;
   }
   $origw = $size[0];
   $origh = $size[1];
   if($alt_filename && ($origw<$width || $origh<$height)) {
      $width = $origw;
      $height = $origh;
   }
   if($origw<$width && $origh<$height) return false;
   
    $watermark = $watermarkfile?1:0;
    if($width || $height)
        $prefix = substr($proportion,0,1) . ".$width.$height.$bgcolor.$watermark.";
    else
        $prefix = "$watermark.";
    $thumb_file = $prefix . str_replace(array(':','/','\\'),  '.' ,substr($filename,strlen($jpath_site)+8));
    switch($GLOBALS['botMtGlobals']['scramble']) {
        case 'md5': $thumb_file = md5($thumb_file) . '.' . $ext; break;
        case 'crc32': $thumb_file = sprintf("%u", crc32($thumb_file)) . '.' . $ext;
    }
    $thumbname = "$jpath_site/plugins/content/multithumb/$dest_folder/$thumb_file";
    if(file_exists($thumbname))
    {
        $size = @getimagesize($thumbname);
        if($size) {
            $width = $size[0];
            $height = $size[1];
        }
    }
    else {
        if(!($width || $height)) { // Both sides zero
            $width = $origw;
            $height = $origh;
            $proportion='stretch';
        }
        elseif(!($width && $height)){ // One of the sides zero
            $proportion = 'bestfit';
        }
        $newheight = $height; $newwidth = $width;
        $dst_x = $dst_y = $src_x = $src_y = 0;
        if((($origw < $origh && $width < $height) || ($origw >= $origh && $width <= $height)) && $width != 0 || $height == 0)
            switch($proportion) {
               case 'fill':
                  $newheight =  $origh/$origw*$width;
                  $dst_y = ($height - $newheight)/2;
                  break;
               case 'crop':
                  $temph = $origw;
                  $origw = $origh / $newheight * $newwidth;
                  $src_x = ($temph-$origw)/2;
                  break;
               case 'bestfit':
                  $newheight = $height = round($width / ($origw / $origh));
            }
         else
            switch($proportion) {
               case 'fill':
                  $newwidth =  $origw/$origh*$height;
                  $dst_x = ($width - $newwidth)/2;
                  break;
               case 'crop':
                  $temph = $origh;
                  $origh = $origw * $newheight / $newwidth;
                  $src_y = ($temph-$origh)/2;
                  break;
               case 'bestfit':
                  $newwidth = $width = round($height * ($origw / $origh));
            }

        switch(strtolower($ext)) {
            case 'png':
                if(!function_exists("imagecreatefrompng")) {
                    if(!$disablepngwarning) $multithumb_msg .= 'PNG is not supported on this server.\n';
                    $disablepngwarning = true;
                    return false;
                }
                else {
                    $src_img = imagecreatefrompng($filename);
                    $imagefunction = "imagepng";
                }
                break;
            case 'gif':
                if(!function_exists("imagecreatefromgif")) {
                    if(!$disablegifwarning) $multithumb_msg .= 'GIF is not supported on this server.\n';
                    $disablegifwarning = true;
                    return false;
                }
                else {
                    $src_img = imagecreatefromgif($filename);
                    $imagefunction = "imagegif";
                }
                break;
            default:
                if(!function_exists("imagecreatefromjpeg")) {
                    if(!$disablejpgwarning) $multithumb_msg .= 'JPG is not supported on this server.\n';
                    $disablejpgwarning = true;
                    return false;
                }
                else {
                    $src_img = imagecreatefromjpeg($filename);
                    $imagefunction = "imagejpeg";
                }
        }
        $dst_img = ImageCreateTrueColor($width, $height);
        imagefill( $dst_img, 0,0, $bgcolor);
        imagecopyresampled($dst_img,$src_img, $dst_x, $dst_y, $src_x, $src_y, $newwidth,$newheight,$origw, $origh);
        
        if($watermarkfile) {
            if($botMtGlobals['transparency_type'] == 'alpha')
                $transcolor = FALSE;
            else
                $transcolor = $botMtGlobals['transparent_color'];
            $this->create_watermark($dst_img, $watermarkfile, $botMtGlobals['watermark_left'], $botMtGlobals['watermark_top'], $transcolor, $botMtGlobals['transparency']);
        }
        if($imagefunction=='imagejpeg')
            $result = @$imagefunction($dst_img, $thumbname, $botMtGlobals['quality']);
        else
            $result = @$imagefunction($dst_img, $thumbname);
            
        imagedestroy($src_img);
        if(!$result) {
            if(!$disablepermissionwarning) $multithumb_msg .= "Could not create image:\\n$thumbname.\\nCheck if you have write permissions in /plugins/content/multihumb/$dest_folder/\\n";
            $disablepermissionwarning = true;
        }
        else
            imagedestroy($dst_img);
    }
	return $this->_live_site."/plugins/content/multithumb/$dest_folder/" . basename($thumbname);
} 
// Old code ends here

// New code starts here
/**	switch ($published) {
*		case 0:
*	 	// The plugin is not published:
*		// remove (no-) multithumb-tags and do nothing
*		  	$row->text = preg_replace('#{(no)?multithumb}#i', '', $row->text);
*		  	$row->text = preg_replace('#{mosimage}#i', '', $row->text);
*		break;	
*		case 1:
*		//plugin is published 
*			// retrieve images from $row->text(per article!), 
*			// store them in 
*			// global array
*			$countImages = 0;
*/
//			$imgRegex='#<img[^>]*/>#i';
/*
*			$countImages=preg_match_all($imgRegex, $row->text, $multithumbImages);
*		
*			//foreach ($multithumbImages[0] as $image) {
*			//	$document->addCustomTag("<!-- ".$image." -->");
*			//}
*
*			// load parameters into local variables
*
*	   		$preg_cat = '/(;|^) *' . preg_quote(trim($row->category)) . ' *(;|$)/i';
*			if($only_cats && !preg_match($preg_cat, $only_cats)) return;
*			if($ignore_cats && preg_match($preg_cat, $ignore_cats)) return;
*			// What mode is Joomla´s content presented in?
*			switch ($intro_only) {
*				case NULL:
*					break;
*			        default:
*					$multithumbGlobals['blog_mode'] = $params->get('blog_mode', 'link');
*			}
*			switch ($full_mode) {
*				case NULL:
*					break;
*			        default:
*					$multithumbGlobals['full_mode'] = $params->get('blog_mode', 'link');
*			}
*			switch ($multithumbGlobals['blog_mode']) {
*				case NULL:
*					$multithumbGlobals['blog_mode']='popup';
*				break;
*				default:
*					break;
*			}
*		break;
*		default:
*		;;
*		}	
*	return 0;
*       }
**/



function botAddMultiThumbHeader($headertype) {
   global $mainframe, $cur_template, $botMtGlobals,$jpath_site;
   static $bot_mt_style_header_added, $bot_mt_lightbox_header_added, $bot_mt_popup_header_added;
   static $bot_mt_greybox_header_added, $bot_mt_slimbox_header_added, $bot_mt_thickbox_header_added;
   global $botmtversion;
   $jpath_site=JPATH_SITE;
    $document 	= &JFactory::getDocument();
   static $libs;
	if($headertype=='style' && !$bot_mt_style_header_added) {
         $bot_mt_style_header_added = 1;
		 // $mainframe->addCustomHeadTag("<style type='text/css'>\n/* $botmtversion */\n" . str_replace(array('<br />', '\[', '\]', '&nbsp;'), array("\n", '{', '}', ' '), $botMtGlobals['css']) . "</style>");
		 $document->addCustomTag("<style type='text/css'>\n/* $botmtversion */\n" . str_replace(array('<br />', '\[', '\]', '&nbsp;'), array("\n", '{', '}', ' '), $botMtGlobals['css']) . "</style>");
	}
    if($botMtGlobals['add_headers']=='never') return; // Don't add headers
    $header = '';
    if(!is_array($libs)) {
      $libs = array();
      if($botMtGlobals['add_headers']=='auto') { // Handle headers automatically
          $indexphp = @file_get_contents("$jpath_site/templates/$cur_template/index.php");
          if(preg_match_all('/<script [^>]*(prototype|mootools|scriptaculous|lightbox|AJS|AJS_fx|gb_scripts|multithumb|slimbox)\.js[^>]*>/', $indexphp, $libs))
              $libs = $libs[1];
      }
    }
   if($headertype=='sb' && !$bot_mt_slimbox_header_added && !$bot_mt_lightbox_header_added) {
	$bot_mt_slimbox_header_added=1;
    if(!in_array('mootools', $libs))
        $header = '<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/multithumb/slimbox/js/mootools.js"></script>'."\n";
    if(!in_array('slimbox', $libs))
        $header .= '<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/multithumb/slimbox/js/slimbox.js"></script>'."\n";
    $header .= '<link rel="stylesheet" href="'.$this->_live_site.'/plugins/content/multithumb/slimbox/css/slimbox.css" type="text/css" media="screen" />'."\n";
	//$mainframe->addCustomHeadTag($header);
	$document->addCustomTag($header);
   }
   
   if($headertype=='lb' && !$bot_mt_lightbox_header_added && !$bot_mt_slimbox_header_added) {
    $bot_mt_lightbox_header_added=1;
    if(!in_array('prototype', $libs))
      $header = '<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/multithumb/lightbox/js/prototype.js?botmt"></script>'."\n";
    if(!in_array('scriptaculous', $libs))
      $header .= '<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/multithumb/lightbox/js/scriptaculous.js?load=effects"></script>'."\n";
    if(!in_array('lightbox', $libs)) {
      $header .= '<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/multithumb/lightbox/js/lightbox.js"></script>'."\n";
      $fileLoadingImage = $this->_live_site.'/plugins/content/multithumb/lightbox/images/loading.gif';
      
      if(file_exists($jpath_site.'/plugins/content/multithumb_languages/lightbox/'.$botMtGlobals['language'].'/closelabel.gif'))
         $fileBottomNavCloseImage = $this->_live_site.'/plugins/content/multithumb_languages/lightbox/'.$botMtGlobals['language'].'/closelabel.gif';
      else
         $fileBottomNavCloseImage =  $this->_live_site.'/plugins/content/multithumb/lightbox/images/closelabel.gif';
         
      if(file_exists($jpath_site.'/plugins/content/multithumb_languages/lightbox/'.$botMtGlobals['language'].'/prevlabel.gif'))
         $prevLinkImage = $this->_live_site.'/plugins/content/multithumb_languages/lightbox/'.$botMtGlobals['language'].'/prevlabel.gif';
      else
         $prevLinkImage =  $this->_live_site.'/plugins/content/multithumb/lightbox/images/prevlabel.gif';
         
      if(file_exists($jpath_site.'/plugins/content/multithumb_languages/lightbox/'.$botMtGlobals['language'].'/nextlabel.gif'))
         $nextLinkImage = $this->_live_site.'/plugins/content/multithumb_languages/lightbox/'.$botMtGlobals['language'].'/nextlabel.gif';
      else
         $nextLinkImage =  $this->_live_site.'/plugins/content/multithumb/lightbox/images/nextlabel.gif';
      
      $keynav = @file_get_contents($jpath_site.'/plugins/content/multithumb_languages/lightbox/'.$botMtGlobals['language'].'/lang.js');
         
      $header .= "<script type='text/javascript' language='javascript'>
var fileLoadingImage = '$fileLoadingImage';
var fileBottomNavCloseImage = '$fileBottomNavCloseImage';
$keynav</script>
<style type='text/css'>
#prevLink:hover, #prevLink:visited:hover { background: url($prevLinkImage) left 15% no-repeat; }
#nextLink:hover, #nextLink:visited:hover { background: url($nextLinkImage) right 15% no-repeat; }
</style>
";
    }
	$header .= '<link rel="stylesheet" href="'.$this->_live_site.'/plugins/content/multithumb/lightbox/css/lightbox.css" type="text/css" media="screen" />';
	//$mainframe->addCustomHeadTag($header);
        $document->addCustomTag($header);
   }

   if($headertype=='gb' && !$bot_mt_greybox_header_added) {
	  $bot_mt_greybox_header_added=1;
    $header = '<script type="text/javascript">var GB_ROOT_DIR = "'.$this->_live_site.'/plugins/content/multithumb/greybox/";</script>'."\n";
    if(!in_array('AJS', $libs))
        $header .= '<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/multithumb/greybox/AJS.js"></script>'."\n";
    if(!in_array('AJS_fx', $libs))
        $header .= '<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/multithumb/greybox/AJS_fx.js"></script>'."\n";
    if(!in_array('gb_scripts', $libs))
        $header .= '<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/multithumb/greybox/gb_scripts.js"></script>'."\n";
	$header .= '<link rel="stylesheet" href="'.$this->_live_site.'/plugins/content/multithumb/greybox/gb_styles.css" type="text/css" media="screen" />'."\n";
	//$mainframe->addCustomHeadTag($header);
        $document->addCustomTag($header);
   }

   if($headertype=='tb' && !$bot_mt_thickbox_header_added) {
	  $bot_mt_thickbox_header_added=1;
    if(!in_array('jquery', $libs))
        $header .= '<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/multithumb/thickbox/jquery.js?botmt"></script>'."\n";
    if(!in_array('thickbox', $libs))
        $header .= '<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/multithumb/thickbox/thickbox.js"></script>'."\n";
	$header .= '<link rel="stylesheet" href="'.$this->_live_site.'/plugins/content/multithumb/thickbox/thickbox.css" type="text/css" media="screen" />'."\n";
	//$mainframe->addCustomHeadTag($header);
        $document->addCustomTag($header);
   }

   if($headertype=='popup' && !$bot_mt_popup_header_added) {
	$bot_mt_popup_header_added=1;
    if(!in_array('multithumb', $libs))
        $header = '<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/multithumb/multithumb.js"></script>'."\n";
	  //$mainframe->addCustomHeadTag($header);
        $document->addCustomTag($header);
   }
}

function bot_mt_makeFullArticleLink(&$img) {
    global $botMtLinkText, $botMtLinkOn;
    $img = preg_replace(array('/((?:title|alt)=(["\']))(.*?)(\\2)/', '/<img[^>]*>/'), array("$1$botMtLinkText$4", "<a href=\"$botMtLinkOn\" title=\"$botMtLinkText\">$0</a>"), $img);
    return;
}

function bot_mt_image_replacer(&$matches) {
   global $mainframe, $mt_thumbnail_count, $mt_gallery_count, $botMtGlobals, $botMtGlobalsDef, $jpath_site;
   $jpath_site=JPATH_SITE;
   global $isfirstimage;
   static $div_id;
   extract($botMtGlobals);

   $alt=$title=$align=$class=$onclick=$img=$border='';
   if(strtolower(substr($matches[0], 0, 11))=='{multithumb') {
    if(isset($matches[3])) { // Now handle manual param settings ...
        $botMtParamStr = str_replace(array('<br />', '&nbsp'), '', $matches[3]);
        if(preg_match_all('/\bdefault\b|[^=\s]+ *=[^=]*(?:\s+|$)(?!=)/is', $botMtParamStr, $botParams)) {
             foreach($botParams[0] as $param) {
               $param = trim(trim($param, ';')); 
               if($param == 'default') {
                    $botMtGlobals = $botMtGlobalsDef;
               }
               else {
                 $param = explode('=', $param);
                 $varname = trim($param[0]);
                 $value = trim($param[1]);
                 if(strtolower($value)=='default')
                    $value = $botMtGlobalsDef[$varname];
                 if(isset($botMtGlobals[$varname])) $botMtGlobals[$varname] = $value;
                }
             }
        }
   }
    return '';
   }
   else { // it's a normal image
	  $imgraw = $matches[0];
	  $imgloc = urldecode($matches[2]);
	  if(preg_match('#alt=(["\'])(.*?)\\1#i', $imgraw, $temp)) $alt = $temp[2];
	  if(preg_match('#title=(["\'])(.*?)\\1#i', $imgraw, $temp)) $title = $temp[2];
	  if(preg_match('#align=(["\'])(.*?)\\1#i', $imgraw, $temp)) $align = $temp[2];
      if(preg_match('#float\s*:\s*(\w+)#i', $imgraw, $temp)) $align = $temp[1];
	  if(preg_match('#class=(["\'])(.*?)\\1#i', $imgraw, $temp)) $class = $temp[2];
   }
   
   if(is_numeric($time_limit)) {
    set_time_limit($time_limit);
    $botMtGlobals['time_limit'] = $botMtGlobalsDef['time_limit'] = '';
   }
   if($memory_limit != 'default') {
    ini_set("memory_limit", $memory_limit);
    $botMtGlobals['memory_limit'] = $botMtGlobalsDef['memory_limit'] = 'default';
   }
      
    $temp = explode(':', $alt, 2);
    $popupmethods = array('none'=>'mt_none', 'normal'=>'mt_popup', 'lightbox'=>'mt_lightbox', 'expansion'=>'mt_expand', 'gallery'=>'mt_gallery', 'ignore'=>'mt_ignore', 'greybox'=>'mt_greybox', 'slimbox'=>'mt_slimbox', 'thickbox'=>'mt_thickbox');
    $new_popup_style = array_search(strtolower($temp[0]), $popupmethods);
   if($new_popup_style!==false) {
	  $popup_type = $new_popup_style;
	  $title = preg_replace('/(mt_none|mt_popup|mt_lightbox|mt_expand|mt_gallery|mt_ignore|mt_greybox|mt_slimbox|mt_thickbox):/i', '', $title);
        if(count($temp)>1) $alt = $temp[1];
   }
   if(!$title) $title = $alt;
   if(!isset($caption)) $caption = $caption_type=='title' ? $title : $alt;
   if(strpos($imgloc, '://')) { // It's a url
	   $imgurl = $imgloc;
	   $imgloc = str_replace($this->_live_site, $jpath_site.'/', $imgloc);
       #if(strpos($imgloc, '://')) return $imgraw; // Still http:// => it must be an external image
   }
   else { // it's a relative path
	  if(!file_exists($imgloc)) // it might be a malformed image location, try removing leading ../
         $imgloc = preg_replace('#^(\.\./)+#', '', $imgloc);
      $imgurl = $this->_live_site.'/'.$imgloc;
      $imgloc = $jpath_site.'/'.$imgloc;
   }
   if($popup_type=='gallery'){ // It's a gallery
	  if(!file_exists($imgloc)) return $imgraw;
	  $pathinfo = pathinfo($imgloc);
	  $filepatt = "$pathinfo[dirname]/{*.gif,*.jpg,*.png,*.GIF,*.JPG,*.PNG}";
	  $old_caption_pos = $botMtGlobals['caption_pos'];
	  $old_caption_type = $caption_type;
      $botMtGlobals['caption_type'] = 'title'; // Make sure we get the titles for the gallery
	  $gallery = '<table class="'.$thumbclass.'" width="100%" cellspacing="0" cellpadding="3" border="0">' . "\n";
      $style = in_array($align, array('left', 'right')) ? ' style="float:'.$align.';"' : '';
	  $gallery = '<table class="'.$thumbclass.'" '.$style.' cellspacing="0" cellpadding="3" border="0">' . "\n";
    
	  $n = 0; $lblinks = '';
      if(file_exists("$imgloc.txt")) {
        $imglist = file_get_contents("$imgloc.txt");
        preg_match_all('/(\S+\.(?:jpg|png|gif))\s(.*)/i', $imglist, $files, PREG_SET_ORDER);
        $dir = dirname($imgloc);
        $alt = basename($imgloc);
        
      }
      else {
        $files = glob($filepatt, GLOB_BRACE);
        sort($files);
        $imgpos = array_search($imgloc, $files);
        $files = array_merge(array_slice($files, $imgpos), array_slice($files, 0, $imgpos));
      }
      if($alt=='mt_gallery') {
        $mt_gallery_count++;
        $alt = "gallery$mt_gallery_count";
      }
      if($title=='mt_gallery')
        $title = ' ';
      foreach($files as $file) {
         if(is_array($file)) {
            $fn = $dir.'/'.$file[1];
            $title = str_replace("'", '&#39;', $file[2]);
         }
         else
            $fn = $file;
		 $fn = str_replace($jpath_site, $this->_live_site, $fn);
         $galimg = preg_replace_callback($GLOBALS['regex'], array('plgContentMultithumb','bot_mt_image_replacer'), '<img alt="'.$alt.'" title="'.$title.'" src="'.$fn.'">' . "\n");
		 if(strpos($galimg, '<img')>0){
			if($n % $num_cols == 0) $gallery .= '<tr>';
			$gallery .= '<td class="'.$thumbclass.'" valign="bottom" nowrap="nowrap" align="center">'.$galimg.'</td>';
			$n++;
			if($n % $num_cols == 0) $gallery .= "</tr>\n";
		 }
		 else if(substr($galimg,0,2)=='<a') {
			$lblinks .= $galimg;
		 }
	  }
	  $gallery .= str_repeat('<td>&nbsp;</td>', $num_cols-1 - ($n-1) % $num_cols) . "</table>\n";
	  $botMtGlobals['caption_pos']= $old_caption_pos;
      $botMtGlobals['caption_type'] = $old_caption_type;
	  return $gallery . $lblinks;
   }
   if($xpos=strpos($full_width, '%') || $ypos=strpos($full_height, '%')) {
    if(!isset($screenres)) $screenres = array(1024, 768);
    if($xpos) $full_width = $screenres[0] * substr($full_width, 0, $xpos) / 100;
    if($ypos) $full_height = $screenres[1] * substr($full_height, 0, $ypos) / 100;
   }

    if(!$watermark) // No watermark
        $watermark_file = '';
    elseif(!$resize) // Watermark but no resize
        $full_width = $full_height = 0;
    
   if($resize || $watermark) {
      $imgtemp = $this->botmt_thumbnail($imgloc, 'images', $full_width, $full_height, $image_proportions, hexdec($image_bg), $watermark_file);
      
      if($imgtemp) $imgurl = $imgtemp;
   }
   if($resize && $imgtemp) { // only resize if a thumbnail was created ($imgtemp not false)
    $oldxsize = $full_width;
    $oldysize = $full_height;
    $size = 'height="'.$oldxsize.'" width="'.$oldysize.'"'; // TODO: needed?
   }
   else {
    $size = @getimagesize(  $imgloc );
    if (is_array( $size )) {
       $oldxsize=$size[0];
       $oldysize=$size[1];
       $size = $size[3];
    }
    else { // For some reason we can't determine real size, so disable thumbnail generation by setting the size to zero
       $oldxsize = $oldysize = 0;
    }
   }
   if(!$enable_thumbs && $resize) { // Resize the raw images
        preg_match('/(.*src=["\'])[^"\']+(["\'].*)/i', $imgraw, $parts);
        $imgraw = $parts[1].$imgurl.$parts[2];
   }

   if( !( ($thumb_width && $thumb_width <= $oldxsize || $thumb_height && $thumb_height <= $oldysize) && ($enable_thumbs) ) )
        $img = $imgraw; // No thumbing for images small than the thumbnails, just show the full image
    else { // Process the varous popup methods
        if($blog_mode=='thumb' || $blog_mode=='link' && $isfirstimage) $popup_type = 'none';
        switch($popup_type) {
           case 'normal': // Normal popup
              $this->botAddMultiThumbHeader('popup');
              $imgtemp = '<a target="_blank" href="'.$imgurl.'" onclick="this.target="_self";this.href="javascript:void(0)";thumbWindow("$imgurl","$alt",$oldxsize,$oldysize,0,$allow_img_toolbar);">';
              break;
           case 'lightbox': // Lightbox
           case 'slimbox': // Slimbox
              $this->botAddMultiThumbHeader($popup_type=="lightbox"?'lb':'sb');
              $imgtemp = '<a target="_blank" href="'.$imgurl.'" rel="lightbox['.$alt.'" title="'.$caption.'">';
              if($max_thumbnails) {
                 if(!isset($mt_thumbnail_count[$alt])) $mt_thumbnail_count[$alt] = 0;
                 $mt_thumbnail_count[$alt]+=1;
                 if($mt_thumbnail_count[$alt]>$max_thumbnails) return $imgtemp."</a>\n";
              }
              break;
           case 'expansion': // Thumbnail expansion
              $this->botAddMultiThumbHeader('popup');
              $imgtemp = '<a href="javascript:void(0);">';
              $div_id++;
              $onclick = "onclick=\"return multithumber_expand('mt_$div_id', this, '$imgurl')\"";
              $size='';
              break;
            case 'greybox': // Greybox
                   $this->botAddMultiThumbHeader('gb');
                   $imgtemp = '<a target="_blank" href="'.$imgurl.'" rel="gb_imageset['.$alt.']" title="'.$caption.'">';
                   if($max_thumbnails) {
                          if(!isset($mt_thumbnail_count[$alt])) $mt_thumbnail_count[$alt] = 0;
                          $mt_thumbnail_count[$alt]+=1;
                          if($mt_thumbnail_count[$alt]>$max_thumbnails) return $imgtemp."</a>\n";
                   }
                   break;
             case 'thickbox': // Thickbox
                   $this->botAddMultiThumbHeader('tb');
                   $imgtemp = '<a target="_blank" href="'.$imgurl.'" rel="'.$alt.'" alt="'.$caption.'" title="'.$caption.'" class="thickbox">';
                   if($max_thumbnails) {
                          if(!isset($mt_thumbnail_count[$alt])) $mt_thumbnail_count[$alt] = 0;
                          $mt_thumbnail_count[$alt]+=1;
                          if($mt_thumbnail_count[$alt]>$max_thumbnails) return $imgtemp."</a>\n";
                   }
                   break;
             case 'none': // No popup, just thumbnail
             default:
              $imgtemp = '';
        }
        if($popup_type=='ignore') { // mt_ignore, don't create thumb
           $thumb_file = '';
           $popup_type='none';
        }
        else {
         $thumb_file = $this->botmt_thumbnail($imgloc, 'thumbs', $thumb_width, $thumb_height, $thumb_proportions, hexdec($thumb_bg));
              $size = ' width="'. $thumb_width .'" height="'. $thumb_height .'"';
        }
        if($popup_type=='expansion') $size = ''; // No size attr for thumbnail expansion!
        $thumbclass = trim("$thumbclass $class");
        if($thumbclass) $thumbclass = 'class="'.$thumbclass.'" ';
        if($thumb_file)
           $imgtemp .= '<img '.$thumbclass.' '.$onclick.' src="'.$thumb_file.'" '.$size;
        else
           $imgtemp .= '<img src="'.$imgurl.'" '.$size;
        if(!$caption) $caption_pos = 'disabled';
        if($border_size!='0' && $border_style!='none')
         $bordercss = 'border: '.$border_size.' '.$border_style.' '.$border_color.';';
        else
         $bordercss = '';
        switch ($caption_pos) {
           case 'disabled': //no caption
           default:
              $img .= $imgtemp;
              if($bordercss)
               $img .= ' style="'.$bordercss.'"';
              $img .= $align ? ' align="'.$align.'" ' : '';
              $img .='  hspace="6" alt="'.$alt.'" title="'.$title.'" border="'.$border.'" />';
              if ($popup_type!='none') $img .= '</a>';
              break;
           case 'below': //caption below
           case 'above': //caption above
              $captionpart = '<div class="mtCapStyle">'.$caption.'</div>';
              if($caption_pos=='below'){
                 $captionbelow = $captionpart;
                 $captionabove = '';
              }
              else {
                 $captionabove = $captionpart;
                 $captionbelow = '';
              }
              if(!$align) $align = "left";
              if($popup_type=='expansion')
                $img = '<div id="mt_$div_id" style="width:'.$thumb_width.'px; float:'.$align.'; '.$bordercss.'" class="mtImgBoxStyle">';
              else
                $img = '<div style="width: '.$thumb_width.'px; float: '.$align.';'.$bordercss.'" class="mtImgBoxStyle">';
              $img .= "$captionabove$imgtemp";
              $img .= '  alt="'.$alt.'" title="'.$title.'" border="'.$border.'" />';
              if ($popup_type!='none') $img .='</a>';
              $img .= "$captionbelow</div>";
        
              break;
        }
    }
   if($blog_mode=='link' && $isfirstimage) {
    $isfirstimage = false;
    $this->bot_mt_makeFullArticleLink($img);
   }
   return $img;
}
} // Class End
?>
