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

kimport('html.bbcode.nbbc');
kimport('database.query');

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

		$db = JFactory::getDBO();
		$query = new KQuery();

		$query->select('s.code, s.location');
		$query->from('#__kunena_smileys AS s');

		$db->setQuery($query->toString());
		$smileys = $db->loadObjectList();

		// echo nl2br(str_replace('#__','jos_',$query->toString())).'<hr/>';
	    // var_dump($smileys);

	    foreach ($smileys as $smiley)
	    {
	        $this->AddSmiley($smiley->code, $smiley->location);
	    }

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
        static $instance = false;
        if (!$instance)
        {
            $instance = new KBBCode();
        }

        $instance->SetSmileyDir(JURI::root().'components/com_kunena/template/default_ex/images/english/emoticons');


        return $instance;
    }

}