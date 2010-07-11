<?php
/**
* @version $Id: kunena.google.maps.class.php 2958 2010-07-09 03:01:13Z mahagr $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

/**
 * @author fxstein
 *
 * Google Maps API V3 wrapper class for Kunena
 *
 */
class KunenaGoogleMaps {
	protected $_db = null;
	protected $_config = null;
	protected $_document = null;
	protected $_mapid = 0;

	protected function __construct() {
		$this->_db = JFactory::getDBO ();
		$this->_config = KunenaFactory::getConfig ();
		$this->_document =& JFactory::getDocument();
		$this->_mapid = 1;

		$this->_initJS();
	}

	public function &getInstance() {
		static $instance = NULL;
		if (! $instance) {
			$instance = new KunenaGoogleMaps ();
		}
		return $instance;
	}

   protected function _initJS($sensor=false)
   {
    	$this->_document->addScript('http://maps.google.com/maps/api/js?sensor='.($sensor == true ? 'true' : 'false'));
   }

   public function addMap($search)
   {
   		$mapid = 'kunena_google_map'.$this->_mapid;

   		$this->_document->addScriptDeclaration("
   			window.addEvent('domready', function() {
				var latlng = new google.maps.LatLng(-34.397, 150.644);
				var myOptions = {
					zoom: 8,
					center: latlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				var map = new google.maps.Map($('".$mapid."'), myOptions);
   			});"
   		);

   		$html = '<div id="'.$mapid.'" class="kunena_google_map"></div>';

   		$this->_mapid ++;

   		return $html;
   }
}
?>