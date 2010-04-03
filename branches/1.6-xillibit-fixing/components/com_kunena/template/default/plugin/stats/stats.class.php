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
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/
// Dont allow direct linking
defined( '_JEXEC' ) or die();


class CKunenaStats {
	protected $_db = null;
	protected $_config = null;

	public $showgenstats = false;
	public $showpopuserstats = false;
	public $showpopsubjectstats = false;
	public $showpoppollstats = false;

	function __construct() {
		$this->_db = &JFactory::getDBO ();
		$this->_config = & CKunenaConfig::getInstance ();

		$show = $this->_config->showstats;
		$this->showgenstats = $show ? $this->_config->showgenstats : 0;
		$this->showpopuserstats = $show ? $this->_config->showpopuserstats : 0;
		$this->showpopsubjectstats = $show ? $this->_config->showpopsubjectstats : 0;
		$this->showpoppollstats = $show ? $this->_config->showpoppollstats : 0;
	}

	public function showStats() {
		CKunenaTools::loadTemplate('/plugin/stats/stats.php');
	}

	public function showFrontStats() {
		CKunenaTools::loadTemplate('/plugin/stats/frontstats.php');
	}


}


