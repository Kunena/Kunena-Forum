<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Categories
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Layout;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RunTimeException;
use stdClass;

/**
 * implements \Kunena specific functions for all layouts.
 *
 * @see     Base
 * @since   Kunena 6.0
 */
class KunenaLayout extends KunenaBase
{
	/**
	 * @var mixed|string|void
	 * @since version
	 */
	public $userkarma_plus;

	/**
	 * @var false|mixed|string|void
	 * @since version
	 */
	public $personalText;

	/**
	 * @var mixed|void|null
	 * @since version
	 */
	public $usermedals;

	/**
	 * @var mixed|void|null
	 * @since version
	 */
	public $userpoints;

	/**
	 * @var integer|mixed|void|null
	 * @since version
	 */
	public $userthankyou;

	/**
	 * @var integer|mixed|void|null
	 * @since version
	 */
	public $userposts;

	/**
	 * @var false|mixed|stdClass|string|void|null
	 * @since version
	 */
	public $userranktitle;

	/**
	 * @var false|mixed|stdClass|string|void|null
	 * @since version
	 */
	public $userrankimage;

	/**
	 * @var mixed|string|void
	 * @since version
	 */
	public $userkarma;

	/**
	 * @var mixed|string|void
	 * @since version
	 */
	public $userkarma_minus;

	/**
	 * @var mixed|string|void
	 * @since version
	 */
	public $userkarma_title;

	/**
	 * Content to be appended after the main output.
	 *
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $after = [];

	/**
	 * Object KunenaView
	 *
	 * @var     mixed
	 * @since   Kunena 6.0
	 */
	protected $legacy;

	/**
	 * Append HTML after the layout content.
	 *
	 * @param   string  $content  content
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function appendAfter(string $content): void
	{
		$this->after[] = $content;
	}

	/**
	 * @param   mixed  $key  key
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function text($key): string
	{
		return Text::_($key);
	}

	/**
	 * Method to render the view.
	 *
	 * @param   string  $layout  layout
	 *
	 * @return  string  The rendered view.
	 *
	 * @throws  Exception|RunTimeException
	 * @since   Kunena 6.0
	 */
	public function render($layout = null): string
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start("render layout '{$this->_name}'") : null;

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
			KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop("render layout '{$this->_name}'") : null;
			throw $e;
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop("render layout '{$this->_name}'") : null;

		return $output;
	}

	/**
	 * @param   string  $link   link
	 * @param   string  $name   name
	 * @param   string  $scope  scope
	 * @param   string  $type   type
	 * @param   null    $id     id
	 *
	 * @return  string
	 *
	 * @throws null
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function getButton(string $link, string $name, string $scope, string $type, $id = null): string
	{
		return KunenaFactory::getTemplate()->getButton(KunenaRoute::_($link), $name, $scope, $type, $id);
	}

	/**
	 * @param   string  $name   name
	 * @param   string  $title  title
	 *
	 * @return  string
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function getIcon(string $name, $title = ''): string
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
	 * @param   int  $number     Number to be formated
	 * @param   int  $precision  Significant digits for output
	 *
	 * @return  string
	 *
	 * @since   Kunena 4.0
	 */
	public function formatLargeNumber(int $number, $precision = 3)
	{
		// Do we need to reduce the number of significant digits?
		if ($number >= 10000)
		{
			$precisionToInt = -1 * (log10($number) + 1) + $precision;

			// Round the number to n significant digits
			$number = round($number, (int) $precisionToInt);
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
	 * @param   KunenaCategory  $category   category
	 * @param   null            $content    content
	 * @param   null            $title      title
	 * @param   null            $class      class
	 * @param   bool            $follow     follow
	 * @param   null            $canonical  canonical
	 *
	 * @return  mixed
	 *
	 * @throws Exception
	 * @throws null
	 * @since   Kunena 6.0
	 */
	public function getCategoryLink(KunenaCategory $category, $content = null, $title = null, $class = null, bool $follow = true, $canonical = null)
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

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

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $link;
	}

	/**
	 * @param   KunenaTopic          $topic      topic
	 * @param   null                 $action     action
	 * @param   null                 $content    content
	 * @param   null                 $title      title
	 * @param   null                 $class      class
	 * @param   KunenaCategory|null  $category   category
	 * @param   bool                 $follow     follow
	 * @param   bool                 $canonical  canonical
	 *
	 * @return  mixed
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function getTopicLink(KunenaTopic $topic, $action = null, $content = null, $title = null, $class = null, KunenaCategory $category = null, bool $follow = true, bool $canonical = false)
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		$url = $topic->getUrl($category ?: (isset($this->category) ? $this->category : $topic->getCategory()), true, $action);

		if (!$content)
		{
			$content = KunenaParser::parseText($topic->subject);
		}

		if ($title === null)
		{
			if ($action instanceof KunenaMessage)
			{
				$title = KunenaParser::stripBBCode($topic->first_post_message, 200, false);
			}
			else
			{
				switch ($action)
				{
					case 'first':
						$title = KunenaParser::stripBBCode($topic->first_post_message, 200, false);
						break;
					case 'unread':
					case 'last':
						if (!KunenaUserHelper::getMyself()->userid && KunenaConfig::getInstance()->teaser)
						{
							$title = KunenaParser::stripBBCode($topic->first_post_message, 200, false);
						}
						else
						{
							$title = KunenaParser::stripBBCode($topic->last_post_message, 200, false);
						}
						break;
					default:
						$title = KunenaParser::stripBBCode($topic->first_post_message, 200, false);
				}
			}

			if ($class !== null)
			{
				if (strpos($class, 'hasTooltip') !== false)
				{
					// Tooltips will decode HTML and we don't want the HTML to be parsed
					$title = $this->escape($title);
				}
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

		$link = HTMLHelper::_('link', $url, $content, $title, $class, $con);

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $link;
	}

	/**
	 * @param   KunenaCategory  $category   The KunenaCategory object
	 * @param   string          $content    The content of last topic subject
	 * @param   string          $title      The title of the link
	 * @param   string          $class      The class attribute of the link
	 * @param   int             $length     length
	 * @param   bool            $follow     follow
	 * @param   bool            $canonical  canonical
	 *
	 * @return  string
	 *
	 * @throws Exception
	 * @throws null
	 * @since   Kunena 6.0
	 */
	public function getLastPostLink(KunenaCategory $category, $content, $title, $class, $length = 30, $follow = true, $canonical = false)
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
			if (KunenaConfig::getInstance()->disableRe)
			{
				$content = KunenaParser::parseText($lastTopic->subject, $length);
			}
			else
			{
				$content = $lastTopic->first_post_id != $lastTopic->last_post_id ? Text::_('COM_KUNENA_RE') . ' ' : '';
				$content .= KunenaParser::parseText($lastTopic->subject, $length);
			}
		}

		if ($title === null)
		{
			$title = KunenaParser::stripBBCode($lastTopic->last_post_message, 200, false);

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
}
