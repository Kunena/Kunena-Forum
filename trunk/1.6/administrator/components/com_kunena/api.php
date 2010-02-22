<?php
/**
 * @version		$Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */

defined ( '_JEXEC' ) or die ( 'Restricted access' );
if (defined ( 'KUNENA_LOADED' ))
	return;

// Component name amd database prefix
define ( 'KUNENA_COMPONENT_NAME', basename ( dirname ( __FILE__ ) ) );
define ( 'KUNENA_NAME', substr ( KUNENA_COMPONENT_NAME, 4 ) );

// Component location
define ( 'KUNENA_COMPONENT_LOCATION', basename ( dirname ( dirname ( __FILE__ ) ) ) );

// Component paths
define ( 'KPATH_COMPONENT_RELATIVE', KUNENA_COMPONENT_LOCATION . '/' . KUNENA_COMPONENT_NAME );
define ( 'KPATH_SITE', JPATH_ROOT . '/' . KPATH_COMPONENT_RELATIVE );
define ( 'KPATH_ADMIN', JPATH_ADMINISTRATOR . '/' . KPATH_COMPONENT_RELATIVE );
define ( 'KPATH_MEDIA', JPATH_ROOT . '/media/' . KUNENA_NAME );

// URLs
define ( 'KURL_SITE', 'index.php?option=' . KUNENA_COMPONENT_NAME );
define ( 'KURL_MEDIA', JURI::Base () . 'media/' . KUNENA_NAME . '/' );

if ('@kunenaversion@' == '@' . 'kunenaversion' . '@') {
	$changelog = file_get_contents ( KPATH_SITE . '/CHANGELOG.php', NULL, NULL, 0, 1000 );
	preg_match ( '|\$Id\: CHANGELOG.php (\d+) (\S+) (\S+) (\S+) \$|', $changelog, $svn );
	preg_match ( '|~~\s+Kunena\s(\d+\.\d+.\d+\S*)|', $changelog, $version );
}

// Version information
define ( 'KUNENA_VERSION', ('@kunenaversion@' == '@' . 'kunenaversion' . '@') ? strtoupper ( $version [1] . '-SVN' ) : strtoupper ( '@kunenaversion@' ) );
define ( 'KUNENA_VERSION_DATE', ('@kunenaversiondate@' == '@' . 'kunenaversiondate' . '@') ? $svn [2] : '@kunenaversiondate@' );
define ( 'KUNENA_VERSION_NAME', ('@kunenaversionname@' == '@' . 'kunenaversionname' . '@') ? 'SVN Revision' : '@kunenaversionname@' );
define ( 'KUNENA_VERSION_BUILD', ('@kunenaversionbuild@' == '@' . 'kunenaversionbuild' . '@') ? $svn [1] : '@kunenaversionbuild@' );

interface iKunenaUserAPI {
	public function __construct($apiversion, $my_id = false);
	public function version();

	public function getReputation($userid);
	public function getRank($userid);
	public function getSignature($userid);
	public function getProfileFields($userid);
	public function getSettings($userid, $setting = false);
	public function getThreads($userid, $start = 0, $limit = 10);
	public function getPosts($userid, $start = 0, $limit = 10);
	public function getFavorites($userid, $start = 0, $limit = 10);
	public function getSubscriptions($userid, $start = 0, $limit = 10);

	public function setReputation($userid, $positive = true);
	public function setRank($userid, $rankid = 0);
	public function setSignature($userid, $signature = '');
	public function subscribe($userid, $catid = false, $mesid = false);
	public function unsubscribe($userid, $catid = false, $mesid = false);
	public function favorite($userid, $catid = false, $mesid = false);
	public function unfavorite($userid, $catid = false, $mesid = false);
}

interface iKunenaForumAPI {
	public function __construct($apiversion, $my_id = false);
	public function version();

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
	public function __construct($apiversion, $my_id = false);
	public function version();

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

// Kunena has been initialized
define ( 'KUNENA_LOADED', 1 );
