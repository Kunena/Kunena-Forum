<?php
/**
 * @version $Id: class.php 4047 2010-12-21 07:59:21Z severdia $
 * Kunena Latest Module
 * @package Kunena latest
 *
* @Copyright (C) 2010-2011 www.kunena.org. All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ( '' );

class modKunenaLatest {
	static $cssadded = false;

	public function __construct($params) {
		require_once KPATH_SITE . '/lib/kunena.link.class.php';
		$this->params = $params;
	}

	function display() {
		// Load CSS only once
		$this->document = JFactory::getDocument ();
		if (self::$cssadded == false) {
			$this->document->addStyleSheet ( JURI::root (true) . '/modules/mod_kunenalatest/tmpl/css/kunenalatest.css' );
			self::$cssadded = true;
		}
		$me = KunenaFactory::getUser();
		$cache = JFactory::getCache('com_kunena', 'output');
		$hash = md5(serialize($this->params));
		if ($cache->start("display.{$me->userid}.{$hash}", 'mod_kunenalatest')) return;

		// Convert module parameters into topics view parameters
		$this->params->set('limitstart', 0);
		$this->params->set('limit', $this->params->get ( 'nbpost',5 ));
		$this->params->set('latestcategory', $this->params->get ( 'category_id', 0 ));
		$this->params->set('latestcategory_in', $this->params->get ( 'sh_category_id_in', 1 ));
		$this->params->set('sel', $this->params->get ( 'show_list_time', -1 ));
		$userid = 0;
		switch ( $this->params->get( 'choosemodel' ) ) {
			case 'latestposts' :
				$layout = 'posts';
				$mode = 'recent';
				break;
			case 'noreplies' :
				$layout = 'default';
				$mode = 'noreplies';
				break;
			case 'catsubscriptions' :
				// TODO
				break;
			case 'subscriptions' :
				$userid = -1;
				$layout = 'user';
				$mode = 'subscriptions';
				break;
			case 'favorites' :
				$userid = -1;
				$layout = 'user';
				$mode = 'favorites';
				break;
			case 'owntopics' :
				$layout = 'user';
				$mode = 'posted';
				break;
			case 'deleted' :
				$layout = 'posts';
				$mode = 'deleted';
				break;
			case 'saidthankyouposts' :
				$userid = -1;
				$layout = 'posts';
				$mode = 'mythanks';
				break;
			case 'gotthankyouposts' :
				$userid = -1;
				$layout = 'posts';
				$mode = 'thankyou';
				break;
			case 'userposts' :
				$userid = -1;
				$layout = 'posts';
				$mode = 'recent';
				break;
			case 'latesttopics' :
			default :
				$layout = 'default';
				$mode = 'recent';
		}
		$this->params->set('layout', $layout);
		$this->params->set('mode', $mode);
		$this->params->set('userid', $userid);

		// Set template path to module
		$this->params->set('templatepath', dirname (JModuleHelper::getLayoutPath ( 'mod_kunenalatest' )));

		// Display topics view
		KunenaForum::display('topics', $layout, null, $this->params);
		$cache->end();
	}
}