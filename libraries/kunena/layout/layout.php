<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Categories
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Implements Kunena specific functions for all layouts.
 *
 * @see KunenaLayoutBase
 */
class KunenaLayout extends KunenaLayoutBase
{
	/**
	 * Content to be appended after the main output.
	 *
	 * @var array
	 */
	protected $after = array();

	protected $legacy;

	/**
	 * Append HTML after the layout content.
	 *
	 * @param  string  $content
	 */
	public function appendAfter($content)
	{
		$this->after[] = $content;
	}

	/**
	 * @param $key
	 * @return string
	 */
	public function text($key)
	{
		return JText::_($key);
	}

	/**
	 * Method to render the view.
	 *
	 * @param   string  Layout.
	 *
	 * @return  string  The rendered view.
	 *
	 * @throws  Exception|RunTimeException
	 */
	public function render($layout = null)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start("render layout '{$this->_name}'") : null;

		try
		{
			$output = parent::render($layout);

			foreach ($this->after as $content)
			{
				$output .= (string) $content;
			}
		}
		catch (Exception $e)
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop("render layout '{$this->_name}'") : null;
			throw $e;
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop("render layout '{$this->_name}'") : null;

		return $output;
	}

	public function setLegacy(KunenaView $view = null) {
		$this->legacy = $view;

		return $this;
	}

	/**
	 * Add legacy template support.
	 *
	 * @param $view
	 * @param $layout
	 * @param null $template
	 * @deprecated K4.0
	 */
	public function displayTemplateFile($view, $layout, $template = null)
	{
		list($layout, $template) = KunenaFactory::getTemplate()->mapLegacyView("{$view}/{$layout}_{$template}");
		echo $this->subLayout($layout)->setLayout($template)->setLegacy($this->legacy);
	}

	/**
	 * Add legacy template support. Overrides the parent class.
	 *
	 * @param $property
	 * @return mixed
	 * @throws InvalidArgumentException
	 * @deprecated K4.0
	 */
	public function __get($property)
	{
		if (!array_key_exists($property, $this->closures))
		{
			if ($this->legacy)
			{
				if (isset($this->legacy->{$property}))
				{
					return $this->legacy->{$property};
				}

				$properties = $this->legacy->getProperties();

				if (array_key_exists($property, $properties))
				{
					return $this->legacy->{$property};
				}
			}
			if (JDEBUG)
			{
				throw new InvalidArgumentException(sprintf('Property "%s" is not defined', $property));
			}
			else
			{
				return null;
			}
		}

		return $this->closures[$property]();
	}

	/**
	 * Add legacy template support.
	 *
	 * @param $name
	 * @param $arguments
	 * @return mixed
	 * @throws InvalidArgumentException
	 * @deprecated K4.0
	 */
	public function __call($name, $arguments)
	{
		try
		{
			return parent::__call($name, $arguments);
		}
		catch (InvalidArgumentException $e)
		{
			$callable = array($this->legacy, $name);

			if ($this->legacy && is_callable($callable))
			{
				return call_user_func_array($callable, $arguments);
			}

			throw $e;
		}
	}

	/**
	 * Add legacy template support.
	 *
	 * @param $property
	 * @return bool
	 * @deprecated K4.0
	 */
	public function __isset($property)
	{
		return parent::__isset($property) || ($this->legacy && (isset($this->legacy->{$property})));
	}

	/**
	 * Add legacy template support.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $value     The value of the property to set.
	 *
	 * @return  KunenaLayout  Method supports chaining.
	 */
	public function set($property, $value = null)
	{
		$isFactory = is_object($value) && method_exists($value, '__invoke');

		if ($isFactory)
		{
			$this->closures[$property] = $value;
		}
		elseif ($this->legacy)
		{
			$this->legacy->{$property} = $value;
		}
		else
		{
			$this->{$property} = $value;
		}

		return $this;
	}

	/**
	 * Add legacy template support.
	 *
	 * @param   $path
	 * @return  KunenaLayout
	 */
	public function subLayout($path)
	{
		return parent::subLayout($path)->setLegacy($this->legacy)->setLayout($this->layout);
	}

	public function getButton($link, $name, $scope, $type, $id = null)
	{
		return KunenaFactory::getTemplate()->getButton(KunenaRoute::_($link), $name, $scope, $type, $id);
	}

	public function getIcon($name, $title='')
	{
		return KunenaFactory::getTemplate()->getIcon($name, $title);
	}

	/**
	 * This function formats a number to n significant digits when above
	 * 10,000. Starting at 10,0000 the out put changes to 10k, starting
	 * at 1,000,000 the output switches to 1m. Both k and m are defined
	 * in the language file. The significant digits are used to limit the
	 * number of digits displayed when in 10k or 1m mode.
	 *
	 * @param int $number 		Number to be formated
	 * @param int $precision	Significant digits for output
	 * @return string
	 */
	public function formatLargeNumber($number, $precision = 3)
	{
		// Do we need to reduce the number of significant digits?
		if ($number >= 10000)
		{
			// Round the number to n significant digits
			$number = round ($number, -1*(log10($number)+1) + $precision);
		}

		if ($number < 10000)
		{
			$output = $number;
		}
		elseif ($number >= 1000000)
		{
			$output = $number / 1000000 . JText::_('COM_KUNENA_MILLION');
		}
		else
		{
			$output = $number / 1000 . JText::_('COM_KUNENA_THOUSAND');
		}

		return $output;
	}

	public function getCategoryLink(KunenaForumCategory $category, $content = null, $title = null, $class = null)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		if (!$content)
		{
			$content = $this->escape($category->name);
		}

		if ($title === null)
		{
			$title = JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_CATEGORY_TITLE', $this->escape($category->name));
		}

		$link = JHtml::_('kunenaforum.link', $category->getUrl(), $content, $title, $class, 'follow');

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		return $link;
	}

	public function getTopicLink(KunenaForumTopic $topic, $action = null, $content = null, $title = null, $class = null, KunenaForumCategory $category = NULL)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		$url = $topic->getUrl($category ? $category : (isset($this->category) ? $this->category : $topic->getCategory()), true, $action);

		if (!$content)
		{
			$content = KunenaHtmlParser::parseText($topic->subject);
		}

		if ($title === null)
		{
			if ($action instanceof KunenaForumMessage)
			{
				$title = JText::sprintf('COM_KUNENA_TOPIC_MESSAGE_LINK_TITLE', $this->escape($topic->subject));
			}
			else
			{
				switch ($action) {
					case 'first':
						$title = JText::sprintf('COM_KUNENA_TOPIC_FIRST_LINK_TITLE', $this->escape($topic->subject));
						break;
					case 'last':
						$title = JText::sprintf('COM_KUNENA_TOPIC_LAST_LINK_TITLE', $this->escape($topic->subject));
						break;
					case 'unread':
						$title = JText::sprintf('COM_KUNENA_TOPIC_UNREAD_LINK_TITLE', $this->escape($topic->subject));
						break;
					default:
						$title = JText::sprintf('COM_KUNENA_TOPIC_LINK_TITLE', $this->escape($topic->subject));
				}
			}
		}
		$link = JHtml::_('kunenaforum.link', $url, $content, $title, $class, 'nofollow');

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		return $link;
	}

	public function getLastPostLink($category, $content = null, $title = null, $class = null, $length = 30)
	{
		$lastTopic = $category->getLastTopic();
		$channels = $category->getChannels();

		if (!isset($channels[$lastTopic->category_id]))
		{
			$category = $lastTopic->getCategory();
		}

		$uri = $lastTopic->getUrl($category, true, 'last');

		if (!$content)
		{
			$content = $lastTopic->first_post_id != $lastTopic->last_post_id ? JText::_('COM_KUNENA_RE').' ' : '';
			$content .= KunenaHtmlParser::parseText($lastTopic->subject, $length);
		}

		if ($title === null)
		{
			$title = JText::sprintf('COM_KUNENA_TOPIC_LAST_LINK_TITLE', $this->escape($category->getLastTopic()->subject));
		}

		return JHtml::_('kunenaforum.link', $uri, $content, $title, $class, 'nofollow');
	}
}
