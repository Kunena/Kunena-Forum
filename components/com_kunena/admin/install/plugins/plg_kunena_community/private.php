<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage Community
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaPrivateCommunity extends KunenaPrivate {
	protected $loaded = false;
	protected $params = null;

	public function __construct($params) {
		$this->params = $params;
		CFactory::load('libraries', 'messaging');
	}

	protected function getOnClick($userid)
	{
		if (!$this->loaded) {
			// PM popup requires JomSocial css to be loaded from selected template
			$cconfig = CFactory::getConfig ();
			$document = JFactory::getDocument ();
			$document->addStyleSheet ( 'components/com_community/assets/window.css' );
			$document->addStyleSheet ( 'components/com_community/templates/' . $cconfig->get ( 'template' ) . '/css/style.css' );
			$this->loaded = true;
		}
		return ' onclick="'.CMessaging::getPopup($userid).'"';
	}

	protected function getURL($userid)
	{
		return "javascript:void(0)";
	}

	public function getInboxLink ($text) {
		if (!$text) $text = JText::_('COM_KUNENA_PMS_INBOX');
		return '<a href="'. CRoute::_('index.php?option=com_community&view=inbox').'" rel="follow">'. $text.'</a>';
	}

	public function getInboxURL () {
		return CRoute::_('index.php?option=com_community&view=inbox');
	}
}
