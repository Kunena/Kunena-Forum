<?php
/**
* Kunena Component
* @package Kunena.Template.Strapless
*
* @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/
defined( '_JEXEC' ) or die();

class KunenaTemplateStrapless extends KunenaTemplate {
	// Try to find missing files from the following parent templates:
	protected $default = array('strapless');
	protected $userClasses = array(
		'kwho-',
		'admin'=>'kwho-admin',
		'globalmod'=>'kwho-globalmoderator',
		'moderator'=>'kwho-moderator',
		'user'=>'kwho-user',
		'guest'=>'kwho-guest',
		'banned'=>'kwho-banned',
		'blocked'=>'kwho-blocked'
	);
	public $categoryIcons = array('kreadforum', 'kunreadforum');

	public function loadLanguage() {
		// Loading language strings for the template
		$lang = JFactory::getLanguage();
		KunenaFactory::loadLanguage('com_kunena.templates', 'site');
		foreach (array_reverse($this->default) as $template) {
			$file = 'kunena_tmpl_'.$template;
			$lang->load($file, JPATH_SITE)
				|| $lang->load($file, KPATH_SITE)
				|| $lang->load($file, KPATH_SITE.'/template/'.$template);
		}
	}

	public function initialize() {
		// Template requires Mootools 1.4+ framework
		$this->loadMootools();
		JHtml::_('behavior.tooltip');

		// New Kunena JS for default template
		$this->addScript ( 'js/plugins.js' );

		$this->compileLess('main.less', 'kunena.css');
		$this->addStyleSheet ( 'css/kunena.css' );

		if ( KunenaFactory::getConfig()->lightbox == 1 ) {
			// Load mediaxboxadvanced library if enabled in configuration
			$this->addScript( 'js/mediaboxAdv.js' );
			$this->addStyleSheet ( 'css/mediaboxAdv.css');
		}

		// Toggler language strings
		JFactory::getDocument()->addScriptDeclaration('// <![CDATA[
var kunena_toggler_close = "'.JText::_('COM_KUNENA_TOGGLER_COLLAPSE').'";
var kunena_toggler_open = "'.JText::_('COM_KUNENA_TOGGLER_EXPAND').'";
// ]]>');

		parent::initialize();
	}

	public function getButton($link, $name, $scope, $type, $id = null) {
		$types = array('communication'=>'comm', 'user'=>'user', 'moderation'=>'mod', 'permanent'=>'mod');
		$names = array('unfavorite'=>'favorite', 'unsticky'=>'sticky', 'unlock'=>'lock', 'create'=>'newtopic',
				'quickreply'=>'reply', 'quote'=>'quote', 'edit'=>'edit', 'permdelete'=>'delete',
				'flat'=>'layout-flat', 'threaded'=>'layout-threaded', 'indented'=>'layout-indented',
				'list'=>'reply');

		$text = JText::_("COM_KUNENA_BUTTON_{$scope}_{$name}");
		$title = JText::_("COM_KUNENA_BUTTON_{$scope}_{$name}_LONG");
		if ($title == "COM_KUNENA_BUTTON_{$scope}_{$name}_LONG") $title = '';
		if ($id) $id = 'id="'.$id.'"';


		return <<<HTML
<a $id class="" style="" href="{$link}" rel="nofollow" title="{$title}">
	<span class="{$name}"><span>{$text}</span></span>
</a>
HTML;
	}

	public function getIcon($name, $title='') {
		return '<span class="kicon '.$name.'" title="'.$title.'"></span>';
	}

	public function getImage($image, $alt='') {
		return '<img src="'.$this->getImagePath($image).'" alt="'.$alt.'" />';
	}
	public function getPaginationListRender($list) {
		$html = '<div class="pagination pagination-small" ><ul class="pagination-small">';
		$last = 0;
		foreach($list['pages'] as $i=>$page) {
			if ($last+1 != $i) $html .= '<li>...</li>';
			$html .= '<li>'.$page['data'].'</li>';
			$last = $i;
		}
		$html .= '</ul></div><div class="clearfix"></div>';
		return $html;
	}
}
