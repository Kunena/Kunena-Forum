<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Template
 * @subpackage      Categories
 *
 * @copyright   (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Implements Kunena specific functions for all layouts.
 *
 * @see   KunenaLayoutBase
 * @since Kunena
 */
class KunenaLayout extends KunenaLayoutBase
{
	/**
	 * Content to be appended after the main output.
	 *
	 * @var array
	 * @since Kunena
	 */
	protected $after = array();

	/**
	 * Object KunenaView
	 *
	 * @var mixed
	 * @since Kunena
	 */
	protected $legacy;

	/**
	 * Append HTML after the layout content.
	 *
	 * @param   string $content content
	 *
	 * @since Kunena
	 * @return void
	 */
	public function appendAfter($content)
	{
		$this->after[] = $content;
	}

	/**
	 * @param   mixed $key key
	 *
	 * @return string
	 * @since Kunena
	 */
	public function text($key)
	{
		return Text::_($key);
	}

	/**
	 * Method to render the view.
	 *
	 * @param   string $layout layout
	 *
	 * @return  string  The rendered view.
	 *
	 * @throws  Exception|RunTimeException
	 * @since Kunena
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
	 * @param   string $link  link
	 * @param   string $name  name
	 * @param   string $scope scope
	 * @param   string $type  type
	 * @param   null   $id    id
	 *
	 * @return string
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function getButton($link, $name, $scope, $type, $id = null)
	{
		return KunenaFactory::getTemplate()->getButton(KunenaRoute::_($link), $name, $scope, $type, $id);
	}

	/**
	 * @param   string $name  name
	 * @param   string $title title
	 *
	 * @return string
	 * @throws Exception
	 * @since Kunena
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
	 * @since Kunena
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
			$output = $number / 1000000 . Text::_('COM_KUNENA_MILLION');
		}
		else
		{
			$output = $number / 1000 . Text::_('COM_KUNENA_THOUSAND');
		}

		return $output;
	}

	/**
	 * @param   KunenaForumCategory $category  category
	 * @param   null                $content   content
	 * @param   null                $title     title
	 * @param   null                $class     class
	 * @param   bool                $follow    follow
	 * @param   null                $canonical canonical
	 *
	 * @return mixed
	 * @since Kunena
	 * @throws Exception
	 * @throws null
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
			$title = Text::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_CATEGORY_TITLE', $category->name);

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

		$link = HTMLHelper::_('kunenaforum.link', $category->getUrl(), $content, $title, $class, $con);

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $link;
	}

	/**
	 * @param   KunenaForumTopic    $topic     topic
	 * @param   null                $action    action
	 * @param   null                $content   content
	 * @param   null                $title     title
	 * @param   null                $class     class
	 * @param   KunenaForumCategory $category  category
	 * @param   bool                $follow    follow
	 * @param   bool                $canonical canonical
	 *
	 * @return mixed
	 * @throws Exception
	 * @since Kunena
	 * @throws null
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
						if (!KunenaUserHelper::getMyself()->userid && KunenaConfig::getInstance()->teaser)
						{
							$title = KunenaHtmlParser::stripBBCode($topic->first_post_message, 200, false);
						}
						else
						{
							$title = KunenaHtmlParser::stripBBCode($topic->last_post_message, 200, false);
						}
						break;
					case 'unread':
						if (!KunenaUserHelper::getMyself()->userid && KunenaConfig::getInstance()->teaser)
						{
							$title = KunenaHtmlParser::stripBBCode($topic->first_post_message, 200, false);
						}
						else
						{
							$title = KunenaHtmlParser::stripBBCode($topic->last_post_message, 200, false);
						}
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

		$link = HTMLHelper::_('kunenaforum.link', $url, $content, $title, $class, $con);

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $link;
	}

	/**
	 * @param   mixed $category  category
	 * @param   null  $content   content
	 * @param   null  $title     title
	 * @param   null  $class     class
	 * @param   int   $length    length
	 * @param   bool  $follow    follow
	 * @param   null  $canonical canonical
	 *
	 * @return mixed
	 * @throws Exception
	 * @since Kunena
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
			if (KunenaConfig::getInstance()->disable_re)
			{
				$content = KunenaHtmlParser::parseText($lastTopic->subject, $length);
			}
			else
			{
				$content = $lastTopic->first_post_id != $lastTopic->last_post_id ? Text::_('COM_KUNENA_RE') . ' ' : '';
				$content .= KunenaHtmlParser::parseText($lastTopic->subject, $length);
			}
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

		return HTMLHelper::_('kunenaforum.link', $uri, $content, $title, $class, $con);
	}

	/**
	 * Removing it only after removed usage of this method, because without it, it cause issue in discuss plugin
	 *
	 * @param   KunenaView $view view
	 *
	 * @since      4.0
	 *
	 * @deprecated 5.0
	 * @return $this
	 * @since      Kunena
	 */
	public function setLegacy(KunenaView $view = null)
	{
		$this->legacy = $view;

		return $this;
	}
}
