<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
defined ( '_JEXEC' ) or die ();

require_once (KUNENA_PATH_LIB . DS . 'kunena.rss.class.php');


class CKunenaRSSView extends CKunenaRSS {


	/**
	 * Dummy constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($catid = 0) {
		parent::__construct();

		if ((int) $catid > 0) {
			$this->setCategory($catid);
		}
	}


	/**
	 * Dummy destructor
	 *
	 * @access public
	 * @return void
	 */
	public function __destruct() {
		parent::__destruct();
	}


	/**
	 * Set a wanted category for feed content
	 *
	 * @param int $catid Id on category wanted in feed
	 * @return bool
	 */
	public function setCategory($catid = 0) {
		$excl_cat	= $this->getQueryOption('excl_cat');
		$catid		= (int) $catid;

		if ($catid > 0) {
			$this->db->setQuery ( "SELECT * FROM #__fb_categories WHERE id = {$catid} ORDER BY ordering LIMIT 1" );
			$category = $this->db->loadObject();
			check_dberror("Unable to load category info.");

			if ($category->published != 1) {
				// forbidden
			}
			else if (in_array($catid, $excl_cat)) {
				// forbidden
			}
			else {
				$this->setQueryOption('incl_cat', array((int) $catid));

				$title = $this->getLabel('name') .' - '. $category->name;
				$this->setLabel('title', $title);
				$this->setLabel('image_title', $title);	// to fix stupid validation error

				return true;
			}
		}

		return false;
	}


	/**
	 * Pulls together data and options and outputs the build feed.
	 * Header and mime is automaticly set.
	 *
	 * @access public
	 * @see self::buildFeed()
	 * @return void
	 * @todo Implement feed caching. Maybe FeedCreator can do this automaticly?
	 */
	public function display() {
		$type		= $this->getOption('type');
		$html		= $this->getOption('allow_html');
		$spec		= $this->getOption('specification');
		$author		= $this->getOption('author_format');
		$words		= $this->getOption('word_count');
		$old_titles	= $this->getOption('old_titles');
		$data		= $this->getData();
		$feed		= $this->buildFeed($data, $type, $html, $author, $words, $old_titles);

		// On the fly
		$feed->outputFeed($spec);

		// Notes from feedcreator examples:
		//$feed->useCached();	// use cached version if age < 1 hour
		//$html = $feed->createFeed($spec);
	}

	/**
	 *
	 * @param unknown_type $items
	 * @param unknown_type $type
	 * @param unknown_type $render_html
	 * @param unknown_type $author_format
	 * @param unknown_type $message_words
	 */
	private function buildFeed($items = array(), $type, $render_html, $author_format, $message_words, $old_titles) {
		// Get the options and values we'll need
		$uri		= JURI::getInstance(JURI::base());
		$uribase	= $uri->toString(array('scheme', 'host', 'port'));

		// Make sure Joomla's FeedCreator is included
		jimport('bitfolge.feedcreator');

		$feed = new UniversalFeedCreator();

		// Set generel labels and info for the feed
		{
			$feed->link				= $this->getLabel('link');
			$feed->description		= $this->getLabel('description');
			$feed->title			= $this->getLabel('title');
			$feed->pubDate			= $this->getLabel('pubDate');
			$feed->lastBuildDate	= $this->getLabel('lastBuildDate');
			$feed->generator		= $this->getLabel('generator');

			$feed->encoding			= 'UTF-8';
			$feed->syndicationURL	= $uribase . $_SERVER["REQUEST_URI"];
			$feed->xslStyleSheet	= '';	// needed, else errors from feedcreator shows
			$feed->cssStyleSheet	= '';	// needed, else errors from feedcreator shows

			// Create image for feed
			$image = new FeedImage();
			$image->title			= $this->getLabel('image_title');
			$image->url				= $this->getLabel('image_url');
			$image->link			= $this->getLabel('image_link');
			$image->description		= $this->getLabel('image_description');

			$feed->image = $image;
		}

		// Build items for feed
		{
			foreach ($items as $data) {
				$item = new FeedItem();

				// Build unique direct linking url for each item (htmlspecialchars_decode because FeedCreator uses htmlspecialchars on input)
				$url = htmlspecialchars_decode(CKunenaLink::GetThreadPageURL(
					$this->config,
					'view',
					$data->catid,
					$data->thread,
					ceil($data->thread_count / $this->config->messages_per_page),
					$this->config->messages_per_page,
					$data->lastpost_id
				));

				// Extract the data, we want to present and store it in $tmp
				$tmp = array();

				switch ($type) {
					case 'thread':
						$tmp['title']		= $data->subject;
						$tmp['text']		= $data->message;
						$tmp['date']		= $data->time;
						$tmp['email']		= $data->email;
						$tmp['name']		= $data->name;
						$tmp['cat_name']	= $data->category_name;
						if ($old_titles)
							$tmp['title']	= JText::_('COM_KUNENA_GEN_SUBJECT') .': '. $data->subject .' - '. JText::_('COM_KUNENA_GEN_BY') .': '. $data->name;
						break;
					case 'post':
						$tmp['title']		= $data->lastpost_subject;
						$tmp['text']		= $data->lastpost_message;
						$tmp['date']		= $data->lastpost_time;
						$tmp['email']		= $data->lastpost_email;
						$tmp['name']		= $data->lastpost_name;
						$tmp['cat_name']	= $data->category_name;
						if ($old_titles)
							$tmp['title']	= JText::_('COM_KUNENA_GEN_SUBJECT') .': '. $data->lastpost_subject .' - '. JText::_('COM_KUNENA_GEN_BY') .': '. $data->lastpost_name;
						break;
					case 'recent':
					default:
						$tmp['title']		= $data->subject;
						$tmp['text']		= $data->lastpost_message;
						$tmp['date']		= $data->lastpost_time;
						$tmp['email']		= $data->lastpost_email;
						$tmp['name']		= $data->lastpost_name;
						$tmp['cat_name']	= $data->category_name;
						if ($old_titles)
							$tmp['title']	= JText::_('COM_KUNENA_GEN_SUBJECT') .': '. $data->subject .' - '. JText::_('COM_KUNENA_GEN_BY') .': '. $data->name;
				}

				// Guid is used by aggregators to uniquely identify each item
				$tmp['guid']	= $uribase . $url;

				// Link and source is always the same
				$tmp['link']	= $uribase . $url;
				$tmp['source']	= $uribase . $url;

				// Determine author format
				switch ($author_format) {
					case 'email':
						$tmp['author'] = $tmp['email'];
						break;
					case 'name':
					default:
						$tmp['author'] = $tmp['name'];
				}

				// Limit number of words
				if ($message_words) {
					$Newmessage = '';
					$t_newString = explode(" ", $tmp['text']);
					foreach ($t_newString as $key => $word) {
						if ($key < $message_words) {
							$Newmessage .= $word.' ';
						}
					}

					// Append userfriendly '...' string
					if (strlen($tmp['text']) != strlen($Newmessage)) {
						$Newmessage .= ' ...';
					}

					$tmp['text'] = $Newmessage;
				}

				if ($render_html) {
					// Not nessecary to convert specialchars or use parsetext.
					// ParseBBCode does it for us
					$tmp['text'] = CKunenaTools::parseBBCode($tmp['text']);
				}
				else {
					// Not nessecary to convert specialchars.
					// FeedCreator does it for us
					$tmp['text'] = CKunenaTools::parseText($tmp['text']);
				}


				// Assign values to feed item
				$item->title		= $tmp['title'];
				$item->link			= $tmp['link'];
				$item->description	= $tmp['text'];
				$item->date			= $tmp['date'];
				$item->source		= $tmp['source'];
				$item->author		= $tmp['author'];
				$item->category		= $tmp['cat_name'];
				$item->guid			= $tmp['guid'];


				// Optional
				//$item->descriptionTruncSize		= 500;		// behaves strangely
				//$item->descriptionHtmlSyndicated	= false;	// doesnt work

				// Optional (enclosure)
				//$item->enclosure = new EnclosureItem();		// Not tried
				//$item->enclosure->url		= 'http://http://www.dailyphp.net/media/voice.mp3';
				//$item->enclosure->length	= "950230";
				//$item->enclosure->type	= 'audio/x-mpeg';

				// Finally add item to feed
				$feed->addItem($item);
			}
		}

		return $feed;
	}
}

?>