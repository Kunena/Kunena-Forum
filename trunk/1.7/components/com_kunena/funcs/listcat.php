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

class CKunenaListcat {
	public $allow = 0;
	public $new = array();
	public $categories = array();

	private $_loaded = false;

	function __construct($catid) {
		kimport('kunena.forum.category.helper');
		kimport('kunena.html.parser');

		$this->catid = intval($catid);
		$this->me = KunenaFactory::getUser ();
		$this->config = KunenaFactory::getConfig ();

		if ($catid) {
			$this->categories[0] = KunenaForumCategoryHelper::getCategories($catid);
			if (empty($this->categories[0]))
				return;
		} else {
			$this->categories[0] = KunenaForumCategoryHelper::getChildren();
		}

		$this->allow = 1;

		$template = KunenaFactory::getTemplate();
		$this->params = $template->params;

		//meta description and keywords
		$app = JFactory::getApplication ();
		$metaDesc = (JText::_('COM_KUNENA_CATEGORIES') . ' - ' . $this->config->board_title );
		$metaKeys = (JText::_('COM_KUNENA_CATEGORIES') . ', ' . $this->config->board_title . ', ' . $app->getCfg ( 'sitename' ));

		$document = JFactory::getDocument ();
		$cur = $document->get ( 'description' );
		$metaDesc = $cur . '. ' . $metaDesc;
		$document = JFactory::getDocument ();
		$document->setMetadata ( 'keywords', $metaKeys );
		$document->setDescription ( $metaDesc );
	}

	function loadCategories() {
		if ($this->_loaded) return;
		$this->_loaded = true;
		$allsubcats = KunenaForumCategoryHelper::getChildren(array_keys($this->categories [0]), 1);
		if (empty ( $allsubcats ))
			return;

		if ($this->config->shownew && $this->me->userid) {
			$this->new = KunenaForumCategoryHelper::getNewTopics(array_keys($allsubcats));
		}

		$modcats = array ();
		$topiclist = array ();
		$lastpostlist = array ();
		$userlist = array();

		foreach ( $allsubcats as $subcat ) {
			if (isset ( $this->categories [0] [$subcat->parent_id] )) {

				$last = $subcat->getLastPosted ();
				if ($last->last_topic_id) {
					// collect user ids for avatar prefetch when integrated
					$userlist [(int)$last->last_post_userid] = (int)$last->last_post_userid;
					$topiclist [(int)$last->last_topic_id] = $last->last_topic_subject;
					$lastpostlist [(int)$subcat->id] = (int)$last->last_post_id;
					$last->_last_post_location = $last->last_topic_posts;
				}

				if ($this->config->listcat_show_moderators) {
					$subcat->moderators = $subcat->getModerators ( false );
					$userlist += $subcat->moderators;
				}

				if ($this->me->isModerator ( $subcat->id ))
					$modcats [] = $subcat->id;
			}
			$this->categories [$subcat->parent_id] [] = $subcat;
		}

		if ($this->me->ordering != '0') {
			$topic_ordering = $this->me->ordering == '1' ? true : false;
		} else {
			$topic_ordering = $this->config->default_sort == 'asc' ? false : true;
		}

		$this->pending = array ();
		if (count ( $modcats )) {
			$catlist = implode ( ',', $modcats );
			$db = JFactory::getDBO ();
			$db->setQuery ( "SELECT catid, COUNT(*) AS count
				FROM #__kunena_messages
				WHERE catid IN ({$catlist}) AND hold='1'
				GROUP BY catid" );
			$pending = $db->loadAssocList ();
			KunenaError::checkDatabaseError();
			foreach ( $pending as $item ) {
				if ($item ['count'])
					$this->pending [$item ['catid']] = $item ['count'];
			}
		}
		// Fix last post position when user can see unapproved or deleted posts
		if (!$topic_ordering && $this->me->isModerator()) {
			$access = KunenaFactory::getAccessControl();
			$list = implode ( ',', $lastpostlist );
			$db = JFactory::getDBO ();
			$db->setQuery ( "SELECT mm.catid, mm.thread, SUM(mm.hold=1) AS unapproved, SUM(mm.hold IN (2,3)) AS deleted
				FROM #__kunena_messages AS m
				INNER JOIN #__kunena_messages AS mm ON m.thread=mm.thread
				WHERE m.id IN ({$list}) AND mm.hold>0 AND mm.id<m.id
				GROUP BY m.thread" );
			$holdtopics = $db->loadObjectList ();
			KunenaError::checkDatabaseError();
			foreach ( $holdtopics as $topic ) {
				$hold = $access->getAllowedHold($this->me->userid, $topic->catid, false);
				$category = KunenaForumCategoryHelper::get($topic->catid);
				if (isset($hold[1]))
					$category->_last_post_location += $topic->unapproved;
				if (isset($hold[2]) || isset($hold[3]))
					$category->_last_post_location += $topic->deleted;
			}
		}

		require_once (KUNENA_PATH . DS . 'router.php');
		KunenaRouter::loadMessages ( $topiclist );

		// Prefetch all users/avatars to avoid user by user queries during template iterations
		kimport('kunena.user');
		KunenaUser::loadUsers($userlist);
	}

	/**
	* Escapes a value for output in a view script.
	*
	* If escaping mechanism is one of htmlspecialchars or htmlentities, uses
	* {@link $_encoding} setting.
	*
	* @param  mixed $var The output to escape.
	* @return mixed The escaped value.
	*/
	function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	public function getCategoryIcon($catid, $thumb = false) {
		if (! $thumb) {
			if ($this->config->shownew && $this->me->userid != 0) {
				if (! empty ( $this->new [$catid] )) {
					// Check Unread    Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $catid . "_on.gif" )) {
						return "<img src=\"" . KUNENA_URLCATIMAGES . $catid . "_on.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
					} else {
						return CKunenaTools::showIcon ( 'kunreadforum', JText::_ ( 'COM_KUNENA_GEN_FORUM_NEWPOST' ) );
					}
				} else {
					// Check Read Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $catid . "_off.gif" )) {
						return "<img src=\"" . KUNENA_URLCATIMAGES . $catid . "_off.gif\" border=\"0\" class='kforum-cat-image' alt=\" \"  />";
					} else {
						return CKunenaTools::showIcon ( 'kreadforum', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
					}
				}
			} else {
				if (is_file ( KUNENA_ABSCATIMAGESPATH . $catid . "_notlogin.gif" )) {
					return "<img src=\"" . KUNENA_URLCATIMAGES . $catid . "_notlogin.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
				} else {
					return CKunenaTools::showIcon ( 'knotloginforum', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
				}
			}
		} elseif ($this->config->showchildcaticon) {
			if ($this->config->shownew && $this->me->userid != 0) {
				if (! empty ( $this->new [$catid] )) {
					// Check Unread    Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $catid . "_on_childsmall.gif" )) {
						return "<img src=\"" . KUNENA_URLCATIMAGES . $catid . "_on_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
					} else {
						return CKunenaTools::showIcon ( 'kunreadforum-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NEWPOST' ) );
					}
				} else {
					// Check Read Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $catid . "_off_childsmall.gif" )) {
						return "<img src=\"" . KUNENA_URLCATIMAGES . $catid . "_off_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
					} else {
						return CKunenaTools::showIcon ( 'kreadforum-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
					}
				}
			} else {
				// Not Login Cat Images
				if (is_file ( KUNENA_ABSCATIMAGESPATH . $catid . "_notlogin_childsmall.gif" )) {
					return "<img src=\"" . KUNENA_URLCATIMAGES . $catid . "_notlogin_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
				} else {
					return CKunenaTools::showIcon ( 'knotloginforum-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
				}
			}
		}
		return '';
	}

	function displayPathway() {
		CKunenaTools::loadTemplate('/pathway.php');
	}

	function displayAnnouncement() {
		if ($this->config->showannouncement > 0) {
			require_once(KUNENA_PATH_LIB .DS. 'kunena.announcement.class.php');
			$ann = new CKunenaAnnouncement();
			$ann->getAnnouncement();
			$ann->displayBox();
		}
	}

	function displayForumJump() {
		if ($this->config->enableforumjump) {
			CKunenaTools::loadTemplate('/forumjump.php');
		}
	}

	function displayCategories() {
		$this->loadCategories();
		CKunenaTools::loadTemplate('/categories/categories.php');
	}

	function displayStats() {
		if ($this->config->showstats > 0) {
			require_once(KUNENA_PATH_LIB .DS. 'kunena.stats.class.php');
			$kunena_stats = CKunenaStats::getInstance ( );
			$kunena_stats->showFrontStats ();
		}
	}

	function displayWhoIsOnline() {
		if ($this->config->showwhoisonline > 0) {
			require_once (KUNENA_PATH_LIB .DS. 'kunena.who.class.php');
			$online = CKunenaWhoIsOnline::getInstance();
			$online->displayWhoIsOnline();
		}
	}

	function getPagination($catid, $page, $totalpages, $maxpages) {
		$startpage = ($page - floor ( $maxpages / 2 ) < 1) ? 1 : $page - floor ( $maxpages / 2 );
		$endpage = $startpage + $maxpages;
		if ($endpage > $totalpages) {
			$startpage = ($totalpages - $maxpages) < 1 ? 1 : $totalpages - $maxpages;
			$endpage = $totalpages;
		}

		$output = '<ul class="kpagination">';
		$output .= '<li class="page">' . JText::_('COM_KUNENA_PAGE') . '</li>';

		if (($startpage) > 1) {
			if ($endpage < $totalpages)
				$endpage --;
			$output .= '<li>' . CKunenaLink::GetCategoryPageLink ( 'showcat', $catid, 1, 1, $rel = 'follow' ) . '</li>';
			if (($startpage) > 2) {
				$output .= '<li class="more">...</li>';
			}
		}

		for($i = $startpage; $i <= $endpage && $i <= $totalpages; $i ++) {
			if ($page == $i) {
				$output .= '<li class="active">' . $i . '</li>';
			} else {
				$output .= '<li>' . CKunenaLink::GetCategoryPageLink ( 'showcat', $catid, $i, $i, $rel = 'follow' ) . '</li>';
			}
		}

		if ($endpage < $totalpages) {
			if ($endpage < $totalpages - 1) {
				$output .= '<li class="more">...</li>';
			}

			$output .= '<li>' . CKunenaLink::GetCategoryPageLink ( 'showcat', $catid, $totalpages, $totalpages, $rel = 'follow' ) . '</li>';
		}

		$output .= '</ul>';
		return $output;
	}

	function displayInfoMessage($header, $contents) {
		$header = JText::_('COM_KUNENA_FORUM_INFORMATION');
		$contents = JText::_('COM_KUNENA_LISTCAT_NO_CATS');
		CKunenaTools::loadTemplate('/categories/infomessage.php');
	}

	function display() {
		if (! $this->allow) {
			echo JText::_('COM_KUNENA_NO_ACCESS');
			return;
		}
		if (empty ( $this->categories [0] )) {
			echo '' . JText::_('COM_KUNENA_GEN_NOFORUMS') . '';
			return;
		}
		CKunenaTools::loadTemplate('/categories/listcat.php');
	}
}
