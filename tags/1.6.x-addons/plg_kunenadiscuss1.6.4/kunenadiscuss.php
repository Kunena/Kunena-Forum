<?php
/**
 * @version $Id$
 * Kunena Discuss Plugin
 * @package Kunena Discuss
 *
 * @Copyright (C) 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ( '' );

jimport ( 'joomla.plugin.plugin' );
jimport ( 'joomla.version');

class plgContentKunenaDiscuss extends JPlugin {
	// Associative array to hold results of the plugin
	static $plgDisplay = array ();
	static $includedCss = false;
	protected $basepath = null;

	// *** initialization ***
	function plgContentKunenaDiscuss(&$subject, $params) {

		$this->_app = JFactory::getApplication ( 'site' );

		// If plugin is not enabled in current scope, do not register it
		if (! $this->enabled ())
			return null;

		$jversion = new JVersion();
		if ($jversion->RELEASE != '1.5') {
			$this->basepath = 'plugins/content/kunenadiscuss';
		} else {
			$this->basepath = 'plugins/content';
		}

		// Load language files
		$this->loadLanguage ( 'plg_content_kunenadiscuss', JPATH_ADMINISTRATOR );

		// Kunena detection and version check
		$minKunenaVersion = '1.6.3';
		if (!class_exists('Kunena') || Kunena::versionBuild() < 4344) {
			$this->_app->enqueueMessage( JText::sprintf ( 'PLG_KUNENADISCUSS_DEPENDENCY_FAIL', $minKunenaVersion ) );
			return null;
		}
		// Kunena online check
		if (!Kunena::enabled()) {
			return null;
		}
		// Initialize session
		$session = KunenaFactory::getSession ();
		$session->updateAllowedForums();

		// Initialize plugin
		parent::__construct ( $subject, $params );

		// Initialize variables
		$this->_db = JFactory::getDbo ();
		$this->_my = JFactory::getUser ();

		require_once (KUNENA_PATH . DS . 'class.kunena.php');

		$this->config = KunenaFactory::getConfig ();

		// load Kunena main language file so we can leverage language strings from it
		KunenaFactory::loadLanguage();

		// Create plugin table if doesn't exist
		$query = "SHOW TABLES LIKE '#__kunenadiscuss'";
		$this->_db->setQuery ( $query );
		if (!$this->_db->loadResult ()) {
			CKunenaTools::checkDatabaseError ();
			$query = "CREATE TABLE IF NOT EXISTS `#__kunenadiscuss`
					(`content_id` int(11) NOT NULL default '0',
					 `thread_id` int(11) NOT NULL default '0',
					 PRIMARY KEY  (`content_id`)
					 )";
			$this->_db->setQuery ( $query );
			$this->_db->query ();
			CKunenaTools::checkDatabaseError ();
			$this->debug ( "Created #__kunenadiscuss cross reference table." );

			// Migrate data from old FireBoard discussbot if it exists
			$query = "SHOW TABLES LIKE '#__fb_discussbot'";
			$this->_db->setQuery ( $query );
			if ($this->_db->loadResult ()) {
				$query = "REPLACE INTO `#__kunenadiscuss`
					SELECT `content_id` , `thread_id`
					FROM `#__fb_discussbot`";
				$this->_db->setQuery ( $query );
				$this->_db->query ();
				CKunenaTools::checkDatabaseError ();
				$this->debug ( "Migrated old data." );
			}
		}

		$this->debug ( "Constructor called in " . $this->_app->scope );
	}

	// Joomla 1.5 support
	public function onPrepareContent(&$article, &$params, $limitstart=0) {
		$context = 'com_content.article';
		return $this->prepare($context, $article, $params);
	}
	function onAfterDisplayContent(&$article, &$params, $limitstart=0) {
		$context = 'com_content.article';
		return $this->display($context, $article, $params);
	}

	// Joomla 1.6 support
	public function onContentBeforeDisplay($context, &$article, &$params, $limitstart=0) {
		return $this->prepare($context, $article, $params);
	}
	public function onContentAfterDisplay($context, &$article, &$params, $limitstart=0) {
		return $this->display($context, $article, $params);
	}

	function enabled() {
		if ($this->_app->scope == 'com_content')
			return true;
		if ($this->_app->scope == 'com_kunena')
			return true;
		return false;
	}

	// *** Prepare content ***
	protected function prepare($context, &$article, &$params) {
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

	// *** display content ***
	protected function display($context, &$article, &$params) {
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
	protected function showPlugin($catid, $thread, &$row, $linkOnly) {
		// Show a simple form to allow posting to forum from the plugin
		$plgShowForm = $this->params->get ( 'form', 1 );
		// Default is to put QuickPost at the very bottom.
		$formLocation = $this->params->get ( 'form_location', 0 );

		// Don't repeat the CSS for each instance of this plugin in a page!
		if (! self::$includedCss) {
			$doc = JFactory::getDocument ();
			$doc->addStyleSheet ( KUNENA_JLIVEURL . $this->basepath .'/kunenadiscuss/discuss.css' );
			self::$includedCss = true;
		}

		$subject = $row->title;
		$published = JFactory::getDate(isset($row->publish_up) ? $row->publish_up : 'now')->toUnix();
		$now = JFactory::getDate()->toUnix();

		// Find cross reference and the real topic
		$query = "SELECT d.content_id, d.thread_id, m.id AS mesid, t.thread, t.time
			FROM #__kunenadiscuss AS d
			LEFT JOIN #__kunena_messages AS m ON d.thread_id = m.id
			LEFT JOIN #__kunena_messages AS t ON t.id = m.thread
			WHERE d.content_id = {$this->_db->quote($row->id)}";
		$this->_db->setQuery ( $query );
		$result = $this->_db->loadObject ();
		CKunenaTools::checkDatabaseError ();

		if ( is_object($result) ) {
			if ($thread && $thread != $result->mesid) {
				// Custom Topic is not the same as cross reference, additional check needed
				$query = "SELECT t.thread
					FROM #__kunena_messages AS m
					LEFT JOIN #__kunena_messages AS t ON t.id = m.thread
					WHERE m.id = {$this->_db->quote($thread)}";
				$this->_db->setQuery ( $query );
				$result->thread = $this->_db->loadResult ();
				CKunenaTools::checkDatabaseError ();
				if (!$result->thread) {
					$this->debug ( "showPlugin: Custom Topic does not exist, aborting" );
					return '';
				}
				$this->debug ( "showPlugin: Custom Topic points to new location {$result->thread}" );
			}
			if ($result->thread != $result->thread_id) {
				// Topic has been moved, has been deleted or tag inside message has been changed
				$this->debug ( "showPlugin: Removing cross reference record pointing to topic {$result->thread_id}" );
				$query = "DELETE FROM #__kunenadiscuss WHERE content_id={$this->_db->quote($result->content_id)}";
				$this->_db->setQuery ( $query );
				$this->_db->query ();
				CKunenaTools::checkDatabaseError ();
				// We may need to add new cross reference or create new topic
				$thread = $result->thread;
				$result = null;
			}
		}
		if ( !is_object($result) && $thread) {
			// Find the real topic
			$query = "SELECT {$this->_db->quote($row->id)} AS content_id, t.id AS thread_id, m.id AS mesid, t.thread, t.time
				FROM #__kunena_messages AS m
				LEFT JOIN #__kunena_messages AS t ON t.id = m.thread
				WHERE m.id = {$this->_db->quote($thread)}";
			$this->_db->setQuery ( $query );
			$result = $this->_db->loadObject ();
			CKunenaTools::checkDatabaseError ();

			if ( !is_object($result) ) {
				$this->createReference ( $row, $thread );
				$this->debug ( "showPlugin: First hit to Custom Topic, created cross reference to topic {$thread}" );
			} else {
				$this->debug ( "showPlugin: First hit to Custom Topic, cross reference not created to topic {$thread} because it exist already" );
			}
		} else if (! is_object($result) ) {
			$thread = 0;
			$create = $this->params->get ( 'create', 0 );
			$createTime = $this->params->get ( 'create_time', 0 )*604800; // Weeks in seconds
			if ($createTime && $published+$createTime < $now) {
				$this->debug ( "showPlugin: Topic creation time expired, cannot start new discussion anymore" );
				return '';
			}
			if ($create) {
				$thread = $this->createTopic ( $row, $catid, $subject );
				$this->debug ( "showPlugin: First hit, created new topic {$thread} into forum" );
			}
		} else {
			$thread = $result->thread_id;
			$this->debug ( "showPlugin: Topic {$thread} exists in the forum" );
		}

		// Do we allow answers into the topic?
		$closeTime = $this->params->get ( 'close_time', 0 ) * 604800; // Weeks in seconds or 0
		if ($closeTime) {
			$this->debug ( "showPlugin: Check if topic is closed" );
			if ($result) {
				$closeReason = $this->params->get ( 'close_reason', 0 );
				if ($closeReason) {
					// Close topic by last post time
					$query = "SELECT MAX(time)
						FROM #__kunena_messages
						WHERE thread = {$this->_db->quote($result->thread)}";
					$this->_db->setQuery ( $query );
					$closeTime = $this->_db->loadResult () + $closeTime;
					CKunenaTools::checkDatabaseError ();
					$this->debug ( "showPlugin: Close time by last post" );
				} else {
					// Close topic by cration time
					$closeTime = $result->time + $closeTime;
					$this->debug ( "showPlugin: Close time by topic creation" );
				}
			} else {
				$closeTime = $now;
			}
		}

		$link_topic = '';
		if ($thread && $linkOnly) {
			$this->debug ( "showPlugin: Displaying only link to the topic" );

			$sql = "SELECT count(*) FROM #__kunena_messages WHERE hold=0 AND parent!=0 AND thread={$this->_db->quote($thread)}";
			$this->_db->setQuery ( $sql );
			$postCount = $this->_db->loadResult ();
			CKunenaTools::checkDatabaseError ();
			$linktitle = JText::sprintf ( 'PLG_KUNENADISCUSS_DISCUSS_ON_FORUMS', $postCount );
			require_once (KPATH_SITE . '/lib/kunena.link.class.php');
			$content = CKunenaLink::GetThreadLink ( 'view', $catid, $thread, $linktitle, $linktitle );
			return $content;
		} elseif ( $thread && !$plgShowForm ) {
			 $this->debug ( "showPlugin: Displaying link to the topic because the form is disabled" );

			$sql = "SELECT count(*) FROM #__kunena_messages WHERE hold=0 AND parent!=0 AND thread={$this->_db->quote($thread)}";
			$this->_db->setQuery ( $sql );
			$postCount = $this->_db->loadResult ();
			CKunenaTools::checkDatabaseError ();
			$linktitle = JText::sprintf ( 'PLG_KUNENADISCUSS_DISCUSS_ON_FORUMS', $postCount );
			require_once (KPATH_SITE . '/lib/kunena.link.class.php');
			$link_topic = CKunenaLink::GetThreadLink ( 'view', $catid, $thread, $linktitle, $linktitle );
		} elseif ( !$thread && !$plgShowForm ) {
			$link_topic = JText::_('PLG_KUNENADISCUSS_NEW_TOPIC_NOT_CREATED');
		}

		// ************************************************************************
		// Process the QuickPost form

		$quickPost = '';
		if ($plgShowForm && (!$closeTime || $closeTime >= $now)) {
			$canPost = $this->canPost ( $catid, $thread );
			if ($canPost && JRequest::getInt ( 'kdiscussContentId', 0, 'POST' ) == $row->id) {
				$this->debug ( "showPlugin: Reply topic!" );
				$quickPost .= $this->replyTopic ( $row, $catid, $thread, $subject );
			} else {
				$quickPost .= $this->showForm ( $row, $catid, $thread, $subject );
			}
		}

		// This will be used all the way through to tell users how many posts are in the forum.
		$this->debug ( "showPlugin: Rendering discussion" );
		if ($link_topic) {
			$content = $link_topic;
			$content .= $this->showTopic ( $catid, $thread, $link_topic );
		} else {
			$content = $this->showTopic ( $catid, $thread, $link_topic );
		}

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

	protected function showTopic($catid, $thread) {
		if (!$thread) return;

		// Limits the number of posts
		$limit = $this->params->get ( 'limit', 25 );
		// Show the first X posts, versus the last X posts
		$ordering = $this->params->get ( 'ordering', 1 ); // 0=ASC, 1=DESC
		$first = (int)!$ordering;

		require_once (KPATH_SITE . '/funcs/view.php');
		$thread = new CKunenaView ( 'view', $catid, $thread, $first, $limit+$first );
		$thread->setTemplate ( "/{$this->basepath}/kunenadiscuss" );
		$thread->ordering = $ordering ? 'DESC' : 'ASC';
		$thread->hold = 0;

		ob_start ();
		$thread->display ();

		$str = ob_get_contents ();
		ob_end_clean ();
		return $str;
	}

	protected function showForm($row, $catid, $thread, $subject ) {
		$canPost = $this->canPost ( $catid, $thread );
		if (! $canPost) {
			if (! $this->_my->id) {
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
		$myprofile = KunenaFactory::getUser();
		$this->open = $this->params->get ( 'quickpost_open', false );
		$this->name = $myprofile->getName();
		ob_start ();
		$this->debug ( "showForm: Rendering form" );
		include (JPATH_ROOT . "/{$this->basepath}/kunenadiscuss/form.php");
		$str = ob_get_contents ();
		ob_end_clean ();
		return $str;
	}

	/******************************************************************************
	 * Create and reply to topic
	 *****************************************************************************/

	protected function createReference($row, $thread) {
		$query = "INSERT INTO #__kunenadiscuss (content_id, thread_id) VALUES(
			{$this->_db->quote($row->id)},
			{$this->_db->quote($thread)})";
		$this->_db->setQuery ( $query );
		$this->_db->query ();
		CKunenaTools::checkDatabaseError ();
	}

	protected function createTopic($row, $catid, $subject) {
		require_once (KPATH_SITE . '/lib/kunena.posting.class.php');
		$message = new CKunenaPosting ( $this->params->get ( 'topic_owner', $row->created_by ) );

		$options = array();
		$fields ['subject'] = $subject;
		switch ($this->params->get('bbcode')) {
			case 'full':
				$fields ['message'] = "[article=full]{$row->id}[/article]";
				break;
			case 'intro':
				$fields ['message'] = "[article=intro]{$row->id}[/article]";
				break;
			case 'link':
				$fields ['message'] = "[article=link]{$row->id}[/article]";
				break;
			default:
				$fields ['message'] = "[article]{$row->id}[/article]";
		}
		$fields ['time'] = JFactory::getDate(isset($row->publish_up) ? $row->publish_up : 'now')->toUnix();
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

		// We'll need to know about the new Thread id later...
		$this->debug ( __FUNCTION__ . "() end" );
		return $newMessageId;
	}

	protected function replyTopic($row, $catid, $thread, $subject) {
		if (JRequest::checkToken () == false) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return false;
		}
		$this->isBanned();
		$this->verifyCaptcha();
		$this->checkFlood();

		require_once (KPATH_SITE . '/lib/kunena.posting.class.php');

		if (intval ( $thread ) == 0) {
			$thread = $this->createTopic ( $row, $catid, $subject );
		}
		$message = new CKunenaPosting ( );
		$myprofile = KunenaFactory::getUser();
		$fields ['name'] = JRequest::getString ( 'name', $myprofile->getName (), 'POST' );
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

		$config = KunenaFactory::getConfig();
		$holdPost = $message->get ( 'hold' );
		require_once (KPATH_SITE . '/lib/kunena.link.class.php');
		$message->emailToSubscribers(false, $config->allowsubscriptions && ! $holdPost, $config->mailmod || $holdPost, $config->mailadmin || $holdPost);

		if ($holdPost) {
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

	protected function debug($msg, $fatal = 0) {
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

	protected function getForumCategory($catid) {
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

	protected function canPost($catid, $thread) {
		require_once (KPATH_SITE . '/lib/kunena.posting.class.php');
		$message = new CKunenaPosting ( );
		if ($thread) {
			return $message->reply ( $thread );
		} else {
			return $message->post ( $catid );
		}
	}

	protected function isBanned() {
		require_once(KPATH_SITE . '/funcs/post.php');
		$post = new CKunenaPost();
		return $post->isUserBanned();
	}

	protected function checkFlood() {
		require_once(KPATH_SITE . '/funcs/post.php');
		$post = new CKunenaPost();
		return $post->floodProtection();
	}

	protected function displayCaptcha() {
		require_once(KPATH_SITE . '/funcs/post.php');
		$post = new CKunenaPost();
		$post->displayCaptcha();
	}

	protected function verifyCaptcha() {
		require_once(KPATH_SITE . '/funcs/post.php');
		$post = new CKunenaPost();
		return $post->verifyCaptcha();
	}
}
