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

class KunenaPrivateUddeIM extends KunenaPrivate
{
	protected $integration = null;
	protected $uddeim = null;

	public function __construct() {
		$this->integration = KunenaIntegration::getInstance ('uddeim');
		if (! $this->integration || ! $this->integration->isLoaded())
			return;
		$this->uddeim = new uddeIMAPI();
		if ($this->uddeim->version() < 1) return;
		$this->priority = 50;
	}

	protected function getURL($userid)
	{
		$itemid = 0;
		if (method_exists($this->uddeim, 'getItemid')) $itemid = $this->uddeim->getItemid();
		if ($itemid) $itemid = '&Itemid='.(int)$itemid;
		else $itemid = '';
		return JRoute::_('index.php?option=com_uddeim&task=new&recip='.(int)$userid.$itemid);
	}
}
