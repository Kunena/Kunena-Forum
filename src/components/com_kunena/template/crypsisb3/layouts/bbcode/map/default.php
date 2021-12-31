<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.BBCode
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

// [map type=roadmap zoom=10 control=0]London, UK[/map]

// Display map location.

static $id;
$params = $this->params;

// Set google map API key if it filled in Kunena configuration
$map_key = '';

if (!empty($this->config->google_map_api_key))
{
	$map_key = '&key=' . $this->config->google_map_api_key;
}

// Load JavaScript API.
if (!isset($id))
{
	$uri = Uri::getInstance();

	if ($uri->isSSL())
	{
		$this->addScript('https://maps.google.com/maps/api/js?v=quarterly&key=' . $map_key);
	}
	else
	{
		$this->addScript('http://maps.google.com/maps/api/js?v=quarterly&key=' . $map_key);
	}

	$id = 0;
}

if (!empty($this->config->google_map_api_key))
{
	$mapid      = 'kgooglemap' . $this->mapid;
	$map_type   = isset($params['type']) ? strtoupper($params['type']) : 'ROADMAP';
	$map_typeId = array('HYBRID', 'ROADMAP', 'SATELLITE', 'TERRAIN');

	if (!in_array($map_type, $map_typeId))
	{
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
}
else
{
	?>
	<div class="alert alert-info" role="alert">
		<?php
		echo Text::_('COM_KUNENA_GOOGLE_MAP_NO_KEY_UNABLE_TO_DISPLAY_MAP');
		?>
	</div>
<?php }
