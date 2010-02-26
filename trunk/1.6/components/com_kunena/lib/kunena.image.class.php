<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/

defined('JPATH_BASE') or die;

jimport('joomla.filesystem.file');

define('KIMAGE_SCALE_FILL', 1);
define('KIMAGE_SCALE_INSIDE', 2);
define('KIMAGE_SCALE_OUTSIDE', 3);

/**
 * Class to manipulate an image.
 *
 * Derived from JXtended JImage class Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 */
class KImage
{
	/**
	 * Scale the image to fill.
	 *
	 * @var		integer
	 * @since	2.0
	 */
	const SCALE_FILL = 1;

	/**
	 * Scale the image based on its innermost dimensions.
	 *
	 * @var		integer
	 * @since	2.0
	 */
	const SCALE_INSIDE = 2;

	/**
	 * Scale the image based on its outermost dimensions.
	 *
	 * @var		integer
	 * @since	2.0
	 */
	const SCALE_OUTSIDE = 3;

	/**
	 * The image handle.
	 *
	 * @var		resource
	 * @since	1.0
	 */
	protected $_handle;

	/**
	 * The source image path.
	 *
	 * @var		string
	 * @since	1.0
	 */
	protected $_path;

	/**
	 * List of file types supported by the server.
	 *
	 * @var		array
	 * @since	2.0
	 */
	protected $_support = array('JPG'=>false, 'GIF'=>false, 'PNG'=>false);

	/**
	 * Constructor.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function __construct($source = null)
	{
		// First we test if dependencies are met.
		if (!KImageHelper::test())
		{
			$this->setError('Unmet Dependencies');
			return false;
		}

		// Determine which image types are supported by GD.
		$info = gd_info();
		if ($info['JPG Support']) {
			$this->_support['JPG'] = true;
		}
		if ($info['GIF Create Support']) {
			$this->_support['GIF'] = true;
		}
		if ($info['PNG Support']) {
			$this->_support['PNG'] = true;
		}

		// If the source input is a resource, set it as the image handle.
		if ((is_resource($source) && get_resource_type($source) == 'gd')) {
			$this->_handle = &$source;
		}
		// If the source input is not empty, assume it is a path and populate the image handle.
		elseif (!empty($source) && is_string($source)) {
			$this->loadFromFile($source);
		}
	}

	function crop($width, $height, $left, $top, $createNew = true, $scaleMethod = KImage::SCALE_INSIDE)
	{
		// Make sure the file handle is valid.
		if ((!is_resource($this->_handle) || get_resource_type($this->_handle) != 'gd'))
		{
			$this->setError('Invalid File Handle');
			return false;
		}

		// Sanitize width.
		$width = ($width === null) ? $height : $width;
		if (preg_match('/^[0-9]+(\.[0-9]+)?\%$/', $width)) {
			$width = intval(round($this->getWidth() * floatval(str_replace('%', '', $width)) / 100));
		}
		else {
			$width = intval(round(floatval($width)));
		}

		// Sanitize height.
		$height = ($height === null) ? $width : $height;
		if (preg_match('/^[0-9]+(\.[0-9]+)?\%$/', $height)) {
			$height = intval(round($this->getHeight() * floatval(str_replace('%', '', $height)) / 100));
		}
		else {
			$height = intval(round(floatval($height)));
		}

		// Sanitize left.
		$left = intval(round(floatval($left)));

		// Sanitize top.
		$top = intval(round(floatval($top)));

		// Create the new truecolor image handle.
		$handle = imagecreatetruecolor($width, $height);

		// Allow transparency for the new image handle.
		imagealphablending($handle, false);
		imagesavealpha($handle, true);

		if ($this->isTransparent())
		{
			// Get the transparent color values for the current image.
			$rgba = imageColorsForIndex($this->_handle, imagecolortransparent($this->_handle));
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

		// If we are cropping to a new image, create a new KImage object.
		if ($createNew)
		{
			// Create the new KImage object for the new truecolor image handle.
			$new = new KImage($handle);
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

		$className = 'KImageFilter_'.ucfirst($name);
		if (!class_exists($className))
		{
			jimport('joomla.filesystem.path');
			$path = JPath::find(KImageFilter::addIncludePath(), strtolower($name).'.php');
			if ($path)
			{
				require_once $path;

				if (!class_exists($className))
				{
					$this->setError($className.' not found in file.');
					return false;
				}
			}
			else
			{
				$this->setError($className.' not supported. File not found.');
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
			$this->setError($className.' not valid.');
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
			$this->setError('Invalid File Handle');
			return false;
		}

		return (imagecolortransparent($this->_handle) >= 0);
	}

	function loadFromFile($path)
	{
		// Make sure the file exists.
		if (!JFile::exists($path))
		{
			$this->setError('File Does Not Exist');
			return false;
		}

		// Get the image properties.
		$properties = KImageHelper::getProperties($path);
		if (!$properties) {
			return false;
		}

		// Attempt to load the image based on the MIME-Type
		switch ($properties->get('mime'))
		{
			case 'image/gif':
				// Make sure the image type is supported.
				if (empty($this->_support['GIF']))
				{
					$this->setError('File Type Not Supported');
					return false;
				}

				// Attempt to create the image handle.
				$handle = @imagecreatefromgif($path);
				if (!is_resource($handle))
				{
					$this->setError('Unable To Process Image');
					return false;
				}
				$this->_handle = &$handle;
				break;

			case 'image/jpeg':
				// Make sure the image type is supported.
				if (empty($this->_support['JPG']))
				{
					$this->setError('File Type Not Supported');
					return false;
				}

				// Attempt to create the image handle.
				$handle = @imagecreatefromjpeg($path);
				if (!is_resource($handle))
				{
					$this->setError('Unable To Process Image');
					return false;
				}
				$this->_handle = &$handle;
				break;

			case 'image/png':
				// Make sure the image type is supported.
				if (empty($this->_support['PNG']))
				{
					$this->setError('File Type Not Supported');
					return false;
				}

				// Attempt to create the image handle.
				$handle = @imagecreatefrompng($path);
				if (!is_resource($handle))
				{
					$this->setError('Unable To Process Image');
					return false;
				}
				$this->_handle = &$handle;
				break;

			default:
				$this->setError('File Type Not Supported');
				return false;
				break;
		}

		// Set the filesystem path to the source image.
		$this->_path = $path;

		return true;
	}

	function resize($width, $height, $createNew = true, $scaleMethod = KImage::SCALE_INSIDE)
	{
		// Make sure the file handle is valid.
		if ((!is_resource($this->_handle) || get_resource_type($this->_handle) != 'gd'))
		{
			$this->setError('Invalid File Handle');
			return false;
		}

		// Prepare the dimensions for the resize operation.
		$dimensions = $this->_prepareDimensions($width, $height, $scaleMethod);
		if (empty($dimensions)) {
			return false;
		}

		// Create the new truecolor image handle.
		$handle = imagecreatetruecolor($dimensions['width'], $dimensions['height']);

		// Allow transparency for the new image handle.
		imagealphablending($handle, false);
		imagesavealpha($handle, true);

		if ($this->isTransparent())
		{
			// Get the transparent color values for the current image.
			$rgba = imageColorsForIndex($this->_handle, imagecolortransparent($this->_handle));
			$color = imageColorAllocate($this->_handle, $rgba['red'], $rgba['green'], $rgba['blue']);

			// Set the transparent color values for the new image.
			imagecolortransparent($handle, $color);
			imagefill($handle, 0, 0, $color);

			imagecopyresized(
				$handle,
				$this->_handle,
				0, 0, 0, 0,
				$dimensions['width'],
				$dimensions['height'],
				$this->getWidth(),
				$this->getHeight()
			);
		}
		else
		{
			imagecopyresampled(
				$handle,
				$this->_handle,
				0, 0, 0, 0,
				$dimensions['width'],
				$dimensions['height'],
				$this->getWidth(),
				$this->getHeight()
			);
		}

		// If we are resizing to a new image, create a new KImage object.
		if ($createNew)
		{
			// Create the new KImage object for the new truecolor image handle.
			$new = new KImage($handle);
			return $new;
		}
		else
		{
			// Swap out the current handle for the new image handle.
			$this->_handle = & $handle;
			return true;
		}
	}

	function toFile($path, $type = IMAGETYPE_JPEG, $options=array())
	{
		switch ($type)
		{
			case IMAGETYPE_GIF:
				imagegif($this->_handle, $path);
				break;

			case IMAGETYPE_PNG:
				imagepng($this->_handle, $path, (array_key_exists('quality', $options)) ? $options['quality'] : 0);
				break;

			case IMAGETYPE_JPEG:
			default:
				imagejpeg($this->_handle, $path, (array_key_exists('quality', $options)) ? $options['quality'] : 100);
				break;
		}
	}

	function _prepareDimensions($width, $height, $scaleMethod)
	{
		// Sanitize width.
		$width = ($width === null) ? $height : $width;
		if (preg_match('/^[0-9]+(\.[0-9]+)?\%$/', $width)) {
			$width = intval(round($this->getWidth() * floatval(str_replace('%', '', $width)) / 100));
		}
		else {
			$width = intval(round(floatval($width)));
		}

		// Sanitize height.
		$height = ($height === null) ? $width : $height;
		if (preg_match('/^[0-9]+(\.[0-9]+)?\%$/', $height)) {
			$height = intval(round($this->getHeight() * floatval(str_replace('%', '', $height)) / 100));
		}
		else {
			$height = intval(round(floatval($height)));
		}

		$dimensions = array();
		if ($scaleMethod == KImage::SCALE_FILL)
		{
			$dimensions['width'] = $width;
			$dimensions['height'] = $height;
		}
		elseif ($scaleMethod == KImage::SCALE_INSIDE || $scaleMethod == KImage::SCALE_OUTSIDE)
		{
			$rx = $this->getWidth() / $width;
			$ry = $this->getHeight() / $height;

			if ($scaleMethod == KImage::SCALE_INSIDE) {
				$ratio = ($rx > $ry) ? $rx : $ry;
			}
			else {
				$ratio = ($rx < $ry) ? $rx : $ry;
			}

			$dimensions['width']	= round($this->getWidth() / $ratio);
			$dimensions['height']	= round($this->getHeight() / $ratio);
		}
		else
		{
			$this->setError('Invalid Fit Option');
			return false;
		}

		return $dimensions;
	}
}

class KImageFilter
{
	/**
	 * Add a directory where KImage should search for filters. You may
	 * either pass a string or an array of directories.
	 *
	 * @access	public
	 * @param	string	A path to search.
	 * @return	array	An array with directory elements
	 * @since	1.0
	 */
	function addIncludePath($path='')
	{
		static $paths=null;

		if (!isset($paths)) {
			$paths = array(dirname(__FILE__).'/image');
		}

		// force path to array
		settype($path, 'array');

		// loop through the path directories
		foreach ($path as $dir)
		{
			if (!empty($dir) && !in_array($dir, $paths)) {
				array_unshift($paths, JPath::clean( $dir ));
			}
		}

		return $paths;
	}

	function execute()
	{
		$this->setError('Method Not Implemented');
		return false;
	}
}

class KImageHelper
{
	public static function getProperties($path)
	{
		// Initialize the path variable.
		$path = (empty($path)) ? $this->_path : $path;

		// Make sure the file exists.
		if (!JFile::exists($path))
		{
			$e = new JException('File Does Not Exist');
			return false;
		}

		// Get the image file information.
		$info = @getimagesize($path);
		if (!$info)
		{
			$e = new JException('Unable To Get Image Size');
			return false;
		}

		// Build the response object.
		$result	= new JObject;
		$result->set('width',		$info[0]);
		$result->set('height',		$info[1]);
		$result->set('type',		$info[2]);
		$result->set('attributes',	$info[3]);
		$result->set('bits',		@$info['bits']);
		$result->set('channels',	@$info['channels']);
		$result->set('mime',		$info['mime']);

		return $result;
	}

	public static function test()
	{
		return (function_exists('gd_info') && function_exists('imagecreatetruecolor'));
	}
}
