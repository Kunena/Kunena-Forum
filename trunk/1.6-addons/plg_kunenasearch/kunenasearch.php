<?php
/**
* @version $Id$
* KunenaSearch Plugin
* @package Kunena Search
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$app =& JFactory::getApplication();

$app->registerEvent( 'onSearch', 'plgSearchkunenasearch' );
$app->registerEvent( 'onSearchAreas', 'plgSearchkunenasearchAreas' );

JPlugin::loadLanguage( 'plg_search_kunenasearch' );

function getKunenaLinkClass()
{
		$path = JPATH_SITE.DS.'components'.DS.'com_kunena'.DS.'lib'.DS.'kunena.link.class.php';
		$false = false;

		// If the file exists include it and try to instantiate the object
		if (file_exists( $path )) {
			require_once( $path );
      $return = new CKunenaLink();
		} else {
			JError::raiseWarning( 0, 'File Kunena Link Class not found.' );
			return $false;
		}

		return $return;
}

function getKunenaConfigClass()
{
		$path = JPATH_SITE.DS. 'components'.DS.'com_kunena'.DS.'lib'.DS.'kunena.config.class.php';    	
		
		$false = false;

		// If the file exists include it and try to instantiate the object
		if (file_exists( $path )) {
			require_once( $path );      
      $return = CKunenaConfig::getInstance();
		} else {
			JError::raiseWarning( 0, 'File Kunena Config Class not found.' );
			return $false;
		}

		return $return;
}

function getKunenaParser()
{
		$path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_kunena'.DS.'libraries'.DS.'html'.DS.'parser.php';
		$false = false;

		// If the file exists include it and try to instantiate the object
		if (file_exists( $path )) {
			require_once( $path );
   } else {
			JError::raiseWarning( 0, 'File Kunena Class Kunena not found.' );
			return $false;
		}		
}

function getKunenaSmileClass()
{
		$path = JPATH_SITE.DS.'components'.DS.'com_kunena'.DS.'lib'.DS.'kunena.smile.class.php';		
		
		$false = false;

		// If the file exists include it and try to instantiate the object
		if (file_exists( $path )) {
			require_once( $path );
      $return = new smile();
		} else {
			JError::raiseWarning( 0, 'File Kunena Smile Class not found.' );
			return $false;
		}

		return $return;
}

function stripBBCode($text) {
		$text = stripslashes ( $text );
		$text = preg_replace ( "'<script[^>]*>.*?</script>'si", "", $text );
		$text = preg_replace ( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text );
		$text = preg_replace ( '/<!--.+?-->/', '', $text );
		$text = preg_replace ( '/{.+?}/', '', $text );
		$text = preg_replace ( '/&nbsp;/', ' ', $text );
		$text = preg_replace ( '/&amp;/', ' ', $text );
		$text = preg_replace ( '/&quot;/', ' ', $text );
		//smilies
		$text = preg_replace ( '/:laugh:/', ' ', $text );
		$text = preg_replace ( '/:angry:/', ' ', $text );
		$text = preg_replace ( '/:mad:/', ' ', $text );
		$text = preg_replace ( '/:unsure:/', ' ', $text );
		$text = preg_replace ( '/:ohmy:/', ' ', $text );
		$text = preg_replace ( '/:blink:/', ' ', $text );
		$text = preg_replace ( '/:huh:/', ' ', $text );
		$text = preg_replace ( '/:dry:/', ' ', $text );
		$text = preg_replace ( '/:lol:/', ' ', $text );
		$text = preg_replace ( '/:money:/', ' ', $text );
		$text = preg_replace ( '/:rolleyes:/', ' ', $text );
		$text = preg_replace ( '/:woohoo:/', ' ', $text );
		$text = preg_replace ( '/:cheer:/', ' ', $text );
		$text = preg_replace ( '/:silly:/', ' ', $text );
		$text = preg_replace ( '/:blush:/', ' ', $text );
		$text = preg_replace ( '/:kiss:/', ' ', $text );
		$text = preg_replace ( '/:side:/', ' ', $text );
		$text = preg_replace ( '/:evil:/', ' ', $text );
		$text = preg_replace ( '/:whistle:/', ' ', $text );
		$text = preg_replace ( '/:pinch:/', ' ', $text );
		//bbcode
		$text = preg_replace ( '/\[hide==([1-3])\](.*?)\[\/hide\]/s', '', $text );
		$text = preg_replace ( '/\[confidential\](.*?)\[\/confidential\]/s', '', $text );
		$text = preg_replace ( '/(\[b\])/', ' ', $text );
		$text = preg_replace ( '/(\[\/b\])/', ' ', $text );
		$text = preg_replace ( '/(\[s\])/', ' ', $text );
		$text = preg_replace ( '/(\[\/s\])/', ' ', $text );
		$text = preg_replace ( '/(\[i\])/', ' ', $text );
		$text = preg_replace ( '/(\[\/i\])/', ' ', $text );
		$text = preg_replace ( '/(\[u\])/', ' ', $text );
		$text = preg_replace ( '/(\[\/u\])/', ' ', $text );
		$text = preg_replace ( '/(\[quote\])/', ' ', $text );
		$text = preg_replace ( '/(\[\/quote\])/', ' ', $text );
		$text = preg_replace ( '/(\[strike\])/', ' ', $text );
		$text = preg_replace ( '/(\[\/strike\])/', ' ', $text );
		$text = preg_replace ( '/(\[sub\])/', ' ', $text );
		$text = preg_replace ( '/(\[\/sub\])/', ' ', $text );
		$text = preg_replace ( '/(\[sup\])/', ' ', $text );
		$text = preg_replace ( '/(\[\/sup\])/', ' ', $text );
		$text = preg_replace ( '/(\[left\])/', ' ', $text );
		$text = preg_replace ( '/(\[\/left\])/', ' ', $text );
		$text = preg_replace ( '/(\[center\])/', ' ', $text );
		$text = preg_replace ( '/(\[\/center\])/', ' ', $text );
		$text = preg_replace ( '/(\[right\])/', ' ', $text );
		$text = preg_replace ( '/(\[\/right\])/', ' ', $text );
		$text = preg_replace ( '/(\[code:1\])(.*?)(\[\/code:1\])/', '\\2', $text );
		$text = preg_replace ( '/(\[ul\])(.*?)(\[\/ul\])/s', '\\2', $text );
		$text = preg_replace ( '/(\[li\])(.*?)(\[\/li\])/s', '\\2', $text );
		$text = preg_replace ( '/(\[ol\])(.*?)(\[\/ol\])/s', '\\2', $text );
		$text = preg_replace ( '/\[img size=([0-9][0-9][0-9])\](.*?)\[\/img\]/s', '\\2', $text );
		$text = preg_replace ( '/\[img size=([0-9][0-9])\](.*?)\[\/img\]/s', '\\2', $text );
		$text = preg_replace ( '/\[img\](.*?)\[\/img\]/s', '\\1', $text );
		$text = preg_replace ( '/\[url\](.*?)\[\/url\]/s', '\\1', $text );
		$text = preg_replace ( '/\[url=(.*?)\](.*?)\[\/url\]/s', '\\2 (\\1)', $text );
		$text = preg_replace ( '/<A (.*)>(.*)<\/A>/i', '\\2', $text );
		$text = preg_replace ( '/\[file(.*?)\](.*?)\[\/file\]/s', '\\2', $text );
		$text = preg_replace ( '/\[hide(.*?)\](.*?)\[\/hide\]/s', ' ', $text );
		$text = preg_replace ( '/\[spoiler(.*?)\](.*?)\[\/spoiler\]/s', ' ', $text );
		$text = preg_replace ( '/\[size=([1-7])\](.+?)\[\/size\]/s', '\\2', $text );
		$text = preg_replace ( '/\[color=(.*?)\](.*?)\[\/color\]/s', '\\2', $text );
		$text = preg_replace ( '/\[video\](.*?)\[\/video\]/s', '\\1', $text );
		$text = preg_replace ( '/\[ebay\](.*?)\[\/ebay\]/s', '\\1', $text );
		$text = preg_replace ( '#/n#s', ' ', $text );
		$text = strip_tags ( $text );
		
		return (trim($text));
	}

//Then define a function to return an array of search areas.
function &plgSearchkunenasearchAreas()
{
        static $areas = array(
                'kunenasearch' => 'Kunenasearch'
        );
        return $areas;
}
 
//Then the real function has to be created. The database connection should be made. 
//The function will be closed with an } at the end of the file.
function plgSearchkunenasearch( $text, $phrase='', $ordering='', $areas=null )
{
        $db    =& JFactory::getDBO();
        $user  =& JFactory::getUser();      
        
        $kconfig = getKunenaConfigClass();
        
        if ( !defined('KUNENA_PATH_LIB')) {
          define('KUNENA_PATH_LIB', JPATH_ROOT .DS. 'components' .DS. 'com_kunena' .DS. 'lib');
        }
        
        if ( !defined('KUNENA_URLEMOTIONSPATH')) {
          define('KUNENA_URLEMOTIONSPATH', JURI::Root(). 'components' .DS.'com_kunena'.DS. 'template'.DS.'default' .DS. 'images'.DS. 'emoticons/');
        }
        
        define('KUNENA_LIVEURLREL', 'index.php?option=com_kunena');
        
        $ksmile = getKunenaSmileClass();       
        $klink = getKunenaLinkClass();
        $ktools = getKunenaParser();             
 
//If the array is not correct, return it:
        if (is_array( $areas )) {
                if (!array_intersect( $areas, array_keys( plgSearchkunenasearchAreas() ) )) {
                        return array();
                }
        }
 
$plugin =& JPluginHelper::getPlugin('search', 'kunenasearch');
$pluginParams = new JParameter( $plugin->params );
 
//And define the parameters. For example like this..
$limit = $pluginParams->def( 'search_limit', 50 );
$contentLimit = $pluginParams->def( 'content_limit', 40 );
$shBbcode = $pluginParams->def( 'show_bbcode', 1 );
 
//Use the function trim to delete spaces in front of or at the back of the searching terms
$text = trim( $text );
 
//Return Array when nothing was filled in
if ($text == '') {
                return array();
        }
 
//After this, you have to add the database part. This will be the most difficult part, because this changes per situation.
//In the coding examples later on you will find some of the examples used by Joomla! 1.5 core Search Plugins.
//It will look something like this.
        $wheres = array();
        switch ($phrase) {
 
//search exact
                case 'exact':
                        $text          = $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );
                        $wheres2       = array();
                        $wheres2[]   = 'LOWER(m.subject) LIKE '.$text.' AND LOWER(t.message) LIKE '.$text;
                        $where                 = '(' . implode( ') OR (', $wheres2 ) . ')';
                        break;
 
//search all or any
                case 'all':
                case 'any':
 
//set default
                default:
                        $words         = explode( ' ', $text );
                        $wheres = array();
                        foreach ($words as $word)
                        {
                                $word          = $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
                                $wheres2       = array();
                                $wheres2[]   = 'LOWER(m.subject) LIKE '.$word.' AND LOWER(t.message) LIKE '.$word;
                                $wheres[]    = implode( ' OR ', $wheres2 );
                        }
                        $where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
                        break;
        }
 
//ordering of the results
        switch ( $ordering ) {
 
//alphabetic, ascending
                case 'alpha':
                        $order = 'm.subject ASC, created ASC';
                        break;
 
//oldest first
                case 'oldest':
                      $order = 'm.subject DESC, created DESC';
                        break;
 
//popular first
                case 'popular':
                    $order = 'm.hits ASC, m.time ASC';
                        break;
//newest first
                case 'newest':
                      $order = 'm.time ASC, m.ordering ASC, m.catid ASC';
                        break;
                      
//default setting: alphabetic, ascending
                default:
                        $order = 'm.subject ASC, created ASC';
        }
 
//replace nameofplugin
        $searchNameofplugin = JText::_( 'Kunenasearch' );
 
        $query = "SELECT m.id, m.subject AS title, m.catid, m.thread, m.name, m.time AS created, t.mesid, t.message AS text, m.ordering, m.hits,
						c.name AS section, 1 AS browsernav
        		FROM #__fb_messages_text AS t JOIN #__fb_messages AS m ON m.id=t.mesid
        		JOIN #__fb_categories AS c ON m.catid = c.id
        		WHERE {$where} AND m.moved=0 AND m.hold=0 GROUP BY m.subject ORDER BY {$order}";
 
//Set query
        $db->setQuery( $query, 0, $limit );
        $rows = $db->loadObjectList();        
 
//The 'output' of the displayed link
        foreach($rows as $key => $row) {
        if ($shBbcode) {
          $row->text = KunenaParser::parseBBCode($row->text);
        } else {
          $row->text = stripBBCode($row->text);
        }
                 
                $row->title = JString::substr(stripslashes($row->title),'0',$contentLimit);
                $row->section = stripslashes($row->section);                                  
                $rows[$key]->href = $klink->GetThreadPageURL ( 'view', $row->catid, $row->thread, 1, $kconfig->messages_per_page, 1, '', $rel = 'follow' );
        }
 
//Return the search results in an array
return $rows;
}

?>
