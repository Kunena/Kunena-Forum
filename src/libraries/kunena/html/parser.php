<?php
/**
 * Kunena Component
 * @package     Kunena.Framework
 * @subpackage  HTML
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaHtmlParser
 */
abstract class KunenaHtmlParser
{
	static $emoticons = null;

	static $relative = true;

	/**
	 * @param   bool $grayscale
	 * @param   bool $emoticonbar
	 *
	 * @return array
	 */
	public static function getEmoticons($grayscale = false, $emoticonbar = false)
	{
		$db = JFactory::getDBO();
		$grayscale == true ? $column = "greylocation" : $column = "location";
		$sql = "SELECT code, {$db->quoteName($column)} AS file FROM #__kunena_smileys";

		if ($emoticonbar == true)
		{
			$sql .= " WHERE emoticonbar='1'";
		}

		$db->setQuery($sql);

		try
		{
			$smilies = $db->loadObjectList();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		$smileyArray = array ();
		$template = KunenaFactory::getTemplate();

		foreach ($smilies as $smiley)
		{
			// We load all smileys in array, so we can sort them
			$smileyArray [$smiley->code] = $template->getSmileyPath($smiley->file);
		}

		if ($emoticonbar == 0)
		{
			// Don't sort when it's only for use in the emoticonbar
			array_multisort(array_keys($smileyArray), SORT_DESC, $smileyArray);
			reset($smileyArray);
		}

		return $smileyArray;
	}

	/**
	 * @param     $txt
	 * @param   int $len
	 *
	 * @return mixed|string|void
	 */
	public static function parseText($txt, $len = 0)
	{
		if (!$txt)
		{
			return;
		}

		if ($len && Joomla\String\StringHelper::strlen($txt) > $len)
		{
			$txt = Joomla\String\StringHelper::substr($txt, 0, $len) . ' ...';
		}

		$txt = self::escape($txt);
		$txt = preg_replace('/(\S{30})/u', '\1', $txt);
		$txt = self::prepareContent($txt, 'title');

		return $txt;
	}

	/**
	 * @param        $txt
	 * @param   null $parent
	 * @param   int  $len
	 *
	 * @param string $context
	 *
	 * @return mixed|void
	 */
	public static function parseBBCode($txt, $parent = null, $len = 0, $context = '')
	{
		if (!$txt)
		{
			return;
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		$bbcode = KunenaBbcode::getInstance(self::$relative);
		$bbcode->parent = $parent;
		$bbcode->SetLimit($len);
		$bbcode->context = $context;
		$bbcode->SetPlainMode(false);
		$txt = $bbcode->Parse($txt);
		$txt = self::prepareContent($txt);

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $txt;
	}

	/**
	 * @param       $txt
	 * @param   int $len
	 *
	 * @return mixed|void
	 */
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
		$txt = self::prepareContent($txt);

		return $txt;
	}

	/**
	 * @param      $txt
	 * @param   int  $len
	 * @param   bool $html
	 *
	 * @return string|void
	 */
	public static function stripBBCode($txt, $len=0, $html = true)
	{
		if (!$txt)
		{
			return;
		}
		else
		{
			$txt = preg_replace('/\[confidential\](.*?)\[\/confidential\]/s', '', $txt);
			$txt = preg_replace('/\[color(.*?)\](.*?)\[\/color\]/s', '', $txt);
			$txt = preg_replace('/\[hide\](.*?)\[\/hide\]/s', '', $txt);
			$txt = preg_replace('/\[spoiler\](.*?)\[\/spoiler\]/s', '', $txt);
			$txt = preg_replace('/\[code(.*?)\](.*?)\[\/code]/s', '', $txt);
			$txt = preg_replace('/\[attachment(.*?)\](.*?)\[\/attachment]/s', '', $txt);
			$txt = preg_replace('/\[attachment]/s', '', $txt);
			$txt = preg_replace('/\[article\](.*?)\[\/article]/s', '', $txt);
			$txt = preg_replace('/\[video(.*?)\](.*?)\[\/video]/s', '', $txt);
			$txt = preg_replace('/\[img(.*?)\](.*?)\[\/img]/s', '', $txt);
			$txt = preg_replace('/\[image]/s', '', $txt);
			$txt = preg_replace('/\[url(.*?)\](.*?)\[\/url]/s', '', $txt);
			$txt = preg_replace('/\[quote(.*?)\](.*?)\[\/quote]/s', '', $txt);
			$txt = preg_replace('/\[spoiler(.*?)\](.*?)\[\/spoiler]/s', '', $txt);
			$txt = preg_replace('/\[tweet(.*?)\](.*?)\[\/tweet]/s', '', $txt);
			$txt = preg_replace('/\[instagram(.*?)\](.*?)\[\/instagram]/s', '', $txt);
			$txt = preg_replace('/\[soundcloud(.*?)\](.*?)\[\/soundcloud]/s', '', $txt);
		}

		if (JPluginHelper::isEnabled('content', 'emailcloak'))
		{
			$pattern = "/[^@\s]*@[^@\s]*\.[^@\s]*/";
			$replacement = ' ';
			$txt = preg_replace($pattern, $replacement, $txt);
		}

		$bbcode = KunenaBbcode::getInstance(self::$relative);
		$bbcode->SetLimit($len);
		$bbcode->SetPlainMode(true);
		$bbcode->SetAllowAmpersand($html);
		$txt = $bbcode->Parse($txt);
		$txt = self::prepareContent($txt);
		$txt = strip_tags($txt);

		if (!$html)
		{
			$txt = $bbcode->UnHTMLEncode($txt);
		}

		return $txt;
	}

	/**
	 * @param        $content
	 * @param   string $target
	 *
	 * @return mixed
	 */
	public static function &prepareContent(&$content, $target='body')
	{
		$config			= KunenaFactory::getConfig()->getPlugin('plg_system_kunena');
		$events			= (int) $config->get('jcontentevents', false);
		$event_target	= (array) $config->get('jcontentevent_target', array('body'));

		if ($events && in_array($target, $event_target))
		{
			$row = new stdClass;
			$row->text =& $content;

			// Run events
			$params = new JRegistry;
			$params->set('ksource', 'kunena');

			$dispatcher = JEventDispatcher::getInstance();
			JPluginHelper::importPlugin('content');
			$dispatcher->trigger('onContentPrepare', array ('text', &$row, &$params, 0));
			$content = $row->text;
		}

		return $content;
	}

	/**
	 * @param $string
	 *
	 * @return string
	 */
	public static function escape($string)
	{
		return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
	}
}
