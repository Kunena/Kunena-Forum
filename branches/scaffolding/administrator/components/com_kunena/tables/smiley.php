<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

/**
 * Smiley Table for the Kunena Package
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaTableSmiley extends JTable
{
	/** @var int */
	var $id = null;
	/** @var varchar */
	var $code = null;
	/** @var varchar */
	var $file_path = null;
	/** @var varchar */
	var $file_path_grey = null;
	/** @var int */
	var $palette = 0;

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	Database object
	 * @return	void
	 * @since	1.0
	 */
	function __construct(&$db)
	{
		parent::__construct('#__kunena_smilies', 'id', $db);
	}

	/**
	 * Overloaded check function
	 *
	 * @return boolean
	 */
	function check()
	{
		// Check for a valid code.
		if((trim($this->code)) == '') {
			$this->setError(JText::_('Smiley must have a code'));
			return false;
		}

		// Check for a valid file path.
		if((trim($this->file_path)) == '') {
			$this->setError(JText::_('Smiley must have a file path'));
			return false;
		}

		return true;
	}
}
