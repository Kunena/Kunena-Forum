<?php
/**
 * @version $Id$
 * Kunena Component - CKunenaAjaxHelper class
 * @package Kunena
 *
 * @Copyright (C) 2008-2011 www.kunena.org All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

jimport('joomla.error.profiler');

/**
 * @author fxstein
 *
 */
class KProfiler extends JProfiler {

	/**
	 *
	 * @var int
	 */
	var $_memstart = 0;

	/**
	 *
	 * @var dbo - Joomla dataase object
	 */
	protected $_db = null;

	/**
	 *
	 * @var integer - Query ticker
	 */
	protected $_tickerstart = 0;

	/**
	 * Constructor
	 *
	 * @access protected
	 * @param string Prefix for mark messages
	 */
	function __construct( $prefix = '' )
	{
		$this->_db = JFactory::getDBO();
		$this->_tickerstart = $this->_db->getTicker();
		$this->_memstart = $this->getMemory();

		// finalize initialization
		parent::__construct('Kunena'.$prefix);
	}

	/**
	 * Returns a reference to the global Kunena Profiler object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 * 		<pre>  $browser = & KProfiler::getInstance( $prefix );</pre>
	 *
	 * @access public
	 * @param string Prefix used to distinguish profiler objects.
	 * @return KProfiler  The Kunena Profiler object.
	 */
	static function &getInstance($prefix = '')
	{
		static $instances = NULL;

		if (!isset($instances)) {
			$instances = array();
		}

		if (empty($instances[$prefix])) {
			$instances[$prefix] = new KProfiler($prefix);
		}

		return $instances[$prefix];
	}

	/**
	 * Output a time mark
	 *
	 * The mark is returned as string but and put in the internal stack
	 *
	 * @access public
	 * @param string A label for the time mark
	 * @return string Mark text
	 */
	function mark( $label )
	{
		$mark = $this->_prefix." $label: ";
		$mark .= sprintf('%0.1f', ($this->getmicrotime() - $this->_start)*1000) . ' ms';
		$mark .= ', '.sprintf('%0.2f', parent::getMemory() / 1048576 ).' MB';
		$mark .= ', '.sprintf('%0.2f', (parent::getMemory() - $this->_memstart) / 1048576 ).' MB incr.';
		$mark .= ', '. ($this->_db->getTicker() - $this->_tickerstart). ' queries';

		$this->_buffer[] = $mark;
		return $mark;
	}

	/**
	 * Get total of Kunena queries to this point
	 *	 *
	 * @access public
	 * @return integer Number of queries since start of KProfiler
	 */
	function getQueryCount() {
		return $this->_db->getTicker() - $this->_tickerstart;
	}

}
