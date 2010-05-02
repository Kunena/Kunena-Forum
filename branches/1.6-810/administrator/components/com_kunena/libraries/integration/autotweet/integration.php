<?php
/**
 * @version $Id: integration.php 2126 2010-03-29 15:49:56Z mahagr $
 * Kunena Component
 * @package Kunena
 * @Autotweet: author	Ulli Storck
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('');


class KunenaIntegrationTwitter extends KunenaIntegration
{
	// general
	protected $post_modified		= 0;
	
	// forum posts
	protected $categories			= '';
	protected $excluded_categories	= '';	
	protected $post_replys			= 0;
	protected $post_private			= 0;
	protected $post_only_usertype	= 0;	
	protected $show_prefix			= 0;
	protected $show_hash			= 0;	
	protected $prefix_text			= '';
	protected $forum_use_text		= 0;
	protected $forum_use_text_count	= 100;
	
	// announcements
	protected $post_announcements		= 0;
	protected $show_announce_prefix		= 0;
	protected $announce_prefix			= '';
	protected $announce_use_text		= 0;
	protected $announce_use_text_count	= 100;
	
	// db table names
	protected $table_announcement		= '#__fb_announcement';
	protected $table_messages			= '#__fb_messages';

	// handles normal forum post
	function onAfterDispatch()
	{
		$func		= JRequest::getVar('func', '');
		$action		= JRequest::getVar('action', '');
		$do			= JRequest::getVar('do', '');
		$catid		= JRequest::getVar('catid', '');
		$parent		= JRequest::getVar('parentid', '0');
		
		$tmp_fboard_settings = JRequest::getVar('fboard_settings', '');
		if (('' != $tmp_fboard_settings) && array_key_exists('member_id', $tmp_fboard_settings)) {
			$user = $tmp_fboard_settings['member_id'];
		}
		else {
			$user = '';
		}
		
		$cat_data = $this->getCatData($catid);
		
		$cat_filter			= explode(',', str_replace(' ', '', $this->categories));
		$excl_cat_filter	= explode(',', str_replace(' ', '', $this->excluded_categories));		

		// ckeck for kunena component and post/announcement: post new/edit, announcements only new posted ...
		if (	('com_kunena' == JRequest::getVar('option'))
			&&	('post' == $func)
			&&	(('post' ==  $action) || (('editpostnow' == $do) && $this->post_modified))
			&&	((0 == $this->post_only_usertype) || ((1 == $this->post_only_usertype) && ('' != $user)) || $this->isModerator($user))
			&&	($this->post_replys || ('0' == $parent))
			&&	($this->post_private || (0 == (int)$cat_data['pub_access'])) 
			&&	(('' == $this->categories) || ('' == $catid) || in_array($catid, $cat_filter))
			&&	(('' == $this->excluded_categories) || !in_array($catid, $excl_cat_filter))		)
		{
			if ('editpostnow' == $do) {
				$id	= (int)JRequest::getVar('id', '');
			}
			else {
				$id	= $this->getID($this->table_messages);
				$id--;	// is already saved!
			}
			
			//
			// special handling to clean message text from formatting chars (Kunena uses own syntax and not html)
			//
			$clean_message = preg_replace('/\s\s+/', ' ', JRequest::getVar('message'));
			$clean_message = preg_replace('%\[[^\]]*\]%', '', $clean_message);
			
			// use title or text as twitter message
			$text = $this->getMessagetext($this->forum_use_text, $this->forum_use_text_count, JRequest::getVar('subject'), $clean_message);
			
			// prefix handling
			if ($this->show_hash) {
				$ht = '#';
				$dp = '';
			}
			else {
				$ht = '';
				$dp = ':';
			}
			
			switch ($this->show_prefix) {
				case 0:	// dont show prefix
					// do nothing, use normal text
					break;
				case 1:	// show category
					$text = $ht . $cat_data['name'] . $dp . ' ' . $text;
					break;
				case 2:	// show static text
					$text = $ht . $this->prefix_text . $dp . ' ' . $text;
					break;
			}
			
			// create url
			//
			// TODO: when Kunena 1.6 is released, test if it is possible to use 'kunenaPath' form params instead this
			//
			$url = 'index.php?option=com_kunena&Itemid=' . JRequest::getVar('Itemid') . '&func=view&catid=' . $catid . '&id=' . $id;
			
			// trigger event to post message
			$this->postTwitterStatusMessage ($id, JFactory::getDate()->toFormat(), $text, $url);
		}
	
		return true;
	}

	// Handles normal announcement (announcement does not trigger onAfterDispatch event!)
	// Also announcements is old code and has some different issues in request parameter handling...
	function onAfterRoute()
	{
		$func		= JRequest::getVar('func', '');
		$do			= JRequest::getVar('do', '');
		$published	= (int)JRequest::getVar('published', '');
		
		// ckeck for kunena component and post/announcement: post new/edit, announcements only new posted ...
		if (	('com_kunena' == JRequest::getVar('option'))
			&&	(('announcement' == $func) && $this->post_announcements && (1 == $published))
			&&	((('doedit' == $do) && $this->post_modified) || ('doadd' == $do))	)
		{
			if ('doedit' == $func) {
				$id	= (int)JRequest::getVar('id', '');
			}
			else {
				$id	= $this->getID($this->table_announcement);
			}

			// use title or text as twitter message
			$tmpDesc = JRequest::getVar('sdescription') . ' ' . JRequest::getVar('description');
			$text = $this->getMessagetext($this->announce_use_text, $this->announce_use_text_count, JRequest::getVar('title'), $tmpDesc);

			// prefix
			if ($this->show_announce_prefix && ('' != $this->announce_prefix)) {
				$text = $this->announce_prefix . ': ' . $text;
			}
						
			// create url
			$url = 'index.php?option=com_kunena&Itemid=' . JRequest::getVar('Itemid') . '&func=announcement&do=read&id=' . $id;
	
			// trigger event to post message
			$this->postTwitterStatusMessage ($id, JFactory::getDate()->toFormat(), $text, $url);
		}

		return true;
	}
	
	private function getCatData($id)
	{
		$result = null;
		
		if ('' != $id) {
			$db = &JFactory::getDBO();		
			$table = '#__fb_categories';
			$query = 'SELECT ' . $db->NameQuote('name') . ', ' . $db->NameQuote('pub_access') . ' FROM ' . $db->NameQuote($table)
				. ' WHERE ' . $db->NameQuote('id') . ' = ' . (int)$id;
			$db->setQuery($query);
			$result = $db->loadAssoc();	
		}
		
		return $result;
	}
	
	private function isModerator($id)
	{
		$result = false;
		
		if ('' != $id) {
			$db = &JFactory::getDBO();		
			$table = '#__fb_users';
			$query = 'SELECT ' . $db->NameQuote('moderator') . ' FROM ' . $db->NameQuote($table)
				. ' WHERE ' . $db->NameQuote('userid') . ' = ' . (int)$id;
			$db->setQuery($query);
			$mod = (int)$db->loadResult();
			if (0 < $mod) { $result = true; }
		}
		
		return $result;
	}

	public function trigger($event, &$params) {}
}
?>