<?php
/**
 *
 *===================================================================
 *
 *  GravatarLib - Gravatar PHP 5.3 OOP Library
 *-------------------------------------------------------------------
 * @category    gravatarlib
 * @package     gravatarlib
 * @author      Pedrollo.org
 * @copyright   (c) 2011 Pedrollo.org
 * @license     MIT License
 * @link        https://github.com/Pedrollo/gravatarlib
 *
 *===================================================================
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 *
 */

namespace Pedrollo\GravatarLib;

use \InvalidArgumentException;

/**
 * GravatarLib - A lightweight library for working with gravatars en masse,
 *      used for setting a ton of options and then just passing in only the essential data needed.
 *
 *
 * @category    gravatarlib
 * @package     gravatarlib
 * @author      Pedrollo.org
 * @license     MIT License
 * @link        https://github.com/emiliopedrollo/gravatarlib
 */
class Gravatar
{
	/**
	 * @var integer - The size to use for avatars.
	 */
	protected $size = 80;

	/**
	 * @var mixed - The default image to use - either a string of the gravatar-recognized default image "type" to use, a URL, or false if using the...default gravatar default image (hah)
	 */
	protected $default_image = null;

	/**
	 * @var string - The maximum rating to allow for the avatar.
	 */
	protected $max_rating = 'g';

	/**
	 * @var boolean - Should we use the secure (HTTPS) URL base?
	 */
	protected $use_secure_url = true;

	/**
	 * @var string - A temporary internal cache of the URL parameters to use.
	 */
	protected $param_cache = null;

	/**#@+
	 * @var string - URL constants for the avatar images
	 */
	const HTTP_URL = 'http://www.gravatar.com/avatar/';
	const HTTPS_URL = 'https://secure.gravatar.com/avatar/';
	/**#@-*/

	/**
	 * Get the currently set avatar size.
	 * @return integer - The current avatar size in use.
	 */
	public function getAvatarSize()
	{
		return $this->size;
	}

	/**
	 * Set the avatar size to use.
	 * @param integer $size - The avatar size to use, must be less than 512 and greater than 0.
	 * @return Gravatar - Provides a fluent interface.
	 *
	 * @throws InvalidArgumentException
	 */
	public function setAvatarSize($size)
	{
		// Wipe out the param cache.
		$this->param_cache = NULL;

		if(!is_int($size) && !ctype_digit($size))
		{
			throw new InvalidArgumentException('Avatar size specified must be an integer');
		}

		$this->size = (int) $size;

		if($this->size > 512 || $this->size < 0)
		{
			throw new InvalidArgumentException('Avatar size must be within 0 pixels and 512 pixels');
		}

		return $this;
	}

	/**
	 * Get the current default image setting.
	 * @return mixed - False if no default image set, string if one is set.
	 */
	public function getDefaultImage()
	{
		return $this->default_image;
	}

	/**
	 * Set the default image to use for avatars.
	 * @param mixed $image - The default image to use. Use boolean false for the gravatar default, a string containing a valid image URL, or a string specifying a recognized gravatar "default".
	 * @return Gravatar - Provides a fluent interface.
	 *
	 * @throws InvalidArgumentException
	 */
	public function setDefaultImage($image)
	{
		// Quick check against boolean false.
		if($image === false)
		{
			$this->default_image = false;

			return $this;
		}

		// Wipe out the param cache.
		$this->param_cache = NULL;

		// Check $image against recognized gravatar "defaults", and if it doesn't match any of those we need to see if it is a valid URL.
		$_image = strtolower($image);
		$valid_defaults = array('404' => 1, 'mm' => 1, 'identicon' => 1, 'monsterid' => 1, 'wavatar' => 1, 'retro' => 1);
		if(!isset($valid_defaults[$_image]))
		{
			if(!filter_var($image, FILTER_VALIDATE_URL))
			{
				throw new InvalidArgumentException('The default image specified is not a recognized gravatar "default" and is not a valid URL');
			}
			else
			{
				$this->default_image = rawurlencode($image);
			}
		}
		else
		{
			$this->default_image = $_image;
		}

		return $this;
	}

	/**
	 * Get the current maximum allowed rating for avatars.
	 * @return string - The string representing the current maximum allowed rating ('g', 'pg', 'r', 'x').
	 */
	public function getMaxRating()
	{
		return $this->max_rating;
	}

	/**
	 * Set the maximum allowed rating for avatars.
	 * @param string $rating - The maximum rating to use for avatars ('g', 'pg', 'r', 'x').
	 * @return Gravatar - Provides a fluent interface.
	 *
	 * @throws InvalidArgumentException
	 */
	public function setMaxRating($rating)
	{
		// Wipe out the param cache.
		$this->param_cache = NULL;

		$rating = strtolower($rating);
		$valid_ratings = array('g' => 1, 'pg' => 1, 'r' => 1, 'x' => 1);
		if(!isset($valid_ratings[$rating]))
		{
			throw new InvalidArgumentException(sprintf('Invalid rating "%s" specified, only "g", "pg", "r", or "x" are allowed to be used.', $rating));
		}

		$this->max_rating = $rating;

		return $this;
	}

	/**
	 * Check if we are using the secure protocol for the image URLs.
	 * @return boolean - Are we supposed to use the secure protocol?
	 */
	public function usingSecureImages()
	{
		return $this->use_secure_url;
	}

	/**
	 * Enable the use of the secure protocol for image URLs.
	 * @return Gravatar - Provides a fluent interface.
	 */
	public function enableSecureImages()
	{
		$this->use_secure_url = true;

		return $this;
	}

	/**
	 * Disable the use of the secure protocol for image URLs.
	 * @return Gravatar - Provides a fluent interface.
	 */
	public function disableSecureImages()
	{
		$this->use_secure_url = false;

		return $this;
	}

	/**
	 * Build the avatar URL based on the provided email address.
	 * @param string $email - The email to get the gravatar for.
	 * @param string $hash_email - Should we hash the $email variable?  (Useful if the email address has a hash stored already)
	 * @return string - The XHTML-safe URL to the gravatar.
	 */
	public function buildGravatarURL($email, $hash_email = true)
	{
		// Start building the URL, and deciding if we're doing this via HTTPS or HTTP.
		if($this->usingSecureImages())
		{
			$url = static::HTTPS_URL;
		}
		else
		{
			$url = static::HTTP_URL;
		}

		// Tack the email hash onto the end.
		if($hash_email == true && !empty($email))
		{
			$url .= $this->getEmailHash($email);
		}
		elseif(!empty($email))
		{
			$url .= $email;
		}
		else
		{
			$url .= str_repeat('0', 32);
		}

		// Check to see if the param_cache property has been populated yet
		if($this->param_cache === NULL)
		{
			// Time to figure out our request params
			$params = array();
			$params[] = 's=' . $this->getAvatarSize();
			$params[] = 'r=' . $this->getMaxRating();
			if($this->getDefaultImage())
			{
				$params[] = 'd=' . $this->getDefaultImage();
			}

			// Stuff the request params into the param_cache property for later reuse
			$this->param_cache = (!empty($params)) ? '?' . implode('&', $params) : '';
		}

		// Handle "null" gravatar requests.
		$tail = '';
		if(empty($email))
		{
			$tail = !empty($this->param_cache) ? '&f=y' : '?f=y';
		}

		// And we're done.
		return $url . $this->param_cache . $tail;
	}

	/**
	 * Get the email hash to use (after cleaning the string).
	 * @param string $email - The email to get the hash for.
	 * @return string - The hashed form of the email, post cleaning.
	 */
	public function getEmailHash($email)
	{
		// Using md5 as per gravatar docs.
		return hash('md5', strtolower(trim($email)));
	}

	/**
	 * ...Yeah, it's just an alias of buildGravatarURL.  This is just to make it easier to use as a twig asset.
	 * @param $email
	 * @param bool $hash_email
	 * @return string
	 * @see Gravatar::buildGravatarURL()
	 */
	public function get($email, $hash_email = true)
	{
		// Just an alias.  Makes it easy to use this as a twig asset.
		return $this->buildGravatarURL($email, $hash_email);
	}
}
