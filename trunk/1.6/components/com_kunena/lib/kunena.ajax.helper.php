<?php
/**
 * @version $Id: kunena.user.class.php 1671 2010-01-15 14:51:15Z mahagr $
 * Kunena Component - CKunenaAjaxHelper class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

/**
 * @author fxstein
 *
 */
class CKunenaAjaxHelper {
	/**
	 * @var JDatabase
	 */
	protected $_db;

	function __construct() {
		$this->_db = &JFactory::getDBO ();
	}

	function &getInstance() {
		static $instance = NULL;

		if (! $instance) {
			$instance = new CKunenaAjaxHelper ( );
		}
		return $instance;
	}

	public function generateJsonResonse($action, $do, $data) {
		$response = '';

		switch ($action) {
			case 'autocomplete' :
				$response = $this->_getAutoComplete ( $do, $data );

				break;
			default :

				break;
		}

		// Output the JSON data.
		return json_encode ( $response );
	}

	// JSON helpers
	protected function _getAutoComplete($do, $data) {
		$result = array ();

		// Verify permissions
		//		$is_admin = CKunenaTools::isAdmin ();
		//		$is_moderator = CKunenaTools::isModerator ( $this->_my->id, $catid );


		//		if (!$is_admin && !$is_moderator){
		// Not an admin nor a moderator for the category
		// nothing to return;
		//			return array();
		//		}


		// Now we can safely continue...
		switch ($do) {
			case 'getcat' :
				$query = "SELECT `name`, 'id' FROM #__fb_categories WHERE `name`
							LIKE '" . $data . "%' ORDER BY 1 LIMIT 0, 10;";

				$this->_db->setQuery ( $query );
				$result = $this->_db->loadResultArray ();
				check_dberror ( "Unable to lookup categories by name." );

				break;
			case 'getmsg' :
				$query = "SELECT `subject` FROM #__fb_messages WHERE `parent`=0 AND `subject`
							LIKE '" . $data . "%' ORDER BY 1 LIMIT 0, 10;";

				$this->_db->setQuery ( $query );
				$result = $this->_db->loadResultArray ();
				check_dberror ( "Unable to lookup topics by subject." );

				break;
			default :
			// Operation not supported


		}

		return $result;
	}

}