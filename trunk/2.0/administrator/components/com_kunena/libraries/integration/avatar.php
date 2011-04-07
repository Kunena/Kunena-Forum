<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once KPATH_ADMIN . '/libraries/integration/integration.php';

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
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		$size = $this->getSize($sizex, $sizey);
		if (!$size->x || !$size->y) return;
		$result = $this->_getURL($user, $size->x, $size->y);
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return $result;
	}

	public function getLink($user, $class='', $sizex=90, $sizey=90)
	{
		$size = $this->getSize($sizex, $sizey);
		$avatar = $this->getURL($user, $size->x, $size->y);
		if (!$avatar) return;
		if ($class) $class=' class="'.$class.'"';

		$link = '<img'.$class.' src="'.$avatar.'" alt="'.JText::sprintf('COM_KUNENA_LIB_AVATAR_TITLE', $user->getName()).'" />';
		//$link = '<img'.$class.' src="'.$avatar.'" alt="'.JText::sprintf('COM_KUNENA_LIB_AVATAR_TITLE', $user->getName()).'" style="max-width: '.$size->x.'px; max-height: '.$size->y.'px" />';

		return $link;
	}
}
