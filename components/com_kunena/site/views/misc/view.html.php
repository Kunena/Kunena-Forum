<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Misc View
 */
class KunenaViewMisc extends KunenaView {
	function displayDefault($tpl = null) {
		$params = $this->app->getParams( 'com_kunena' );
		$this->header = $params->get( 'page_title' );
		$this->body = $params->get( 'body' );

		$this->_prepareDocument();

		$format = $params->get( 'body_format' );

		$this->header = $this->escape($this->header);
		if ($format == 'html') {
			$this->body = trim($this->body);
		} elseif ($format == 'text') {
			$this->body = $this->escape($this->body);
		} else {
			$this->body = KunenaHtmlParser::parseBBCode($this->body);
		}

		$this->display ();
	}

	protected function _prepareDocument(){
		$this->setTitle($this->header);

		// TODO: set keywords and description

		$active = $this->app->getMenu()->getActive();

		if ( version_compare(JVERSION, '3.0', '<') )
		{
			$menuMetaDescription = $this->app->getMenu()->getParams($active->id)->getValue('menu-meta_description');
			$menuMetaKeywords = $this->app->getMenu()->getParams($active->id)->getValue('menu-meta_keywords');
		}
		else
		{
			$menuMetaDescription = $this->app->getMenu()->getParams($active->id)->get('menu-meta_description');
			$menuMetaKeywords = $this->app->getMenu()->getParams($active->id)->get('menu-meta_keywords');
		}
		if ( !empty($menuMetaDescription) )
		{
			$this->setDescription($menuMetaDescription);
		}

		if ( !empty($menuMetaKeywords) )
		{
			$this->setKeywords($menuMetaKeywords);
		}
	}

}
