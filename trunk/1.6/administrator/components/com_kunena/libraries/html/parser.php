<?php
/**
* @version $Id$
* Kunena Component - Kunena Factory
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

abstract class KunenaParser {
	static $emoticons = null;

	function parseText($txt) {
		if (!$txt) return;
		$txt = self::escape ( $txt );
		$txt = self::prepareContent ( $txt );
		return $txt;
	}

	function parseBBCode($txt) {
		if (!$txt) return;
		if (!self::$emoticons) self::$emoticons = smile::getEmoticons ( 0 );

		$config = KunenaFactory::getConfig ();
		$txt = smile::smileReplace ( $txt, 0, $config->disemoticons, self::$emoticons );
		$txt = nl2br ( $txt );
		$txt = str_replace ( "__FBTAB__", "&#009;", $txt ); // For [code]
		$txt = self::prepareContent ( $txt );
		return $txt;
	}

	function stripBBCode($txt, $len=0) {
		if (!$txt) return;
		if (!self::$emoticons) self::$emoticons = smile::getEmoticons ( 0 );

		$txt = smile::purify ( $txt );
		if ($len) $txt = JString::substr ( $txt, 0, $len );
		$txt = self::escape ( $txt );
		$txt = self::prepareContent ( $txt );
		return $txt;
	}

	function &prepareContent(&$content)
	{
		$config = KunenaFactory::getConfig();

		if ($config->jmambot)
		{
			$row = new stdClass();
			$row->text =& $content;
			$params = new JParameter( '' );
			$dispatcher	= JDispatcher::getInstance();
			JPluginHelper::importPlugin('content');
			$results = $dispatcher->trigger('onPrepareContent', array (&$row, &$params, 0));
			$content =& $row->text;
		}
		return $content;
	}


	function escape($string) {
		return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
	}
}