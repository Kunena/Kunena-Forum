<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('');

class KunenaPrivateCommunityBuilder extends KunenaPrivate
{
	protected $integration = null;

	public function __construct() {
		$this->integration = KunenaIntegration::getInstance ('communitybuilder');
		if (! $this->integration || ! $this->integration->isLoaded())
			return;
		$this->priority = 50;
	}

	protected function getURL($userid) {}

	public function showIcon($userid)
	{
		global $_CB_framework, $_CB_PMS;

		$myid = $_CB_framework->myId();

		// Don't send messages from/to anonymous and to yourself
		if ($myid == 0 || $userid == 0 || $userid == $myid) return '';

		outputCbTemplate( $_CB_framework->getUi() );
		$resultArray = $_CB_PMS->getPMSlinks( $userid, $myid, '', '', 1);
		$html = '';
		if ( count( $resultArray ) > 0) {
			$linkItem = '<span class="pm" alt="' .JText::_('COM_KUNENA_VIEW_PMS'). '" />';
			foreach ( $resultArray as $res ) {
				if ( is_array( $res ) ) {
					$html .= '<a href="' . cbSef( $res["url"] ) . '" title="' . getLangDefinition( $res["tooltip"] ) . '">' . $linkItem . '</a> ';
				}
			}
		}
		return $html;
	}

	public function getInboxLink ($text) {
		if (!$text) $text = JText::_('COM_KUNENA_PMS_INBOX');
		global $_CB_framework;

		$cbpath = KUNENA_ROOT_PATH_ADMIN . '/components/com_comprofiler/plugin.foundation.php';
		if (file_exists($cbpath)) require_once($cbpath);
		else return;

		$userid = $_CB_framework->myId();

		$cbUser =& CBuser::getInstance( (int) $userid );
		if($cbUser === null) return;

		$itemid = getCBprofileItemid();

		return CKunenaLink::GetSefHrefLink ( 'index.php?option=com_comprofiler&task=userProfile&user=' .$userid. $itemid, $text, '', 'follow');
	}
}
