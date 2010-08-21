<?php
/**
 * @version $Id$
 * KunenaStats Module
 * @package Kunena Stats
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class modKStatisticsHelper {
	public $statsType = '';
	public $nbItems = '';

	function getModel() {
		if (! class_exists ( 'KunenaStatsAPI' )) {
			// Build the path to the model based upon a supplied base path
			$path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena' . DS . 'libraries' . DS . 'api.php';
			$false = false;

			// If the model file exists include it and try to instantiate the object
			if (file_exists ( $path )) {
				require_once ($path);
				if (! class_exists ( 'KunenaStatsAPI' )) {
					JError::raiseWarning ( 0, 'Model class KunenaStatsAPI not found in file.' );
					return $false;
				}
			} else {
				JError::raiseWarning ( 0, 'Model KunenaStatsAPI not supported. File not found.' );
				return $false;
			}
		}

		$model = new KunenaStatsAPI ();
		return $model;
	}

	function getKunenaLinkClass() {
		$path = JPATH_SITE . DS . 'components' . DS . 'com_kunena' . DS . 'lib' . DS . 'kunena.link.class.php';

		$false = false;

		// If the file exists include it and try to instantiate the object
		if (file_exists ( $path )) {
			require_once ($path);
			$return = new CKunenaLink ();
		} else {
			JError::raiseWarning ( 0, 'File Kunena Link Class not found.' );
			return $false;
		}

		return $return;
	}

	function getKunenaConfigClass() {
		$path = JPATH_SITE . DS . 'components' . DS . 'com_kunena' . DS . 'lib' . DS . 'kunena.config.class.php';

		$false = false;

		// If the file exists include it and try to instantiate the object
		if (file_exists ( $path )) {
			require_once ($path);
			$return = CKunenaConfig::getInstance ();
		} else {
			JError::raiseWarning ( 0, 'File Kunena Config Class not found.' );
			return $false;
		}

		return $return;
	}

	function getDatas(&$params) {
		$model = self::getModel ();

		$this->statsType = ( int ) $params->get ( 'stats_type' );
		$this->nbItems = ( int ) $params->get ( 'nb_items' );

		if ($this->statsType == '0') {
			// Popular topics
			$toptitle = $model->getTopicsStats ( $this->nbItems );

			return $toptitle;
		} elseif ($this->statsType == '1') {
			// Popular polls
			$toppolls = $model->getTopPollStats ( $this->nbItems );
			return $toppolls;
		} elseif ($this->statsType == '2') {
			// Popular users
			$topusers = $model->getPostersStats ( $this->nbItems );
			return $topusers;
		} elseif ($this->statsType == '3') {
			// Popular users profiles
			$topprofiles = $model->getProfileStats ( $this->nbItems );
			return $topprofiles;
		} elseif ($this->statsType == '5') {
			// Popular thank you
			$topthankyou = $model->getTopThanks ( $this->nbItems );
			return $topthankyou;
		}
	}

	function getTopTitlesHits($nbItems) {
		$model = self::getModel ();

		$topTitlesHits = $model->getTopTitlesHits ( $nbItems );

		return $topTitlesHits;
	}

	function getTopPollVotesStats($nbItems) {
		$model = self::getModel ();

		$topPollVotes = $model->getTopPollVotesStats ( $nbItems );

		return $topPollVotes;
	}

	function getTopMessage($nbItems) {
		$model = self::getModel ();

		$topMessage = $model->getTopMessage ( $nbItems );

		return $topMessage;
	}

	function getTopProfileHits($nbItems) {
		$model = self::getModel ();

		$topMessage = $model->getTopTitlesHits ( $nbItems );

		return $topMessage;
	}

	function getTopUserThanks($nbItems) {
		$model = self::getModel ();

		$topUserThanks = $model->getTopUserThanks ( $nbItems );

		return $topUserThanks;
	}

}