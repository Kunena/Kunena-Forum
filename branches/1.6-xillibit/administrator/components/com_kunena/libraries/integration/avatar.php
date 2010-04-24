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
		if (!$this->avatarSizes) {
			CKunenaTools::loadTemplate('/settings.php');
		}
		$size = new StdClass();
		$size->x = intval($sizex);
		$size->y = intval($sizey);
		if (!intval($sizex) && isset($this->avatarSizes[$sizex])) {
			$size->x = intval($this->avatarSizes[$sizex][0]);
			$size->y = intval($this->avatarSizes[$sizex][1]);
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
		if ($class) $class=' class="'.$class.'"';
		return '<img'.$class.' src="'.$avatar.'" alt="" style="max-width: '.$size->x.'px; max-height: '.$size->y.'px" />';
	}
}
