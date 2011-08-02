<?php
/**
* Kunena Component
* @package Kunena.Template.Default
*
* @copyright (C) 2011 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/
defined( '_JEXEC' ) or die();

class KunenaTemplateBlue_Eagle extends KunenaTemplate {
	protected $default = 'blue_eagle';
	protected $css_compile = false;
	public $categoryIcons = array('kreadforum', 'kunreadforum');

	public function initialize() {
		require_once dirname(__FILE__).'/initialize.php';
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
}