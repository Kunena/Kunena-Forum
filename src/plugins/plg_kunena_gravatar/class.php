<?php
/**
 * Class KunenaGravatar
 *
 * From Gravatar Help:
 * "A gravatar is a dynamic image resource that is requested from our server. The request
 * URL is presented here, broken into its segments."
 * Source:
 * http://site.gravatar.com/site/implement
 *
 * @package   Gravatarlib
 * @author    Emberlabs  <info@emberlabs.org>
 * @copyright 2015-2020 gravatar
 * @license   MIT License
 * @link      https://github.com/emberlabs/gravatarlib
 * @since     Kunena
 */

namespace Kunena\Forum\Plugin\Kunena\Gravatar;

defined('_JEXEC') or die();

use InvalidArgumentException;
use Joomla\CMS\Uri\Uri;
use function defined;

/**
 * @package Gravatar
 *
 * @since   Kunena 6.0
 */
class KunenaGravatar
{
	/**
	 * @var     string - URL constants for the avatar images
	 * @since   Kunena 6.0
	 */
	const HTTP_URL = 'http://www.gravatar.com/avatar/';

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	const HTTPS_URL = 'https://secure.gravatar.com/avatar/';

	/**
	 * @var     integer - The size to use for avatars.
	 * @since   Kunena 6.0
	 */
	protected $size = 80;

	/**
	 * @var     string - The maximum rating to allow for the avatar.
	 * @since   Kunena 6.0
	 */
	protected $max_rating = 'g';

	/**
	 * @var     string - A temporary internal cache of the URL parameters to use.
	 * @since   Kunena 6.0
	 */
	protected $param_cache = null;

	/**
	 * @var     string - The email address of the user.
	 * @since   Kunena 6.0
	 */
	protected $email = null;

	/**
	 * @var     mixed - The default image to use - either a string of the gravatar-recognized default image "type" to
	 *          use, a URL, or false if using the...default gravatar default image (hah)
	 * @since   Kunena 6.0
	 */
	protected $default_image = false;

	/**
	 * Extra attributes to the IMG tag like ALT, CLASS, STYLE...
	 *
	 * @var     string
	 *
	 * @since   Kunena 6.0
	 */
	protected $extra = "";

	/**
	 * @param   string  $email  email
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct($email = null)
	{
		$this->setEmail($email);
	}

	/**
	 * @param   string  $email  email
	 *
	 * @return  void $email
	 * @since   Kunena 6.0
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * Define extras html attributes to be added into the HTML
	 *
	 * @param   string  $extra  extra
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function setExtra($extra)
	{
		$this->extra = $extra;
	}

	/**
	 * Set the avatar size to use.
	 *
	 * @param   integer  $size  - The avatar size to use, must be less than 512 and greater than 0.
	 *
	 * @return  KunenaGravatar
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws InvalidArgumentException
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
	 * Provide HTML with gravatar URL, and extras HTML attibutes if provided
	 *
	 * @param   string  $hash_email  email
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getLink($hash_email)
	{
		$gravatarURL = $this->buildGravatarURL($hash_email);

		return '<img loading="lazy" src="' . $gravatarURL . '"' . (!isset($this->size) ? "" : ' width="' . $this->size . '" height="' . $this->size . '"') . $this->extra . ' />';
	}

	/**
	 * Build the avatar URL based on the provided email address.
	 *
	 * @param   bool|string  $hash_email  - Should we hash the $email variable? (Useful if the email address has a hash
	 *                                    stored already)
	 *
	 * @return  string - The XHTML-safe URL to the gravatar.
	 *
	 * @since   Kunena 6.0
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
			$params   = [];
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
	 * Check if we are using the secure protocol for the image URLs.
	 *
	 * @return  boolean - Are we supposed to use the secure protocol?
	 *
	 * @since   Kunena 6.0
	 */
	public function usingSecureURL()
	{
		$uri = Uri::getInstance();

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
	 * Get the email hash to use (after cleaning the string).
	 *
	 * @param   string  $email  - The email to get the hash for.
	 *
	 * @return  string - The hashed form of the email, post cleaning.
	 *
	 * @since   Kunena 6.0
	 */
	public function getEmailHash($email)
	{
		// Using md5 as per gravatar docs.
		return hash('md5', strtolower(trim($email)));
	}

	/**
	 * Get the currently set avatar size.
	 *
	 * @return integer - The current avatar size in use.
	 *
	 * @since   Kunena 6.0
	 */
	public function getAvatarSize()
	{
		return $this->size;
	}

	/**
	 * Get the current maximum allowed rating for avatars.
	 *
	 * @return  string - The string representing the current maximum allowed rating ('g', 'pg', 'r', 'x').
	 *
	 * @since   Kunena 6.0
	 */
	public function getMaxRating()
	{
		return $this->max_rating;
	}

	/**
	 * Set the maximum allowed rating for avatars.
	 *
	 * @param   string  $rating  - The maximum rating to use for avatars ('g', 'pg', 'r', 'x').
	 *
	 * @return  KunenaGravatar
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws InvalidArgumentException
	 */
	public function setMaxRating($rating)
	{
		// Wipe out the param cache.
		$this->param_cache = null;

		$rating        = strtolower($rating);
		$valid_ratings = ['g' => 1, 'pg' => 1, 'r' => 1, 'x' => 1];

		if (!isset($valid_ratings[$rating]))
		{
			throw new InvalidArgumentException(sprintf('Invalid rating "%s" specified, only "g", "pg", "r", or "x" are allowed to be used.', $rating));
		}

		$this->max_rating = $rating;

		return $this;
	}

	/**
	 * Get the current default image setting.
	 *
	 * @return  mixed - False if no default image set, string if one is set.
	 *
	 * @since   Kunena 6.0
	 */
	public function getDefaultImage()
	{
		return $this->default_image;
	}

	/**
	 * Set the default image to use for avatars.
	 *
	 * @param   mixed  $image  - The default image to use. Use boolean false for the gravatar default, a string
	 *                         containing a valid image URL, or a string specifying a recognized gravatar "default".
	 *
	 * @return  KunenaGravatar
	 *
	 * @since   Kunena 6.0
	 * @throws  InvalidArgumentException
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
		$valid_defaults = ['404' => 1, 'mm' => 1, 'identicon' => 1, 'monsterid' => 1, 'wavatar' => 1, 'retro' => 1];

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
	 * toString
	 *
	 * @return string
	 *
	 * @since   Kunena 6.0
	 */
	public function __toString()
	{
		return $this->buildGravatarURL();
	}
}
