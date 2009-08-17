<?php
/**
* @version $Id: $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
**/
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

kimport('html.bbcode.nbbcode');

/**
 * The Kunena bbcode parser
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KBBCode extends BBCode
{
	/**
	 * Object Constructor
	 *
	 * @param
	 * @return	void
	 * @since	1.0
	 */
	function __construct()
	{
		parent::__construct();

	}

	/**
	 * Get Singleton Instance
	 *
	 * @param
	 * @return	void
	 * @since	1.0
	 */
	public function &getInstance()
    {
        static $instance;
        if (!$instance)
        {
            $userinfo = new KBBCode();
        }
        return $instance;
    }

}