<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
defined ( '_JEXEC' ) or die ();

require_once (KPATH_SITE .'/lib/kunena.rss.class.php');

class CKunenaRSSView {
	protected	$app;
	protected	$config;

	/**
	 * @access public
	 * @return void
	 */
	public function __construct( $catid = 0 ) {
		$this->app		= JFactory::getApplication();
		$this->config	= KunenaFactory::getConfig();
		$this->document = JFactory::getDocument();
		$this->uri		= JURI::getInstance(JURI::base());

		// Important!
		if (!$this->config->enablerss) {
			JError::raiseError ( 404, JText::_ ( 'COM_KUNENA_RSS_DISABLED') );
		}

		// Load global options from config
		$this->type					= strtolower($this->config->rss_type);
		$this->specification		= strtolower(str_replace('rss', '', $this->config->rss_specification));
		$this->allow_html 			= (bool) $this->config->rss_allow_html;
		$this->author_format	 	= strtolower($this->config->rss_author_format);
		$this->author_in_title		= strtolower($this->config->rss_author_in_title);
		$this->word_count 			= (int) $this->config->rss_word_count;
		$this->old_titles 			= (bool) $this->config->rss_old_titles;
		$this->caching				= (int) $this->config->rss_cache;
		//$this->caching				= 0;

		// Queryoptions which will be verified by CKunenaRSSData later
		$this->timelimit			= $this->config->rss_timelimit;
		$this->limit				= $this->config->rss_limit;
		$this->incl_cat				= $this->config->rss_included_categories;	// for global feeds
		$this->excl_cat				= $this->config->rss_excluded_categories;

		// Is this feed for a specific category?
		if ((int) $catid > 0) {
			// Override categories wanted
			$this->incl_cat = array( (int) $catid );
		}
	}


	/**
	 * Pulls together data and options and outputs the build feed.
	 * Header and mime is automaticly set.
	 *
	 * @access public
	 * @uses JCache
	 * @see JFactory::getCache()
	 * @see CKunenaRSSData::fetch()
	 * @see self::buildFeed()
	 * @return void
	 */
	public function display() {
		$cache = JFactory::getCache( 'com_kunena_rss' );

		if ( $this->caching ) {
			$cache->setCaching( 1 );
			$cache->setLifeTime( $this->caching );
		}

		// Fetch data
		$data = $cache->call( array( 'CKunenaRSSData', 'fetch' ), $this->type, $this->incl_cat, $this->excl_cat, $this->limit, $this->timelimit );

		// Set datas document header
		$this->document = $this->document->getInstance('feed');
		$this->document->setTitle($this->app->getCfg('sitename') .' - Forum');
		$this->document->setDescription('Kunena Site Syndication');
		$this->document->setGenerator('Kunena 1.6');

		// Image link for feed
		$link			= JURI::root();
		$rss_url		= $this->uri->toString(array('scheme', 'host', 'port')) . $_SERVER["REQUEST_URI"];
		$rss_icon		= KUNENA_URLICONSPATH . 'rss.png';

		// Create image for feed
		$image = new JFeedImage();
		$image->title			= $this->document->getTitle();
		$image->url				= $rss_icon;
		$image->link			= $link;
		$image->description		= $this->document->getDescription();

		$this->document->image = $image;

		$this->buildFeed( $data );

		// Render the feed
		echo $this->document->render();
	}


	/**
	 * Pulls together data and options and outputs the build feed.
	 * Header and mime is automaticly set.
	 *
	 * @access private
	 * @param array $items
	 */
	private function buildFeed( $items = array() ) {
		// Build items for feed
		foreach ($items as $data) {
			$item = new JFeedItem();

			// Build unique direct linking url for each item (htmlspecialchars_decode because FeedCreator uses htmlspecialchars on input)
			$url = htmlspecialchars_decode(CKunenaLink::GetThreadPageURL(
				'view',
				$data->catid,
				$data->thread,
				1,
				$this->config->messages_per_page,
				$data->id
			));

			// Extract the data, we want to present and store it in $tmp
			$tmp = array();

			$tmp['title']		= $data->subject;
			// Remove confidential information from message
			$data->message = preg_replace ( '/\[confidential\](.*?)\[\/confidential\]/s', '', $data->message );
			$data->message = preg_replace ( '/\[hide\](.*?)\[\/hide\]/s', '', $data->message );
			$data->message = preg_replace ( '/\[spoiler\]/s', '[spoilerlight]', $data->message );
			$data->message = preg_replace ( '/\[\/spoiler\]/s', '[/spoilerlight]', $data->message );
			$data->message = preg_replace ( '/\[code\](.*?)\[\/code]/s', '', $data->message );

			$tmp['text']		= $data->message;
			$tmp['date']		= $data->time;
			$tmp['email']		= $data->email;
			$tmp['name']		= $data->name;
			$tmp['cat_name']	= $data->catname;

			// Guid is used by aggregators to uniquely identify each item
			$tmp['guid']		= $this->uri->toString(array('scheme', 'host', 'port')) . $url;

			// Link and source is always the same
			$tmp['link']		= $this->uri->toString(array('scheme', 'host', 'port')) . $url;
			$tmp['source']		= $this->uri->toString(array('scheme', 'host', 'port')) . $url;

			// Determine title format
			if ($this->old_titles) {
				$tmp['title'] = JText::_('COM_KUNENA_GEN_SUBJECT') .': '. $tmp['title'];
			}

			// Determine author format
			switch ($this->author_format) {
				case 'both':
					$tmp['author'] = $tmp['email'] .' ('. $tmp['name'] .')';
					break;
				case 'email':
					$tmp['author'] = $tmp['email'];
					break;
				case 'name':
				default:
					$tmp['author'] = $tmp['name'];
			}

			// Do we want author in item titles?
			if ($this->author_in_title) {
				 $tmp['title'] .= ' - '. JText::_('COM_KUNENA_GEN_BY') .': '. $tmp['name'];
			}

			// Limit number of words
			if ($this->word_count) {
				$Newmessage = '';
				$t_newString = explode(" ", $tmp['text']);
				foreach ($t_newString as $key => $word) {
					if ($key < $this->word_count) {
						$Newmessage .= $word .' ';
					}
				}

				// Append userfriendly '...' string
				if (strlen($tmp['text']) != strlen($Newmessage)) {
					$Newmessage .= ' ...';
				}

				$tmp['text'] = $Newmessage;
			}

			if ($this->allow_html) {
				// Not nessecary to convert specialchars or use parsetext.
				// ParseBBCode does it for us
				$tmp['text'] = KunenaParser::parseBBCode($tmp['text']);
			}
			else {
				// Not nessecary to convert specialchars.
				// FeedCreator does it for us
				$tmp['text'] = KunenaParser::parseText($tmp['text']);
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

			// Finally add item to feed
			$this->document->addItem($item);
		}
	}
}

?>