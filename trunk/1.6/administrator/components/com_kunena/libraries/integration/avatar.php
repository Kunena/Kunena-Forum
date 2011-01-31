<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
//
// Dont allow direct linking
defined ( '_JEXEC' ) or die ( '' );

kimport ( 'integration.integration' );

abstract class KunenaAvatar {
	public $priority = 0;
	public $avatarSizes = null;

	protected static $instance = false;

	abstract public function __construct();

	static public function getInstance($integration = null) {
		if (self::$instance === false) {
			$config = KunenaFactory::getConfig ();
			if (! $integration)
				$integration = $config->integration_avatar;
			self::$instance = KunenaIntegration::initialize ( 'avatar', $integration );
		}
		return self::$instance;
	}

	public function load($userlist) {}

	abstract public function getEditURL();
	abstract protected function _getURL($user, $sizex, $sizey);

	public function getSize($sizex=90, $sizey=90) {
		$size = new StdClass();
		$size->x = intval($sizex);
		$size->y = intval($sizey);
		if (!intval($sizex)) {
			$template = KunenaFactory::getTemplate();
			$name = ucfirst(strtolower($sizex));
			$size->x = intval($template->params->get('avatarSizeX'.$name, 90));
			$size->y = intval($template->params->get('avatarSizeY'.$name, 90));
		}
		return $size;
	}

	public function getURL($user, $sizex=90, $sizey=90) {
		$size = $this->getSize($sizex, $sizey);
		if (!$size->x || !$size->y) return;
		return $this->_getURL($user, $size->x, $size->y);
	}

	public function getLink($user, $class='', $sizex=90, $sizey=90)
	{
		$size = $this->getSize($sizex, $sizey);
		$avatar = $this->getURL($user, $size->x, $size->y);
		if (!$avatar) return;
		if ($class) $class=' class="'.$class.'"';
		
		// Why in the world do you need to have CSS here????
		$link = '<img'.$class.' src="'.$avatar.'" alt="" style="max-width: '.$size->x.'px; max-height: '.$size->y.'px" />';
		
		// Correcting that...but leaving that line above just in case...
		//$link = '<img'.$class.' src="'.$avatar.'" alt="User Avatar" />';
		
		return $link;
	}
}
