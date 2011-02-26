<?php
/**
 * @version		$Id: categories.php 4192 2011-01-15 12:06:53Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.model' );
kimport('kunena.model');

/**
 * Cpanel Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminModelCpanel extends KunenaModel {
	function getLatestVersion() {
		$latestVersion = $this->getLatestKunenaVersion();

		if ( $latestVersion['connect'] ) {
			if ( version_compare($latestVersion['latest_version'], KunenaForum::version(), '<=') ) {
				$needUpgrade = JText::sprintf('COM_KUNENA_COM_A_CHECK_VERSION_CORRECT', KunenaForum::version());
			} else {
				$needUpgrade = JText::sprintf('COM_KUNENA_COM_A_CHECK_VERSION_NEED_UPGRADE',$latestVersion['latest_version'],$latestVersion['released']);
			}
		} else {
			$needUpgrade = JText::_('COM_KUNENA_COM_A_CHECK_VERSION_CANNOT_CONNECT');
		}

		return $needUpgrade;
	}

	/* Get latest kunena version
	*
	* Code originally taken from AlphaUserPoints
	* copyright Copyright (C) 2008-2010 Bernard Gilly
	* license : GNU/GPL
	* Website : http://www.alphaplug.com
	*/
	function getLatestKunenaVersion() {
		$app = JFactory::getApplication ();

		$url = 'http://update.kunena.org/kunena_update.xml';
		$data = '';
		$check = array();
		$check['connect'] = 0;

		$data = $app->getUserState('com_kunena.version_check', null);
		if ( empty($data) ) {
			//try to connect via cURL
			if(function_exists('curl_init') && function_exists('curl_exec')) {
				$ch = @curl_init();

				@curl_setopt($ch, CURLOPT_URL, $url);
				@curl_setopt($ch, CURLOPT_HEADER, 0);
				//http code is greater than or equal to 300 ->fail
				@curl_setopt($ch, CURLOPT_FAILONERROR, 1);
				@curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				//timeout of 5s just in case
				@curl_setopt($ch, CURLOPT_TIMEOUT, 5);
				$data = @curl_exec($ch);
				@curl_close($ch);
			}

			//try to connect via fsockopen
			if(function_exists('fsockopen') && $data == '') {

				$errno = 0;
				$errstr = '';

				//timeout handling: 5s for the socket and 5s for the stream = 10s
				$fsock = @fsockopen("update.kunena.org", 80, $errno, $errstr, 5);

				if ($fsock) {
					@fputs($fsock, "GET /kunena_update.xml HTTP/1.1\r\n");
					@fputs($fsock, "HOST: update.kunena.org\r\n");
					@fputs($fsock, "Connection: close\r\n\r\n");

					//force stream timeout...
					@stream_set_blocking($fsock, 1);
					@stream_set_timeout($fsock, 5);

					$get_info = false;
					while (!@feof($fsock)) {
						if ($get_info) {
							$data .= @fread($fsock, 1024);
						} else {
							if (@fgets($fsock, 1024) == "\r\n") {
								$get_info = true;
							}
						}
					}
					@fclose($fsock);

					//need to check data cause http error codes aren't supported here
					if(!strstr($data, '<?xml version="1.0" encoding="utf-8"?><update>')) {
						$data = '';
					}
				}
			}

			//try to connect via fopen
			if (function_exists('fopen') && ini_get('allow_url_fopen') && $data == '') {

			//set socket timeout
			ini_set('default_socket_timeout', 5);

			$handle = @fopen ($url, 'r');

			//set stream timeout
			@stream_set_blocking($handle, 1);
			@stream_set_timeout($handle, 5);

			$data	= @fread($handle, 1000);

			@fclose($handle);
		}

		return $data;

	}

	if( !empty($data) && strstr($data, '<?xml version="1.0" encoding="utf-8"?>') ) {
		$xml = JFactory::getXMLparser('Simple');
		$xml->loadString($data);
		$version 				= $xml->document->version[0];
		$check['latest_version'] = $version->data();
		$released 				= $xml->document->released[0];
		$check['released'] 		= $released->data();
		$check['connect'] 		= 1;
		$check['enabled'] 		= 1;
	}

	return $check;
	}
}
