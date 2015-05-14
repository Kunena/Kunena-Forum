<?php
/**
 * Kunena Content Plugin
 * @package Kunena.Plugins
 * @subpackage Content
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class plgContentKunena
 */
class plgContentKunena extends JPlugin
{

	/**
	 * Construct plugin.
	 *
	 * @param object $subject
	 * @param array $config
	 */
	public function __construct(&$subject, $config)
	{
		// Do not enable plugin in administration.
		if (JFactory::getApplication()->isAdmin())
		{
			return;
		}

		// Do not load if Kunena version is not supported or Kunena is not installed
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('4.0') && KunenaForum::installed()))
		{
			return;
		}

		parent::__construct ($subject, $config);

		$this->loadLanguage('plg_content_kunena.sys');
	}

	/**
	 * Replace {{ Kunena.Display(...) }} with corresponding Kunena controller.
	 *
	 * @param $context
	 * @param $article
	 * @param $params
	 * @param int $page
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		// Don't display in Kunena.
		if (strpos($context, 'kunena') !== false)
		{
			return;
		}

		$this->process($article->text);
	}

	/**
	 * Plugin processor.
	 *
	 * @param string $content
	 * @return bool
	 */
	protected function process(&$content)
	{
		// Quick search to optimize pages which do not contain the tag.
		if (stripos($content, 'Kunena.Display') === false)
		{
			return false;
		}

		$regex = '/{{\s*Kunena.Display\(([^\)]+)\)\s*}}/i';
		preg_match_all($regex, $content, $matches, PREG_SET_ORDER);

		if (!$matches)
		{
			return false;
		}

		foreach ($matches as $match)
		{
			$paramslist = (array) explode(',', $match[1]);
			$layout = trim(trim(array_shift($paramslist)), '\'"').'/Display';

			try
			{
				// TODO: de we need to load the language files?
				$input = new JInput($this->getParams($paramslist));
				$output = KunenaRequest::factory($layout, $input);
			}
			catch (Exception $e)
			{
				$output = '<div class="alert">' . $e->getMessage() . '</div>';
			}

			$pos = strpos($content, $match[0]);
			if ($pos !== false)
			{
				$content = substr_replace($content, (string) $output, $pos, strlen($match[0]));
			}
		}

		return true;
	}

	protected function getParams(array $params)
	{
		$list = array();

		foreach ($params as $param)
		{
			list($key, $value) = explode('=', $param);
			$key = trim($key);

			if (!$key)
			{
				continue;
			}

			$value = trim(trim($value), '\'"');
			$list[$key] = $value;
		}

		return $list;
	}
}
