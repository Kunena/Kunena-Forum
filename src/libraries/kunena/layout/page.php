<?php
/**
 * Kunena Component
 * @package       Kunena.Administrator.Template
 * @subpackage    Categories
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Implements Kunena specific functions for page layouts.
 *
 * @see   KunenaLayout
 * @since Kunena
 */
class KunenaLayoutPage extends KunenaLayout
{
	/**
	 * Returns layout class.
	 *
	 * <code>
	 *    // Output pagination/pages layout with current cart instance.
	 *    echo KunenaLayout::factory('Pagination/Pages')->set('pagination', $this->pagination);
	 * </code>
	 *
	 * @param   mixed  $paths String or array of strings.
	 * @param   string $base  Base path.
	 *
	 * @return  KunenaLayout
	 * @throws Exception
	 * @since Kunena
	 */
	public static function factory($paths, $base = 'pages')
	{
		$paths = (array) $paths;

		$app = Factory::getApplication();

		// Add all paths for the template overrides.
		if ($app->isClient('administrator'))
		{
			$template = KunenaFactory::getAdminTemplate();
		}
		else
		{
			$template = KunenaFactory::getTemplate();
		}

		$templatePaths = array();

		foreach ($paths as $path)
		{
			if (!$path)
			{
				continue;
			}

			$path   = (string) preg_replace('|\\\|', '/', strtolower($path));
			$lookup = $template->getTemplatePaths("{$base}/{$path}", true);

			foreach ($lookup as $loc)
			{
				array_unshift($templatePaths, $loc);
			}
		}

		// Go through all the matching layouts.
		$path = 'Undefined';

		foreach ($paths as $path)
		{
			if (!$path)
			{
				continue;
			}

			// Attempt to load layout class if it doesn't exist.
			$class = 'KunenaPage' . (string) preg_replace('/[^A-Z0-9_]/i', '', $path);
			$fpath = (string) preg_replace('|\\\|', '/', strtolower($path));

			if (!class_exists($class))
			{
				$filename = JPATH_BASE . "/components/com_kunena/page/{$fpath}.php";

				if (!is_file($filename))
				{
					continue;
				}

				require_once $filename;
			}

			// Create layout object.
			return new $class($fpath, $templatePaths);
		}

		// Create default layout object.
		return new KunenaLayoutPage($path, $templatePaths);
	}

	/**
	 * Execute main MVC triad to get the current layout.
	 *
	 * @param   mixed $path    path
	 * @param   mixed $input   input
	 * @param   mixed $options options
	 *
	 * @return  KunenaLayout
	 * @since Kunena
	 * @throws Exception
	 */
	public function execute($path, \Joomla\Input\Input $input = null, $options = null)
	{
		return $this->request($path, $input, $options)->execute();
	}

	/**
	 * Get main MVC triad from current layout.
	 *
	 * @param   mixed $path    path
	 * @param   mixed $input   input
	 * @param   mixed $options options
	 *
	 * @return  KunenaControllerDisplay
	 * @since Kunena
	 */
	public function request($path, \Joomla\Input\Input $input = null, $options = null)
	{
		return KunenaRequest::factory($path . '/Display', $input, $options ? $options : $this->getOptions())
			->setPrimary()->set('layout', $this->getLayout());
	}

	/**
	 * Add path to breadcrumbs.
	 *
	 * @param   string $text   text
	 * @param   string $uri    uri
	 * @param   bool   $ignore ignore
	 *
	 * @return $this
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function addBreadcrumb($text, $uri, $ignore = true)
	{
		if ($ignore)
		{
			$active = KunenaRoute::$active;
			$view   = isset($active->query['view']) ? $active->query['view'] : '';
			$layout = isset($active->query['layout']) ? $active->query['layout'] : 'default';

			if ($active && $active->component == 'com_kunena' && strtolower("{$view}/{$layout}") == strtolower($this->_name))
			{
				return $this;
			}
		}

		$this->breadcrumb->addItem($text, KunenaRoute::normalize($uri));

		return $this;
	}
}
