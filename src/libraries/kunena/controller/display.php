<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Controller
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Class KunenaControllerDisplay
 * @since Kunena
 */
abstract class KunenaControllerDisplay extends KunenaControllerBase
{
	/**
	 * @var null|KunenaLayout
	 * @since Kunena
	 */
	public $output = null;

	/**
	 * @var string
	 * @since Kunena
	 */
	public $layout = 'default';

	/**
	 * @var string
	 * @since Kunena
	 */
	public $config;

	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Empty';

	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected $primary = false;

	/**
	 * @internal
	 * @since Kunena
	 * @return mixed
	 */
	public function setPrimary()
	{
		$this->primary = true;

		return $this;
	}

	/**
	 * Return view as a string.
	 *
	 * @return string
	 * @throws Exception
	 * @since Kunena
	 */
	public function __toString()
	{
		try
		{
			$output = $this->execute();
		}
		catch (KunenaExceptionAuthorise $e)
		{
			if (!$this->primary)
			{
				return (string) KunenaLayout::factory('Empty');
			}

			$document = Factory::getDocument();
			$document->setTitle($e->getResponseStatus());
			Factory::getApplication()->setHeader('Status', $e->getResponseStatus(), true);
			Factory::getApplication()->sendHeaders();
			$output = KunenaLayout::factory('Misc/Default', 'pages')
				->set('header', $e->getResponseStatus())
				->set('body', $e->getMessage());
		}
		catch (Exception $e)
		{
			if (!$this->primary)
			{
				return "<b>Exception</b> in layout <b>{$this->name}!</b>" . (!JDEBUG ? $e->getMessage() : '');
			}

			$title    = '500 Internal Server Error';
			$document = Factory::getDocument();
			$document->setTitle($title);
			Factory::getApplication()->setHeader('Status', $title, true);
			Factory::getApplication()->sendHeaders();
			$output = KunenaLayout::factory('Misc/Default', 'pages')
				->set('header', $title)
				->set('body', $e->getMessage());
		}

		return (string) $output;
	}

	/**
	 * @see   KunenaControllerBase::execute()
	 * @since Kunena
	 * @return \Joomla\CMS\Layout\BaseLayout|KunenaLayout|null
	 * @throws Exception
	 */
	public function execute()
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . get_class($this) . '::' . __FUNCTION__ . '()') : null;

		try
		{
			// Run before executing action.
			$result = $this->before();

			if ($result === false)
			{
				KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . get_class($this) . '::' . __FUNCTION__ . '()') : null;

				return KunenaLayout::factory('Empty')->setOptions($this->getOptions());
			}

			// Display layout with given parameters.
			$this->output = $this->display();

			// Run after executing action.
			$this->after();
		}
		catch (KunenaExceptionAuthorise $e)
		{
			if ($this->primary)
			{
				KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . get_class($this) . '::' . __FUNCTION__ . '()') : null;
				throw $e;
			}
			else
			{
				$this->output = KunenaLayout::factory('Empty')->setOptions($this->getOptions());
			}
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . get_class($this) . '::' . __FUNCTION__ . '()') : null;

		return $this->output;
	}

	/**
	 * Executed before display.
	 * @since Kunena
	 * @throws Exception
	 * @return void
	 */
	protected function before()
	{
		$this->layout = $this->input->getCmd('layout', 'default');
		$this->config = KunenaConfig::getInstance();

		if ($this->primary)
		{
			$this->document = Factory::getDocument();
		}
	}

	/**
	 * Initialize and display the layout.
	 *
	 * @return \Joomla\CMS\Layout\BaseLayout|KunenaLayout
	 * @throws Exception
	 * @since Kunena
	 */
	protected function display()
	{
		// Display layout with given parameters.
		$content = KunenaLayout::factory($this->name)
			->setProperties($this->getProperties())
			->setOptions($this->getOptions());

		return $content;
	}

	/**
	 * Returns an associative array of public object properties.
	 *
	 * @return  array
	 * @since Kunena
	 */
	public function getProperties()
	{
		$properties = (array) $this;
		$list       = array();

		foreach ($properties as $property => $value)
		{
			if ($property[0] != "\0")
			{
				$list[$property] = $value;
			}
		}

		return $list;
	}

	/**
	 * Executed after display.
	 * @since Kunena
	 * @return void
	 * @throws Exception
	 */
	protected function after()
	{
		if ($this->primary)
		{
			$this->prepareDocument();
		}
	}

	/**
	 * Prepare title, description, keywords, breadcrumb etc.
	 * @since Kunena
	 * @return boolean
	 * @throws Exception
	 */
	protected function prepareDocument()
	{
		$app    = Factory::getApplication();
		$format = $app->input->getCmd('format');

		if (!empty($format) && $format != 'html')
		{
			return false;
		}
	}

	/**
	 * Method to get the view layout.
	 *
	 * @return  string  The layout name.
	 * @since Kunena
	 */
	public function getLayout()
	{
		$layout = preg_replace('/[^a-z0-9_]/', '', strtolower($this->layout));

		return $layout ? $layout : 'default';
	}

	/**
	 * Method to set the view layout.
	 *
	 * @param   string $layout The layout name.
	 *
	 * @return KunenaControllerDisplay|KunenaLayout
	 * @since Kunena
	 */
	public function setLayout($layout)
	{
		if (!$layout)
		{
			$layout = 'default';
		}

		$this->layout = $layout;

		return $this;
	}

	/**
	 * Set the object properties based on a named array/hash.
	 *
	 * @param   mixed $properties Either an associative array or another object.
	 *
	 * @return  KunenaControllerDisplay  Method supports chaining.
	 *
	 * @see     set()
	 * @throws \InvalidArgumentException
	 * @since   Kunena
	 */
	public function setProperties($properties)
	{
		if (!is_array($properties) && !is_object($properties))
		{
			throw new \InvalidArgumentException('Parameter should be either array or an object.');
		}

		foreach ((array) $properties as $k => $v)
		{
			// Use the set function which might be overridden.
			if ($k[0] != "\0")
			{
				$this->$k = $v;
			}
		}

		return $this;
	}

	/**
	 * Shortcut for $this->input->set()
	 *
	 * @param   mixed $key   key
	 * @param   mixed $value value
	 *
	 * @return $this
	 * @since Kunena
	 */
	public function set($key, $value)
	{
		$this->input->set($key, $value);

		return $this;
	}

	/**
	 * @param   mixed $title   title
	 * @param   bool  $replace replace
	 *
	 * @throws Exception
	 * @since Kunena
	 * @return void
	 */
	protected function setTitle($title, $replace = false)
	{
		if (!$replace)
		{
			// Obey Joomla configuration.
			if ($this->app->get('sitename_pagetitles', 0) == 1)
			{
				$title = Text::sprintf('JPAGETITLE', $this->app->get('sitename'), $title . ' - ' . $this->config->board_title);
			}
			elseif ($this->app->get('sitename_pagetitles', 0) == 2)
			{
				if ($this->config->board_title == $this->app->get('sitename'))
				{
					$title = Text::sprintf('JPAGETITLE', $title, $this->app->get('sitename'));
				}
				else
				{
					$title = Text::sprintf('JPAGETITLE', $title . ' - ' . $this->config->board_title, $this->app->get('sitename'));
				}
			}
			else
			{
				$title = $title . ' - ' . KunenaFactory::getConfig()->board_title;
			}
		}

		$title = strip_tags($title);
		$this->document->setTitle($title);
	}

	/**
	 * @param   string $keywords keywords
	 *
	 * @since Kunena
	 * @return void
	 */
	protected function setKeywords($keywords)
	{
		$this->document->setMetadata('keywords', $keywords);
	}

	/**
	 * @param   string $description description
	 *
	 * @since Kunena
	 * @return void
	 */
	protected function setDescription($description)
	{
		$this->document->setMetadata('description', $description);
	}

	/**
	 * @param   string $robots robots
	 *
	 * @since Kunena
	 * @return void
	 */
	protected function setRobots($robots)
	{
		$this->document->setMetaData('robots', $robots, 'robots');
	}

	/**
	 * @param        $name
	 * @param        $content
	 * @param   string $attribute attribute
	 *
	 * @return void
	 * @since Kunena
	 */
	protected function setMetaData($name, $content, $attribute = 'name')
	{
		$this->document->setMetaData($name, $content, $attribute);
	}
}
