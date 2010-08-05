<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

defined ( '_JEXEC' ) or die ();


/**
 * @author littlejohn
 * @class CKunenaRSS
 */
class CKunenaRSS extends CKunenaRSSDatasource {
	protected	$app;
	protected	$config;


	/**
	 * Labels for the rss feed
	 * Will be filled with relevant labels on init.
	 * Should contain all kind labels for the required rss tags.
	 *
	 * @access private
	 * @var $labels array
	 */
	private		$labels = array();


	/**
	 * Options for rss feed
	 * Will be loaded from kunena config options on init.
	 * Should contain:
	 * 		type			string		Can be 'thread', 'post' or 'recent'. Defines the feed content type
	 * 		specification	string		Can be: 'atom1.0' 'rss0.91', 'rss1.0', 'rss2.0'. Defines the template used for content.
	 * 		allow_html		bool		Can be: true, false. Convert bbcode and allow html in output.
	 * 		author_format	string		Can be: 'name', 'email'. Defines which format to present in feed
	 * 		word_count		int			Can be: 0-1000. Number of words to present in feed items. 0 Disables the limited word counter
	 * 		old_titles		bool		Can be: true, false. Use plain titles or titles like: 'Subject: #title# - By: #author#'
	 *
	 * @access private
	 * @var $options array
	 */
	private		$options = array();


	/**
	 * Loads application and database object from application.
	 * Needed query options is loaded from config.
	 * Default options is then loaded too, and finally default labels is set.
	 *
	 * @access protected
	 * @return void
	 */
	protected function __construct() {
		parent::__construct();

		$this->app		= JFactory::getApplication();
		$this->config	= KunenaFactory::getConfig ();

		if (!$this->config->enablerss) {
			die();
		}

		// Load global parameters from config (data specific)
		$this->setQueryOption('timelimit',		(int) $this->config->rss_timelimit);
		$this->setQueryOption('limit',			(int) $this->config->rss_limit);
		$this->setQueryOption('excl_cat',		explode(',', strtolower($this->config->rss_excluded_categories)));
		$this->setQueryOption('incl_cat',		explode(',', strtolower($this->config->rss_included_categories)));

		// Global parameters from config (feed specific)
		$this->setOption('type',				strtolower($this->config->rss_type));
		$this->setOption('specification',		strtolower(str_replace('rss', '', $this->config->rss_specification)));
		$this->setOption('allow_html', 			(bool) $this->config->rss_allow_html);
		$this->setOption('author_format', 		strtolower($this->config->rss_author_format));
		$this->setOption('word_count', 			(int) $this->config->rss_word_count);
		$this->setOption('old_titles', 			(bool) $this->config->rss_old_titles);

		// Labels for the needed rss tags in feed (combined with all needed tags for rss 0.9, 0.9, 1.0, 2.0)
		$this->setLabel('name',					kunena_htmlspecialchars($this->app->getCfg('sitename')));
		$this->setLabel('title',				$this->getLabel('name') .' - Forum');
		$this->setLabel('description',			'Kunena Site Syndication');
		$this->setLabel('link',					JURI::root());
		$this->setLabel('lastBuildDate',		date('r'));
		$this->setLabel('pubDate',				date('r'));
		$this->setLabel('generator',			'Kunena ' . KUNENA_VERSION);

		$this->setLabel('image_url',			KUNENA_URLICONSPATH . 'rss.png');
		$this->setLabel('image_title',			$this->getLabel('name') .' - Forum');
		$this->setLabel('image_link',			JURI::root());
		$this->setLabel('image_description',	'Kunena Site Syndication');

		// Leave out those as they may vary from site to site
		//'copyright'	- may vary
		//'language'	- may vary
		//'ttl'			- problematic, prefer HTTP 1.1 cache control
	}


	/**
	 * Dummy destructor
	 *
	 * @access protected
	 * @return void
	 */
	protected function __destruct() {
		parent::__destruct();
	}


	/**
	 * Set option for feed
	 * Valid keys is:
	 * 		'type'				string
	 * 		'specification'		string
	 * 		'allow_html'		bool
	 * 		'author_format'		string
	 * 		'word_count'		int
	 * 		'old_titles'		bool
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return mixed Value of key set
	 */
	public function setOption($key, $value = '') {
		if (!empty($key)) {
			$this->options[$key] = $value;
		}

		return @$this->options[$key];
	}


	/**
	 * Get option for query
	 *
	 * @access public
	 * @param string $key
	 * @return mixed Value of key
	 */
	public function getOption($key) {
		return @$this->options[$key];
	}


	/**
	 * Set label for feed
	 * All tags for rss feeds is valid keys.
	 * Eg:
	 * 		'generator'
	 *		'webmaster'
	 *		'copyright'
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return mixed Value of key set
	 */
	public function setLabel($key, $value = '') {
		if (!empty($key)) {
			$this->labels[$key] = $value;
		}

		return @$this->labels[$key];
	}


	/**
	 * Get label for feed
	 *
	 * @access public
	 * @param string $key
	 * @return mixed Value of key
	 */
	public function getLabel($key) {
		return @$this->labels[$key];
	}


	/**
	 * Return data from database, depending on $option key 'type'
	 *
	 * @access protected
	 * @return objectlist
	 */
	protected function getData() {
		$func = '';

		switch ($this->getOption('type')) {
			case 'thread':
				$func = 'getThreads';
				break;
			case 'post':
				$func = 'getPosts';
				break;
			case 'recent':
				$func = 'getRecentActivity';
				break;
			default:
		}

		return $this->$func();
	}
}


/**
 * 3 different queries. 1 for each type of data wanted.
 * Queries could maybe be combined, but I wasn't able to see any simpler logic.
 * Also, it seems ppl before me have had a headache with a single unified query, so maybe they are meant to be apart ;)
 *
 * @author littlejohn
 * @class CKunenaRSSDatasource
 * @abstract
 */
abstract class CKunenaRSSDatasource {
	protected	$db;

	/**
	 * Options for the data query
	 * Should contain:
	 * 		timelimit		integer		Timelimit for items in feed in hours. 0 to disable (not recommended)
	 * 		limit			integer		Limit on number of items in feed. 0 to disable (not recommended)
	 * 		incl_cat		array		Category id's that will be included in feed. Length on 0 to select all
	 * 		excl_cat		array		Category id's that will be excluded in feed. Length on 0 to disable
	 *
	 * @access private
	 * @var $queryOptions array
	 */
	private		$queryOptions = array();


	/**
	 * Default options for the database query
	 * These values will be used, if there is an error or
	 * supplied option(s) is undefined.
	 *
	 * @access private
	 * @var $queryOptionsDefault array
	 */
	private		$queryOptionsDefault = array(
						'timelimit'		=> 168,
						'limit'			=> 1000,
						'incl_cat'		=> array(),
						'excl_cat'		=> array()
	);


	/**
	 * Loads database object from application
	 *
	 * @access public
	 * @return void
	 */
	protected function __construct() {
		$this->db		= JFactory::getDBO();
	}


	/**
	 * Dummy destructor
	 *
	 * @access public
	 * @return void
	 */
	protected function __destruct() {
	}


	/**
	 * Set option for query
	 * Valid keys is:
	 * 		'timelimit'		int
	 * 		'limit'			int
	 * 		'incl_cat'		array
	 * 		'excl_cat'		array
	 *
	 * @access protected
	 * @param string $key ('timelimit', 'limit', 'incl_cat', 'excl_'cat')
	 * @param mixed $value
	 * @return mixed Value of key set
	 */
	protected function setQueryOption($key, $value = '') {
		if (!empty($key)) {
			$this->queryOptions[$key] = $value;
		}

		return @$this->queryOptions[$key];
	}


	/**
	 * Get option for query
	 *
	 * @access protected
	 * @param string $key ('timelimit', 'limit', 'incl_cat', 'excl_'cat')
	 * @return mixed Value of key
	 */
	protected function getQueryOption($key) {
		return @$this->queryOptions[$key];
	}


	/**
	 * Verify all needed query options and return verified options.
	 *
	 * @access private
	 * @param array $options
	 * @see self::$queryOptionsDefault
	 * @return array
	 */
	private function getVerifiedQueryOptions() {
		$options	= $this->queryOptions;
		$verified	= $this->queryOptionsDefault;

		// Make sure included_categories is array of integers or 0
		{
			if (isset($options['incl_cat']) && is_array($options['incl_cat'])) {
				foreach ($options['incl_cat'] as $val) {
					$tmp = (int) $val;
					if ($tmp > 0) {
						$verified['incl_cat'][] = $tmp;
					}
				}
			}
			unset($options['included_categories']);
		}

		// Make sure excluded_categories is array of integers or 0
		{
			if (isset($options['excl_cat']) && is_array($options['excl_cat'])) {
				foreach ($options['excl_cat'] as $val) {
					$tmp = (int) $val;
					if ($tmp > 0) {
						$verified['excl_cat'][] = $tmp;
					}
				}
			}
			unset($options['excluded_categories']);
		}

		// Limit query in numbers (if 0 then disable limiter)
		{
			if (isset($options['limit'])) {
				$tmp = (int) $options['limit'];
				if ($tmp >= 0) {
					$verified['limit'] = $tmp;
				}
			}
			unset($options['limit']);
		}

		// Limit query by date (if 0 then disable limiter)
		{
			if (isset($options['timelimit'])) {
				$tmp = (int) $options['timelimit'];
				if ($tmp >= 0) {
					$verified['timelimit'] = $tmp;
				}
				//$querytime =
			}
			if ($verified['timelimit'] > 0) {
				$verified['timelimit'] = (time() - ($verified['timelimit'] * 3600));
			}
			unset($options['timelimit']);
		}

		// Just migrate the rest of the options so we dont loose any (no further options is needed)
		{
			foreach ($options as $key => $val) {
				if (!isset($verified[$key])) {
					$verified[$key] = $val;
				}
			}
		}

		return $verified;
	}


	/**
	 * Get newest threads with the lastest posting as array, ordered by threads creation time.
	 * Input array (options) is verified before query is run.
	 *
	 * @access protected
	 * @uses self::getVerifiedQueryOptions()
	 * @uses self::getQueryResult()
	 * @return array
	 */
	public function getThreads() {
		// New threads (ordered by threads)
		$options = $this->getVerifiedQueryOptions();

		$columns = array(
			'timelimit' =>	'thread.time',
			'incl_cat'	=>	'thread.catid',
			'excl_cat'	=>	'thread.catid',
			'group_by'	=>	'thread.thread',
			'order_by'	=>	'thread.time'
		);

		$query = $this->getQuery($columns, $options);

		return $this->getQueryResult($query);
	}


	/**
	 * Get newest postings as array, ordered by the creation time.
	 * Input array (options) is verified before query is run.
	 * Note: Output array might be different from getThreads and getRecentActivity.
	 *
	 * @access protected
	 * @uses self::getVerifiedQueryOptions()
	 * @uses self::getQueryResult()
	 * return array
	 */
	protected function getPosts() {
		// Threads with recent activity (ordered by newest post)
		$options = $this->getVerifiedQueryOptions();

		$columns = array(
			'timelimit' =>	'thread.time',
			'incl_cat'	=>	'thread.catid',
			'excl_cat'	=>	'thread.catid',
			'group_by'	=>	'post.lastpost_id',
			'order_by'	=>	'post.lastpost_time'
		);

		$query = $this->getQuery($columns, $options);

		return $this->getQueryResult($query);
	}


	/**
	 * Get threads with the lastest posting as array, ordered by postings creation time.
	 * That means, threads with latest activity (postings), will be first.
	 * Input array (options) is verified before query is run.
	 *
	 * @access protected
	 * @uses self::getVerifiedQueryOptions()
	 * @uses self::getQueryResult()
	 * @return array
	 */
	protected function getRecentActivity() {
		// Threads with recent activity (ordered by newest post)
		$options = $this->getVerifiedQueryOptions();

		$columns = array(
			'timelimit' =>	'thread.time',
			'incl_cat'	=>	'thread.catid',
			'excl_cat'	=>	'thread.catid',
			'group_by'	=>	'thread.thread',
			'order_by'	=>	'post.lastpost_time'
		);

		$query = $this->getQuery($columns, $options);

		return $this->getQueryResult($query);
	}


	/**
	 * Get standard query, which is common to all 3 data types.
	 *
	 * @access private
	 * @return string query
	 */
	private function getQuery($columns, $options) {
		$query = "
				SELECT
					thread.*,
					message.*,
					count(thread.thread) as thread_count,
					post.*,
					category.id AS category_id,
					category.name AS category_name,
					category.cat_emoticon AS category_emoticon
				FROM
					#__kunena_messages AS thread
				INNER JOIN #__kunena_messages_text AS message ON (message.mesid = thread.id)
				INNER JOIN #__kunena_categories AS category ON (category.id = thread.catid)
				INNER JOIN (
					SELECT
						tmp1.id AS lastpost_id,
						tmp1.parent AS lastpost_parent,
						tmp1.thread AS lastpost_thread,
						tmp1.name AS lastpost_name,
						tmp1.userid AS lastpost_userid,
						tmp1.email AS lastpost_email,
						tmp1.subject AS lastpost_subject,
						tmp1.time AS lastpost_time,
						tmp1.ip AS lastpost_ip,
						tmp1.hits AS lastpost_hits,
						tmp1.modified_by AS lastpost_modified_by,
						tmp1.modified_time AS lastpost_modified_time,
						tmp1.modified_reason AS lastpost_modified_reason,
						tmp2.message as lastpost_message
					FROM
						#__kunena_messages tmp1,
						#__kunena_messages_text tmp2
					WHERE
						tmp1.hold = '0'
						AND tmp1.moved = '0'
						AND tmp1.id = tmp2.mesid
					ORDER BY
						tmp1.time DESC
				) AS post ON (post.lastpost_thread = thread.thread)
				WHERE
					category.published = '1'
					AND thread.parent = '0'
					AND thread.moved = '0'
					AND thread.hold = '0'
					AND category.pub_access = '0'
		";

		if (isset($columns['timelimit'])) {
			$query .= "			AND ". $columns['timelimit'] ." > '". $options['timelimit'] ."'
			";
		}

		if (isset($columns['incl_cat']) && count($options['incl_cat']) > 0) {
			$query .= "			AND ". $columns['incl_cat'] ." IN (". implode(',', $options['incl_cat']) .")
			";
		}

		if (isset($columns['excl_cat']) && count($options['excl_cat']) > 0) {
			$query .= "		AND ". $columns['excl_cat'] ." NOT IN (". implode(',', $options['excl_cat']) .")
			";
		}

		if (isset($columns['group_by'])) {
			$query .= "		GROUP BY ". $columns['group_by'] ."
			";
		}

		if (isset($columns['order_by'])) {
			$query .= "		ORDER BY ". $columns['order_by'] ." DESC
			";
		}

		if ($options['limit'] > 0) {
			$query .= "		LIMIT 0, ". $options['limit'] ."
			";
		}

		return $query;
	}


	/**
	 * Internal function
	 *
	 * @access private
	 * @param string $query
	 * @return objectlist
	 */
	private function getQueryResult($query = '') {
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();
		KunenaError::checkDatabaseError();

		return $results;
	}
}