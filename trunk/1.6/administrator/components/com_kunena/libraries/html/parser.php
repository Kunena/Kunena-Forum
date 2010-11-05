<?php
/**
* @version $Id$
* Kunena Component - Kunena Factory
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.org All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

abstract class KunenaParser {
	static $emoticons = null;

	function JSText($txt) {
		$txt = JText::_($txt);
		$txt = preg_replace('`\'`','\\\\\'', $txt);
		return $txt;
	}

	function parseText($txt, $len=0) {
		if (!$txt) return;
		if ($len && JString::strlen($txt) > $len) $txt = JString::substr ( $txt, 0, $len ) . ' ...';
		$txt = self::escape ( $txt );
		$txt = preg_replace('/(\S{30})/u', '\1&#8203;', $txt);
		$txt = self::prepareContent ( $txt );
		return $txt;
	}

	function parseBBCode($txt, $parent=null) {
		if (!$txt) return;
		if (!self::$emoticons) self::$emoticons = smile::getEmoticons ( 0 );

		$config = KunenaFactory::getConfig ();
		$txt = smile::smileReplace ( $txt, 0, $config->disemoticons, self::$emoticons, $parent );
		$txt = nl2br ( $txt );
		$txt = str_replace ( "__KTAB__", "&#009;", $txt ); // For [code]
		$txt = str_replace ( "__KRN__", "\n", $txt ); // For [code]
		$txt = self::prepareContent ( $txt );
		return $txt;
	}

	function stripBBCode($txt, $len=0) {
		if (!$txt) return;
		if (!self::$emoticons) self::$emoticons = smile::getEmoticons ( 0 );

		$txt = smile::purify ( $txt );
		if ($len && JString::strlen($txt) > $len) $txt = JString::substr ( $txt, 0, $len ) . '...';
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