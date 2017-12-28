<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Template
 * @subpackage      Categories
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

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

	/**
	 * Object KunenaView
	 *
	 * @var unknown
	 */
	protected $legacy;

	/**
	 * Append HTML after the layout content.
	 *
	 * @param   string $content
	 */
	public function appendAfter($content)
	{
		$this->after[] = $content;
	}

	/**
	 * @param $key
	 *
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

	/**
	 * @param        $link
	 * @param        $name
	 * @param        $scope
	 * @param        $type
	 * @param   null $id
	 *
	 * @return string
	 */
	public function getButton($link, $name, $scope, $type, $id = null)
	{
		return KunenaFactory::getTemplate()->getButton(KunenaRoute::_($link), $name, $scope, $type, $id);
	}

	/**
	 * @param          $name
	 * @param   string $title
	 *
	 * @return string
	 */
	public function getIcon($name, $title = '')
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
	 * @param   int $number    Number to be formated
	 * @param   int $precision Significant digits for output
	 *
	 * @return string
	 */
	public function formatLargeNumber($number, $precision = 3)
	{
		// Do we need to reduce the number of significant digits?
		if ($number >= 10000)
		{
			// Round the number to n significant digits
			$number = round($number, -1 * (log10($number) + 1) + $precision);
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

	/**
	 * @param   KunenaForumCategory $category
	 * @param   null                $content
	 * @param   null                $title
	 * @param   null                $class
	 *
	 * @param bool                  $follow
	 * @param null                  $canonical
	 *
	 * @return mixed
	 */
	public function getCategoryLink(KunenaForumCategory $category, $content = null, $title = null, $class = null, $follow = true, $canonical = null)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if (!$content)
		{
			$content = $this->escape($category->name);
		}

		if ($title === null)
		{
			$title = JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_CATEGORY_TITLE', $category->name);

			if (strpos($class, 'hasTooltip') !== false)
			{
				// Tooltips will decode HTML and we don't want the HTML to be parsed
				$title = $this->escape($title);
			}
		}

		if ($follow)
		{
			$rel = '';
		}
		else
		{
			$rel = 'nofollow';
		}

		if ($canonical)
		{
			$con = 'canonical';
		}
		else
		{
			$con = $rel;
		}

		if ($category->locked)
		{
			$class .= ' locked';
		}

		$link = JHtml::_('kunenaforum.link', $category->getUrl(), $content, $title, $class, $con);

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $link;
	}

	/**
	 * @param   KunenaForumTopic    $topic
	 * @param   null                $action
	 * @param   null                $content
	 * @param   null                $title
	 * @param   null                $class
	 * @param   KunenaForumCategory $category
	 *
	 * @param bool                  $follow
	 * @param bool                  $canonical
	 *
	 * @return mixed
	 */
	public function getTopicLink(KunenaForumTopic $topic, $action = null, $content = null, $title = null, $class = null, KunenaForumCategory $category = null, $follow = true, $canonical = false)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		$url = $topic->getUrl($category ? $category : (isset($this->category) ? $this->category : $topic->getCategory()), true, $action);

		if (!$content)
		{
			$content = KunenaHtmlParser::parseText($topic->subject);
		}

		if ($title === null)
		{
			if ($action instanceof KunenaForumMessage)
			{
				$title = KunenaHtmlParser::stripBBCode($topic->first_post_message, 200, false);
			}
			else
			{
				switch ($action)
				{
					case 'first':
						$title = KunenaHtmlParser::stripBBCode($topic->first_post_message, 200, false);
						break;
					case 'last':
						$title = KunenaHtmlParser::stripBBCode($topic->last_post_message, 200, false);
						break;
					case 'unread':
						$title = KunenaHtmlParser::stripBBCode($topic->last_post_message, 200, false);
						break;
					default:
						$title = KunenaHtmlParser::stripBBCode($topic->first_post_message, 200, false);
				}
			}

			if (strpos($class, 'hasTooltip') !== false)
			{
				// Tooltips will decode HTML and we don't want the HTML to be parsed
				$title = $this->escape($title);
			}
		}

		if ($follow)
		{
			$rel = '';
		}
		else
		{
			$rel = 'nofollow';
		}

		if ($canonical)
		{
			$con = 'canonical';
		}
		else
		{
			$con = $rel;
		}

		if ($topic->locked)
		{
			$class .= ' locked';
		}

		$link = JHtml::_('kunenaforum.link', $url, $content, $title, $class, $con);

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $link;
	}

	/**
	 * @param        $category
	 * @param   null $content
	 * @param   null $title
	 * @param   null $class
	 * @param   int  $length
	 *
	 * @param bool   $follow
	 * @param null   $canonical
	 *
	 * @return mixed
	 */
	public function getLastPostLink($category, $content = null, $title = null, $class = null, $length = 30, $follow = true, $canonical = null)
	{
		$lastTopic = $category->getLastTopic();
		$channels  = $category->getChannels();

		if (!isset($channels[$lastTopic->category_id]))
		{
			$category = $lastTopic->getCategory();
		}

		$uri = $lastTopic->getUrl($category, true, 'last');

		if (!$content)
		{
			$content = $lastTopic->first_post_id != $lastTopic->last_post_id ? JText::_('COM_KUNENA_RE') . ' ' : '';
			$content .= KunenaHtmlParser::parseText($lastTopic->subject, $length);
		}

		if ($title === null)
		{
			$title = KunenaHtmlParser::stripBBCode($lastTopic->last_post_message, 200, false);

			if (strpos($class, 'hasTooltip') !== false)
			{
				// Tooltips will decode HTML and we don't want the HTML to be parsed
				$title = $this->escape($title);
			}
		}

		if ($follow)
		{
			$rel = '';
		}
		else
		{
			$rel = 'nofollow';
		}

		if ($canonical)
		{
			$con = 'canonical';
		}
		else
		{
			$con = $rel;
		}

		if ($lastTopic->locked)
		{
			$class .= ' locked';
		}

		return JHtml::_('kunenaforum.link', $uri, $content, $title, $class, $con);
	}

	/**
	 * Removing it only after removed usage of this method, because without it, it cause issue in discuss plugin
	 *
	 * @param KunenaView $view
	 *
	 * @since      4.0
	 *
	 * @deprecated 5.0
	 * @return $this
	 */
	public function setLegacy(KunenaView $view = null)
	{
		$this->legacy = $view;

		return $this;
	}
}
