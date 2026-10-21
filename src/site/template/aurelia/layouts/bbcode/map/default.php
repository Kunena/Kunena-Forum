<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.BBCode
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Html\KunenaMap;

// [map type=roadmap zoom=10 control=0]London, UK[/map]

// Display map location.

static $id;
$params = $this->params;

// OpenStreetMap: the embedded map of openstreetmap.org, no key and no script of a third party.
if (KunenaMap::provider() === 'osm') {
    $place  = trim(htmlspecialchars_decode((string) $this->content, ENT_QUOTES));
    $zoom   = isset($params['zoom']) ? max(1, min(18, (int) $params['zoom'])) : 10;
    $coords = KunenaMap::geocode($place);

    if ($coords) {
        ?>
        <div class="kosmmap">
            <iframe width="100%" height="350" style="border:1px solid #ccc;max-width:100%" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade" title="<?php echo $this->escape($place); ?>"
                    src="<?php echo $this->escape(KunenaMap::osmEmbedUrl($coords[0], $coords[1], $zoom)); ?>"></iframe>
            <br/>
            <small>
                <a href="<?php echo $this->escape(KunenaMap::osmPageUrl($coords[0], $coords[1], $zoom)); ?>" target="_blank"
                   rel="nofollow noopener noreferrer"><?php echo Text::_('COM_KUNENA_MAP_VIEW_LARGER'); ?></a>
                &middot; &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap</a>
            </small>
        </div>
        <?php
    } else {
        // The place is not known (yet): a link to the search of OpenStreetMap.
        ?>
        <a href="<?php echo $this->escape(KunenaMap::linkUrl($place)); ?>" target="_blank"
           rel="nofollow noopener noreferrer"><?php echo $this->escape($place); ?></a>
        <?php
    }

    return;
}

// Google Maps needs the API key which is filled in the Kunena configuration.
$map_key = (string) $this->config->googleMapApiKey;

// Load JavaScript API (once, and only with a key: without it the map can not be shown).
if (!isset($id)) {
    if ($map_key !== '') {
        $this->addScript('https://maps.google.com/maps/api/js?v=quarterly&key=' . rawurlencode($map_key));
    }

    $id = 0;
}

if (!empty($this->config->googleMapApiKey)) {
    $mapid      = 'kgooglemap' . $this->mapid;
    $map_type   = isset($params['type']) ? strtoupper($params['type']) : 'ROADMAP';
    $map_typeId = ['HYBRID', 'ROADMAP', 'SATELLITE', 'TERRAIN'];

    if (!\in_array($map_type, $map_typeId)) {
        $map_type = 'ROADMAP';
    }

    $map_zoom      = isset($params['zoom']) ? (int) $params['zoom'] : 10;
    $map_control   = isset($params['control']) ? (int) $params['control'] : 0;
    $content       = json_encode(addslashes($this->content));
    $contentString = Text::_('COM_KUNENA_GOOGLE_MAP_NO_GEOCODE', true);

    $this->addScriptDeclaration(
        "
	// <![CDATA[
		var geocoder;
		var {$mapid};

		jQuery(document).ready(function() {
			geocoder = new google.maps.Geocoder();
			var latlng = new google.maps.LatLng(60.173602,24.940978);
			var myOptions = {
			zoom: {$map_zoom},
				disableDefaultUI: {$map_control},
				center: latlng,
				mapTypeId: google.maps.MapTypeId.{$map_type}
			};
			$mapid = new google.maps.Map(document.getElementById('{$mapid}'), myOptions);

			var address = {$content};
			if (geocoder) {
				geocoder.geocode( { 'address': address}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						{$mapid}.setCenter(results[0].geometry.location);
						var marker = new google.maps.Marker({
							position: results[0].geometry.location,
							map: {$mapid}
						});
					} else {
						var contentString = '<p><strong>{$contentString} <i>{$content}</i></strong></p>';
						var infowindow{$mapid} = new google.maps.InfoWindow({ content: contentString });
						infowindow{$mapid}.open({$mapid});
					}
				});
			}
		});
	// ]]>"
    );
    ?>

    <div id="<?php echo $mapid; ?>"
         class="kgooglemap"><?php echo Text::_('COM_KUNENA_GOOGLE_MAP_NOT_VISIBLE'); ?></div>

    <?php
} else {
    ?>
    <div class="alert alert-info" role="alert">
        <?php
        echo Text::_('COM_KUNENA_GOOGLE_MAP_NO_KEY_UNABLE_TO_DISPLAY_MAP');
        ?>
    </div>
<?php }
