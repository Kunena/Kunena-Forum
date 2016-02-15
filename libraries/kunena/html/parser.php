<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage HTML
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaHtmlParser
 */
abstract class KunenaHtmlParser
{
	static $emoticons = null;
	static $relative = true;

	public static function getEmoticons($grayscale = false, $emoticonbar = false)
	{
		$db = JFactory::getDBO ();
		$grayscale == true ? $column = "greylocation" : $column = "location";
		$sql = "SELECT code, {$db->quoteName($column)} AS file FROM #__kunena_smileys";

		if ($emoticonbar == true)
		{
			$sql .= " WHERE emoticonbar='1'";
		}

		$db->setQuery ( $sql );
		$smilies = $db->loadObjectList ();
		KunenaError::checkDatabaseError();

		$smileyArray = array ();
		$template = KunenaFactory::getTemplate();

		foreach ( $smilies as $smiley )
		{
			// We load all smileys in array, so we can sort them
			$smileyArray [$smiley->code] = JUri::root(true) .'/'. $template->getSmileyPath($smiley->file);
		}

		if ($emoticonbar == 0)
		{
			// don't sort when it's only for use in the emoticonbar
			array_multisort ( array_keys ( $smileyArray ), SORT_DESC, $smileyArray );
			reset ( $smileyArray );
		}
		return $smileyArray;
	}

	/**
	 * @deprecated 3.0.0
	 *
	 * @param $txt
	 *
	 * @return string
	 */
	public static function JSText($txt)
	{
		return JText::_($txt, true);
	}

	public static function parseText($txt, $len = 0)
	{
		if (!$txt)
		{
			return;
		}

		if ($len && JString::strlen($txt) > $len)
		{
			$txt = JString::substr ( $txt, 0, $len ) . ' ...';
		}

		$txt = self::escape ( $txt );
		$txt = preg_replace('/(\S{30})/u', '\1&#8203;', $txt);
		$txt = self::prepareContent ( $txt, 'title' );

		return $txt;
	}

	public static function parseBBCode($txt, $parent = null, $len = 0, $context = '')
	{
		if (!$txt)
		{
			return;
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		$bbcode = KunenaBbcode::getInstance(self::$relative);
		$bbcode->parent = $parent;
		$bbcode->SetLimit($len);
		$bbcode->context = $context;
		$bbcode->SetPlainMode(false);
		$txt = $bbcode->Parse($txt);
		$txt = self::prepareContent ( $txt );

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		return $txt;
	}

	public static function plainBBCode($txt, $len = 0)
	{
		if (!$txt)
		{
			return;
		}

		$bbcode = KunenaBbcode::getInstance(self::$relative);
		$bbcode->SetLimit($len);
		$bbcode->SetPlainMode(true);
		$txt = $bbcode->Parse($txt);
		$txt = self::prepareContent ( $txt );

		return $txt;
	}

	public static function stripBBCode($txt, $len=0, $html = true)
	{
		if (!$txt)
		{
			return;
		}

		$bbcode = KunenaBbcode::getInstance(self::$relative);
		$bbcode->SetLimit($len);
		$bbcode->SetPlainMode(true);
		$bbcode->SetAllowAmpersand($html);
		$txt = $bbcode->Parse($txt);
		$txt = self::prepareContent ( $txt );
		$txt = strip_tags($txt);

		if (!$html)
		{
			$txt = $bbcode->UnHTMLEncode($txt);
		}

		return $txt;
	}


	public static function &prepareContent(&$content, $target='body')
	{
		$config = KunenaFactory::getConfig()->getPlugin('plg_system_kunena');
		$events			= (int) $config->get('jcontentevents', false);
		$event_target	= (array) $config->get('jcontentevent_target', array('body'));

		if ($events && in_array($target, $event_target))
		{
			$row = new stdClass();
			$row->text =& $content;
			// Run events
			$params = new JRegistry();
			$params->set('ksource', 'kunena');

			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('content');
			$dispatcher->trigger('onContentPrepare', array ('text', &$row, &$params, 0));
			$content = $row->text;
		}

		return $content;
	}

	public static function escape($string)
	{
		return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
	}
}
