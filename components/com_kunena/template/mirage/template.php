<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaTemplateMirage extends KunenaTemplate {
	protected $default = array('default20', 'blue_eagle');
	public $categoryIcons = array('knonew', 'knew');

	public function initialize() {
		// Template requires Mootools 1.2 framework
		$this->loadMootools();

		// New Kunena JS for default template
		$this->addScript ( 'js/default.js' );

		//$this->addStyleSheet ( 'css/mirage.css' );

		$this->addStyleSheet ( 'css/reset.css' );

		// Load all css files (they are combined into one)
		$this->addStyleSheet ( 'css/global.css' );
		$this->addStyleSheet ( 'css/main.css' );
		$this->addStyleSheet ( 'css/menu.css' );
		$this->addStyleSheet ( 'css/icons.css' );
		$this->addStyleSheet ( 'css/category.css' );
		$this->addStyleSheet ( 'css/topic.css' );
		$this->addStyleSheet ( 'css/user.css' );
		//$this->addStyleSheet ( 'css/search.css' );
		//$this->addStyleSheet ( 'css/uploader.css' );
		$this->addStyleSheet ( 'css/buttons.css' );
		$this->addStyleSheet ( 'css/icons-social.css' );
		//$this->addStyleSheet ( 'css/icons-editor.css' );

		$this->addStyleSheet ( 'css/position.css' );
		$this->addStyleSheet ( 'css/style.css' );
		$this->addStyleSheet ( 'css/button.css' );
		$this->addStyleSheet ( 'css/effect.css' );
		$this->addStyleSheet ( 'css/emoticon.css' );
		$this->addStyleSheet ( 'css/icon-category.css' );
		$this->addStyleSheet ( 'css/icon-general.css' );
		$this->addStyleSheet ( 'css/icon-editor.css' );
		$this->addStyleSheet ( 'css/icon-topic.css' );
		$this->addStyleSheet ( 'css/quirks.css' );

		//$this->addIEStyleSheet ( 'css/ie.css' );
		//$this->addIEStyleSheet ( 'css/ie7.css', 'IE 7' );
		//$this->addIEStyleSheet ( 'css/ie8.css', 'IE 8' );

		if ( KunenaFactory::getConfig()->lightbox == 1 ) {
			// Load mediaxboxadvanced library if enabled in configuration
			$this->addScript( 'js/mediaboxAdv.js' );
			$this->addStyleSheet ( 'css/mediaboxAdv.css');
		}
		parent::initialize();
	}

	public function initializeBackend() {
		$this->loadMootools();
		$this->addScript ( 'backend/backend.js' );

		$this->addStyleSheet ( 'backend/backend.css', false );

		parent::initializeBackend();
	}

	public function getButton($link, $name, $scope, $type, $id = null) {
		$types = array('communication'=>'comm', 'user'=>'user', 'moderation'=>'mod', 'permanent'=>'perm');

		$text = JText::_("COM_KUNENA_BUTTON_{$scope}_{$name}");
		$title = JText::_("COM_KUNENA_BUTTON_{$scope}_{$name}_LONG");
		if ($title == "COM_KUNENA_BUTTON_{$scope}_{$name}_LONG") $title = '';
		if ($id) $id = 'id="'.$id.'"';

		if (isset($types[$type])) $type = $types[$type];
		if ($name == 'quickreply') $type .= ' kqreply';

		$html = '<li class="button button-type-'.$type.' button-'.$scope.'-'.$name.'"><a '.$id.' href="'.$link.'" rel="nofollow" title="'.$title.'">';
		$html .= '<span class="'.$name.'">'.$text.'</span>';
		$html .= '</a></li>';

		return $html;
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
		$html = '<div class="pagination">';
		$html .= '<ul class="list-pagination">';
		$last = 0;
		foreach($list['pages'] as $i=>$page) {
			if ($last+1 != $i) $html .= '<li class="kpage-more">...</li>';
			$html .= $page['data'];
			$last = $i;
		}
		$html .= '</ul>';
		$html .= '</div>';
		return $html;
	}

	public function getPaginationItemActive(&$item) {
		return '<li class="page-item page-active"><a class="button" title="'.$item->text.'" href="'.$item->link.'"><span>'.$item->text.'</span></span></a></li>';
	}

	public function getPaginationItemInactive(&$item) {
		return '<li class="page-item"><a class="button" title="'.$item->text.'"><span><span>'.$item->text.'</span></span></a></li>';
	}
}