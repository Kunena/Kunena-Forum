<?php
/**
 * @version		$Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.view' );

/**
 * Kunena View Class
 */
class KunenaView extends JView {
	function getModulePosition($position) {
		$html = '';
		if (JDocumentHTML::countModules ( $position )) {
			$renderer = JFactory::getDocument ()->loadRenderer ( 'modules' );
			$options = array ('style' => 'xhtml' );
			$html .= '<div class="'.$position.'">';
			$html .= $renderer->render ( $position, $options, null );
			$html .= '</div>';
		}
		echo $html;
	}

	function getButton($name, $text) {
		return '<span class="'.$name.'"><span>'.$text.'</span></span>';
	}

	function getIcon($name, $title='') {
		return '<span class="kicon '.$name.'" title="'.$title.'"></span>';
	}

	function getTime() {
		$time = JProfiler::getmicrotime() - $this->starttime;
		return sprintf('%0.3f', $time);
	}

	function isMenu() {
		return JDocumentHTML::countModules ( 'kunena_menu' );
	}

	function getMenu() {
		jimport ( 'joomla.application.module.helper' );
		$position = "kunena_menu";
		$options = array ('style' => 'xhtml' );
		$modules = JModuleHelper::getModules ( $position );
		$html = '';
		foreach ( $modules as $module ) {
			if ($module->module == 'mod_mainmenu') {
				$basemenu = KunenaRoute::getMenu ();
				if ($basemenu) {
					$module = clone $module;
					// FIXME: J1.5 only
					$search = array ('/menutype=(.*)(\s)/', '/startLevel=(.*)(\s)/', '/endLevel=(.*)(\s)/' );
					$replace = array ("menutype={$basemenu->menutype}\\2", 'startLevel=' . ($basemenu->sublevel + 1) . '\2', 'endLevel=' . ($basemenu->sublevel + 2) . '\2' );
					$module->params = preg_replace ( $search, $replace, $module->params );
				}
			}
			$html .= JModuleHelper::renderModule ( $module, $options );
		}
		return $html;
	}

	function addStyleSheet($filename) {
		if (!KunenaFactory::getConfig ()->debug && !KunenaForum::isSvn()) {
			// If we are in debug more, make sure we load the unpacked css
			$filename = preg_replace ( '/\.css$/u', '-min.css', $filename );
		}
		$document = JFactory::getDocument ();
		$template = KunenaFactory::getTemplate();
		return $document->addStyleSheet ( $template->getFile($filename) );
	}

	function addScript($filename) {
		if (!KunenaFactory::getConfig ()->debug && !KunenaForum::isSvn()) {
			// If we are in debug more, make sure we load the unpacked css
			$filename = preg_replace ( '/\.js$/u', '-min.js', $filename );
		}
		$document = JFactory::getDocument ();
		$template = KunenaFactory::getTemplate();
		return $document->addScript ( $template->getFile($filename) );
	}

	function displayNoAccess($errors = array()) {
		$output = '';
		foreach ($errors as $error) $output .= "<div>{$error}</div>";
		$this->common->setLayout ( 'default' );
		$this->common->assign ( 'header', JText::_('COM_KUNENA_ACCESS_DENIED'));
		$this->common->assign ( 'body', $output);
		$this->common->display();
	}

	function displayCommon($layout) {
		$this->common->setLayout ( $layout );
		$this->common->display();
	}

	function setTitle($title) {
		$this->document->setTitle ( $this->document->getTitle() .' :: '. strip_tags($title) );
	}
}