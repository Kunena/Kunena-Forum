<?php
/**
 * @version $Id$
 * KunenaLatest Module
 * @package Kunena latest
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */

// no direct access
defined ( '_JEXEC' ) or die ( '' );

class modKunenaLatestHelper {
	function getModel()
	{
		if (!class_exists( 'CKunenaLatestX' ))
		{
			// Build the path to the model based upon a supplied base path
			$path = JPATH_SITE.DS.'components'.DS.'com_kunena'.DS.'funcs'.DS.'latestx.php';
			$false = false;

			// If the model file exists include it and try to instantiate the object
			if (file_exists( $path )) {
				require_once( $path );
				if (!class_exists( 'CKunenaLatestX' )) {
					JError::raiseWarning( 0, 'Model class CKunenaLatestX not found in file.' );
					return $false;
				}
			} else {
				JError::raiseWarning( 0, 'Model CKunenaLatestX not supported. File not found.' );
				return $false;
			}
		}

		$model = new CKunenaLatestX('', '0');

		return $model;
	}

	function getKunenaLatestList($params) {
		KunenaFactory::getSession( true );
		$model = modKunenaLatestHelper::getModel();
		$model->threads_per_page = $params->get ( 'nbpost' );
		//if ( !empty( $params->get( 'category_id' ) )) $model->config->latestcategory = $params->get( 'category_id' );
        $model->getLatest();

    	$result = array();
		foreach ( $model->messages as $message ) {
			if ($message->parent == 0) {
				$result [$message->id] = $message;
			}
		}

		return $result;
	}

	function userAvatar( $userid,  $params ) {
    	$kunena_user = KunenaFactory::getUser((int)$userid);
	  	$avatarlink = $kunena_user->getAvatarLink('', $params->get ( 'avatarwidth') ,$params->get ( 'avatarheight' ));
    	return CKunenaLink::GetProfileLink ($userid,$avatarlink, $kunena_user->name  );

  }
}
