<?php
/**
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaTemplateOrao extends KunenaTemplate {
	protected $default = 'orao';
	public $categoryIcons = array('knonew', 'knew');

	public function initialize() {
		$this->loadMootools();

		$rtl = JFactory::getLanguage()->isRTL();

		$template = KunenaFactory::getTemplate();
		$this->params = $template->params;

		$this->addScript ( 'js/default.js' );
		$this->addScript ( 'js/tabcontent.js' );
		$this->addScript ( 'js/switchcontent.js' );

		$this->addStyleSheet ( 'css/global.css' );
		$this->addStyleSheet ( 'css/common.css' );
		$this->addStyleSheet ( 'css/images.css' );
		$this->addStyleSheet ( 'css/colours.css' );
		$this->addStyleSheet ( 'css/category.css' );
		$this->addStyleSheet ( 'css/editor.css' );
		$this->addStyleSheet ( 'css/login.css' );
		$this->addStyleSheet ( 'css/menu.css' );
		$this->addStyleSheet ( 'css/moderation.css' );
		$this->addStyleSheet ( 'css/misc.css' );
		$this->addStyleSheet ( 'css/search.css' );
		$this->addStyleSheet ( 'css/topic.css' );
		$this->addStyleSheet ( 'css/topics.css' );
		$this->addStyleSheet ( 'css/user.css' );
		$this->addStyleSheet ( 'css/direction.css', $rtl );

		if ( KunenaFactory::getConfig()->lightbox == 1 ) {
			$this->addScript( 'js/mediaboxAdv.js' );
			$this->addStyleSheet ( 'css/mediaboxAdv.css');
		}

		$lang =& JFactory::getLanguage();
		$lang->load( 'com_kunena.tpl_'.$template->name, KPATH_SITE.'/template/'.$template->name.'/' );

		if (version_compare(JVERSION, '1.6','>')) {
			$this->addStyleSheet ( 'css/j16plus.css' );
			include dirname(__FILE__).'/styles/j17style.php';
		}

		include dirname(__FILE__).'/styles/preset.php';
		include dirname(__FILE__).'/styles/style.php';


		$document = JFactory::getDocument();
		$document->addScriptDeclaration('// <![CDATA[
			var kunena_toggler_close = "'.JText::_('COM_KUNENA_TOGGLER_COLLAPSE').'";
			var kunena_toggler_open = "'.JText::_('COM_KUNENA_TOGGLER_EXPAND').'";
		// ]]>');

		parent::initialize();
	}

	public function initializeBackend() {
		$this->loadMootools();
		$this->addScript ( 'backend/backend.js' );

		$this->addStyleSheet ( 'backend/backend.css', false );

		parent::initializeBackend();
	}

	public function getButton($link, $name, $scope, $type, $id = null) {
		return parent::getButton($link, $name, $scope, $type, $id);
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
		$html = '<ul class="kpagination">';
		$html .= '<li class="page">'.JText::_('COM_KUNENA_PAGE').'</li>';
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
		return '<li class="link"><a title="'.$item->text.'" href="'.$item->link.'">'.$item->text.'</a></li>';
	}

	public function getPaginationItemInactive(&$item) {
		return '<li class="active"><span title="'.$item->text.'">'.$item->text.'</span></li>';
	}
}