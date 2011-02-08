<?php
/**
 * @version $Id: kunenadiscuss.php 4094 2010-12-25 08:21:38Z fxstein $
 * Kunena Discuss Plugin
 * @package Kunena Discuss
 *
 * @Copyright (C) 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ( '' );

jimport ( 'joomla.plugin.plugin' );

class plgContentKunenaDiscuss extends JPlugin {
	// Associative array to hold results of the plugin
	static $plgDisplay = array ();
	static $includedCss = false;

	// *** initialization ***
	function plgContentKunenaDiscuss(&$subject, $params) {

		$this->_app = JFactory::getApplication ( 'site' );

		// If plugin is not enabled in current scope, do not register it
		if (! $this->enabled ())
			return null;

		// Load language files
		$this->loadLanguage ( 'plg_content_kunenadiscuss', JPATH_ADMINISTRATOR );

		// Kunena detection and version check
		$minKunenaVersion = '2.0.0-DEV';
		$minKunenaBuild = 2385;
		if (!class_exists('KunenaForum') && !KunenaForum::isCompatible($minKunenaVersion, $minKunenaBuild)) {
			echo JText::sprintf ( 'PLG_KUNENADISCUSS_DEPENDENCY_FAIL', $minKunenaVersion );
			return null;
		}
		// Kunena online check
		if (!KunenaForum::enabled()) {
			return null;
		}

		// Initialize plugin
		parent::__construct ( $subject, $params );

		kimport ('kunena.error');
		kimport ('kunena.captcha');
		kimport ('kunena.forum.category.helper');
		kimport ('kunena.forum.topic.helper');
		require_once KPATH_SITE . '/lib/kunena.link.class.php';

		// Initialize variables
		$this->_db = JFactory::getDBO ();
		$this->user = KunenaFactory::getUser ();

		$this->config = KunenaFactory::getConfig ();

		// load Kunena main language file so we can leverage language strings from it
		KunenaFactory::loadLanguage();

		// Create plugin table if doesn't exist
		$query = "SHOW TABLES LIKE '#__kunenadiscuss'";
		$this->_db->setQuery ( $query );
		if (!$this->_db->loadResult ()) {
			KunenaError::checkDatabaseError ();
			$query = "CREATE TABLE IF NOT EXISTS `#__kunenadiscuss`
					(`content_id` int(11) NOT NULL default '0',
					 `thread_id` int(11) NOT NULL default '0',
					 PRIMARY KEY  (`content_id`)
					 )";
			$this->_db->setQuery ( $query );
			$this->_db->query ();
			KunenaError::checkDatabaseError ();
			$this->debug ( "Created #__kunenadiscuss reference table." );

			// Migrate data from old FireBoard discussbot if it exists
			$query = "SHOW TABLES LIKE '#__fb_discussbot'";
			$this->_db->setQuery ( $query );
			if ($this->_db->loadResult ()) {
				$query = "REPLACE INTO `#__kunenadiscuss`
					SELECT `content_id` , `thread_id`
					FROM `#__fb_discussbot`";
				$this->_db->setQuery ( $query );
				$this->_db->query ();
				KunenaError::checkDatabaseError ();
				$this->debug ( "Migrated old data." );
			}
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

		// Only proceed if this event is not originated by Kunena itself or we run the danger of an event recursion
		$ksource = '';
		if ($params instanceof JParameter){
			$ksource = $params->get( 'ksource', '');
		}

		if ($ksource != 'kunena' ){

			$customTopics = $this->params->get ( 'custom_topics', 1 );

			$articleCategory = (isset ( $article->catid ) ? $article->catid : 0);
			$isStaticContent = ! $articleCategory;
			if ($isStaticContent) {
				$kunenaCategory = false;
			} else {
				$kunenaCategory = $this->getForumCategory ( $articleCategory );
				if (! $kunenaCategory ) {
					if ( ! $customTopics)
						return true;
					else
						$this->debug ( "onPrepareContent: Allowing only Custom Topics" );
				}
			}
			$kunenaTopic = false;

			$regex = '/{kunena_discuss:(\d+?)}/s';

			if (JRequest::getVar ( 'tmpl', '' ) == 'component' || JRequest::getBool ( 'print' )
				|| JRequest::getVar ( 'format', 'html' ) != 'html' || (isset ( $article->state ) && ! $article->state)
				|| empty ( $article->id ) || $this->_app->scope == 'com_kunena') {
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
			if (! $show || isset ( self::$plgDisplay [$article->id] )) {
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

			if (! $customTopics) {
				$this->debug ( "onPrepareContent: Custom Topics disabled" );
			} else {
				// Get fulltext from frontpage articles (tag can be inside fulltext)
				if ($isFrontPage) {
					$query = "SELECT `fulltext` FROM #__content WHERE id ={$this->_db->quote($article->id)}";
					$this->_db->setQuery ( $query );
					$fulltext = $this->_db->loadResult ();
					KunenaError::checkDatabaseError ();
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
					if ($kunenaTopic == 0) {
						$this->debug ( "onPrepareContent: Searched for {kunena_discuss:#}: Discussion of this article has been disabled." );
						return true;
					}
				}
				$this->debug ( "onPrepareContent: Searched for {kunena_discuss:#}: Custom Topic " . ($kunenaTopic ? "{$kunenaTopic} found." : "not found.") );
			}

			if ($kunenaCategory || $kunenaTopic) {
				self::$plgDisplay [$article->id] = $this->showPlugin ( $kunenaCategory, $kunenaTopic, $article, $show == 1 );
			}
		} // end of $ksource!='kunena' check

		return true;
	}

	// *** event after display content ***
	function onAfterDisplayContent(&$article, &$params, $limitstart) {
		if (empty ( $article->id ))
			return '';

		if (isset ( self::$plgDisplay [$article->id] )) {
			$this->debug ( "onAfterDisplayContent: Returning content for article {$article->id}" );
			return self::$plgDisplay [$article->id];
		} else {
			return '';
		}
	}

	// *** internal functions follows ***
	/******************************************************************************
	 *
	 *****************************************************************************/
	function showPlugin($catid, $topic_id, &$row, $linkOnly) {
		// Show a simple form to allow posting to forum from the plugin
		$plgShowForm = $this->params->get ( 'form', 1 );
		// Default is to put QuickPost at the very bottom.
		$formLocation = $this->params->get ( 'form_location', 0 );

		// Don't repeat the CSS for each instance of this plugin in a page!
		if (! self::$includedCss) {
			$doc = JFactory::getDocument ();
			$doc->addStyleSheet ( JURI::root() . 'plugins/content/kunenadiscuss/tmpl/discuss.css' );
			self::$includedCss = true;
		}

		// Find referenced topic
		$query = "SELECT thread_id FROM #__kunenadiscuss WHERE content_id = {$this->_db->quote($row->id)}";
		$this->_db->setQuery ( $query );
		$result = $this->_db->loadResult ();
		KunenaError::checkDatabaseError ();

		if ($topic_id) {
			// Custom Topic found
			$this->debug ( "showPlugin: Loading Custom Topic {$topic_id}" );
			$id = $topic_id;
		} elseif ($result) {
			// Reference found
			$this->debug ( "showPlugin: Loading Stored Topic {$result}" );
			$id = $result;
		} else {
			$this->debug ( "showPlugin: No topic found" );
			$id = 0;
		}

		// If topic has been moved, find the real topic
		$topic = KunenaForumTopicHelper::get($id);
		while ($topic->moved_id) {
			$this->debug ( "showPlugin: Topic {$topic->id} has been moved to {$topic->moved_id}" );
			$topic = KunenaForumTopicHelper::get($topic->moved_id);
		}
		if ($result) {
			if (!$topic->exists()) {
				$this->debug ( "showPlugin: Topic does not exist, removing reference to {$result}" );
				$this->deleteReference ( $row );
			} elseif (!$topic->authorise('read')) {
				$this->debug ( "showPlugin: {$topic->getError()}" );
				return '';
			}elseif ($topic->id != $result) {
				$this->debug ( "showPlugin: Topic has been moved or changed, updating reference to {$topic->id}" );
				$this->updateReference ( $row, $topic->id );
			}
		} elseif ($topic_id) {
			if (!$topic->authorise('read')) {
				$this->debug ( "showPlugin: {$topic->getError()}" );
				return '';
			} else {
				$this->debug ( "showPlugin: First hit to Custom Topic, created reference to topic {$topic_id}" );
				$this->createReference ( $row, $topic_id );
			}
		}

		$subject = $row->title;
		$published = JFactory::getDate(isset($row->publish_up) ? $row->publish_up : 'now')->toUnix();
		$now = JFactory::getDate()->toUnix();

		if (! $topic->exists() ) {
			$category = KunenaForumCategoryHelper::get($catid);
			$create = $this->params->get ( 'create', 0 );
			$createTime = $this->params->get ( 'create_time', 0 )*604800; // Weeks in seconds
			if ($createTime && $published+$createTime < $now) {
				$this->debug ( "showPlugin: Topic creation time expired, cannot start new discussion anymore" );
				return '';
			}
			if ($create) {
				$this->debug ( "showPlugin: First hit, created new topic {$topic_id} into forum" );
				$topic = $this->createTopic ( $row, $category, $subject );
			}
		} else {
			$category = $topic->getCategory();
		}

		// Do we allow answers into the topic?
		$closeTime = $this->params->get ( 'close_time', 0 ) * 604800; // Weeks in seconds or 0 (forever)
		if ($closeTime && $topic->exists()) {
			$closeReason = $this->params->get ( 'close_reason', 0 );
			if ($closeReason) {
				$this->debug ( "showPlugin: Close time by last post" );
				$closeTime += $topic->last_post_time;
			} else {
				$this->debug ( "showPlugin: Close time by topic creation" );
				$closeTime += $topic->first_post_time;
				}
		} else {
			// Topic has not yet been created or will kept open forever
			$closeTime = $now + 1;
		}

		$linktopic = '';
		$linktitle = JText::sprintf ( 'PLG_KUNENADISCUSS_DISCUSS_ON_FORUMS', $topic->posts );
		if ($topic->exists() && $linkOnly) {
			$this->debug ( "showPlugin: Displaying only link to the topic" );
			return CKunenaLink::GetThreadLink ( 'view', $category->id, $topic->id, $linktitle, $linktitle );
		} elseif ( $topic->exists() && !$plgShowForm ) {
			$this->debug ( "showPlugin: Displaying link to the topic because the form is disabled" );
			$linktopic = CKunenaLink::GetThreadLink ( 'view', $category->id, $topic->id, $linktitle, $linktitle );
		} elseif ( !$topic->exists() && !$plgShowForm ) {
			$linktopic = JText::_('PLG_KUNENADISCUSS_NEW_TOPIC_NOT_CREATED');
		}

		// ************************************************************************
		// Process the QuickPost form

		$quickPost = '';
		$canPost = $this->canPost ( $category, $topic );
		if ($canPost && $plgShowForm && $closeTime >= $now) {
			if (JRequest::getInt ( 'kdiscussContentId', 0, 'POST' ) == $row->id) {
				$this->debug ( "showPlugin: Reply topic!" );
				$quickPost .= $this->replyTopic ( $row, $category, $topic, $subject );
			} else {
				$quickPost .= $this->showForm ( $row, $category, $topic, $subject );
			}
		}

		// This will be used all the way through to tell users how many posts are in the forum.
		$this->debug ( "showPlugin: Rendering discussion" );
		$content = $this->showTopic ( $category, $topic, $linktopic );

		if ($formLocation) {
			$content = '<div class="kunenadiscuss">' . $content . '<br />' . $quickPost . '</div>';
		} else {
			$content = '<div class="kunenadiscuss">' . $quickPost . "<br />" . $content . '</div>';
		}

		return $content;
	}

	/******************************************************************************
	 * Output
	 *****************************************************************************/

	function showTopic($category, $topic, $link_topic) {
		if (!$topic->exists()) return '';

		$ordering = $this->params->get ( 'ordering', 1 ); // 0=ASC, 1=DESC
		$params = array(
			'catid' => $category->id,
			'id' => $topic->id,
			'limitstart' => (int)!$ordering,
			'limit' => $this->params->get ( 'limit', 25 ),
			'filter_order_Dir' => $ordering ? 'desc' : 'asc',
			'templatepath' => dirname (__FILE__) . '/kunenadiscuss/tmpl'
		);
		ob_start ();
		KunenaForum::display('topic', 'default', null, $params);
		$str = ob_get_contents ();
		ob_end_clean ();
		return $link_topic . $str;
	}

	function showForm($row, $category, $topic, $subject ) {
		$canPost = $this->canPost ( $category, $topic );
		if (! $canPost) {
			if (! $this->user->exists()) {
				$this->debug ( "showForm: Public posting is not permitted, show login instead" );
				$login = KunenaFactory::getLogin ();
				$loginlink = $login->getLoginURL ();
				$registerlink = $login->getRegistrationURL ();
				$this->msg = JText::sprintf ( 'PLG_KUNENADISCUSS_LOGIN_OR_REGISTER', '"' . $loginlink . '"', '"' . $registerlink . '"' );
			} else {
				$this->debug ( "showForm: Unfortunately you cannot discuss this item" );
				$this->msg = JText::_ ( 'PLG_KUNENADISCUSS_NO_PERMISSION_TO_POST' );
			}
		}
		$this->open = $this->params->get ( 'quickpost_open', false );
		$this->name = $this->user->getName();
		ob_start ();
		$this->debug ( "showForm: Rendering form" );
		include (JPATH_ROOT . '/plugins/content/kunenadiscuss/tmpl/form.php');
		$str = ob_get_contents ();
		ob_end_clean ();
		return $str;
	}

	/******************************************************************************
	 * Create and reply to topic
	 *****************************************************************************/

	function createReference($row, $topic_id) {
		$query = "INSERT INTO #__kunenadiscuss (content_id, thread_id) VALUES(
			{$this->_db->quote($row->id)},
			{$this->_db->quote($topic_id)})";
		$this->_db->setQuery ( $query );
		$this->_db->query ();
		KunenaError::checkDatabaseError ();
	}

	function updateReference($row, $topic_id) {
		$query = "UPDATE #__kunenadiscuss SET content_id={$this->_db->quote($row->id)}, thread_id={$this->_db->quote($topic_id)})";
		$this->_db->setQuery ( $query );
		$this->_db->query ();
		KunenaError::checkDatabaseError ();
	}

	function deleteReference($row) {
		$query = "DELETE FROM #__kunenadiscuss WHERE content_id={$this->_db->quote($row->id)}";
		$this->_db->setQuery ( $query );
		$this->_db->query ();
		KunenaError::checkDatabaseError ();
	}

	function createTopic($row, $category, $subject) {
		$type = $this->params->get('bbcode');
		switch ($type) {
			case 'full':
			case 'intro':
			case 'link':
				$contents = "[article={$type}]{$row->id}[/article]";
				break;
			default:
				$contents= "[article]{$row->id}[/article]";
		}
		$params = array(
			'subject' => $subject,
			'message' => $contents,
		);
		list ($topic, $message) = $category->newTopic($params, $this->params->get ( 'topic_owner', $row->created_by ));
		$message->time = JFactory::getDate(isset($row->publish_up) ? $row->publish_up : 'now')->toUnix();

		$success = $message->save ();
		if (! $success) {
			$this->_app->enqueueMessage ( $message->getError (), 'error' );
			return false;
		}

		// Create a reference
		$this->createReference ( $row, $topic->id );
		return $topic;
	}

	function replyTopic($row, $category, $topic, $subject) {
		if (JRequest::checkToken () == false) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return false;
		}
		if (!$this->verifyCaptcha()) {
			return false;
		}
		// Create topic if it doesn't exist
		if (!$topic->exists()) {
			$topic = $this->createTopic ( $row, $category, $subject );
		}
		$params = array (
			'subject' => $subject,
			'message' => JRequest::getString ( 'message', null, 'POST' ),
		);
		$message = $topic->newReply($params);
		$success = $message->save ();
		if (! $success) {
			$this->_app->enqueueMessage ( $message->getError(), 'error' );
			return false;
		}
		$message->sendNotification();

		if ($message->hold) {
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


		if (! $debug || ($debugUsers && ! in_array ( $this->user->userid, explode ( ',', $debugUsers ) )))
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
			if ($key > 0)
				$categoryMap [$key] = $value;
		}
		// Limit plugin to the following content catgeories
		$allowCategories = explode ( ',', $this->params->get ( 'allow_categories', '' ) );
		// Exclude the plugin from the following categories
		$denyCategories = explode ( ',', $this->params->get ( 'deny_categories', '' ) );

		if (! is_numeric ( $catid ) || intval ( $catid ) == 0) {
			$this->debug ( "onPrepareContent.Deny: Category {$catid} is not valid" );
			return false;
		}

		if (!empty ( $categoryMap ) && isset ( $categoryMap [$catid] )) {
			$forumcatid = $categoryMap [$catid];
			if (!$forumcatid) {
				$this->debug ( "onPrepareContent.Deny: Category {$catid} was disabled in the category map." );
				return false;
			}
			$this->debug ( "onPrepareContent.Allow: Category {$catid} is in the category map using Kunena category {$forumcatid}" );
			return $forumcatid;
		}

		if (!$default) {
			$this->debug ( "onPrepareContent.Deny: There is no default Kunena category" );
			return false;
		}

		if (in_array('0', $allowCategories ) || in_array($catid, $allowCategories )) {
			$this->debug ( "onPrepareContent.Allow: Category {$catid} was listed in allow list and is using default Kunena category {$default}" );
			return $default;
		}
		if (in_array('0', $denyCategories ) || in_array($catid, $denyCategories )) {
			$this->debug ( "onPrepareContent.Deny: Category {$catid} was listed in deny list" );
			return false;
		}

		$this->debug ( "onPrepareContent.Allow: Category {$catid} is using default Kunena category {$default}" );
		return $default;
	}

	function canPost($category, $topic) {
		return ($topic->exists() && $topic->authorise ( 'reply' )) || (!$topic->exists() && $category->authorise ( 'topic.reply'));
	}

	public function hasCaptcha() {
		return KunenaCaptcha::enabled();
	}

	public function displayCaptcha() {
		return KunenaCaptcha::display();
	}

	public function verifyCaptcha() {
		return KunenaCaptcha::verify();
	}
}