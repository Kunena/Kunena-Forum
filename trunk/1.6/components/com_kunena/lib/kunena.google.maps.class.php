<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
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

		$this->_initJS(true);
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

   public function addMap($address)
   {
   		$mapid = 'kgooglemap'.$this->_mapid;

   		$this->_document->addScriptDeclaration("
   		// <![CDATA[
   		  	var geocoder;
  			var $mapid;

   			window.addEvent('domready', function() {
   			    geocoder = new google.maps.Geocoder();
				var latlng = new google.maps.LatLng(37.333586,-121.894684);
				var myOptions = {
					zoom: 10,
      				center: latlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				$mapid = new google.maps.Map(document.id('".$mapid."'), myOptions);
   			});

   			window.addEvent('domready', function() {
			    var address = '$address';
			    if (geocoder) {
			      geocoder.geocode( { 'address': address}, function(results, status) {
			        if (status == google.maps.GeocoderStatus.OK) {
			          $mapid.setCenter(results[0].geometry.location);
			          var marker = new google.maps.Marker({
			              map: $mapid,
			              position: results[0].geometry.location
			          });
			        } else {
			        	var contentString = '<p><strong>".KunenaParser::JSText('COM_KUNENA_GOOGLE_MAP_NO_GEOCODE')." <i>$address</i></strong></p>';
			        	var infowindow$mapid = new google.maps.InfoWindow({ content: contentString });
  						infowindow$mapid.open($mapid);
			        }
			      });
			    }
      		});

   			// ]]>"
   		);

   		$html = '<div id="'.$mapid.'" class="kgooglemap">'.KunenaParser::JSText('COM_KUNENA_GOOGLE_MAP_NOT_VISIBLE').'</div>';

   		$this->_mapid ++;

   		return $html;
   }
}
?>