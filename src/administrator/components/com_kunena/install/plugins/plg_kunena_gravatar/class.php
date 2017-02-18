<?php

/**
 * Class KunenaGravatar
 *
 * From Gravatar Help:
 * "A gravatar is a dynamic image resource that is requested from our server. The request
 * URL is presented here, broken into its segments."
 * Source:
 * https://site.gravatar.com/site/implement
 *
 * @package gravatarlib
 * @author  emberlabs.org
 * @license MIT License
 * @link    https://github.com/emberlabs/gravatarlib
 */
class KunenaGravatar
{
	/**
	 * @var integer - The size to use for avatars.
	 */
	protected $size = 80;

	/**
	 * @var string - The maximum rating to allow for the avatar.
	 */
	protected $max_rating = 'g';

	/**
	 * @var string - A temporary internal cache of the URL parameters to use.
	 */
	protected $param_cache = null;

	/**
	 * @var string - URL constants for the avatar images
	 */
	const HTTP_URL = 'https://www.gravatar.com/avatar/';
	const HTTPS_URL = 'https://secure.gravatar.com/avatar/';

	/**
	 * @var string - The email address of the user.
	 */
	protected $email = null;

	/**
	 * @var mixed - The default image to use - either a string of the gravatar-recognized default image "type" to use,
	 *      a URL, or false if using the...default gravatar default image (hah)
	 */
	protected $default_image = false;

	/**
	 * Extra attributes to the IMG tag like ALT, CLASS, STYLE...
	 */
	protected $extra = "";

	/**
	 * @param   string $email
	 */
	public function __construct($email = null)
	{
		$this->setEmail($email);
	}

	/**
	 * @param   string $email
	 *
	 * @return string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * Define extras html attributes to be added into the HTML
	 *
	 * @param   string $extra
	 */
	public function setExtra($extra)
	{
		$this->extra = $extra;
	}

	/**
	 * Get the currently set avatar size.
	 *
	 * @return integer - The current avatar size in use.
	 */
	public function getAvatarSize()
	{
		return $this->size;
	}

	/**
	 * Set the avatar size to use.
	 *
	 * @param   integer $size - The avatar size to use, must be less than 512 and greater than 0.
	 *
	 * @return \emberlabs\GravatarLib\Gravatar - Provides a fluent interface.
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setAvatarSize($size)
	{
		// Wipe out the param cache.
		$this->param_cache = null;

		if (!is_int($size) && !ctype_digit($size))
		{
			throw new InvalidArgumentException('Avatar size specified must be an integer');
		}

		$this->size = (int) $size;

		if ($this->size > 512 || $this->size < 0)
		{
			throw new InvalidArgumentException('Avatar size must be within 0 pixels and 512 pixels');
		}

		return $this;
	}

	/**
	 * Get the current default image setting.
	 *
	 * @return mixed - False if no default image set, string if one is set.
	 */
	public function getDefaultImage()
	{
		return $this->default_image;
	}

	/**
	 * Set the default image to use for avatars.
	 *
	 * @param   mixed $image - The default image to use. Use boolean false for the gravatar default, a string containing
	 *                       a valid image URL, or a string specifying a recognized gravatar "default".
	 *
	 * @return \emberlabs\GravatarLib\Gravatar - Provides a fluent interface.
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setDefaultImage($image)
	{
		// Quick check against boolean false.
		if ($image === false)
		{
			$this->default_image = false;

			return $this;
		}

		// Wipe out the param cache.
		$this->param_cache = null;

		// Check $image against recognized gravatar "defaults", and if it doesn't match any of those we need to see if it is a valid URL.
		$_image         = strtolower($image);
		$valid_defaults = array('404' => 1, 'mm' => 1, 'identicon' => 1, 'monsterid' => 1, 'wavatar' => 1, 'retro' => 1);

		if (!isset($valid_defaults[$_image]))
		{
			if (!filter_var($image, FILTER_VALIDATE_URL))
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
	 *
	 * @return string - The string representing the current maximum allowed rating ('g', 'pg', 'r', 'x').
	 */
	public function getMaxRating()
	{
		return $this->max_rating;
	}

	/**
	 * Set the maximum allowed rating for avatars.
	 *
	 * @param   string $rating - The maximum rating to use for avatars ('g', 'pg', 'r', 'x').
	 *
	 * @return \emberlabs\GravatarLib\Gravatar - Provides a fluent interface.
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setMaxRating($rating)
	{
		// Wipe out the param cache.
		$this->param_cache = null;

		$rating        = strtolower($rating);
		$valid_ratings = array('g' => 1, 'pg' => 1, 'r' => 1, 'x' => 1);

		if (!isset($valid_ratings[$rating]))
		{
			throw new InvalidArgumentException(sprintf('Invalid rating "%s" specified, only "g", "pg", "r", or "x" are allowed to be used.', $rating));
		}

		$this->max_rating = $rating;

		return $this;
	}

	/**
	 * Get the email hash to use (after cleaning the string).
	 *
	 * @param   string $email - The email to get the hash for.
	 *
	 * @return string - The hashed form of the email, post cleaning.
	 */
	public function getEmailHash($email)
	{
		// Using md5 as per gravatar docs.
		return hash('md5', strtolower(trim($email)));
	}

	/**
	 * Check if we are using the secure protocol for the image URLs.
	 *
	 * @return boolean - Are we supposed to use the secure protocol?
	 */
	public function usingSecureURL()
	{
		$uri = JURI::getInstance();

		if ($uri->isSSL())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Build the avatar URL based on the provided email address.
	 *
	 * @param bool|string $hash_email - Should we hash the $email variable? (Useful if the email address has a hash stored
	 *                                already)
	 *
	 * @return string - The XHTML-safe URL to the gravatar.
	 */
	public function buildGravatarURL($hash_email = true)
	{
		if ($this->usingSecureURL())
		{
			$url = self::HTTPS_URL;
		}
		else
		{
			$url = self::HTTP_URL;
		}

		// Tack the email hash onto the end.
		if ($hash_email == true && !empty($this->email))
		{
			$url .= $this->getEmailHash($this->email);
		}
		elseif (!empty($this->email))
		{
			$url .= $this->email;
		}
		else
		{
			$url .= str_repeat('0', 32);
		}

		// Check to see if the param_cache property has been populated yet
		if ($this->param_cache === null)
		{
			// Time to figure out our request params
			$params   = array();
			$params[] = 's=' . $this->getAvatarSize();
			$params[] = 'r=' . $this->getMaxRating();

			if ($this->getDefaultImage())
			{
				$params[] = 'd=' . $this->getDefaultImage();
			}

			// Stuff the request params into the param_cache property for later reuse
			$this->params_cache = (!empty($params)) ? '?' . implode('&amp;', $params) : '';
		}

		// Handle "null" gravatar requests.
		$tail = '';

		if (empty($this->email))
		{
			$tail = !empty($this->params_cache) ? '&amp;f=y' : '?f=y';
		}

		// And we're done.
		return $url . $this->params_cache . $tail;
	}

	/**
	 * Provide HTML with gravatar URL, and extras HTML attibutes if provided
	 *
	 * @param $hash_email
	 *
	 * @return IMG HTML attribute
	 */
	public function getLink($hash_email)
	{
		$gravatarURL = $this->buildGravatarURL($hash_email);

		return '<img src="' . $gravatarURL . '"' . (!isset($this->size) ? "" : ' width="' . $this->size . '" height="' . $this->size . '"') . $this->extra . ' />';
	}

	/**
	 * toString
	 */
	public function __toString()
	{
		return $this->buildGravatarURL();
	}
}
