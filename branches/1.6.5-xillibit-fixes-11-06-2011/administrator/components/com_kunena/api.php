<?php
/**
 * @version		$Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();
if (defined ( 'KUNENA_LOADED' ))
	return;

// Component name amd database prefix
define ( 'KUNENA_COMPONENT_NAME', basename ( dirname ( __FILE__ ) ) );
define ( 'KUNENA_NAME', substr ( KUNENA_COMPONENT_NAME, 4 ) );

// Component location
define ( 'KUNENA_COMPONENT_LOCATION', basename ( dirname ( dirname ( __FILE__ ) ) ) );

// Component paths
define ( 'KPATH_COMPONENT_RELATIVE', KUNENA_COMPONENT_LOCATION . '/' . KUNENA_COMPONENT_NAME );
define ( 'KPATH_SITE', JPATH_ROOT .'/'.  KPATH_COMPONENT_RELATIVE );
define ( 'KPATH_ADMIN', JPATH_ADMINISTRATOR .'/'. KPATH_COMPONENT_RELATIVE );
define ( 'KPATH_MEDIA', JPATH_ROOT .'/media/'. KUNENA_NAME );

// URLs
define ( 'KURL_COMPONENT', 'index.php?option=' . KUNENA_COMPONENT_NAME );
define ( 'KURL_SITE', JURI::Root () . KPATH_COMPONENT_RELATIVE . '/' );
define ( 'KURL_MEDIA', JURI::Root () . 'media/' . KUNENA_NAME . '/' );

/**
 * Intelligent library importer.
 *
 * @param	string	A dot syntax path.
 * @return	boolean	True on success
 * @since	1.6
 */
function kimport($path)
{
	//return JLoader::import($path, KPATH_ADMIN.'/libraries');
	require_once(KPATH_ADMIN.'/libraries/'.str_replace( '.', '/', $path).'.php');
}

// Give access to all KunenaTables
JTable::addIncludePath(KPATH_ADMIN.'/libraries/tables');

// Import KunenaFactory
kimport('factory');
kimport('route');

/**
 * Defines public interface for class Kunena. Loads version information and APIs to be used in the third party application.
 *
 * Usage:
 *  $kapipath = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';
 *  if (file_exists ($kapipath)) {
 *  	require_once($kapipath);
 *  	$kunenaVersion = Kunena::version();
 *  	$kunenaUserApi = Kunena::getUserAPI();
 *  } else {
 *  	// Kunena 1.6+ not detected
 *  }
 *
 * @since	1.6
 */
interface iKunena {
	/**
	 * Get Kunena version.
	 *
	 * @return	string	Version
	 */
	public static function version();

	/**
	 * Get Kunena version, date, name and build number.
	 *
	 * @return	stdClass	Class containing version, date, name, build
	 */
	public static function getVersionInfo();

	/**
	 * Get Kunena configuration
	 *
	 * @return	CKunenaConfig
	 */
	public static function getConfig();

	/**
	 * Return instance of KunenaUserAPI.
	 *
	 * @return	KunenaUserAPI
	 */
	// TODO: will be deprecated!
	public static function getUserAPI();

	/**
	 * Return instance of KunenaForumAPI.
	 *
	 * @return	KunenaForumAPI
	 */
	//public static function getForumAPI();

	/**
	 * Return instance of KunenaPostAPI.
	 *
	 * This API is meant to post, reply, edit and delete individual messages.
	 * By default it obeys the permissions of current user.
	 *
	 * @return	KunenaPostAPI
	 */
	//public static function getPostAPI();
}

// TODO: will be deprecated!
interface iKunenaUserAPI {
	public function __construct();
	public static function version();

	/**
	 * Get Kunena User Profile
	 *
	 * If $userid = 0 or user profile does not exist, default profile will be returned
	 *
	 * @param int $userid User ID
	 * @return KunenaUser
	 */
	public function getProfile($userid);
	/**
	 * Get User Rank
	 *
	 * Note: for buildin ranks (default, visitor, moderator, administrator) rank_id = 0
	 *
	 * @param int $userid User ID
	 * @return stdClass Rank object with rank_id, rank_title and rank_image
	 *
	 * Note! rank_title and/or rank_image may be null if ranks are hidden!
	 */
	public function getRank($userid);

	public function getTopics($userid, $start = 0, $limit = 10, $search=false);
	public function getPosts($userid, $start = 0, $limit = 10, $search=false);
	public function getFavorites($userid, $start = 0, $limit = 10, $search=false);
	public function getSubscriptions($userid, $start = 0, $limit = 10, $search=false);

	/**
	 * Subscribe to Threads
	 *
	 * subscribeThreads( $userid, 1 );
	 * subscribeThreads( $userid, array(1,2,3) );
	 *
	 * @param int $userid Only current user is accepted
	 * @param mixed $thread Thread or array of threads
	 */
	public function subscribeThreads($userid, $threads);
	/**
	 * Unsubscribe from Threads
	 *
	 * unsubscribeThreads( $userid, true ); // All
	 * unsubscribeThreads( $userid, 1 );
	 * unsubscribeThreads( $userid, array(1,2,3) );
	 *
	 * @param int $userid Only current user is accepted
	 * @param mixed $thread true, Thread or array of threads
	 */
	public function unsubscribeThreads($userid, $threads = false);
	/**
	 * Subscribe to Categories
	 *
	 * subscribeCategories( $userid, 1 );
	 * subscribeCategories( $userid, array(1,2,3) );
	 *
	 * @param int $userid Only current user is accepted
	 * @param mixed $catid Category or array of categories
	 */
	public function subscribeCategories($userid, $catids);
	/**
	 * Unsubscribe from Categories
	 *
	 * unsubscribeCategories( $userid, true ); // All
	 * unsubscribeCategories( $userid, 1 );
	 * unsubscribeCategories( $userid, array(1,2,3) );
	 *
	 * @param int $userid Only current user is accepted
	 * @param mixed $catid true, category or array of categories
	 */
	public function unsubscribeCategories($userid, $catids = false);
	/**
	 * Favorite Threads
	 *
	 * favoriteThreads( $userid, 1 );
	 * favoriteThreads( $userid, array(1,2,3) );
	 *
	 * @param int $userid Only current user is accepted
	 * @param mixed $thread Thread or array of threads
	 */
	public function favoriteThreads($userid, $threads);
	/**
	 * Unfavorite Threads
	 *
	 * unfavoriteThreads( $userid, true ); // All
	 * unfavoriteThreads( $userid, 1 );
	 * unfavoriteThreads( $userid, array(1,2,3) );
	 *
	 * @param int $userid Only current user is accepted
	 * @param mixed $thread true, Thread or array of threads
	 */
	public function unfavoriteThreads($userid, $threads = false);
}

/*
interface iKunenaForumAPI {
	public function __construct();
	public static function version();

	public function get($catid);
	public function create($foruminfo);
	public function modify($foruminfo);
	public function delete($catid);

	public function getModerators($catid);
	public function isModerator($catid, $userid);
	public function addModerator($catid, $userid);
	public function removeModerator($catid, $userid);

	public function getLatestThreads($start = 0, $limit = 10);
	public function getUnansweredThreads($start = 0, $limit = 10);
	public function getCategoryThreads($catid, $start = 0, $limit = 10);
}

interface iKunenaPostAPI {
	public function __construct();
	public static function version();

	public function canRead($mesid);
	public function canPost($catid);
	public function canReply($mesid);
	public function canEdit($mesid);
	public function canDelete($mesid);

	public function get($mesid);
	public function post($catid, $msginfo);
	public function reply($mesid, $msginfo);
	public function edit($mesid, $msginfo);
	public function delete($mesid);
}
*/

kimport('api');

// Kunena has been initialized
define ( 'KUNENA_LOADED', 1 );
