<?php
/**
 * @version $Id$
 * Kunena Discuss Plugin
 * @package Kunena Discuss
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
defined ( '_JEXEC' ) or die ( '' );

jimport ( 'joomla.plugin.plugin' );

class plgContentKunenaDiscuss extends JPlugin {
	// Associative array to hold results of the plugin
	static $botDisplay = array ();
	static $includedCss = false;

	// *** initialization ***
	function plgContentKunenaDiscuss(&$subject, $params) {
		// Initialize variables
		$this->_db = JFactory::getDbo ();
		$this->_app = JFactory::getApplication ( 'site' );
		$this->_my = JFactory::getUser ();

		// If plugin is not enabled in current scope, do not register it
		if (! $this->enabled ())
			return null;

		// Initialize plugin
		parent::__construct ( $subject, $params );

		// Detect and load Kunena 1.6+
		$kunena_api = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena' . DS . 'api.php';
		if (! is_file ( $kunena_api ))
			return;
		require_once ($kunena_api);
		require_once (KUNENA_PATH . DS . 'class.kunena.php');

		$this->config = KunenaFactory::getConfig ();

		// Load language files
		$this->loadLanguage ( '', JPATH_ADMINISTRATOR );
		$lang = JFactory::getLanguage ();
		$lang->load ( 'com_kunena', KPATH_SITE );

		// Create discussbot table if doesn't exist
		$query = "SHOW TABLES LIKE '{$this->_db->_table_prefix}kunena_discuss'";
		$this->_db->setQuery ( $query );
		if ($this->_db->loadResult () === null) {
			$query = "CREATE TABLE IF NOT EXISTS `#__kunenadiscuss`
					(`content_id` int(11) NOT NULL default '0',
					 `thread_id` int(11) NOT NULL default '0',
					 PRIMARY KEY  (`content_id`)
					 )";
			$this->_db->setQuery ( $query );
			$this->_db->query ();
			CKunenaTools::checkDatabaseError ();
			$this->debug ( "Created #__kunenadiscuss cross reference table." );
		}

		$this->debug ( "Constructor called in " . $this->_app->scope );
	}

	function enabled() {
		if ($this->_app->scope == 'com_content')
			return true;
		if ($this->_app->scope == 'com_kunena')
			return true;
		return false;
	}

	// *** event prepare content ***
	function onPrepareContent(&$article, &$params, $limitstart) {
		$articleCategory = (isset ( $article->catid ) ? $article->catid : 0);
		$isStaticContent = ! $articleCategory;
		if ($isStaticContent) {
			$kunenaCategory = false;
		} else {
			$kunenaCategory = $this->getForumCategory ( $articleCategory );
			if (! $kunenaCategory)
				return true;
		}
		$kunenaTopic = false;

		$regex = '/{kunena_discuss:(\d+?)}/s';

		if (JRequest::getVar ( 'tmpl', '' ) == 'component' || JRequest::getBool ( 'print' ) || JRequest::getVar ( 'format', 'html' ) != 'html' || (isset ( $article->state ) && ! $article->state) || empty ( $article->id ) || $this->_app->scope == 'com_kunena') {
			$this->debug ( "onPrepareContent: Not allowed - removing tags." );
			if (isset ( $article->text ))
				$article->text = preg_replace ( $regex, '', $article->text );
			if (isset ( $article->introtext ))
				$article->introtext = preg_replace ( $regex, '', $article->introtext );
			if (isset ( $article->fulltext ))
				$article->fulltext = preg_replace ( $regex, '', $article->fulltext );
			return true;
		}

		$isFrontPage = JRequest::getVar ( 'view' ) == 'frontpage';
		$isBlogPage = JRequest::getVar ( 'layout' ) == 'blog';
		if ($isBlogPage) {
			$show = $this->params->get ( 'show_blog_page', 2 );
		} else if ($isFrontPage) {
			$show = $this->params->get ( 'show_front_page', 2 );
		} else {
			$show = $this->params->get ( 'show_other_pages', 2 );
		}
		if (! $show || isset ( self::$botDisplay [$article->id] )) {
			$this->debug ( "onPrepareContent: Configured to show nothing" );
			if (isset ( $article->text ))
				$article->text = preg_replace ( $regex, '', $article->text );
			if (isset ( $article->introtext ))
				$article->introtext = preg_replace ( $regex, '', $article->introtext );
			if (isset ( $article->fulltext ))
				$article->fulltext = preg_replace ( $regex, '', $article->fulltext );
			return true;
		}

		$this->debug ( "onPrepareContent: Article {$article->id}" );

		$customTopics = $this->params->get ( 'custom_topics', 1 );
		if (! $customTopics) {
			$this->debug ( "onPrepareContent: Custom Topics disabled" );
		} else {
			// Get fulltext from frontpage articles (tag can be inside fulltext)
			if ($isFrontPage) {
				$query = "SELECT fulltext FROM #__content WHERE id ={$article->id}";
				$this->_db->setQuery ( $query );
				$fulltext = $this->_db->loadResult ();
				CKunenaTools::checkDatabaseError ();
				$text = $article->introtext . ' ' . $fulltext;
			} else {
				if (isset ( $article->text )) {
					$text = $article->text;
				} else {
					if (isset ( $article->introtext )) {
						$text [] = $article->introtext;
					}
					if (isset ( $article->fulltext )) {
						$text [] = $article->fulltext;
					}
					$text = implode ( "\n\n", $text );
				}
			}

			$matches = array ();
			if (preg_match ( $regex, $text, $matches )) {
				$kunenaTopic = intval ( $matches [1] );
				if (isset ( $article->text ))
					$article->text = preg_replace ( "/{kunena_discuss:$kunenaTopic}/", '', $article->text, 1 );
				if (isset ( $article->introtext ))
					$article->introtext = preg_replace ( "/{kunena_discuss:$kunenaTopic}/", '', $article->introtext, 1 );
				if (isset ( $article->fulltext ))
					$article->fulltext = preg_replace ( "/{kunena_discuss:$kunenaTopic}/", '', $article->fulltext, 1 );
			}
			$this->debug ( "onPrepareContent: Custom Topic " . ($kunenaTopic ? "found" : "not found") . ". Searched for {kunena_discuss:#}" );
		}

		if ($kunenaCategory || $kunenaTopic) {
			$this->debug ( "onPrepareContent: Let's show you something!" );
			self::$botDisplay [$article->id] = $this->showPlugin ( $kunenaCategory, $kunenaTopic, $article, $show == 1 );
		}
		return true;
	}

	// *** event after display content ***
	function onAfterDisplayContent(&$article, &$params, $limitstart) {
		if (empty ( $article->id ))
			return '';

		if (isset ( self::$botDisplay [$article->id] )) {
			$this->debug ( "onAfterDisplayContent: Returning content for article {$article->id}" );
			return self::$botDisplay [$article->id];
		} else {
			return '';
		}
	}

	// *** internal functions follows ***
	/******************************************************************************
	 *
	 *****************************************************************************/
	function showPlugin($catid, $thread, &$row, $linkOnly) {
		// Show a simple form to allow posting to forum from the bot
		$botShowForm = $this->params->get ( 'form', 1 );
		// Default is to put QuickPost at the very bottom.
		$formLocation = $this->params->get ( 'form_location', 0 );

		// Don't repeat the CSS for each instance of this bot in a page!
		if (! self::$includedCss) {
			$doc = JFactory::getDocument ();
			$doc->addStyleSheet ( KUNENA_JLIVEURL . 'plugins/content/kunenadiscuss/discuss.css' );
			self::$includedCss = true;
		}

		$subject = $row->title;

		$query = "SELECT b.*, m.thread AS thread FROM #__kunenadiscuss AS b
			LEFT JOIN #__fb_messages AS m ON b.thread_id = m.id AND hold=0
			WHERE b.content_id = {$row->id}";
		$this->_db->setQuery ( $query );
		$result = $this->_db->loadObject ();
		CKunenaTools::checkDatabaseError ();

		if ($result && (! $result->thread || $result->thread != $thread)) {
			$this->debug ( "showPlugin: Removing cross reference record pointing to a non-existent or wrong topic" );
			$query = "DELETE FROM #__kunenadiscuss WHERE content_id={$result->content_id}";
			$this->_db->setQuery ( $query );
			$this->_db->query ();
			CKunenaTools::checkDatabaseError ();
			$result = null;
		}

		if (! $result && $thread) {
			$this->debug ( "showPlugin: First hit to Custom Topic, create cross reference" );
			$this->createReference ( $row, $thread );
		} else if (! $result) {
			$this->debug ( "showPlugin: First hit, create new topic into forum" );
			$thread = $this->createTopic ( $row, $catid, $subject );
		} else {
			$this->debug ( "showPlugin: Topic exists in the forum" );
			$thread = $result->thread_id;
		}

		if ($linkOnly) {
			$this->debug ( "showPlugin: Displaying only link to the topic" );

			$sql = "SELECT count(*) FROM #__fb_messages WHERE hold=0 AND parent!=0 AND thread=$thread";
			$this->_db->setQuery ( $sql );
			$postCount = $this->_db->loadResult ();
			CKunenaTools::checkDatabaseError ();
			$linktitle = JText::sprintf ( 'PLG_KUNENADISCUSS_DISCUSS_ON_FORUMS', $postCount );
			require_once (KPATH_SITE . '/lib/kunena.link.class.php');
			$content = CKunenaLink::GetThreadLink ( 'view', $catid, $thread, $linktitle, $linktitle );
			return $content;
		}

		// ************************************************************************
		// Process the QuickPost form


		$quickPost = '';
		$canPost = $this->canPost ( $thread );
		if ($botShowForm) {
			if (! $canPost && ! $this->_my->id) {
				$this->debug ( "showPlugin: Public posting is not permitted, show login instead" );
				$login = KunenaFactory::getLogin ();
				$loginlink = $login->getLoginURL ();
				$registerlink = $login->getRegisterURL ();
				$quickPost = JText::sprintf ( 'PLG_KUNENADISCUSS_LOGIN_OR_REGISTER', '"' . $loginlink . '"', '"' . $registerlink . '"' );
			} else if ($canPost) {
				$this->debug ( "showPlugin: You can discuss this item" );
				if (JRequest::getInt ( 'kdiscussContentId', 0, 'POST' ) == $row->id) {
					$this->debug ( "showPlugin: Reply topic!" );
					$quickPost .= $this->replyTopic ( $row, $catid, $thread, $subject );
				} else {
					$this->debug ( "showPlugin: Rendering form" );
					$quickPost .= $this->showForm ( $row );
				}
			} else {
				$this->debug ( "showPlugin: Unfortunately you cannot discuss this item" );
				$quickPost .= JText::_ ( 'PLG_KUNENADISCUSS_NO_PERMISSION_TO_POST' );
			}
		}

		// This will be used all the way through to tell users how many posts are in the forum.
		$this->debug ( "showPlugin: Rendering discussion" );
		$content = $this->showTopic ( $catid, $thread );

		if ($formLocation) {
			$content = '<div class="kunenadiscuss">' . $content . '<br />' . $quickPost . '</div>';
		} else {
			$content = '<div class="kunenadiscuss">' . $quickPost . "<br />" . $content . '</div>';
		}

		return $content;
	}

	/******************************************************************************
	 * Get user information from Kunena
	 *****************************************************************************/

	function getAuthorName() {
		if (! $this->_my->id) {
			$name = '';
		} else {
			$name = $this->_config->username ? $this->_my->username : $this->_my->name;
		}
		return $name;
	}

	/******************************************************************************
	 * Output
	 *****************************************************************************/

	function showTopic($catid, $thread) {
		// Limits the number of posts
		$limit = $this->params->get ( 'limit', 25 );
		// Show the first X posts, versus the last X posts
		$botFirstPosts = $this->params->get ( 'ordering', 1 );

		require_once (KPATH_SITE . '/funcs/view.php');
		$thread = new CKunenaView ( 'view', $catid, $thread, 1, $limit );
		$thread->setTemplate ( '/plugins/content/kunenadiscuss' );
		ob_start ();
		$thread->display ();
		$str = ob_get_contents ();
		ob_end_clean ();
		return $str;
	}

	function showForm($row) {
		$this->open = $this->params->get ( 'quickpost_open', false );
		$this->name = $this->config->username ? $this->_my->username : $this->_my->name;
		ob_start ();
		include (JPATH_ROOT . '/plugins/content/kunenadiscuss/form.php');
		$str = ob_get_contents ();
		ob_end_clean ();
		return $str;
	}

	/******************************************************************************
	 * Create and reply to topic
	 *****************************************************************************/

	function createReference($row, $thread) {
		$query = "INSERT INTO #__kunenadiscuss (content_id, thread_id) VALUES(
			{$this->_db->quote($row->id)},
			{$this->_db->quote($thread)})";
		$this->_db->setQuery ( $query );
		$this->_db->query ();
		CKunenaTools::checkDatabaseError ();
	}

	function createTopic($row, $catid, $subject) {
		require_once (KPATH_SITE . '/lib/kunena.posting.class.php');
		$message = new CKunenaPosting ( );

		$options ['authorid'] = $this->params->get ( 'topic_owner', $row->created_by );
		$fields ['subject'] = $subject;
		$fields ['message'] = "[article]{$row->id}[/article]";
		$success = $message->post ( $catid, $fields, $options );

		if ($success) {
			$newMessageId = $message->save ();
		}

		// Handle errors
		if (! $success || ! $newMessageId) {
			$errors = $message->getErrors ();
			foreach ( $errors as $field => $error ) {
				$this->_app->enqueueMessage ( $field . ': ' . $error, 'error' );
			}
			return false;
		}

		// Keep a cross reference of Threads we create through this plugin
		$this->createReference ( $row, $newMessageId );

		// TODO: Handle User subscrtiptions and Moderator notifications.
		// $message->emailToSubscribers('index.php');


		// We'll need to know about the new Thread id later...
		$this->debug ( __FUNCTION__ . "() end" );
		return $newMessageId;
	}

	function replyTopic($row, $catid, $thread, $subject) {
		// TODO: handle captcha, token, flood


		require_once (KPATH_SITE . '/lib/kunena.posting.class.php');

		if (intval ( $thread ) == 0) {
			$thread = $this->createTopic ( $row, $catid, $subject );
		}
		$message = new CKunenaPosting ( );
		$fields ['name'] = JRequest::getString ( 'name', $this->getAuthorName (), 'POST' );
		$fields ['email'] = JRequest::getString ( 'email', null, 'POST' );
		$fields ['subject'] = $subject;
		$fields ['message'] = JRequest::getString ( 'message', null, 'POST' );

		$success = $message->reply ( $thread, $fields );
		if ($success) {
			$newMessageId = $message->save ();
		}

		// Handle errors
		if (! $success || ! $newMessageId) {
			$errors = $message->getErrors ();
			foreach ( $errors as $field => $error ) {
				$this->_app->enqueueMessage ( $field . ': ' . $error, 'error' );
			}
			return false;
		}

		// TODO: Handle User subscrtiptions and Moderator notifications.
		// $message->emailToSubscribers('index.php');


		if ($message->get ( 'hold' )) {
			$result = JText::_ ( 'PLG_KUNENADISCUSS_PENDING_MODERATOR_APPROVAL' );
		} else {
			$result = JText::_ ( 'PLG_KUNENADISCUSS_MESSAGE_POSTED' );
		}

		// Redirect
		$uri = JFactory::getURI ();
		$app = JFactory::getApplication ( 'site' );
		$app->redirect ( $uri->toString (), $result );
	}

	/******************************************************************************
	 * Debugging and error handling
	 *****************************************************************************/

	function debug($msg, $fatal = 0) {
		$debug = $this->params->get ( 'show_debug', false ); // Print out debug info!
		$debugUsers = $this->params->get ( 'show_debug_userids', '' ); // Joomla Id's of Users who can see debug info


		if (! $debug || ($debugUsers && ! in_array ( $this->_my->id, explode ( ',', $debugUsers ) )))
			return;

		if ($fatal) {
			echo "<br /><span class=\"kdb-fatal\">[KunenaDiscuss FATAL: $msg ]</span>";
		} else {
			echo "<br />[KunenaDiscuss debug: $msg ]";
		}
	}

	/******************************************************************************
	 * Permission checks
	 *****************************************************************************/

	function getForumCategory($catid) {
		// Default Kunena category to put new topics into
		$default = intval ( $this->params->get ( 'default_category', 0 ) );
		// Category pairs will be always allowed
		$categoryPairs = explode ( ';', $this->params->get ( 'category_mapping', '' ) );
		$categoryMap = array ();
		foreach ( $categoryPairs as $pair ) {
			$pair = explode ( ',', $pair );
			$key = isset ( $pair [0] ) ? intval ( $pair [0] ) : 0;
			$value = isset ( $pair [1] ) ? intval ( $pair [1] ) : 0;
			if ($key > 0 && $value > 0)
				$categoryMap [$key] = $value;
		}
		// Limit bot to the following content catgeories
		$allowCategories = explode ( ',', $this->params->get ( 'allow_categories', '' ) );
		// Exclude the bot from the following categories
		$denyCategories = explode ( ',', $this->params->get ( 'deny_categories', '' ) );

		if (! is_numeric ( $catid ) || intval ( $catid ) == 0) {
			$this->debug ( "onPrepareContent.Deny: Category {$catid} is not valid" );
			return false;
		}

		if (count ( $categoryMap ) > 0 && isset ( $categoryMap [$catid] )) {
			$this->debug ( "onPrepareContent.Allow: Category {$catid} is in the category map using Kunena category {$categoryMap[$catid]}" );
			return $categoryMap [$catid];
		}
		if ($allowCategories && (in_array ( $catid, $allowCategories ) || in_array ( 0, $allowCategories ))) {
			$this->debug ( "onPrepareContent.Allow: Category {$catid} is in the include list using Kunena category {$default}" );
			return $default;
		}
		if ($denyCategories && (in_array ( $catid, $denyCategories ) || in_array ( 0, $denyCategories ))) {
			$this->debug ( "onPrepareContent.Deny: Category {$catid} is in the exclude list" );
			return false;
		}
		$this->debug ( "onPrepareContent.Allow: Category {$catid} using Kunena category {$default}" );
		return $default;
	}

	function canPost($thread) {
		require_once (KPATH_SITE . '/lib/kunena.posting.class.php');
		$message = new CKunenaPosting ( );
		return $message->reply ( $thread );
	}
}
