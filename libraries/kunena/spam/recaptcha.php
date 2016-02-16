<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/*
 * This class is based on a PHP library that handles calling reCAPTCHA.
 *    - Documentation and latest version
 *          http://recaptcha.net/plugins/php/
 *    - Get a reCAPTCHA API Key
 *          https://www.google.com/recaptcha/admin/create
 *    - Discussion group
 *          http://groups.google.com/group/recaptcha
 *
 * Copyright (c) 2007 reCAPTCHA -- http://recaptcha.net
 * AUTHORS:
 *   Mike Crawford
 *   Ben Maurer
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Class KunenaSpamRecaptcha
 *
 * @deprecated 4.0
 */
class KunenaSpamRecaptcha
{
	protected $error = '';
	protected $config = null;
	protected $publickey = null;
	protected $privatekey = null;
	protected $host = null;

	public function __construct()
	{
		$this->config = KunenaFactory::getConfig();
		$this->publickey = $this->config->recaptcha_publickey;
		$this->privatekey = $this->config->recaptcha_privatekey;
		$this->host = JRequest::getString('REMOTE_ADDR', null, 'server');
	}

	public static function getInstance()
	{
		static $instance = null;

		if (!$instance)
		{
			$instance = new KunenaSpamRecaptcha();
		}

		return $instance;
	}

	public function enabled()
	{
		$me = KunenaUserHelper::getMyself();
		$config = KunenaFactory::getConfig();

		// Enabled if guest captcha is enabled and user is not logged in
		if ($config->captcha && !$me->exists())
		{
			return true;
		}

		// Enabled if user is moderator or has more posts than the threshold
		// FIXME: we need a better logic for trusted users
		if ($me->exists() && !$me->isModerator() && $me->posts < $config->captcha_post_limit)
		{
			if ( !empty($this->publickey) &&   !empty($this->privatekey))
			{
				return true;
			}
		}

		// Captcha is disabled
		return false;
	}

	/**
	* Gets the challenge HTML (javascript and non-javascript version).
	* This is called from the browser, and the resulting reCAPTCHA HTML widget
	* is embedded within the HTML form it was called from.
	* @return string - The HTML to be embedded in the user's form.
	*/
	public function getHtml()
	{
		if (empty($this->publickey))
		{
			$this->error = JText::sprintf ( 'COM_KUNENA_RECAPTCHA_ERROR_INVALID_CONFIGURATION', JText::_ ( 'COM_KUNENA_RECAPTCHA_ERROR_INVALID_CONFIGURATION_NO_PUBLIC_KEY' ) );

			return false;
		}

		if (empty($this->privatekey))
		{
			$this->error = JText::sprintf ( 'COM_KUNENA_RECAPTCHA_ERROR_INVALID_CONFIGURATION', JText::_ ( 'COM_KUNENA_RECAPTCHA_ERROR_INVALID_CONFIGURATION_NO_PRIVATE_KEY' ) );

			return false;
		}

		JFactory::getDocument()->addCustomTag('<script type="text/javascript">
		<!--
			var RecaptchaOptions = {
				custom_translations : {
				instructions_visual : "'.JText::_('COM_KUNENA_RECAPTCHA_JS_INSTRUCTIONS_VISUAL').'",
				instructions_audio : "'.JText::_('COM_KUNENA_RECAPTCHA_JS_INSTRUCTIONS_AUDIO').'",
				play_again : "'.JText::_('COM_KUNENA_RECAPTCHA_JS_PLAY_AGAIN').'",
				cant_hear_this : "'.JText::_('COM_KUNENA_RECAPTCHA_JS_CANT_HEAR_THIS').'",
				visual_challenge : "'.JText::_('COM_KUNENA_RECAPTCHA_JS_VISUAL_CHALLENGE').'",
				audio_challenge : "'.JText::_('COM_KUNENA_RECAPTCHA_JS_AUDIO_CHALLENGE').'",
				refresh_btn : "'.JText::_('COM_KUNENA_RECAPTCHA_JS_REFRESH_CODE').'",
				help_btn : "'.JText::_('COM_KUNENA_RECAPTCHA_JS_HELP_BTN').'",
				incorrect_try_again : "'.JText::_('COM_KUNENA_RECAPTCHA_ERROR_INCORRECT_CAPTCHA_SOL').'",
			},
			theme : "'.$this->config->recaptcha_theme.'"
			};
		//-->
		</script>
		');

		$server = 'http'.($this->_isSSLConnection() ? 's' : '').'://www.google.com/recaptcha/api';

		$errorpart = '';

		if ($this->error)
		{
			$errorpart = "&amp;error=" . $this->error;
		}

		// TODO: see http://karlsheen.com/mootools/recaptcha-using-mootools-request
		// TODO: see http://code.google.com/intl/fi-FI/apis/recaptcha/docs/display.html (json)
		return '<script type="text/javascript" src="'. $server . '/challenge?k=' . $this->publickey . $errorpart . '"></script>
		<noscript>
			<iframe src="'. $server . '/noscript?k=' . $this->publickey . $errorpart . '" height="300" width="500" frameborder="0"></iframe><br />
			<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
			<input type="hidden" name="recaptcha_response_field" value="manual_challenge" />
		</noscript>';
	}

	/**
	* Calls an HTTP POST function to verify if the user's guess was correct.
	 *
	* @param array $extra_params An array of extra variables to post to the server.
	 *
	* @return bool
	*/
	public function verify($extra_params = array())
	{
		if (empty($this->privatekey))
		{
			$this->error =  JText::sprintf ( 'COM_KUNENA_RECAPTCHA_ERROR_INVALID_CONFIGURATION', JText::_ ( 'COM_KUNENA_RECAPTCHA_ERROR_INVALID_CONFIGURATION_NO_PRIVATE_KEY' ) );

			return false;
		}

		$challenge	= JRequest::getString('recaptcha_challenge_field');
		$response	= JRequest::getString('recaptcha_response_field');

		// Discard spam
		if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0)
		{
			$this->error = JText::_ ( 'COM_KUNENA_RECAPTCHA_ERROR_INCORRECT_CAPTCHA_SOL' );

			return false;
		}

		$response = $this->_query ("/recaptcha/api/verify",
			array (
			'privatekey' => $this->privatekey,
			'remoteip' => $this->host,
			'challenge' => $challenge,
			'response' => $response
			) + $extra_params
		);

		if (!$response)
		{
			$response[1] = "false\nrecaptcha-not-reachable";
		}

		$answers = preg_split('/[\s,]+/', $response[1]);

		if (empty($answers[0]) || trim ($answers [0]) != 'true')
		{
			$this->error = JText::_ ('COM_KUNENA_RECAPTCHA_ERROR_' . preg_replace('/[^\w\d]+/', '_', strtoupper($answers[1])));

			return false;
		}

		return true;
	}

	/**
	 * Get error string
	 * @return string Error code
	 */
	public function getError()
	{
		return $this->error;
	}

	protected function _isSSLConnection()
	{
		return ((isset($_SERVER['HTTPS']) &&
			($_SERVER['HTTPS'] == 'on')) ||
			getenv('SSL_PROTOCOL_VERSION'));
	}

	/**
	* Encodes the given data into a query string format
	* @param $data - array of string elements to be encoded
	* @return string - encoded request
	*/
	private function _encode ($data)
	{
		$req = '';
		foreach ( $data as $key => $value )
		{
			$req .= $key . '=' . urlencode( $value ) . '&';
		}

		// Cut the last '&'
		$req=substr($req,0,strlen($req)-1);

		return $req;
	}

	/**
	* Submits an HTTP POST to a reCAPTCHA server
	* @param string $path
	* @param array $data
	* @param int $port
	* @return array response
	*/
	private function _query($path, $data, $port = 80)
	{
		$req = $this->_encode ($data);
		$host = 'www.google.com';
		$http_request  = "POST $path HTTP/1.0\r\n";
		$http_request .= "Host: $host\r\n";
		$http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
		$http_request .= "Content-Length: " . strlen($req) . "\r\n";
		$http_request .= "User-Agent: Kunena Forum/".KunenaForum::version()."\r\n";
		$http_request .= "\r\n";
		$http_request .= $req;

		$response = '';
		if (false == ($fs = @fsockopen($host, $port, $errno, $errstr, 10)))
		{
			return null;
		}

		fwrite($fs, $http_request);

		while (!feof($fs))
		{
			$response .= fgets($fs, 1160);
		}

		 // One TCP-IP packet
		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);

		return $response;
	}
}
