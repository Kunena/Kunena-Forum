<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaTemplateDefault20 extends KunenaTemplate {
	protected $default = 'default';
	public $categoryIcons = array('knonew', 'knew');

	public function initialize() {
		// Template requires Mootools 1.2 framework
		$this->loadMootools();

		// New Kunena JS for default template
		$this->addScript ( 'js/default.js' );

		if ( KunenaFactory::getConfig()->lightbox == 1 ) {
			// Load mediaxboxadvanced library if enabled in configuration
			$this->addScript( 'js/mediaboxAdv.js' );
			$this->addStyleSheet ( 'css/mediaboxAdv.css');
		}

		// Load css from default template
		$this->addStyleSheet ( 'css/global.css' );
		$this->addStyleSheet ( 'css/design.css' );
	}

	public function getButton($name, $text) {
		return '<span class="'.$name.'"><span>'.$text.'</span></span>';
	}

	public function getIcon($name, $title='') {
		return '<span class="kicon '.$name.'" title="'.$title.'"></span>';
	}

	public function getImage($image, $alt='') {
		return '<img src="'.$this->getImagePath($image).'" alt="'.$alt.'" />';
	}

	public function getPaginationListFooter($list) {
		$html = '<div class="list-footer">';
		$html .= '<div class="limit">'.JText::_('COM_KUNENA_LIB_HTML_DISPLAY_NUM').' '.$list['limitfield'].'</div>';
		$html .= $list['pageslinks'];
		$html .= '<div class="counter">'.$list['pagescounter'].'</div>';
		$html .= '<input type="hidden" name="' . $list['prefix'] . 'limitstart" value="'.$list['limitstart'].'" />';
		$html .= '</div>';
		return $html;
	}

	public function getPaginationListRender($list) {
		$html = '<ul class="kpage">';
		$html .= '<li class="kpage-title">'.JText::_('COM_KUNENA_PAGE').'</li>';
		$last = 0;
		foreach($list['pages'] as $i=>$page) {
			if ($last+1 != $i) $html .= '<li class="kpage-more">...</li>';
			$html .= $page['data'];
			$last = $i;
		}
		$html .= '</ul>';
		return $html;
	}

	public function getPaginationItemActive(&$item) {
		return '<li class="kpage-active"><a title="'.$item->text.'" href="'.$item->link.'">'.$item->text.'</a></li>';
	}

	public function getPaginationItemInactive(&$item) {
		return '<li class="kpage-item"><span title="'.$item->text.'">'.$item->text.'</span></li>';
	}
}