<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('');

class KunenaPrivateJomSocial extends KunenaPrivate
{
	protected $integration = null;
	protected $loaded = false;

	public function __construct() {
		$this->integration = KunenaIntegration::getInstance ('jomsocial');
		if (! $this->integration || ! $this->integration->isLoaded())
			return;
		CFactory::load('libraries', 'messaging');
		$this->priority = 40;
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
}
