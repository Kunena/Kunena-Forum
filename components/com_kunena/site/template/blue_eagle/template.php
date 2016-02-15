<?php
/**
* Kunena Component
* @package Kunena.Template.Blue_Eagle
*
* @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link https://www.kunena.org
**/
defined( '_JEXEC' ) or die();

class KunenaTemplateBlue_Eagle extends KunenaTemplate {
	// Try to find missing files from the following parent templates:
	protected $default = array('blue_eagle');
	protected $css_compile = false;
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

	public function initialize() {
		KunenaFactory::loadLanguage('com_kunena.tpl_blue_eagle');

		// Enable legacy mode
		KunenaTemplateLegacy::load();

		require_once JPATH_SITE. '/' . $this->getFile('initialize.php');
		$this->addStyleSheet ( 'kunena.20.css' );
		$filename = JPATH_SITE . '/components/com_kunena/template/blue_eagle/css/custom.css';
		if (file_exists($filename))
		{
			$this->addStyleSheet ( 'custom.css' );
		}

		// Toggler language strings
		JFactory::getDocument()->addScriptDeclaration('// <![CDATA[
var kunena_toggler_close = "'.JText::_('COM_KUNENA_TOGGLER_COLLAPSE', true).'";
var kunena_toggler_open = "'.JText::_('COM_KUNENA_TOGGLER_EXPAND', true).'";
// ]]>');

	}

	public function getButton($link, $name, $scope, $type, $id = null) {
		$types = array('communication'=>'comm', 'user'=>'user', 'moderation'=>'mod', 'permanent'=>'mod');
		$names = array('unsubscribe'=>'subscribe', 'unfavorite'=>'favorite', 'unsticky'=>'sticky', 'unlock'=>'lock', 'create'=>'newtopic',
				'quickreply'=>'reply', 'quote'=>'quote', 'edit'=>'edit', 'permdelete'=>'delete',
				'flat'=>'layout-flat', 'threaded'=>'layout-threaded', 'indented'=>'layout-indented',
				'list'=>'reply');

		$text = JText::_("COM_KUNENA_BUTTON_{$scope}_{$name}");
		$title = JText::_("COM_KUNENA_BUTTON_{$scope}_{$name}_LONG");
		if ($title == "COM_KUNENA_BUTTON_{$scope}_{$name}_LONG") $title = '';
		if ($id) $id = 'id="'.$id.'"';

		if (isset($types[$type])) $type = $types[$type];
		if ($name == 'quickreply') $type .= ' kqreply';
		if (isset($names[$name])) $name = $names[$name];

		return <<<HTML
<a $id class="kicon-button kbutton{$type} btn-left" href="{$link}" rel="nofollow" title="{$title}">
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
}
