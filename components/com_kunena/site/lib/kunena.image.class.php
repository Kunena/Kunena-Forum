<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Site
 * @subpackage    Lib
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 *
 **/

defined('JPATH_BASE') or die;

jimport('joomla.filesystem.file');

/**
 * Class to manipulate an image.
 *
 * Derived from JXtended JImage class Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved..
 */
class CKunenaImage
{
	/**
	 * Scale the image to fill.
	 *
	 * @var        integer
	 * @since    1.6
	 */
	const SCALE_FILL = 1;

	/**
	 * Scale the image based on its innermost dimensions.
	 *
	 * @var        integer
	 * @since    1.6
	 */
	const SCALE_INSIDE = 2;

	/**
	 * Scale the image based on its outermost dimensions.
	 *
	 * @var        integer
	 * @since    1.6
	 */
	const SCALE_OUTSIDE = 3;

	/**
	 * The image handle.
	 *
	 * @var        resource
	 * @since    1.6
	 */
	protected $_handle = null;

	/**
	 * The source image path.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $_path = null;

	/**
	 * The image type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $_type = null;

	/**
	 * List of file types supported by the server.
	 *
	 * @var        array
	 * @since    1.6
	 */
	protected $_support = array('JPG' => false, 'GIF' => false, 'PNG' => false);

	/**
	 * Error message
	 *
	 * @var        array
	 * @since    1.6
	 */
	protected $_error = null;

	/**
	 * Constructor.
	 *
	 * @param object $source
	 *
	 * @since    1.6
	 */
	public function __construct($source = null)
	{
		// First we test if dependencies are met.
		if (!CKunenaImageHelper::test())
		{
			$this->setError(JText::_('COM_KUNENA_ATTACHMENT_ERROR_UNMET_DEP'));

			return;
		}

		// Initialize image type
		$this->_type = IMAGETYPE_JPEG;

		// Determine which image types are supported by GD.
		$info = gd_info();
		if (!empty($info['JPG Support']) || !empty($info['JPEG Support']))
		{
			$this->_support['JPG'] = true;
		}
		if (!empty($info['GIF Create Support']))
		{
			$this->_support['GIF'] = true;
		}
		if (!empty($info['PNG Support']))
		{
			$this->_support['PNG'] = true;
		}

		// If the source input is a resource, set it as the image handle.
		if ((is_resource($source) && get_resource_type($source) == 'gd'))
		{
			$this->_handle = &$source;
		}
		// If the source input is not empty, assume it is a path and populate the image handle.
		elseif (!empty($source) && is_string($source))
		{
			$this->loadFromFile($source);
		}
	}

	function setError($errormsg)
	{
		$this->_error = $errormsg;
	}

	function getError()
	{
		return $this->_error;
	}

	function getType()
	{
		return $this->_type;
	}

	function crop($width, $height, $left, $top, $createNew = true, $scaleMethod = CKunenaImage::SCALE_INSIDE)
	{
		// Make sure the file handle is valid.
		if ((!is_resource($this->_handle) || get_resource_type($this->_handle) != 'gd'))
		{
			$this->setError(JText::_('COM_KUNENA_ATTACHMENT_ERROR_INVALID_FILE_HANDLE'));

			return false;
		}

		// Sanitize width.
		$width = ($width === null) ? $height : $width;
		if (preg_match('/^[0-9]+(\.[0-9]+)?\%$/', $width))
		{
			$width = intval(round($this->getWidth() * floatval(str_replace('%', '', $width)) / 100));
		}
		else
		{
			$width = intval(round(floatval($width)));
		}

		// Sanitize height.
		$height = ($height === null) ? $width : $height;
		if (preg_match('/^[0-9]+(\.[0-9]+)?\%$/', $height))
		{
			$height = intval(round($this->getHeight() * floatval(str_replace('%', '', $height)) / 100));
		}
		else
		{
			$height = intval(round(floatval($height)));
		}

		// Sanitize left.
		$left = intval(round(floatval($left)));

		// Sanitize top.
		$top = intval(round(floatval($top)));

		// Create the new truecolor image handle.
		$handle = imagecreatetruecolor($width, $height);

		// Allow transparency for the new image handle.
		imagealphablending($handle, true);
		imagesavealpha($handle, true);

		if ($this->isTransparent())
		{
			// Get the transparent color values for the current image.
			$rgba  = imageColorsForIndex($this->_handle, imagecolortransparent($this->_handle));
			$color = imageColorAllocate($this->_handle, $rgba['red'], $rgba['green'], $rgba['blue']);

			// Set the transparent color values for the new image.
			imagecolortransparent($handle, $color);
			imagefill($handle, 0, 0, $color);

			imagecopyresized(
				$handle,
				$this->_handle,
				0, 0,
				$left,
				$top,
				$width,
				$height,
				$width,
				$height
			);
		}
		else
		{
			imagecopyresampled(
				$handle,
				$this->_handle,
				0, 0,
				$left,
				$top,
				$width,
				$height,
				$width,
				$height
			);
		}

		// If we are cropping to a new image, create a new CKunenaImage object.
		if ($createNew)
		{
			// Create the new CKunenaImage object for the new truecolor image handle.
			$new = new CKunenaImage($handle);

			return $new;
		}
		else
		{
			// Swap out the current handle for the new image handle.
			$this->_handle = &$handle;

			return true;
		}
	}

	function filter($type)
	{
		// Initialize variables.
		$name = preg_replace('#[^A-Z0-9_]#i', '', $type);

		$className = 'CKunenaImageFilter_' . ucfirst($name);
		if (!class_exists($className))
		{
			jimport('joomla.filesystem.path');
			$path = JPath::find(CKunenaImageFilter::addIncludePath(), strtolower($name) . '.php');
			if ($path)
			{
				require_once $path;

				if (!class_exists($className))
				{
					$this->setError($className . ' not found in file.');

					return false;
				}
			}
			else
			{
				$this->setError($className . ' not supported. File not found.');

				return false;
			}
		}

		$instance = new $className;
		if (is_callable(array($instance, 'execute')))
		{
			// Setup the arguments to call the filter execute method.
			$args = func_get_args();
			array_shift($args);
			array_unshift($args, $this->_handle);

			// Call the filter execute method.
			$return = call_user_func_array(array($instance, 'execute'), $args);

			// If the filter failed, proxy the error and return false.
			if (!$return)
			{
				$this->setError($instance->getError());

				return false;
			}

			return true;
		}
		else
		{
			$this->setError($className . ' not valid.');

			return false;
		}
	}

	function getHeight()
	{
		return imagesy($this->_handle);
	}

	function getWidth()
	{
		return imagesx($this->_handle);
	}

	function isTransparent()
	{
		// Make sure the file handle is valid.
		if ((!is_resource($this->_handle) || get_resource_type($this->_handle) != 'gd'))
		{
			$this->setError(JText::_('COM_KUNENA_ATTACHMENT_ERROR_INVALID_FILE_HANDLE'));

			return false;
		}

		return (imagecolortransparent($this->_handle) >= 0);
	}

	function loadFromFile($path)
	{
		// Make sure the file exists.
		if (!JFile::exists($path))
		{
			$this->setError(JText::_('COM_KUNENA_ATTACHMENT_ERROR_FILE_DONOT_EXIST'));

			return false;
		}

		// Get the image properties.
		$properties = CKunenaImageHelper::getProperties($path);
		if (!$properties)
		{
			return false;
		}

		// Attempt to load the image based on the MIME-Type
		switch ($properties->get('mime'))
		{
			case 'image/gif':
				// Make sure the image type is supported.
				if (empty($this->_support['GIF']))
				{
					$this->setError(JText::_('COM_KUNENA_ATTACHMENT_ERROR_FILETYPE_NOT_SUPPORTED'));

					return false;
				}

				$this->_type = IMAGETYPE_GIF;

				// Attempt to create the image handle.
				$handle = @imagecreatefromgif($path);
				if (!is_resource($handle))
				{
					$this->setError(JText::_('COM_KUNENA_ATTACHMENT_ERROR_UNABLE_PROCESS_IMAGE'));

					return false;
				}
				$this->_handle = &$handle;
				break;

			case 'image/jpeg':
				// Make sure the image type is supported.
				if (empty($this->_support['JPG']))
				{
					$this->setError(JText::_('COM_KUNENA_ATTACHMENT_ERROR_FILETYPE_NOT_SUPPORTED'));

					return false;
				}

				$this->_type = IMAGETYPE_JPEG;

				// Attempt to create the image handle.
				$handle = @imagecreatefromjpeg($path);
				if (!is_resource($handle))
				{
					$this->setError(JText::_('COM_KUNENA_ATTACHMENT_ERROR_UNABLE_PROCESS_IMAGE'));

					return false;
				}
				$this->_handle = &$handle;
				break;

			case 'image/png':
				// Make sure the image type is supported.
				if (empty($this->_support['PNG']))
				{
					$this->setError(JText::_('COM_KUNENA_ATTACHMENT_ERROR_FILETYPE_NOT_SUPPORTED'));

					return false;
				}

				$this->_type = IMAGETYPE_PNG;

				// Attempt to create the image handle.
				$handle = @imagecreatefrompng($path);
				if (!is_resource($handle))
				{
					$this->setError(JText::_('COM_KUNENA_ATTACHMENT_ERROR_UNABLE_PROCESS_IMAGE'));

					return false;
				}
				$this->_handle = &$handle;
				break;

			default:
				$this->setError(JText::_('COM_KUNENA_ATTACHMENT_ERROR_FILETYPE_NOT_SUPPORTED'));

				return false;
				break;
		}

		// Set the filesystem path to the source image.
		$this->_path = $path;

		return true;
	}

	function resize($width, $height, $createNew = true, $scaleMethod = CKunenaImage::SCALE_INSIDE)
	{
		// Make sure the file handle is valid.
		if ((!is_resource($this->_handle) || get_resource_type($this->_handle) != 'gd'))
		{
			$this->setError(JText::_('COM_KUNENA_ATTACHMENT_ERROR_INVALID_FILE_HANDLE'));

			return false;
		}

		// Prepare the dimensions for the resize operation.
		$dimensions = $this->_prepareDimensions($width, $height, $scaleMethod);
		if (empty($dimensions))
		{
			return false;
		}

		// Create the new truecolor image handle.
		$handle = imagecreatetruecolor($dimensions['width'], $dimensions['height']);

		// Allow transparency for the new image handle.
		imagealphablending($handle, false);
		imagesavealpha($handle, true);

		if (($this->_type == IMAGETYPE_GIF) || ($this->_type == IMAGETYPE_PNG))
		{
			$trnprt_indx = imagecolortransparent($this->_handle);

			// If we have a specific transparent color
			if ($trnprt_indx >= 0)
			{

				// Get the original image's transparent color's RGB values
				// FIXME: Warning: imagecolorsforindex() [function.imagecolorsforindex]: Color index 255 out of range
				$trnprt_color = @imagecolorsforindex($this->_handle, $trnprt_indx);

				// Allocate the same color in the new image resource
				$trnprt_indx = imagecolorallocate($handle, $trnprt_color ['red'], $trnprt_color ['green'], $trnprt_color ['blue']);

				// Completely fill the background of the new image with allocated color.
				imagefill($handle, 0, 0, $trnprt_indx);

				// Set the background color for new image to transparent
				imagecolortransparent($handle, $trnprt_indx);

			} // Always make a transparent background color for PNGs that don't have one allocated already
			elseif ($this->_type == IMAGETYPE_PNG)
			{

				// Turn off transparency blending (temporarily)
				imagealphablending($handle, false);

				// Create a new transparent color for image
				$color = imagecolorallocatealpha($handle, 0, 0, 0, 127);

				// Completely fill the background of the new image with allocated color.
				imagefill($handle, 0, 0, $color);

				// Restore transparency blending
				imagesavealpha($handle, true);
			}
		}
		imagecopyresampled(
			$handle,
			$this->_handle,
			0, 0, 0, 0,
			$dimensions['width'],
			$dimensions['height'],
			$this->getWidth(),
			$this->getHeight()
		);

		// If we are resizing to a new image, create a new CKunenaImage object.
		if ($createNew)
		{
			// Create the new CKunenaImage object for the new truecolor image handle.
			$new        = new CKunenaImage($handle);
			$new->_type = $this->_type;

			return $new;
		}
		else
		{
			// Swap out the current handle for the new image handle.
			$this->_handle = &$handle;

			return true;
		}
	}

	function toFile($path, $type = null, $options = array())
	{
		if (!$type)
		{
			$type = $this->_type;
		}
		switch ($type)
		{
			case IMAGETYPE_GIF:
				imagegif($this->_handle, $path);
				break;

			case IMAGETYPE_PNG:
				imagepng($this->_handle, $path, (array_key_exists('quality', $options)) ? intval(($options['quality'] - 1) / 10) : 6);
				break;

			case IMAGETYPE_JPEG:
			default:
				imagejpeg($this->_handle, $path, (array_key_exists('quality', $options)) ? $options['quality'] : 60);
				break;
		}
	}

	function _prepareDimensions($width, $height, $scaleMethod)
	{
		// Sanitize width.
		$width = ($width === null) ? $height : $width;
		if (preg_match('/^[0-9]+(\.[0-9]+)?\%$/', $width))
		{
			$width = intval(round($this->getWidth() * floatval(str_replace('%', '', $width)) / 100));
		}
		else
		{
			$width = intval(round(floatval($width)));
		}

		// Sanitize height.
		$height = ($height === null) ? $width : $height;
		if (preg_match('/^[0-9]+(\.[0-9]+)?\%$/', $height))
		{
			$height = intval(round($this->getHeight() * floatval(str_replace('%', '', $height)) / 100));
		}
		else
		{
			$height = intval(round(floatval($height)));
		}

		$dimensions = array();
		if ($scaleMethod == CKunenaImage::SCALE_FILL)
		{
			$dimensions['width']  = $width;
			$dimensions['height'] = $height;
		}
		elseif ($scaleMethod == CKunenaImage::SCALE_INSIDE || $scaleMethod == CKunenaImage::SCALE_OUTSIDE)
		{
			$rx = $this->getWidth() / $width;
			$ry = $this->getHeight() / $height;

			if ($scaleMethod == CKunenaImage::SCALE_INSIDE)
			{
				$ratio = ($rx > $ry) ? $rx : $ry;
			}
			else
			{
				$ratio = ($rx < $ry) ? $rx : $ry;
			}
			if ($ratio < 1)
			{
				$ratio = 1;
			}

			$dimensions['width']  = round($this->getWidth() / $ratio);
			$dimensions['height'] = round($this->getHeight() / $ratio);
		}
		else
		{
			$this->setError(JText::_('COM_KUNENA_ATTACHMENT_ERROR_INVALID_FIT'));

			return false;
		}

		return $dimensions;
	}
}

class CKunenaImageFilter
{
	/**
	 * Add a directory where CKunenaImage should search for filters. You may
	 * either pass a string or an array of directories.
	 *
	 * @access    public
	 *
	 * @param    string $path A path to search.
	 *
	 * @return    array    An array with directory elements
	 * @since     1.0
	 */
	public static function addIncludePath($path = '')
	{
		static $paths = null;

		if (!isset($paths))
		{
			$paths = array(dirname(__FILE__) . '/image');
		}

		// force path to array
		settype($path, 'array');

		// loop through the path directories
		foreach ($path as $dir)
		{
			if (!empty($dir) && !in_array($dir, $paths))
			{
				array_unshift($paths, JPath::clean($dir));
			}
		}

		return $paths;
	}

	function execute()
	{
		$this->setError(JText::_('COM_KUNENA_ATTACHMENT_ERROR_METHOD_NOT_IMPLEMENTED'));

		return false;
	}
}

class CKunenaImageHelper
{
	public static function getProperties($path)
	{
		// Initialize the path variable.
		if (empty($path))
		{
			return false;
		}

		// Make sure the file exists.
		if (!JFile::exists($path))
		{
			$e = new JException(JText::_('COM_KUNENA_ATTACHMENT_ERROR_FILE_DONOT_EXIST'));

			return false;
		}

		// Get the image file information.
		$info = @getimagesize($path);
		if (!$info)
		{
			$e = new JException(JText::_('COM_KUNENA_ATTACHMENT_ERROR_UNABLE_TO_GET_IMAGESIZE'));

			return false;
		}

		// Build the response object.
		$result = new JObject;
		$result->set('width', $info[0]);
		$result->set('height', $info[1]);
		$result->set('type', $info[2]);
		$result->set('attributes', $info[3]);
		$result->set('bits', @$info['bits']);
		$result->set('channels', @$info['channels']);
		$result->set('mime', $info['mime']);

		return $result;
	}

	public static function version($file, $newpath, $newfile, $maxwidth = 800, $maxheight = 800, $quality = 70, $scale = CKunenaImage::SCALE_INSIDE)
	{
		require_once(KPATH_SITE . '/lib/kunena.file.class.php');
		// create upload directory if it does not exist
		$imageinfo = self::getProperties($file);
		if (!$imageinfo)
		{
			return false;
		}

		if (!JFolder::exists($newpath))
		{
			if (!JFolder::create($newpath))
			{
				return false;
			}
		}

		KunenaFolder::createIndex($newpath);

		if ($imageinfo->width > $maxwidth || $imageinfo->height > $maxheight)
		{
			$image = new CKunenaImage($file);
			if ($image->getError())
			{
				return false;
			}
			if ($quality < 1 || $quality > 100)
			{
				$quality = 70;
			}
			$options = array('quality' => $quality);
			$image   = $image->resize($maxwidth, $maxheight, true, $scale);
			$type    = $image->getType();
			$temp    = KunenaPath::tmpdir() . '/kunena_' . md5(rand());
			$image->toFile($temp, $type, $options);
			unset ($image);
			if (!KunenaFile::move($temp, $newpath . '/' . $newfile))
			{
				unlink($temp);

				return false;
			}
		}
		else
		{
			if (!KunenaFile::copy($file, $newpath . '/' . $newfile))
			{
				return false;
			}
		}

		return true;
	}

	public static function test()
	{
		return (function_exists('gd_info') && function_exists('imagecreatetruecolor'));
	}
}
