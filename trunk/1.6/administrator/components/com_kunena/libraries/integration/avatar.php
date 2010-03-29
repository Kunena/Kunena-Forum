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

	protected static $instance = false;

	abstract public function __construct();

	static public function getInstance($integration = null) {
		if (self::$instance === false) {
			$config = KunenaFactory::getConfig ();
			if (! $integration)
				$integration = $config->integration_access;
			self::$instance = KunenaIntegration::initialize ( 'avatar', $integration );
		}
		return self::$instance;
	}

	public function load($userlist) {}

	abstract public function getEditURL();
	abstract public function getURL($user, $size='thumb');

	public function getLink($user, $class='', $size='thumb')
	{
		$avatar = $this->getURL($user, $size);
		if ($class) $class=' class="'.$class.'"';
		return '<img'.$class.' src="'.$avatar.'" alt="" />';
	}
}
