<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/
defined ( '_JEXEC' ) or die ();

include_once (KUNENA_PATH_LIB . DS . "kunena.parser.base.php");
include_once (KUNENA_PATH_LIB . DS . "kunena.parser.php");

class smile {
	function smileParserCallback($fb_message, $history, $emoticons, $iconList = null) {
		// from context HTML into HTML

		// where $history can be 1 or 0. If 1 then we need to load the grey
		// emoticons for the Topic History. If 0 we need the normal ones

		static $regexp_trans = array ('/' => '\/', '^' => '\^', '$' => '\$', '.' => '\.', '[' => '\[', ']' => '\]', '|' => '\|', '(' => '\(', ')' => '\)', '?' => '\?', '*' => '\*', '+' => '\+', '{' => '\{', '}' => '\}', '\\' => '\\\\', '^' => '\^', '-' => '\-' );

		$type = ($history == 1) ? "-grey" : "";
		$message_emoticons = array ();
		$message_emoticons = $iconList ? $iconList : smile::getEmoticons ( $history );
		// now the text is parsed, next are the emoticons
		$fb_message_txt = $fb_message;

		if ($emoticons != 1) {
			foreach ( $message_emoticons as $emo_txt => $emo_src ) {
				$emo_rtxt = strtr ( $emo_txt, $regexp_trans );
				// Check that smileys are not part of text like:soon (:s)
				$fb_message_txt = preg_replace ( '/(\W|\A)' . $emo_rtxt . '(\W|\Z)/u', '\1<img class="ksmiley" src="' . $emo_src . '" alt="" />\2', $fb_message_txt );
				// Previous check causes :) :) not to work, workaround is to run the same regexp twice
				$fb_message_txt = preg_replace ( '/(\W|\A)' . $emo_rtxt . '(\W|\Z)/u', '\1<img class="ksmiley" src="' . $emo_src . '" alt="" />\2', $fb_message_txt );
			}
		}
		return $fb_message_txt;
	}

	function smileReplace($fb_message, $history, $emoticons, $iconList = null, $parent = null) {

		$fb_message_txt = $fb_message;

		//implement the new parser
		$parser = new TagParser ( );
		$interpreter = new KunenaBBCodeInterpreter ( $parser, $parent );
		$task = $interpreter->NewTask ();
		$task->SetText ( $fb_message_txt . ' _EOP_' );
		$task->dry = FALSE;
		$task->drop_errtag = FALSE;
		$task->history = $history;
		$task->emoticons = $emoticons;
		$task->iconList = $iconList;
		$task->Parse ();

		return JString::trim( JString::substr ( $task->text, 0, - 6 ) );
	}

	/**
	 * function to retrieve the emoticons out of the database
	 *
	 * @author Niels Vandekeybus <progster@wina.be>
	 * @version 1.0
	 * @since 2005-04-19
	 * @param boolean $grayscale
	 *            determines wether to return the grayscale or the ordinary emoticon
	 * @param boolean  $emoticonbar
	 *            only list emoticons to be displayed in the emoticonbar (currently unused)
	 * @return array
	 *             array consisting of emoticon codes and their respective location (NOT the entire img tag)
	 */
	function getEmoticons($grayscale, $emoticonbar = 0) {
		$kunena_db = &JFactory::getDBO ();
		$grayscale == 1 ? $column = "greylocation" : $column = "location";
		$sql = "SELECT code, `$column` FROM #__kunena_smileys";

		if ($emoticonbar == 1)
			$sql .= " WHERE emoticonbar='1'";

		$kunena_db->setQuery ( $sql );
		$smilies = $kunena_db->loadObjectList ();
		KunenaError::checkDatabaseError();

		$smileyArray = array ();
		foreach ( $smilies as $smiley ) { // We load all smileys in array, so we can sort them
			$iconurl = JURI::Root() . CKunenaTools::getTemplateImage("emoticons/{$smiley->$column}");
			$smileyArray [$smiley->code] = '' . $iconurl; // This makes sure that for example :pinch: gets translated before :p
		}

		if ($emoticonbar == 0) { // don't sort when it's only for use in the emoticonbar
			array_multisort ( array_keys ( $smileyArray ), SORT_DESC, $smileyArray );
			reset ( $smileyArray );
		}

		return $smileyArray;
	}

	function purify($text) {

		$text = preg_replace ( "'<script[^>]*>.*?</script>'si", "", $text );
		$text = preg_replace ( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text );
		$text = preg_replace ( '/<!--.+?-->/', '', $text );
		$text = preg_replace ( '/{.+?}/', '', $text );
		$text = preg_replace ( '/&nbsp;/', ' ', $text );
		$text = preg_replace ( '/&amp;/', ' ', $text );
		$text = preg_replace ( '/&quot;/', ' ', $text );
		//smilies
		$text = preg_replace ( '/:laugh:/', ':-D', $text );
		$text = preg_replace ( '/:angry:/', ' ', $text );
		$text = preg_replace ( '/:mad:/', ' ', $text );
		$text = preg_replace ( '/:unsure:/', ' ', $text );
		$text = preg_replace ( '/:ohmy:/', ':-O', $text );
		$text = preg_replace ( '/:blink:/', ' ', $text );
		$text = preg_replace ( '/:huh:/', ' ', $text );
		$text = preg_replace ( '/:dry:/', ' ', $text );
		$text = preg_replace ( '/:lol:/', ':-))', $text );
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
		$text = preg_replace ( '/(\[code.*?\])(.*?)(\[\/code\])/', '\\2', $text );
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
		$text = preg_replace ( '/\[attachment(.*?)\](.*?)\[\/attachment\]/s', '\\2', $text );
		$text = preg_replace ( '/\[hide(.*?)\](.*?)\[\/hide\]/s', ' ', $text );
		$text = preg_replace ( '/\[spoiler(.*?)\](.*?)\[\/spoiler\]/s', ' ', $text );
		$text = preg_replace ( '/\[size=([1-7])\](.+?)\[\/size\]/s', '\\2', $text );
		$text = preg_replace ( '/\[color=(.*?)\](.*?)\[\/color\]/s', '\\2', $text );
		$text = preg_replace ( '/\[highlight(.*?)\](.*?)\[\/highlight\]/s', '\\2', $text );
		$text = preg_replace ( '/\[indent(.*?)\](.*?)\[\/indent\]/s', '\\2', $text );
		$text = preg_replace ( '/\[video\](.*?)\[\/video\]/s', '\\1', $text );
		$text = preg_replace ( '/\[ebay\](.*?)\[\/ebay\]/s', '\\1', $text );
		$text = preg_replace ( '/\[table(.*?)\](.*?)\[\/table\]/s', '\\2', $text );
		$text = preg_replace ( '/\[tr(.*?)\](.*?)\[\/tr\]/s', '\\2', $text );
		$text = preg_replace ( '/\[th(.*?)\](.*?)\[\/th\]/s', '\\2', $text );
		$text = preg_replace ( '/\[td(.*?)\](.*?)\[\/td\]/s', '\\2', $text );
		$text = preg_replace ( '/\[email(.*?)\](.*?)\[\/email\]/s', '\\2', $text );
		$text = preg_replace ( '/\[quote=(.*?)\](.*?)\[\/quote\]/s', '\\2', $text );
		$text = preg_replace ( '/\[module(.*?)\](.*?)\[\/module\]/s', ' ', $text );
		$text = preg_replace ( '/\[article(.*?)\](.*?)\[\/article\]/s', ' ', $text );
		$text = preg_replace ( '/\[list(.*?)\](.*?)\[\/list\]/s', '\\2', $text );
		$text = preg_replace ( '/\[map(.*?)\](.*?)\[\/map\]/s', '\\2', $text );
		$text = preg_replace ( '/\[indent(.*?)\](.*?)\[\/indent\]/s', '\\2', $text );

		$text = preg_replace ( '#/n#s', ' ', $text );
		$text = strip_tags ( $text );

		return (trim($text));
	} //purify
}
