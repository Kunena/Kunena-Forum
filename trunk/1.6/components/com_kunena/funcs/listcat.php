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

class CKunenaListcat {
	public $allow = 0;

	private $_loaded = false;

	function __construct($catid) {
		kimport('html.parser');
		$this->catid = $catid;

		$this->db = JFactory::getDBO ();
		$this->my = JFactory::getUser ();
		$this->session = KunenaFactory::getSession ();
		$this->config = KunenaFactory::getConfig ();

		if ($this->catid && ! $this->session->canRead ( $this->catid ))
			return;

		$this->allow = 1;

		$this->prevCheck = $this->session->lasttime;

		$kunena_app = JFactory::getApplication ();

		// Start getting the categories
		if ($this->catid) {
			$catlist = $this->catid;
			$where = "";
		} else {
			$catlist = $this->session->allowed;
			$where = "parent='0' AND";
		}
		$this->categories = array ();
		$this->db->setQuery ( "SELECT * FROM #__kunena_categories WHERE {$where} published='1' AND id IN ({$catlist}) ORDER BY ordering, name" );
		$this->categories [0] = $this->db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;

		//meta description and keywords
		$metaDesc = (JText::_('COM_KUNENA_CATEGORIES') . ' - ' . $this->config->board_title );
		$metaKeys = (JText::_('COM_KUNENA_CATEGORIES') . ', ' . $this->config->board_title . ', ' . $kunena_app->getCfg ( 'sitename' ));

		$document = JFactory::getDocument ();
		$cur = $document->get ( 'description' );
		$metaDesc = $cur . '. ' . $metaDesc;
		$document = JFactory::getDocument ();
		$document->setMetadata ( 'keywords', $metaKeys );
		$document->setDescription ( $metaDesc );

		$template = KunenaFactory::getTemplate();
		$this->params = $template->params;
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

	function loadCategories() {
		if ($this->_loaded) return;
		$this->_loaded = true;
		$catids = array ();
		foreach ( $this->categories [0] as $cat )
			$catids [] = $cat->id;
		if (empty ( $catids ))
			return;
		$catlist = implode ( ',', $catids );
		$readlist = $this->session->readtopics;

		if ($this->config->shownew && $this->my->id) $subquery = " (SELECT COUNT(DISTINCT thread) FROM #__kunena_messages AS mmm WHERE c.id=mmm.catid AND mmm.hold='0' AND mmm.time>{$this->db->Quote($this->prevCheck)} AND mmm.thread NOT IN ({$readlist})) AS new";
		else $subquery = " 0 AS new";

		// TODO: optimize this query (just combined many queries into one)
		$query = "SELECT c.*, m.id AS mesid, m.thread, m.catid, t.subject AS topicsubject, m.subject, m.name AS mname, u.id AS userid, u.username, u.name AS uname,
			(SELECT COUNT(*) FROM #__kunena_messages AS mm WHERE m.thread=mm.thread) AS msgcount, {$subquery}
			FROM #__kunena_categories AS c
			LEFT JOIN #__kunena_messages AS m ON c.id_last_msg=m.id
			LEFT JOIN #__kunena_messages AS t ON m.thread=t.id
			LEFT JOIN #__users AS u ON u.id=m.userid
			WHERE c.parent IN ({$catlist}) AND c.published='1' AND c.id IN({$this->session->allowed}) ORDER BY ordering, name";
		$this->db->setQuery ( $query );
		$allsubcats = $this->db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;

		$this->tabclass = array ("row1", "row2" );

		$subcats = array ();
		$routerlist = array ();
		$userlist = array();

		$myprofile = KunenaFactory::getUser ();
		if ($myprofile->ordering != '0') {
			$topic_ordering = $myprofile->ordering == '1' ? true : false;
		} else {
			$topic_ordering = $this->config->default_sort == 'asc' ? false : true;
		}

		foreach ( $allsubcats as $i => $subcat ) {
			if ($subcat->mesid)
				$routerlist [$subcat->thread] = $subcat->subject;

			if($topic_ordering) $subcat->page = 1;
			else $subcat->page = ceil ( $subcat->msgcount / $this->config->messages_per_page );

			if ($this->config->shownew && $this->my->id != 0) {
				if ($subcat->new) {
					// Check Unread    Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $subcat->id . "_on.gif" )) {
						$allsubcats [$i]->htmlCategoryIcon = "<img src=\"" . KUNENA_URLCATIMAGES . $subcat->id . "_on.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
					} else {
						$allsubcats [$i]->htmlCategoryIcon = CKunenaTools::showIcon ( 'kunreadforum', JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') );
					}
				} else {
					// Check Read Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $subcat->id . "_off.gif" )) {
						$allsubcats [$i]->htmlCategoryIcon = "<img src=\"" . KUNENA_URLCATIMAGES . $subcat->id . "_off.gif\" border=\"0\" class='kforum-cat-image' alt=\" \"  />";
					} else {
						$allsubcats [$i]->htmlCategoryIcon = CKunenaTools::showIcon ( 'kreadforum', JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') );
					}
				}
			} else {
				if (is_file ( KUNENA_ABSCATIMAGESPATH . $subcat->id . "_notlogin.gif" )) {
					$allsubcats [$i]->htmlCategoryIcon = "<img src=\"" . KUNENA_URLCATIMAGES . $subcat->id . "_notlogin.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
				} else {
					$allsubcats [$i]->htmlCategoryIcon = CKunenaTools::showIcon ( 'knotloginforum', JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') );
				}
			}

			// collect user ids for avatar prefetch when integrated
			$userlist[intval($subcat->userid)] = intval($subcat->userid);
		}

		require_once (KUNENA_PATH . '/router.php');
		KunenaRouter::loadMessages ( $routerlist );

		$modcats = array ();
		foreach ( $allsubcats as $subcat ) {
			$this->categories [$subcat->parent] [] = $subcat;
			$subcats [] = $subcat->id;
			if ($subcat->moderated)
				$modcats [] = $subcat->id;
		}

		// Get the childforums
		$this->childforums = array ();
		if (count ( $subcats )) {
			$subcatlist = implode ( ',', $subcats );
			if ($this->config->shownew && $this->my->id) $subquery = " (SELECT COUNT(DISTINCT thread) FROM #__kunena_messages AS m WHERE c.id=m.catid AND m.hold='0' AND m.time>{$this->db->Quote($this->prevCheck)} AND m.thread NOT IN ({$readlist})) AS new";
			else $subquery = "0 AS new";

			$query = "SELECT c.id, c.name, c.description, c.parent, c.numTopics, c.numPosts, {$subquery}
			FROM #__kunena_categories AS c
			WHERE c.parent IN ({$subcatlist}) AND c.published='1' AND c.id IN({$this->session->allowed}) ORDER BY c.ordering, c.name";
			$this->db->setQuery ($query);
			$childforums = $this->db->loadObjectList ();
			KunenaError::checkDatabaseError();
			foreach ( $childforums as $i => $childforum ) {
				//Begin: parent read unread iconset
				if ($this->config->showchildcaticon) {
					if ($this->config->shownew && $this->my->id != 0) {
						if ($childforum->new) {
							// Check Unread    Cat Images
							if (is_file ( KUNENA_ABSCATIMAGESPATH . $childforum->id . "_on_childsmall.gif" )) {
								$childforum->htmlCategoryIcon = "<img src=\"" . KUNENA_URLCATIMAGES . $childforum->id . "_on_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
							} else {
								$childforum->htmlCategoryIcon = CKunenaTools::showIcon ( 'kunreadforum-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NEWPOST' ) );
							}
						} else {
							// Check Read Cat Images
							if (is_file ( KUNENA_ABSCATIMAGESPATH . $childforum->id . "_off_childsmall.gif" )) {
								$childforum->htmlCategoryIcon = "<img src=\"" . KUNENA_URLCATIMAGES . $childforum->id . "_off_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
							} else {
								$childforum->htmlCategoryIcon = CKunenaTools::showIcon ( 'kreadforum-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
							}
						}
					} else {
						// Not Login Cat Images
						if (is_file ( KUNENA_ABSCATIMAGESPATH . $childforum->id . "_notlogin_childsmall.gif" )) {
							$childforum->htmlCategoryIcon = "<img src=\"" . KUNENA_URLCATIMAGES . $childforum->id . "_notlogin_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
						} else {
							$childforum->htmlCategoryIcon = CKunenaTools::showIcon ( 'knotloginforum-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
						}
					}
				} else {
					$childforum->htmlCategoryIcon = '';
				}
				$this->childforums [$childforum->parent] [] = $childforum;
			}
		}

		$this->modlist = array ();
		$this->pending = array ();
		if (count ( $modcats )) {
			if ($this->config->listcat_show_moderators) {
				$modcatlist = implode ( ',', $modcats );
				$this->db->setQuery ( "SELECT * FROM #__kunena_moderation AS m
					INNER JOIN #__users AS u ON u.id=m.userid
					WHERE m.catid IN ({$modcatlist}) AND u.block=0" );
				$modlist = $this->db->loadObjectList ();
				KunenaError::checkDatabaseError();
				foreach ( $modlist as $mod ) {
					$this->modlist [$mod->catid] [] = $mod;
					$userlist[intval($mod->userid)] = intval($mod->userid);
				}
			}
			if (CKunenaTools::isModerator ( $this->my->id )) {
				foreach ( $modcats as $i => $catid ) {
					if (! CKunenaTools::isModerator ( $this->my->id, $catid ))
						unset ( $modcats [$i] );
				}
				if (count ( $modcats )) {
					$modcatlist = implode ( ',', $modcats );
					$this->db->setQuery ( "SELECT catid, COUNT(*) AS count
					FROM #__kunena_messages
					WHERE catid IN ($modcatlist) AND hold='1'
					GROUP BY catid" );
					$pending = $this->db->loadAssocList ();
					KunenaError::checkDatabaseError();
					foreach ( $pending as $i ) {
						if ($i ['count'])
							$this->pending [$i ['catid']] = $i ['count'];
					}
				}
			}
		}

		// Prefetch all users/avatars to avoid user by user queries during template iterations
		kimport('user');
		KunenaUser::loadUsers($userlist);
	}

	function displayPathway() {
		CKunenaTools::loadTemplate('/pathway.php');
	}

	function displayAnnouncement() {
		if ($this->config->showannouncement > 0) {
			require_once(KUNENA_PATH_LIB . '/kunena.announcement.class.php');
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
			require_once(KUNENA_PATH_LIB . '/kunena.stats.class.php');
			$kunena_stats = CKunenaStats::getInstance ( );
			$kunena_stats->showFrontStats ();
		}
	}

	function displayWhoIsOnline() {
		if ($this->config->showwhoisonline > 0) {
			require_once (KUNENA_PATH_LIB . '/kunena.who.class.php');
			$online =& CKunenaWhoIsOnline::getInstance();
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
