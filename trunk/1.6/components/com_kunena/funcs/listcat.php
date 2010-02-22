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

	private $_loaded = false;

	function __construct($catid) {
		$this->catid = $catid;

		$this->db = JFactory::getDBO ();
		$this->my = JFactory::getUser ();
		$this->session = CKunenaSession::getInstance ();
		$this->config = CKunenaConfig::getInstance ();

		//get the allowed forums and turn it into an array
		$this->allow_forum = ($this->session->allowed != '') ? explode ( ',', $this->session->allowed ) : array ();

		if ($this->catid && ! in_array ( $this->catid, $this->allow_forum ))
			return;

		$this->allow = 1;

		$this->prevCheck = $this->session->lasttime;
		$this->read_topics = explode ( ',', $this->session->readtopics );

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
		$this->db->setQuery ( "SELECT * FROM #__fb_categories WHERE {$where} published='1' AND id IN ({$catlist}) ORDER BY ordering" );
		$this->categories [0] = $this->db->loadObjectList ();
		check_dberror ( "Unable to load categories." );

		//meta description and keywords
		$metaDesc = (JText::_('COM_KUNENA_CATEGORIES') . ' - ' . stripslashes ( $this->config->board_title ));
		$metaKeys = (JText::_('COM_KUNENA_CATEGORIES') . ', ' . stripslashes ( $this->config->board_title ) . ', ' . $kunena_app->getCfg ( 'sitename' ));

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
		$catids = array ();
		foreach ( $this->categories [0] as $cat )
			$catids [] = $cat->id;
		if (empty ( $catids ))
			return;
		$catlist = implode ( ',', $catids );
		$readlist = '0' . $this->session->readtopics;

		if ($this->config->shownew && $this->my->id) $subquery = " (SELECT COUNT(DISTINCT thread) FROM #__fb_messages AS mmm WHERE c.id=mmm.catid AND mmm.hold='0' AND mmm.time>'{$this->prevCheck}' AND mmm.thread NOT IN ({$readlist})) AS new";
		else $subquery = " 0 AS new";

		// TODO: optimize this query (just combined many queries into one)
		$query = "SELECT c.*, m.id AS mesid, m.thread, m.catid, t.subject AS topicsubject, m.subject, m.name AS mname, u.id AS userid, u.username, u.name AS uname,
			(SELECT COUNT(*) FROM #__fb_messages AS mm WHERE m.thread=mm.thread) AS msgcount, {$subquery}
			FROM #__fb_categories AS c
			LEFT JOIN #__fb_messages AS m ON c.id_last_msg=m.id
			LEFT JOIN #__fb_messages AS t ON m.thread=t.id
			LEFT JOIN #__users AS u ON u.id=m.userid
			WHERE c.parent IN ({$catlist}) AND c.published='1' AND c.id IN({$this->session->allowed}) ORDER BY ordering";
		$this->db->setQuery ( $query );
		$allsubcats = $this->db->loadObjectList ();
		check_dberror ( "Unable to load categories." );

		global $kunena_icons;
		$this->tabclass = array ("sectiontableentry1", "sectiontableentry2" );

		$subcats = array ();
		$routerlist = array ();
		foreach ( $allsubcats as $i => $subcat ) {
			if ($subcat->mesid)
				$routerlist [$subcat->mesid] = $subcat->subject;

			$allsubcats [$i]->forumdesc = CKunenaTools::parseBBCode ( $subcat->description );

			$subcat->page = ceil ( $subcat->msgcount / $this->config->messages_per_page );

			$categoryicon = '';

			if ($this->config->shownew && $this->my->id != 0) {

				if ($subcat->new) {
					// Check Unread    Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $subcat->id . "_on.gif" )) {
						$allsubcats [$i]->categoryicon .= "<img src=\"" . KUNENA_URLCATIMAGES . $subcat->id . "_on.gif\" border=\"0\" class='forum-cat-image' alt=\" \" />";
					} else {
						$allsubcats [$i]->categoryicon .= isset ( $kunena_icons ['unreadforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['unreadforum'] . '" border="0" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" title="' . JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '"/>' : stripslashes ( $this->config->newchar );
					}
				} else {
					// Check Read Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $subcat->id . "_off.gif" )) {
						$allsubcats [$i]->categoryicon .= "<img src=\"" . KUNENA_URLCATIMAGES . $subcat->id . "_off.gif\" border=\"0\" class='forum-cat-image' alt=\" \"  />";
					} else {
						$allsubcats [$i]->categoryicon .= isset ( $kunena_icons ['readforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['readforum'] . '" border="0" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') . '" title="' . JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') . '"/>' : stripslashes ( $this->config->newchar );
					}
				}
			} else {
				if (is_file ( KUNENA_ABSCATIMAGESPATH . $subcat->id . "_notlogin.gif" )) {
					$allsubcats [$i]->categoryicon .= "<img src=\"" . KUNENA_URLCATIMAGES . $subcat->id . "_notlogin.gif\" border=\"0\" class='forum-cat-image' alt=\" \" />";
				} else {
					$allsubcats [$i]->categoryicon .= isset ( $kunena_icons ['notloginforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['notloginforum'] . '" border="0" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') . '" title="' . JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') . '"/>' : stripslashes ( $this->config->newchar );
				}
			}
		}

		require_once (KUNENA_PATH . DS . 'router.php');
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
			if ($this->config->shownew && $this->my->id) $subquery = " (SELECT COUNT(DISTINCT thread) FROM #__fb_messages AS m WHERE c.id=m.catid AND m.hold='0' AND m.time>'{$this->prevCheck}' AND m.thread NOT IN ({$readlist})) AS new";
			else $subquery = " 0 AS new";

			$query = "SELECT id, name, parent, numTopics, numPosts, {$subquery}
			FROM #__fb_categories AS c
			WHERE c.parent IN ({$subcatlist}) AND c.published='1'";
			$this->db->setQuery ($query);
			$childforums = $this->db->loadObjectList ();
			check_dberror ( "Unable to load categories." );
			foreach ( $childforums as $cat ) {
				$this->childforums [$cat->parent] [] = $cat;
			}
		}

		$this->modlist = array ();
		$this->pending = array ();
		if (count ( $modcats )) {
			$modcatlist = implode ( ',', $modcats );
			$this->db->setQuery ( "SELECT * FROM #__fb_moderation AS m LEFT JOIN #__users AS u ON u.id=m.userid WHERE m.catid IN ({$modcatlist})" );
			$modlist = $this->db->loadObjectList ();
			check_dberror ( "Unable to load moderators." );
			foreach ( $modlist as $mod ) {
				$this->modlist [$mod->catid] [] = $mod;
			}

			if (CKunenaTools::isModerator ( $this->my->id )) {
				foreach ( $modcats as $i => $catid ) {
					if (! CKunenaTools::isModerator ( $this->my->id, $catid ))
						unset ( $modcats [$i] );
				}
				if (count ( $modcats )) {
					$modcatlist = implode ( ',', $modcats );
					$this->db->setQuery ( "SELECT catid, COUNT(*) AS count FROM #__fb_messages WHERE catid IN ($modcatlist) AND hold='1' GROUP BY catid" );
					$pending = $this->db->loadAssocList ();
					check_dberror ( "Unable to load pending messages." );
					foreach ( $pending as $i ) {
						if ($i ['count'])
							$this->pending [$i ['catid']] = $i ['count'];
					}
				}
			}
		}
	}

	function displayPathway() {
		CKunenaTools::loadTemplate('/pathway.php');
	}

	function displayAnnouncement() {
		if ($this->config->showannouncement > 0) {
			CKunenaTools::loadTemplate('/plugin/announcement/announcementbox.php');
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
			CKunenaTools::loadTemplate('/plugin/stats/stats.class.php');

			$kunena_stats = new CKunenaStats ( );
			$kunena_stats->showFrontStats ();
		}
	}

	function displayWhoIsOnline() {
		if ($this->config->showwhoisonline > 0) {
			CKunenaTools::loadTemplate('/plugin/who/whoisonline.php');
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
