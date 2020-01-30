<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Controller
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Controller;

defined('_JEXEC') or die();

use Exception;
use InvalidArgumentException;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Exception\Authorise;
use Kunena\Forum\Libraries\Layout\Layout;
use Kunena\Forum\Libraries\Layout\Base;
use Kunena\Forum\Libraries\Config\Config;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use function defined;

/**
 * Class KunenaControllerDisplay
 *
 * @since   Kunena 6.0
 */
abstract class KunenaControllerDisplay extends KunenaControllerBase
{
	/**
	 * @var     null|Layout
	 * @since   Kunena 6.0
	 */
	public $output = null;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $layout = 'default';

	/**
	 * @var     Config
	 * @since   Kunena 6.0
	 */
	public $config;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Empty';

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $primary = false;

	/**
	 * @var     Document
	 * @since   Kunena 6.0
	 */
	protected $document;

	/**
	 * @internal
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function setPrimary()
	{
		$this->primary = true;

		return $this;
	}

	/**
	 * Return view as a string.
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function __toString()
	{
		try
		{
			$output = $this->execute();
		}
		catch (Authorise $e)
		{
			if (!$this->primary)
			{
				return (string) Layout::factory('Empty');
			}

			$document = Factory::getApplication()->getDocument();
			$document->setTitle($e->getResponseStatus());
			Factory::getApplication()->setHeader('Status', $e->getResponseStatus(), true);
			Factory::getApplication()->sendHeaders();
			$output = Layout::factory('Misc/Default', 'pages')
				->set('header', $e->getResponseStatus())
				->set('body', $e->getMessage());
		}
		catch (Exception $e)
		{
			if (!$this->primary)
			{
				return "<div class=\"alert alert-danger\" role=\"alert\"><b>Exception</b> in layout <b>{$this->name}!</b>" . (JDEBUG ? '<br>' . $e->getMessage() : '') . '</div>';
			}

			$title    = '500 Internal Server Error';
			$document = Factory::getApplication()->getDocument();
			$document->setTitle($title);
			Factory::getApplication()->setHeader('Status', $title, true);
			Factory::getApplication()->sendHeaders();
			$output = Layout::factory('Misc/Default', 'pages')
				->set('header', $title)
				->set('body', $e->getMessage());
		}

		return (string) $output;
	}

	/**
	 * @see     KunenaControllerBase::execute()
	 *
	 * @return  Layout|null
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
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

				return Layout::factory('Empty')->setOptions($this->getOptions());
			}

			// Display layout with given parameters.
			$this->output = $this->display();

			// Run after executing action.
			$this->after();
		}
		catch (Authorise $e)
		{
			if ($this->primary)
			{
				KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . get_class($this) . '::' . __FUNCTION__ . '()') : null;
				throw $e;
			}
			else
			{
				$this->output = Layout::factory('Empty')->setOptions($this->getOptions());
			}
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . get_class($this) . '::' . __FUNCTION__ . '()') : null;

		return $this->output;
	}

	/**
	 * Executed before display.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function before()
	{
		$this->layout = $this->input->getCmd('layout', 'default');
		$this->config = Config::getInstance();

		if ($this->primary)
		{
			$this->document = Factory::getApplication()->getDocument();
		}
	}

	/**
	 * Initialize and display the layout.
	 *
	 * @return  Base|Layout
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function display()
	{
		// Display layout with given parameters.
		$content = Layout::factory($this->name)
			->setProperties($this->getProperties())
			->setOptions($this->getOptions());

		return $content;
	}

	/**
	 * Returns an associative array of public object properties.
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function getProperties()
	{
		$properties = (array) $this;
		$list       = [];

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
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
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
	 *
	 * @return  boolean|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
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
	 *
	 * @since   Kunena 6.0
	 */
	public function getLayout()
	{
		$layout = preg_replace('/[^a-z0-9_]/', '', strtolower($this->layout));

		return $layout ? $layout : 'default';
	}

	/**
	 * Method to set the view layout.
	 *
	 * @param   string  $layout  The layout name.
	 *
	 * @return  KunenaControllerDisplay|Layout
	 *
	 * @since   Kunena 6.0
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
	 * @see     set()
	 *
	 * @param   mixed  $properties  Either an associative array or another object.
	 *
	 * @return  KunenaControllerDisplay  Method supports chaining.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  InvalidArgumentException
	 */
	public function setProperties($properties)
	{
		if (!is_array($properties) && !is_object($properties))
		{
			throw new InvalidArgumentException('Parameter should be either array or an object.');
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
	 * @param   mixed  $key    key
	 * @param   mixed  $value  value
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function set($key, $value)
	{
		$this->input->set($key, $value);

		return $this;
	}

	/**
	 * @param   mixed  $title    title
	 * @param   bool   $replace  replace
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
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
				$title = $title . ' - ' . $this->config->board_title;
			}
		}

		$title = strip_tags($title);
		$this->document->setTitle($title);
	}

	/**
	 * @param   string  $keywords  keywords
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setKeywords($keywords)
	{
		$this->document->setMetadata('keywords', $keywords);
	}

	/**
	 * @param   string  $description  description
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setDescription($description)
	{
		$this->document->setMetadata('description', $description);
	}

	/**
	 * @param   string  $robots  robots
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setRobots($robots)
	{
		$this->document->setMetaData('robots', $robots, 'robots');
	}

	/**
	 * @param   string  $name      name
	 * @param   string  $content   content
	 * @param   string  $attribute attribute
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setMetaData($name, $content, $attribute = 'name')
	{
		$this->document->setMetaData($name, $content, $attribute);
	}
}
