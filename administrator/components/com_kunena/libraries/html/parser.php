<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage HTML
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

abstract class KunenaHtmlParser {
	static $emoticons = null;

	public static function getEmoticons($grayscale = false, $emoticonbar = false) {
		$db = JFactory::getDBO ();
		$grayscale == true ? $column = "greylocation" : $column = "location";
		$sql = "SELECT code, `$column` as file FROM #__kunena_smileys";

		if ($emoticonbar == true)
			$sql .= " WHERE emoticonbar='1'";

		$db->setQuery ( $sql );
		$smilies = $db->loadObjectList ();
		KunenaError::checkDatabaseError();

		$smileyArray = array ();
		$template = KunenaFactory::getTemplate();
		foreach ( $smilies as $smiley ) { // We load all smileys in array, so we can sort them
			$smileyArray [$smiley->code] = JURI::root(true) .'/'. $template->getSmileyPath($smiley->file);
		}

		if ($emoticonbar == 0) { // don't sort when it's only for use in the emoticonbar
			array_multisort ( array_keys ( $smileyArray ), SORT_DESC, $smileyArray );
			reset ( $smileyArray );
		}
		return $smileyArray;
	}

	public static function JSText($txt) {
		$txt = JText::_($txt);
		$txt = preg_replace('`\'`','\\\\\'', $txt);
		return $txt;
	}

	public static function parseText($txt, $len=0) {
		if (!$txt) return;

		if ($len && JString::strlen($txt) > $len) $txt = JString::substr ( $txt, 0, $len ) . ' ...';
		$txt = self::escape ( $txt );
		$txt = preg_replace('/(\S{30})/u', '\1&#8203;', $txt);
		return $txt;
	}

	public static function parseBBCode($txt, $parent=null, $len=0) {
		if (!$txt) return;

		$bbcode = KunenaBbcode::getInstance();
		$bbcode->parent = $parent;
		$bbcode->SetLimit($len);
		$bbcode->SetPlainMode(false);
		$txt = $bbcode->Parse($txt);
		return $txt;
	}

	public static function plainBBCode($txt, $len=0) {
		if (!$txt) return;

		// Strip content not allowed for guests
		// TODO: do this in a better way inside BBCode parser..
		$txt = preg_replace ( '/\[hide\](.*?)\[\/hide\]/s', '', $txt );
		$txt = preg_replace ( '/\[confidential\](.*?)\[\/confidential\]/s', '', $txt );
		$txt = preg_replace ( '/\[spoiler\]/s', '[spoilerlight]', $txt );
		$txt = preg_replace ( '/\[\/spoiler\]/s', '[/spoilerlight]', $txt );
		$txt = preg_replace ( '/\[attachment(.*?)\](.*?)\[\/attachment\]/s', '', $txt );
		$txt = preg_replace ( '/\[code\](.*?)\[\/code]/s', '', $txt );

		$bbcode = KunenaBbcode::getInstance();
		$bbcode->SetLimit($len);
		$bbcode->SetPlainMode(true);
		$txt = $bbcode->Parse($txt);
		return $txt;
	}

	public static function stripBBCode($txt, $len=0) {
		if (!$txt) return;

		$bbcode = KunenaBbcode::getInstance();
		$bbcode->SetLimit($len);
		$bbcode->SetPlainMode(true);
		$txt = strip_tags($bbcode->Parse($txt));
		return $txt;
	}

	public static function escape($string) {
		return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
	}
}