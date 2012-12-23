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
	static $relative = true;

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
		$txt = self::prepareContent ( $txt, 'title' );

		return $txt;
	}

	public static function parseBBCode($txt, $parent=null, $len=0) {
		if (!$txt) return;

		$bbcode = KunenaBbcode::getInstance(self::$relative);
		$bbcode->parent = $parent;
		$bbcode->SetLimit($len);
		$bbcode->SetPlainMode(false);
		$txt = $bbcode->Parse($txt);
		$txt = self::prepareContent ( $txt );

		return $txt;
	}

	public static function plainBBCode($txt, $len=0) {
		if (!$txt) return;

		$bbcode = KunenaBbcode::getInstance(self::$relative);
		$bbcode->SetLimit($len);
		$bbcode->SetPlainMode(true);
		$txt = $bbcode->Parse($txt);
		$txt = self::prepareContent ( $txt );

		return $txt;
	}

	public static function stripBBCode($txt, $len=0, $html = true) {
		if (!$txt) return;

		$bbcode = KunenaBbcode::getInstance(self::$relative);
		$bbcode->SetLimit($len);
		$bbcode->SetPlainMode(true);
		$bbcode->SetAllowAmpersand($html);
		$txt = $bbcode->Parse($txt);
		$txt = self::prepareContent ( $txt );
		$txt = strip_tags($txt);
		if (!$html)
			$txt = $bbcode->UnHTMLEncode($txt);

		return $txt;
	}


	public static function &prepareContent(&$content, $target='body')
	{
		$config = KunenaFactory::getConfig()->getPlugin('plg_system_kunena');
		$events			= (int) $config->get('jcontentevents', false);
		$event_target	= (array) $config->get('jcontentevent_target', array('body'));

		if ($events && in_array($target, $event_target)) {
			$row = new stdClass();
			$row->text =& $content;
			// Run events
			if (version_compare(JVERSION, '1.6', '>')) {
				// Joomla 1.6+
				$params = new JRegistry();
			} else {
				// Joomla 1.5
				$params = new JParameter( '' );
			}
			$params->set('ksource', 'kunena');

			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('content');
			if (version_compare(JVERSION, '1.6', '>')) {
				// Joomla 1.6+
				$results = $dispatcher->trigger('onContentPrepare', array ('text', &$row, &$params, 0));
			} else {
				// Joomla 1.5
				$results = $dispatcher->trigger('onPrepareContent', array (&$row, &$params, 0));
			}
			$content = $row->text;
		}
		return $content;
	}

	public static function escape($string) {
		return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
	}
}