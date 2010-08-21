<?php
/**
 * @version $Id$
 * KunenaStats Module
 * @package Kunena Stats
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined ( '_JEXEC' ) or die ();

class ModuleKunenaStats {
	protected $params = null;
	protected $api = null;
	protected $type = null;
	protected $items = 0;
	protected $stats = null;
	protected $titleHeader = '';
	protected $valueHeader = '';
	protected $top = 0;

	public function __construct($params) {
		require_once KPATH_SITE . '/lib/kunena.link.class.php';
		$this->params = $params;
		$this->api = Kunena::getStatsAPI();

		$this->type = $this->params->get ( 'type', 'general' );
		$this->items = ( int ) $this->params->get ( 'items', 5 );
	}

	function display() {
		$this->stats = $this->getStats ();
		require JModuleHelper::getLayoutPath ( 'mod_kunenastats' );
	}

	function getBarWidth($count) {
		if ($count == $this->top) {
			$barwidth = 100;
		} else {
			$barwidth = round(($count * 100) / $this->top);
		}
		return $barwidth;
	}

	function getStats() {
		switch ($this->type) {
			case 'topics':
				$this->titleHeader = JText::_('MOD_KUNENASTATS_TOPTOPICS');
				$this->valueHeader = JText::_('MOD_KUNENASTATS_HITS');
				$items = $this->api->getTopicsStats ( $this->items );
				if (!empty($items)) $this->top = $items[0]->hits;
				break;
			case 'posters':
				$this->titleHeader = JText::_('MOD_KUNENASTATS_TOPPOSTERS');
				$this->valueHeader = JText::_('MOD_KUNENASTATS_HITS');
				$items = $this->api->getPostersStats ( $this->items );
				if (!empty($items)) $this->top = $items[0]->posts;
				break;
			case 'profiles':
				$this->titleHeader = JText::_('MOD_KUNENASTATS_TOPPROFILES');
				$this->valueHeader = JText::_('MOD_KUNENASTATS_HITS');
				$items = $this->api->getProfileStats ( $this->items );
				if (!empty($items)) $this->top = $items[0]->hits;
				break;
			case 'polls':
				$this->titleHeader = JText::_('MOD_KUNENASTATS_TOPPOLLS');
				$this->valueHeader = JText::_('MOD_KUNENASTATS_VOTES');
				$items = $this->api->getTopPollStats ( $this->items );
				if (!empty($items)) $this->top = $items[0]->total;
				break;
			case 'thanks':
				$this->titleHeader = JText::_('MOD_KUNENASTATS_TOPTHANKS');
				$this->valueHeader = JText::_('MOD_KUNENASTATS_THANKS');
				$items = $this->api->getTopThanks ( $this->items );
				if (!empty($items)) $this->top = $items[0]->hits;
				break;
			default:
				$items = array();
				break;
		}
		return $items;
	}
}