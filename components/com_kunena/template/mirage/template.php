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

		$this->addStyleSheet ( 'css/mirage.css' );

		// Load all css files (they are combined into one)
		$this->addStyleSheet ( 'css/global.css' );
		$this->addStyleSheet ( 'css/main.css' );
		$this->addStyleSheet ( 'css/menu.css' );
		$this->addStyleSheet ( 'css/icons.css' );
		$this->addStyleSheet ( 'css/category.css' );
		$this->addStyleSheet ( 'css/topic.css' );
		$this->addStyleSheet ( 'css/user.css' );
		$this->addStyleSheet ( 'css/search.css' );
		$this->addStyleSheet ( 'css/uploader.css' );
		$this->addStyleSheet ( 'css/buttons.css' );
		$this->addStyleSheet ( 'css/icons-social.css' );
		$this->addStyleSheet ( 'css/icons-editor.css' );

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
		$types = array('communication'=>'comm', 'user'=>'user', 'moderation'=>'mod');
		$names = array('unsubscribe'=>'subscribe', 'unfavorite'=>'favorite', 'unsticky'=>'sticky', 'unlock'=>'lock', 'create'=>'newtopic',
				'quickreply'=>'reply', 'quote'=>'kquote', 'edit'=>'kedit');

		$text = JText::_("COM_KUNENA_BUTTON_{$scope}_{$name}");
		$title = JText::_("COM_KUNENA_BUTTON_{$scope}_{$name}_LONG");
		if ($title == "COM_KUNENA_BUTTON_{$scope}_{$name}_LONG") $title = '';
		if ($id) $id = 'id="'.$id.'"';

		if (isset($types[$type])) $type = $types[$type];
		if ($name == 'quickreply') $type .= ' kqreply';
		if (isset($names[$name])) $name = $names[$name];

		$html = '<a '.$id.' href="'.$link.'" rel="nofollow" title="'.$title.'">';
		$html .= '<span class="'.$name.'"><span>'.$text.'</span></span>';
		$html .= '</a>';

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