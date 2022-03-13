<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Controller
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Controller;

\defined('_JEXEC') or die();

use Exception;
use InvalidArgumentException;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Layout\KunenaBase;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;

/**
 * Class KunenaControllerDisplay
 *
 * @since   Kunena 6.0
 */
abstract class KunenaControllerDisplay extends KunenaControllerBase
{
	/**
	 * @var     null|KunenaLayout
	 * @since   Kunena 6.0
	 */
	public $output = null;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $layout = 'default';

	public $user;

	public $headerText;

	public $pagination;

	/**
	 * @var     KunenaConfig
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
	 * @return  \Kunena\Forum\Libraries\Controller\KunenaControllerDisplay
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
	public function __toString(): string
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

			$document = Factory::getApplication()->getDocument();
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
				return "<div class=\"alert alert-danger\" role=\"alert\"><b>Exception</b> in layout <b>{$this->name}!</b>" . (JDEBUG ? '<br>' . $e->getMessage() : '') . '</div>';
			}

			$title    = '500 Internal Server Error';
			$document = Factory::getApplication()->getDocument();
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
	 * @see     KunenaControllerBase::execute()
	 *
	 * @return  KunenaLayout|null
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function execute()
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . \get_class($this) . '::' . __FUNCTION__ . '()') : null;

		try
		{
			// Run before executing action.
			$result = $this->before();

			if ($result === false)
			{
				KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . \get_class($this) . '::' . __FUNCTION__ . '()') : null;

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
				KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . \get_class($this) . '::' . __FUNCTION__ . '()') : null;
				throw $e;
			}
			else
			{
				$this->output = KunenaLayout::factory('Empty')->setOptions($this->getOptions());
			}
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . \get_class($this) . '::' . __FUNCTION__ . '()') : null;

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
		$this->config = KunenaConfig::getInstance();

		if ($this->primary)
		{
			$this->document = Factory::getApplication()->getDocument();
		}
	}

	/**
	 * @param   int  $code  code
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function setResponseStatus($code = 404): void
	{
		switch ((int) $code)
		{
			case 400:
				Factory::getApplication()->setHeader('Status', '400 Bad Request', true);
				break;
			case 401:
				Factory::getApplication()->setHeader('Status', '401 Unauthorized', true);
				break;
			case 403:
				Factory::getApplication()->setHeader('Status', '403 Forbidden', true);
				break;
			case 404:
				Factory::getApplication()->setHeader('Status', '404 Not Found', true);
				break;
			case 410:
				Factory::getApplication()->setHeader('Status', '410 Gone', true);
				break;
			case 503:
				Factory::getApplication()->setHeader('Status', '503 Service Temporarily Unavailable', true);
				break;
			case 500:
			default:
				Factory::getApplication()->setHeader('Status', '500 Internal Server Error', true);
		}

		Factory::getApplication()->sendHeaders();
	}

	/**
	 * Initialize and display the layout.
	 *
	 * @return  KunenaBase|KunenaLayout
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function display()
	{
		// Display layout with given parameters.
		return KunenaLayout::factory($this->name)
			->setProperties($this->getProperties())
			->setOptions($this->getOptions());
	}

	/**
	 * Returns an associative array of public object properties.
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function getProperties(): array
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
	 * Prepare title, description, breadcrumb etc.
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
	public function getLayout(): string
	{
		$layout = preg_replace('/[^a-z0-9_]/', '', strtolower($this->layout));

		return $layout ? $layout : 'default';
	}

	/**
	 * Method to set the view layout.
	 *
	 * @param   string  $layout  The layout name.
	 *
	 * @return  KunenaControllerDisplay
	 *
	 * @since   Kunena 6.0
	 */
	public function setLayout(string $layout)
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
	public function setProperties($properties): KunenaControllerDisplay
	{
		if (!\is_array($properties) && !\is_object($properties))
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
	public function set($key, $value): KunenaControllerDisplay
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
	protected function setTitle($title, $replace = false): void
	{
		if (!$replace)
		{
			// Obey Joomla configuration.
			if ($this->app->get('sitename_pagetitles', 0) == 1)
			{
				$title = Text::sprintf('JPAGETITLE', $this->app->get('sitename'), $title . ' - ' . $this->config->boardTitle);
			}
			elseif ($this->app->get('sitename_pagetitles', 0) == 2)
			{
				if ($this->config->boardTitle == $this->app->get('sitename'))
				{
					$title = Text::sprintf('JPAGETITLE', $title, $this->app->get('sitename'));
				}
				else
				{
					$title = Text::sprintf('JPAGETITLE', $title . ' - ' . $this->config->boardTitle, $this->app->get('sitename'));
				}
			}
			else
			{
				$title = $title . ' - ' . $this->config->boardTitle;
			}
		}

		$title = strip_tags($title);
		$this->document->setTitle($title);
	}

	/**
	 * @param   string  $description  description
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setDescription(string $description): void
	{
		$this->document->setMetadata('description', $description);
	}

	/**
	 * @param   string  $name       name
	 * @param   string  $content    content
	 * @param   string  $attribute  attribute
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setMetaData(string $name, $content, $attribute = 'name'): void
	{
		$this->document->setMetaData($name, $content, $attribute);
	}
}
