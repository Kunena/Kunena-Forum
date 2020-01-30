<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Search
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Search;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Kunena\Forum\Libraries\Html\Parser;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Layout\Layout;
use Joomla\Registry\Registry;
use function defined;

/**
 * KunenaLayoutSearchResults
 *
 * @since   Kunena 4.0
 */
class KunenaLayoutSearchResults extends Layout
{
	/**
	 * @var     \Kunena\Forum\Libraries\Forum\Message\Message
	 * @since   Kunena 6.0
	 */
	public $message;

	/**
	 * @var     \Kunena\Forum\Libraries\Forum\Category\Category
	 * @since   Kunena 6.0
	 */
	public $category;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $categoryLink;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $results;

	/**
	 * @var     \Kunena\Forum\Libraries\Forum\Topic\Topic
	 * @since   Kunena 6.0
	 */
	public $topic;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $useravatar;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $searchwords;

	/**
	 * @var     \Kunena\Forum\Libraries\User\KunenaUser
	 * @since   Kunena 6.0
	 */
	public $author;

	/**
	 * @var     \Kunena\Forum\Libraries\User\KunenaUser
	 * @since   Kunena 6.0
	 */
	public $topicAuthor;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $topicTime;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $subjectHtml;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $messageHtml;

	/**
	 * Method to display the layout of search results
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 * @throws  null
	 */
	public function displayRows()
	{
		// Run events
		$params = new Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'search');
		$params->set('kunena_layout', 'default');

		PluginHelper::importPlugin('kunena');
		Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.messages', &$this->results, &$params, 0]);

		foreach ($this->results as $this->message)
		{
			$this->topic        = $this->message->getTopic();
			$this->category     = $this->message->getCategory();
			$this->categoryLink = $this->getCategoryLink($this->category->getParent()) . ' / ' . $this->getCategoryLink($this->category);
			$ressubject         = Parser::parseText($this->message->subject);
			$resmessage         = Parser::parseBBCode($this->message->message, 500);

			$profile          = KunenaFactory::getUser((int) $this->message->userid);
			$this->useravatar = $profile->getAvatarImage('kavatar', 'post');

			foreach ($this->searchwords as $searchword)
			{
				if (empty($searchword))
				{
					continue;
				}

				$ressubject = preg_replace("/" . preg_quote($searchword, '/') . "/iu",
					'<span  class="searchword" >' . $searchword . '</span>', $ressubject
				);

				// FIXME: enable highlighting, but only after we can be sure that we do not break html
				// $resmessage = preg_replace ( "/" . preg_quote ( $searchword, '/' ) . "/iu",
				// '<span  class="searchword" >' . $searchword . '</span>', $resmessage );
			}

			$this->author      = $this->message->getAuthor();
			$this->topicAuthor = $this->topic->getAuthor();
			$this->topicTime   = $this->topic->first_post_time;
			$this->subjectHtml = $ressubject;
			$this->messageHtml = $resmessage;

			$contents = $this->subLayout('Search/Results/Row')->setProperties($this->getProperties());
			echo $contents;
		}
	}
}
