<?php
/**
 * @version $Id: activity.php 2183 2010-04-09 02:03:01Z mahagr $
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

class KunenaActivityCommunityBuilder extends KunenaActivity {
	protected $integration = null;

	public function __construct() {
		$this->integration = KunenaIntegration::getInstance ('communitybuilder');
		if (! $this->integration || ! $this->integration->isLoaded())
			return;
		$this->priority = 50;
	}

	public function onAfterPosting($message) {
		$params = array ($message->userid, $message );
		$this->integration->trigger ( 'onAfterPost', $params );
	}

	public function onAfterReply($message) {
		$params = array ($message->userid, $message );
		$this->integration->trigger ( 'onAfterReply', $params );
	}
}
