<?php
/**
 * Joomla! 1.5 component: Kunena Forum Importer
 *
 * @version $Id$
 * @author Kunena Team
 * @package Joomla
 * @subpackage Kunena Forum Importer
 * @license GNU/GPL
 *
 * Imports forum data into Kunena
 *
 * @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');
jimport('joomla.application.application');

class KunenaimporterModelExportPNphpBB2 extends JModel {
	var $ext_database;
	var $ext_table_prefix;
	var $messages = array();
	var $error = '';
	var $version;
	var $auth_method;

	function __construct() {
		$app =& JFactory::getApplication();
		parent::__construct();

		$component = JComponentHelper::getComponent( 'com_kunenaimporter' );
		$params = new JParameter( $component->params );

		$db_name = $params->get('db_name');
		$db_tableprefix = $params->get('db_tableprefix');
		if (empty($db_name)) {
			$this->ext_database =& JFactory::getDBO();
		} else {
			$option['driver']   = $app->getCfg('dbtype');
			$option['host']     = $params->get('db_host');
			$option['user']     = $params->get('db_user');
			$option['password'] = $params->get('db_passwd');
			$option['database'] = $params->get('db_name');
			$option['prefix']   = $params->get('db_prefix');

			$this->ext_database =& JDatabase::getInstance( $option );
		}
	}

	function addMessage($msg) {
		$this->messages[] = $msg;
	}

	function getMessages() {
		return implode('', $this->messages);
	}

	function getError() {
		return $this->error;
	}

	function getAuthMethod() {
		return $this->auth_method;
	}

	function getOptions() {
		$options = array(
			array('name'=>'users', 'task'=>'Import Users', 'desc'=>'<b>Warning!</b> This will delete all existing user profiles from Kunena! (existing Joomla users are left alone)'),
			array('name'=>'categories', 'task'=>'Import Categories', 'desc'=>'<b>Warning!</b> This will delete all existing forums from Kunena!'),
			array('name'=>'messages', 'task'=>'Import Messages', 'desc'=>'<b>Warning!</b> This will delete all existing messages from Kunena!'),
//			array('name'=>'smilies', 'task'=>'Import Smilies', 'desc'=>'<b>Warning!</b> This will delete all smilies from Kunena!'),
//			array('name'=>'', 'task'=>'', 'desc'=>''),
		); 
		return $options;
	}

	function checkConfig() {
		$this->addMessage('<h2>Importer Status</h2>');
		if (JError::isError($this->ext_database)) $this->error = $this->ext_database->toString();
		if ($this->error) {
			$this->addMessage('<div>Database connection: <b style="color:red">FAILED</b></div>');
			$this->addMessage('<br /><div><b>Please check that your external database settings are correct!</b></div><div><b>Error:</b> '.$this->error.'</div>');
			return false;
		}
		$this->addMessage('<div>Database connection: <b style="color:green">OK</b></div>');
		$query="SELECT config_value FROM #__config WHERE config_name='pnphpbb2_version'";
		$this->ext_database->setQuery($query);
		$this->version = $this->ext_database->loadResult();
		if (!$this->version) {
			$this->error = $this->ext_database->getErrorMsg();
			if (!$this->error) $this->error = 'Configuration information missing: phpBB version not found';
		}
		if ($this->error) {
			$this->addMessage('<div>phpBB version: <b style="color:red">FAILED</b></div>');
			return false;
		}

		$version = explode('.', $this->version, 2);
		if ($version[0] != 1 || $version[1] != '2i-p3') $this->error = "Unsupported forum: PNphpBB $this->version"; 
		if ($this->error) {
			$this->addMessage('<div>phpBB version: <b style="color:red">'.$this->version.'</b></div>');
			$this->addMessage('<div><b>Error:</b> '.$this->error.'</div>');
			return false;
		}
		$this->addMessage('<div>phpBB version: <b style="color:green">'.$this->version.'</b></div>');

		$query="SELECT config_value FROM #__config WHERE config_name='auth_method'";
		$this->ext_database->setQuery($query);
		$this->auth_method = $this->ext_database->loadResult() or die("<br />Invalid query:<br />$query<br />" . $this->ext_database->errorMsg()); 
		$this->addMessage('<div>phpBB authentication method: <b style="color:green">'.$this->auth_method.'</b></div>');
	}

	function &exportCategories($start=0, $limit=0) {
		$query="SELECT forum_id AS id, parent_id AS parent, forum_name AS name, left_id AS ordering, ".
			"forum_desc AS description, 0 AS pub_access, 0 AS pub_recurse, 1 AS published, forum_posts AS numPosts, ".
			"forum_topics AS numTopics, forum_last_post_id AS id_last_msg, forum_last_post_time AS time_last_msg ".
			"FROM #__forums ";
		$this->ext_database->setQuery($query, $start, $limit); 
		$result = $this->ext_database->loadObjectList('id');
		if ($this->ext_database->getErrorNum()) die("<br />Invalid query:<br />$query<br />" . $this->ext_database->getErrorMsg()); 
		foreach ($result as $item) { 
			$row =& $result[$item->id];
			$row->name = prep($row->name);
			$row->description = prep($row->description);
		}		
		return $result;
	}

	function &exportMessages($start=0, $limit=0) {
		$query="SELECT p.post_id AS id, p.poster_ip AS ip, p.poster_id AS extuserid, ".
			" p.post_username AS name, p.post_time AS time, t.topic_views AS hits, ".
			" t.forum_id AS catid, p.post_subject AS bb_post_subject, ".
			"p.topic_id AS bb_topic_id, t.topic_title AS bb_topic_title, ".
			" t.forum_id AS bb_forum_id, post_text AS message ".
			" FROM `#__posts` AS p ".
			" LEFT JOIN `#__topics` AS t ON p.topic_id = t.topic_id ".
			" ORDER BY p.topic_id, p.post_id";
		$this->ext_database->setQuery($query, $start, $limit); 
		$result = $this->ext_database->loadObjectList('id');
		if ($this->ext_database->getErrorNum()) die("<br />Invalid query:<br />$query<br />" . $this->ext_database->getErrorMsg()); 

		// Iterate over all the posts and convert them to Kunena
		$currentthread = "";
		$currenttopicid = 0;
		$lastpost = 0;
		foreach ($result as $item) { 
			$row =& $result[$item->id];
			// Check if we have a new thread number
			if ($currenttopicid != $row->bb_topic_id) {
				$currenttopicid = $row->bb_topic_id;
				$currentthread = $row->id;
				$lastpost = 0;
				$row->subject = addslashes(prep($row->bb_topic_title));
				$row->parent = 0;
			} else {
				if ( $row->bb_post_subject == "" ) { // $JMI ini
					$row->subject = "Re: " . addslashes(prep($row->bb_topic_title)); 
				} else {
					$row->subject = addslashes(prep($row->bb_post_subject));
				}
				$row->parent = $currentthread;
			}
			$row->thread = $currentthread;
			$row->message = addslashes(prep($row->message));
		}
		return $result;
	}

	function &exportUsers($start=0, $limit=0)
	{
		$query="SELECT u.user_id as extuserid, u.*, b.ban_userid FROM `#__users` AS u LEFT OUTER JOIN `#__banlist` AS b ON u.user_id = b.ban_userid WHERE user_id > 0 AND u.user_type != 2 ORDER BY u.user_id";
		$this->ext_database->setQuery($query, $start, $limit); 
		$result = $this->ext_database->loadObjectList('user_id');
		if ($this->ext_database->getErrorNum()) die("<br />Invalid query:<br />$query<br />" . $this->ext_database->getErrorMsg()); 

		foreach ($result as $item) {
			$row =& $result[$item->user_id];			// Is this user banned?
			$row->blocked = "0";
			if ( $row->ban_userid ) {
				$row->blocked = "1";
			}

			$row->name = $row->username = html_entity_decode($row->username);
			$row->email = $row->user_email;
			$row->password = $row->user_password;

			// Convert date for last visit and register date.
			$row->registerDate  = date( "Y-m-d H:i:s", $row->user_regdate );
			$row->lastvisitDate = date( "Y-m-d H:i:s", $row->user_lastvisit );

			// Set user type and group id - 0=regular, 2=bots, 3=Admin
			if ( $row->user_type == "3" ) { 
				$row->usertype = "Administrator";
			} else {
				$row->usertype = "Registered";
			}

			// Convert bbcode in signature
			$row->user_sig = prep( $row->user_sig );

			// No imported users will get mails from the admin
			$row->emailadmin = "0";
			unset($row->ban_userid, $row->user_regdate, $row->user_lastvisit, $row->user_type);
		}
		return $result;
	}

	function &exportSmileys($start=0, $limit=0)
	{
		$query="SELECT smiley_id AS id, code AS code, smiley_url AS location, smiley_url AS greylocation, 1 AS emoticonbar FROM `#__smilies` ";
		$this->ext_database->setQuery($query, $start, $limit); 
		$result = $this->ext_database->loadObjectList('id');
		if ($this->ext_database->getErrorNum()) die("<br />Invalid query:<br />$query<br />" . $this->ext_database->getErrorMsg()); 
		return $result;
	}

}

//--- Function to prepare strings for MySQL storage ---/
function prep($s) {
// Parse out the $uid things that fuck up bbcode

    $s = preg_replace('/\&lt;/', '<', $s);
    $s = preg_replace('/\&gt;/', '>', $s);
    $s = preg_replace('/\&quot;/','"',$s);
    $s = preg_replace('/\&amp;/','&',$s);
    $s = preg_replace('/\&nbsp;/',' ',$s);  
    
    $s = preg_replace('/\&#39;/',"'",$s);
    $s = preg_replace('/\&#40;/','(',$s);
    $s = preg_replace('/\&#41;/',')',$s);
    $s = preg_replace('/\&#46;/', '.', $s);    
    $s = preg_replace('/\&#58;/', ':', $s);
    $s = preg_replace('/\&#123;/', '{', $s);
    $s = preg_replace('/\&#125;/', '}', $s);

    // <strong> </strong>    
    $s = preg_replace('/\[b:(.*?)\]/', '[b]', $s);
    $s = preg_replace('/\[\/b:(.*?)\]/', '[/b]', $s);
        
    // <em> </em>
    $s = preg_replace('/\[i:(.*?)\]/', '[i]', $s);
    $s = preg_replace('/\[\/i:(.*?)\]/', '[/i]', $s);
    
    // <u> </u>    
    $s = preg_replace('/\[u:(.*?)\]/', '[u]', $s);
    $s = preg_replace('/\[\/u:(.*?)\]/', '[/u]', $s);

    // quote
    $s = preg_replace('/\[quote:(.*?)\]/', '[quote]', $s);
    $s = preg_replace('/\[quote(:(.*?))?="(.*?)"\]/', '[b]\\3[/b]\n[quote]', $s);    
    $s = preg_replace('/\[\/quote:(.*?)\]/', '[/quote]', $s);

    // image
    #$s = preg_replace('/\[img:(.*?)="(.*?)"\]/', '[img="\\2"]', $s);
    $s = preg_replace('/\[img:(.*?)\](.*?)\[\/img:(.*?)\]/si', '[img]\\2[/img]', $s);

    // color
    $s = preg_replace('/\[color=(.*?):(.*?)\]/', '[color=\\1]', $s);
    $s = preg_replace('/\[\/color:(.*?)\]/', '[/color]', $s);

    // size
    $s = preg_replace('/\[size=\d:(.*?)\]/',    '[size=1]', $s);
    $s = preg_replace('/\[size=1[0123]:(.*?)\]/',    '[size=2]', $s);
    $s = preg_replace('/\[size=1[4567]:(.*?)\]/',    '[size=3]', $s);
    $s = preg_replace('/\[size=((1[89])|(2[01])):(.*?)\]/',    '[size=4]', $s);
    $s = preg_replace('/\[size=2[234567]:(.*?)\]/',    '[size=5]', $s);
    $s = preg_replace('/\[size=((2[89])|(3[01])):(.*?)\]/',    '[size=6]', $s);
    $s = preg_replace('/\[size=3[2-9]:(.*?)\]/',    '[size=7]', $s);
    $s = preg_replace('/\[\/size:(.*?)\]/', '[/size]', $s);

    // code
    // $s = preg_replace('/\[code:(.*?):(.*?)\]/',    '[code:\\1]', $s);
    // $s = preg_replace('/\[\/code:(.*?):(.*?)\]/', '[/code:\\1]', $s);
    
    // $s = preg_replace('/\[code:(.*?):(.*?)\]/',    '[code]', $s);
    // #$s = preg_replace('/\[\/code:(.*?):(.*?)\]/', '[/code]', $s);

    $s = preg_replace('/\[code:(.*?)]/',    '[code]', $s);
    $s = preg_replace('/\[\/code:(.*?)]/', '[/code]', $s);
    
    // lists
    $s = preg_replace('/\[list(:(.*?))?\]/', '[ul]', $s);
    $s = preg_replace('/\[list=([a1]):(.*?)\]/', '[ol]', $s);
    $s = preg_replace('/\[\/list:u:(.*?)\]/', '[/ul]', $s);
    $s = preg_replace('/\[\/list:o:(.*?)\]/', '[/ol]', $s);

    $s = preg_replace('/\[\*:(.*?)\]/', '[li]', $s);
    $s = preg_replace('/\[\/\*:(.*?)\]/', '[/li]', $s);
    
    $s = preg_replace('/<!-- s(.*?) --><img src=\"{SMILIES_PATH}.*?\/><!-- s.*? -->/', ' \\1 ', $s);

    $s = preg_replace('/\<!-- e(.*?) -->/', '', $s);
    $s = preg_replace('/\<!-- w(.*?) -->/', '' , $s);
    $s = preg_replace('/\<!-- m(.*?) -->/', '' , $s);

    $s = preg_replace('/\<a class=\"postlink\" href=\"(.*?)\">(.*?)<\/a>/','[url=\\1]\\2[/url]' ,$s );
    $s = preg_replace('/\<a href=\"(.*?)\">(.*?)<\/a>/','[url=\\1]\\2[/url]' ,$s );

    $s = preg_replace('/\<a href=.*?mailto:.*?>/','' , $s);

    $s = preg_replace('/\[\/url:(.*?)]/', '[/url]', $s);

    $s = preg_replace('/\<\/a>/' ,'' , $s);
    
    # $s = preg_replace('/\\\\/', '', $s);


    $s = addslashes($s);
    
    return $s;
}
?>
