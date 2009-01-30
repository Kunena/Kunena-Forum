<?php
/**
 * sh404SEF support for fireboard forum component.
 * Copyright Yannick Gaultier (shumisha) - 2007
 * shumisha@gmail.com
 * @version     $Id: com_fireboard.php 229 2008-01-21 19:53:39Z silianacom-svn $
 * {shSourceVersionTag: Version x - 2007-09-20}
 */

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// ------------------  standard plugin initialize function - don't change ---------------------------
global $sh_LANG, $sefConfig;  
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin( $lang, $shLangName, $shLangIso, $option);
if ($dosef == false) return;
// ------------------  standard plugin initialize function - don't change ---------------------------

// ------------------  load language file - adjust as needed ----------------------------------------
$shLangIso = shLoadPluginLanguage( 'com_fireboard', $shLangIso, '_SH404SEF_FB_SHOW_USER_PROFILE');
// ------------------  load language file - adjust as needed ----------------------------------------

shRemoveFromGETVarsList('option');
if (!empty($lang))
  shRemoveFromGETVarsList('lang');
if (!empty($Itemid))
  shRemoveFromGETVarsList('Itemid');

// start Fireboard specific stuff
$func = isset($func) ? @$func : null; 
$task = isset($task) ? $task : null; // V 1.2.4.t  
$Itemid = isset($Itemid) ? $Itemid : null; // V 1.2.4.t 
$do = isset($do) ? $do : null; // V 1.2.4.t 

if (!function_exists('shFBCategoryName')) {
  function shFBCategoryName( $option, $catid, $shLangIso, $shLangName) {

  global $sefConfig, $database, $sh_LANG;
  
  if (empty($catid)) return '';
  if ( $sefConfig->shFbInsertCategoryName) { 
    $query  = "SELECT id, name FROM #__fb_categories" ;
		$query .= "\n WHERE id=".$catid;
	  $database->setQuery( $query );
	  if (!shTranslateUrl($option, $shLangName))  // V 1.2.4.m
	    $database->loadObject($result, false);
	  else   
	    $database->loadObject($result);
  } else $result = '';  
  if (!empty($result))
    $result->name = str_replace( '&amp;', '', 
                             $result->name); // V x 03/09/2007 13:28:48 FB stores URl with & replaced by &amp;    
	$shCat = empty($result)?  // no name available
    $sh_LANG[$shLangIso]['_SH404SEF_FB_CATEGORY'].$sefConfig->replacement.$catid // put ID
    : ($sefConfig->shFbInsertCategoryId ? $catid.$sefConfig->replacement : ''); // if name, put ID only if requested
  //return $shCat.(empty( $result ) ? '' :  $result->name);  
  return $shCat.(empty( $result ) ? '' : html_entity_decode(stripslashes($result->name), ENT_COMPAT)); // V w 27/08/2007 20:17:55
  }        
}

// shumisha : insert magazine name from menu
$shFireboardName = shGetComponentPrefix($option);
$shFireboardName = empty($shFireboardName) ?  getMenuTitle($option, null, $Itemid, null, $shLangName ) : $shFireboardName;
$shFireboardName = (empty($shFireboardName) || $shFireboardName == '/') ? 'Forum':$shFireboardName; // V 1.2.4.t 
 
switch ($func)
{
    case 'userprofile': 
      shRemoveFromGETVarsList('func');
      switch ($do) {
        case 'show':
          if ($sefConfig->shInsertFireboardName) $title[] = $shFireboardName;
		      $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_SHOW_USER_PROFILE'];
		      shRemoveFromGETVarsList('do');
		    break;
		    case 'unfavorite':
		      if ($sefConfig->shInsertFireboardName) $title[] = $shFireboardName;
		      $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_SHOW_USER_UNFAVORITE'];
		      shRemoveFromGETVarsList('do');
		    break;
		    case 'unsubscribe':
		      if ($sefConfig->shInsertFireboardName) $title[] = $shFireboardName;
		      $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_SHOW_USER_UNSUBSCRIBE'];
		      shRemoveFromGETVarsList('do');
		    break;
		    case 'update':
		      if ($sefConfig->shInsertFireboardName) $title[] = $shFireboardName;  // V1.2.4.s
		      $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_SHOW_USER_UPDATE'];
		      shRemoveFromGETVarsList('do');
		    break;
      }
      
		break;
		case 'latest':
		  if ($sefConfig->shInsertFireboardName) $title[] = $shFireboardName;
		  if ($do == 'show' && isset($sel))
		      $title[]= $sh_LANG[$shLangIso]['_SH404SEF_FB_SHOW_LATEST_'.$sel];
		  else $title[]= $sh_LANG[$shLangIso]['_SH404SEF_FB_SHOW_LATEST'];   
      shRemoveFromGETVarsList('do'); 
      shRemoveFromGETVarsList('func');
      if (isset($sel))
        shRemoveFromGETVarsList('sel');
		break;
		case 'rules':  // V 1.2.4.g 2007-04-07
		  if ($sefConfig->shInsertFireboardName) $title[] = $shFireboardName;
      shRemoveFromGETVarsList('func');
      $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_SHOW_RULES'];
		break;
		
		case 'faq':
		  if ($sefConfig->shInsertFireboardName) $title[] = $shFireboardName;
      $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_SHOW_FAQ'];
      shRemoveFromGETVarsList('func');
		break;
		  
    case 'showcat':
      //catid = 2 
      // view = threaded // view = flat
      if ($sefConfig->shInsertFireboardName) $title[] = $shFireboardName;
      shRemoveFromGETVarsList('func');
      $shCat = shFBCategoryName( $option, $catid, $shLangIso, $shLangName);
		  if (!empty ($shCat)) {
        $title[] = $shCat;
		    shRemoveFromGETVarsList('catid');
		  }
		  switch ($view){
        case 'threaded':
          $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_THREADED'];
          shRemoveFromGETVarsList('view');
        break;
        case 'flat':
          $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_FLAT'];
          shRemoveFromGETVarsList('view');
        break;
      }
      if (!empty($title)) $title[] = '/';  // V 1.2.4.s   
    break;
		
		case 'view':
		  //catid= 2
		  //id=1
		  if ($sefConfig->shInsertFireboardName) $title[] = $shFireboardName;
      shRemoveFromGETVarsList('func');
      $shCat = shFBCategoryName( $option, $catid, $shLangIso, $shLangName);  // V 1.2.4.q $option was missing !
		  if (!empty ($shCat)) $title[] = $shCat;
		  shRemoveFromGETVarsList('catid');
		  $result = null;
		  if (!empty($id)) {
		    if ( $sefConfig->shFbInsertMessageSubject) { 
          $query  = "SELECT id, subject FROM #__fb_messages" ;
		      $query .= "\n WHERE id=".$id;
		      $database->setQuery( $query );
		      if (!shTranslateUrl($option, $shLangName))  // V 1.2.4.m
		        $database->loadObject($result, false);
		      else   
		        $database->loadObject($result);
        } else $result = '';  
		    $shMsg = empty($result)?  // no name available
          $sh_LANG[$shLangIso]['_SH404SEF_FB_MESSAGE'].$sefConfig->replacement.$id // put ID
          : ($sefConfig->shFbInsertMessageId ? $id.$sefConfig->replacement : ''); // if name, put ID only if requested
        $result->subject = str_replace( '&amp;', '', 
                             $result->subject); // V x 03/09/2007 13:28:48 FB stores URl with & replaced by &amp;
		    $title[] = $shMsg.(empty( $result ) ? '' 
           : html_entity_decode(stripslashes($result->subject), ENT_COMPAT)); // V w 27/08/2007 20:20:24
		    shRemoveFromGETVarsList('id');
      } 
		break;
		
		case 'upload':
		  $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_UPLOAD_AVATAR'];
		  shRemoveFromGETVarsList('func');
		break;
		
		case 'stats':
		  if ($sefConfig->shInsertFireboardName) $title[] = $shFireboardName;
		  $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_STATS'];
		  shRemoveFromGETVarsList('func');
		break;
		
		case 'post':
		  if ($sefConfig->shInsertFireboardName) $title[] = $shFireboardName;
		  shRemoveFromGETVarsList('func');
		  switch ($do) {
		    case 'reply': // do = reply replyto=1 catid=2
		      $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_REPLY'];
		      $shCat = shFBCategoryName( $option, $catid, $shLangIso, $shLangName);  // V 1.2.4.q $option was missing
		      if (!empty ($shCat)) { 
            $title[] = $shCat;
            shRemoveFromGETVarsList('catid');
          }   
		      shRemoveFromGETVarsList('do');
		    break;
		    // do = subscribe catid=2 id = 1 fb_thread = 1
		    case 'subscribe':
		      $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_SUBSCRIBE'];
		      shRemoveFromGETVarsList('do');
		    break;
		    // do = favorite catid=2 id = 1 fb_thread = 1
		    case 'favorite':
   		    $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_FAVORITE'];
		      shRemoveFromGETVarsList('do');
		    break;
		    // do=quote&replyto=1&catid=2
		    case 'quote':
		      $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_QUOTE'];
		      shRemoveFromGETVarsList('do');
		    break;
		    // do=delete&id=1&catid=2
		    case 'delete':
		      $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_DELETE'];
		      shRemoveFromGETVarsList('do');
		    break;
		    // do=move&id=1&catid=2&name=bestofjoomla
		    case 'move':
		      $dosef = false;
		    break;
		    // do=edit&id=1&catid=2
		    case 'edit':
          $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_EDIT'];
		      shRemoveFromGETVarsList('do');
		    break;
		    case 'newFromBot':  // V 1.2.4.s
		    case 'newfrombot':
          $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_NEW_FROM_BOT'];
          // workaround for discuss bot/ FB 1.0.0 and 1.0.1 bug
          if ($do != 'newFromBot') {
            $do = 'newFromBot';
            shAddToGETVarsList('do', $do);
          }  
		      shRemoveFromGETVarsList('do');
		    break;
		    // do=sticky&id=1&catid=2
		    case 'sticky':
		      $dosef = false;
		    break;
		    // do=lock&id=1&catid=2
		    case 'lock':
		      $dosef = false;
		    break;
		    default:  // if creating new post, data is passed through POST, so other variables than func is not available
		      $dosef = false;
		    break;
		  }
		break;
		
		case 'markThisRead':
		  //catid = 2
		  $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_MARK_THIS_READ'];
		  shRemoveFromGETVarsList('func');
		break;
		
    case 'fb_pdf':
      // &id=1&catid=2&func=fb_pdf
      $dosef=false;
		break;
		
    default:
      switch ($task) {
        case 'listcat':
          $title[] = $sh_LANG[$shLangIso]['_SH404SEF_FB_LIST_CAT'];
		      shRemoveFromGETVarsList('task');
		      $shCat = shFBCategoryName( $option, $catid, $shLangIso, $shLangName);  // V 1.2.4.q $option was missing
		      if (!empty ($shCat)) { 
            $title[] = $shCat;
            shRemoveFromGETVarsList('catid');
          }   
        break;
        default:
          $title[] = $shFireboardName;
          $title[] = '/'; // V 1.2.4.s
        break;
      }
    break;
}

// ------------------  standard plugin finalize function - don't change ---------------------------  
if ($dosef){
   $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString, 
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), 
      (isset($shLangName) ? @$shLangName : null));
}      
// ------------------  standard plugin finalize function - don't change ---------------------------

?>
