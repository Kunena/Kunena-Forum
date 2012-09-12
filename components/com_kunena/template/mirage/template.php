<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaTemplateMirage extends KunenaTemplate {
	protected $default = array('blue_eagle');
	public $categoryIcons = array('knonew', 'knew');

	public function initialize() {
		KunenaFactory::loadLanguage('com_kunena.tpl_mirage');

		// Template requires Mootools 1.2+ framework
		$this->loadMootools();
		JHtml::_('behavior.tooltip');

		// New Kunena JS for default template
		$this->addScript ( 'js/default.js' );

		//$this->addStyleSheet ( 'css/mirage.css' );

		$this->addStyleSheet ( 'css/reset.css' );

		// Load all css files (they are combined into one)
		//$this->addStyleSheet ( 'css/global.css' );
		//$this->addStyleSheet ( 'css/main.css' );
		//$this->addStyleSheet ( 'css/menu.css' );
		$this->addStyleSheet ( 'css/icons.css' ); /* FIXME: What do we want to do with this */
		//$this->addStyleSheet ( 'css/category.css' );
		//$this->addStyleSheet ( 'css/topic.css' );
		//$this->addStyleSheet ( 'css/user.css' );
		//$this->addStyleSheet ( 'css/search.css' );
		//$this->addStyleSheet ( 'css/buttons.css' );
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
		$this->addStyleSheet ( 'css/icons-social.css' );
		$this->addStyleSheet ( 'css/uploader.css' );
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

		$html = '<li class="item-button"><a class="kbutton button-type-'.$type.' button-'.$scope.'-'.$name.'" '.$id.' href="'.$link.'" rel="nofollow" title="'.$title.'">';
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
		$html = '<ul class="kpagination-list list-unstlyed">';
		$last = 0;
		foreach($list['pages'] as $i=>$page) {
			if ($last+1 != $i) $html .= '<li class="page-item kpage-more"><a class="disabled">...</a></li>';
			$html .= $page['data'];
			$last = $i;
		}
		$html .= '</ul>';
		return $html;
	}

	/**
	 * (non-PHPdoc)
	 * @see KunenaTemplate::getPaginationItemActive()
	 */
	public function getPaginationItemActive(&$item) {
		return '<li class="page-item"><a title="'.$item->text.'" href="'.$item->link.'"><span>'.$item->text.'</span></a></li>';
	}

	/**
	 * (non-PHPdoc)
	 * @see KunenaTemplate::getPaginationItemInactive()
	 */
	public function getPaginationItemInactive(&$item) {
		return '<li class="page-item page-active"><a class="active" title="'.$item->text.'"><span>'.$item->text.'</span></a></li>';
	}
}
