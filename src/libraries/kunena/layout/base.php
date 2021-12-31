<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Template
 * @subpackage      Categories
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;

/**
 * Implements Kunena layouts for the views.
 *
 * This class is part of Kunena HMVC implementation, allowing calls to
 * any layout file.
 *
 * <code>
 *    echo KunenaLayout::factory('Pagination')->set('pagination', $this->pagination);
 *    echo KunenaLayout::factory('Pagination/Footer')->set('pagination', $this->pagination);
 * </code>
 *
 * Individual layout classes are located in /components/com_kunena/layout,
 * but they are not needed to get layouts to work. They are useful, though,
 * if template files would otherwise have too much code in them.
 *
 * Layout template files can be found from /components/com_kunena/template/[template]/layouts.
 * Default layout can be overridden by ->setLayout():
 *
 * <code>
 *    echo KunenaLayout::factory('Pagination')->set('pagination', $this->pagination)->setLayout('mini');
 * </code>
 *
 * @see   KunenaRequest
 * @since Kunena
 */
class KunenaLayoutBase extends KunenaCompatLayoutBase
{
	/**
	 * Layout name.
	 * @var string
	 * @since Kunena
	 */
	protected $_name = '';

	/**
	 * The view layout.
	 *
	 * @var    string
	 * @since Kunena
	 */
	protected $layout = 'default';

	/**
	 * The paths queue.
	 *
	 * @var    array
	 * @since Kunena
	 */
	protected $includePaths;

	/**
	 * Support for closure variables.
	 *
	 * @var array
	 * @since Kunena
	 */
	protected $closures = array();

	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected $debug;

	/**
	 * Method to instantiate the layout.
	 *
	 * @param   string $name  name
	 * @param   array  $paths The paths queue.
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function __construct($name, array $paths = null)
	{
		// Setup dependencies.
		$this->_name        = $name;
		$this->includePaths = isset($paths) ? $paths : $this->loadPaths();
		$this->debug        = JDEBUG || KunenaConfig::getInstance()->get('debug');
	}

	/**
	 * Method to load the paths queue.
	 *
	 * @return  array  The paths queue.
	 * @since Kunena
	 */
	protected function loadPaths()
	{
		return array();
	}

	/**
	 * Magic toString method that is a proxy for the render method.
	 *
	 * @return  string
	 * @since Kunena
	 */
	public function __toString()
	{
		try
		{
			return (string) $this->render();
		}
		catch (Exception $e)
		{
			return $this->renderException($e);
		}
		catch (Throwable $t)
		{
			return $this->renderException($t);
		}

	}

	/**
	 * Method to render the view.
	 *
	 * @param   string $layout layout
	 *
	 * @return  string  The rendered view.
	 *
	 * @throws  Exception|RunTimeException
	 * @since Kunena
	 */
	public function render($layout = null)
	{
		if (0 && $this->debug)
		{
			echo $this->debugInfo();
		}

		// Get the layout path.
		if (!$layout)
		{
			$layout = $this->getLayout();
		}

		$path = $this->getPath($layout);

		// Check if the layout path was found.
		if (!$path)
		{
			throw new RuntimeException("Layout '{$this->_name}:{$layout}' Not Found");
		}

		try
		{
			// Start an output buffer.
			ob_start();

			// Load the layout.
			include $path;

			// And get the contents.
			$output = ob_get_clean();
		}
		catch (Exception $e)
		{
			// Flush the contents and re-throw the exception.
			ob_end_clean();
			throw $e;
		}

		if ($this->debug)
		{
			$output = trim($output);
			$output = "\n<!-- START {$path} -->\n{$output}\n<!-- END {$path} -->\n";
		}

		return $output;
	}

	/**
	 * Dirty function to debug layout/path errors
	 *
	 * @return  string
	 * @since Kunena
	 */
	public function debugInfo()
	{
		$rawPath = strtolower(str_replace('.', '/', $this->_name)) . '/' . $this->layout . '.php';

		$html = "<pre>";
		$html .= '<strong>Layout:</strong> ' . $this->_name . '<br />';
		$html .= '<strong>Template:</strong> ' . $this->layout . '.php<br />';
		$html .= '<strong>RAW Layout path:</strong> ' . $rawPath . '<br>';
		$html .= '<strong>includePaths:</strong> ';
		$html .= print_r($this->includePaths, true);
		$html .= '<strong>Checking paths:</strong> <br />';

		foreach ($this->includePaths as $path)
		{
			$file = $path . '/' . $this->layout . '.php';

			if (!is_file($file))
			{
				$html .= 'NOT exists: ' . $file . '<br />';
				$file = $path . '/default.php';

				if (!is_file($file))
				{
					$html .= 'NOT exists: ' . $file . '<br />';
				}
				else
				{
					$html .= '<strong>EXISTS: ' . $file . '</strong><br />';
					break;
				}
			}
			else
			{
				$html .= '<strong>EXISTS: ' . $file . '</strong><br />';
				break;
			}
		}

		$html .= "</pre>";

		return $html;
	}

	/**
	 * Method to get the view layout.
	 *
	 * @return  string  The layout name.
	 * @since Kunena
	 */
	public function getLayout()
	{
		$layout = preg_replace('/[^a-z0-9_]/', '', strtolower($this->layout));

		return $layout ? $layout : 'default';
	}

	/**
	 * Method to set the view layout.
	 *
	 * @param   string $layout The layout name.
	 *
	 * @return KunenaLayout|KunenaLayoutBase
	 * @since Kunena
	 */
	public function setLayout($layout)
	{
		if (!$layout)
		{
			$layout = 'default';
		}

		$this->layout = $layout;

		return $this;
	}

	/**
	 * Method to get the layout path. If layout file isn't found, fall back to default layout.
	 *
	 * @param   string $layout The layout name, defaulting to the current one.
	 *
	 * @return  mixed  The layout file name if found, false otherwise.
	 * @since Kunena
	 */
	public function getPath($layout = null)
	{
		if (!$layout)
		{
			$layout = $this->getLayout();
		}

		$paths = array();

		foreach ($this->includePaths as $path)
		{
			$paths[] = $path;
		}

		// Find the layout file path.
		$path = KunenaPath::find($paths, "{$layout}.php");

		if (!$path)
		{
			$path = KunenaPath::find($paths, 'default.php');
		}

		return $path;
	}

	/**
	 * Render the exception as string
	 *
	 * @param   Throwable|Exception $e exception
	 *
	 * @return string
	 *
	 * @since Kunena
	 */
	public function renderException($e)
	{
		// Exceptions aren't allowed in string conversion, log the error and output it as a string.
		$trace    = $e->getTrace();
		$location = null;

		foreach ($trace as $caller)
		{
			if (!$location && isset($caller['file']) && !strstr($caller['file'], '/libraries/'))
			{
				$location = $caller;
			}

			if (isset($caller['class']) && isset($caller['function'])
				&& $caller['function'] == '__toString' && $caller['class'] == __CLASS__
			)
			{
				break;
			}
		}

		if (!$location)
		{
			$location = reset($trace);
		}

		if (isset($caller['file']) && strstr($caller['file'], '/libraries/'))
		{
			$caller = next($trace);
		}

		$error = "Rendering Error in layout {$this->_name}: {$e->getMessage()}";
		$error .= " in {$location['file']} on line {$location['line']}";

		if (isset($caller['file']))
		{
			$error .= " called from {$caller['file']} on line {$caller['line']}";
		}

		Log::add($error, Log::CRITICAL, 'com_kunena');

		$error = "<b>Rendering Error</b> in layout <b>{$this->_name}</b>: {$e->getMessage()}";

		if ($this->debug)
		{
			$error .= " in <b>{$location['file']}</b> on line {$location['line']}<br />";

			if (isset($caller['file']))
			{
				$error .= "Layout was rendered in <b>{$caller['file']}</b> on line {$caller['line']}";
			}
		}
		else
		{
			$error .= '. Please enable debug mode for more information.';
		}

		return '<br />' . $error . '<br />';
	}

	/**
	 * Set/override debug mode.
	 *
	 * @param   array $data data
	 *
	 * @return KunenaLayoutBase Instance of $this to allow chaining.
	 * @internal param bool $value
	 *
	 * @internal param bool $value
	 * @since    Kunena
	 */
	public function debug($data = array())
	{
		$this->debug = (bool) $data;

		return $this;
	}

	/**
	 * Add stylesheet to the document.
	 *
	 * @param   string $filename filename
	 *
	 * @return mixed
	 * @throws Exception
	 * @since Kunena
	 */
	public function addStyleSheet($filename)
	{
		return KunenaFactory::getTemplate()->addStyleSheet($filename);
	}

	/**
	 * Add script to the document.
	 *
	 * @param   string $filename filename
	 *
	 * @return mixed
	 * @throws Exception
	 * @since Kunena
	 */
	public function addScript($filename)
	{
		return KunenaFactory::getTemplate()->addScript($filename);
	}

	/**
	 * Add script declaration to the document.
	 *
	 * @param   string $content content
	 * @param   string $type    type
	 *
	 * @return mixed
	 * @throws Exception
	 * @since Kunena
	 */
	public function addScriptDeclaration($content, $type = 'text/javascript')
	{
		return KunenaFactory::getTemplate()->addScriptDeclaration($content, $type);
	}

	/**
	 * Add script options to the document.
	 *
	 * @param $key
	 * @param $options
	 * @param $merge
	 *
	 * @return mixed
	 * @throws Exception
	 * @since Kunena
	 */
	public function addScriptOptions($key, $options, $merge = true)
	{
		return KunenaFactory::getTemplate()->addScriptOptions($key, $options, $merge);
	}

	/**
	 * Method to get the view paths.
	 *
	 * @return  array  The paths queue.
	 * @since Kunena
	 */
	public function getPaths()
	{
		return $this->includePaths;
	}

	/**
	 * Method to set the view paths.
	 *
	 * @param   string $path The paths queue.
	 *
	 * @return KunenaLayout|KunenaLayoutBase
	 * @since Kunena
	 */
	public function setPath($path)
	{
		array_unshift($this->includePaths, $path);

		return $this;
	}

	/**
	 * Method to set the view paths.
	 *
	 * @param   array $paths The paths queue.
	 *
	 * @return KunenaLayout|KunenaLayoutBase
	 * @since Kunena
	 */
	public function setPaths(array $paths)
	{
		$this->includePaths = $paths;

		return $this;
	}

	/**
	 * Property overloading.
	 *
	 * @param   mixed $property property
	 *
	 * @return mixed
	 * @throws InvalidArgumentException
	 * @since Kunena
	 */
	public function __get($property)
	{
		if (!array_key_exists($property, $this->closures))
		{
			if ($this->debug)
			{
				throw new InvalidArgumentException(sprintf('Property "%s" is not defined', $property));
			}
			else
			{
				return;
			}
		}

		return $this->closures[$property]();
	}

	/**
	 * Property overloading.
	 *
	 * @param   mixed   $property property
	 * @param   integer $value    value
	 *
	 * @since Kunena
	 * @return void
	 */
	public function __set($property, $value)
	{
		$this->set($property, $value);
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param   string $property The name of the property.
	 * @param   mixed  $value    The value of the property to set.
	 *
	 * @return KunenaLayout|KunenaLayoutBase
	 * @since Kunena
	 */
	public function set($property, $value = null)
	{
		$isFactory = is_object($value) && method_exists($value, '__invoke');

		if ($isFactory)
		{
			$this->closures[$property] = $value;
		}
		else
		{
			$this->{$property} = $value;
		}

		return $this;
	}

	/**
	 * @param   string $name      name
	 * @param   mixed  $arguments arguments
	 *
	 * @return mixed
	 * @throws InvalidArgumentException
	 * @since Kunena
	 */
	public function __call($name, $arguments)
	{
		throw new InvalidArgumentException(sprintf('Method %s() is not defined', $name));
	}

	/**
	 * Property overloading.
	 *
	 * @param   mixed $property properties
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function __isset($property)
	{
		return array_key_exists($property, $this->closures);
	}

	/**
	 * Set the object properties based on a named array/hash.
	 *
	 * @param   mixed $properties Either an associative array or another object.
	 *
	 * @return KunenaLayout|KunenaLayoutBase
	 *
	 * @see     set()
	 * @throws \InvalidArgumentException
	 * @since   Kunena
	 */
	public function setProperties($properties)
	{
		if (!is_array($properties) && !is_object($properties))
		{
			throw new \InvalidArgumentException('Parameter should be either array or an object.');
		}

		foreach ((array) $properties as $k => $v)
		{
			// Use the set function which might be overridden.
			if ($k[0] != "\0")
			{
				$this->set($k, $v);
			}
		}

		return $this;
	}

	/**
	 * Returns an associative array of public object properties.
	 *
	 * @return  array
	 * @since Kunena
	 */
	public function getProperties()
	{
		$properties = (array) $this;
		$list       = array();

		foreach ($properties as $property => $value)
		{
			if ($property[0] != "\0")
			{
				$list[$property] = $value;
			}
		}

		return $list;
	}

	/**
	 * Display layout from current layout.
	 *
	 * By using $this->subLayout() instead of KunenaLayout::factory() you can make your template files both
	 * easier to read and gain some context awareness -- for example possibility to use setLayout().
	 *
	 * @param   string $path path
	 *
	 * @return \Joomla\CMS\Layout\BaseLayout|KunenaLayout
	 * @throws Exception
	 * @since Kunena
	 */
	public function subLayout($path)
	{
		return self::factory($path)
			->setLayout($this->getLayout())
			->setOptions($this->getOptions());
	}

	/**
	 * Returns layout class.
	 *
	 * <code>
	 *    // Output pagination/pages layout with current cart instance.
	 *    echo KunenaLayout::factory('Pagination/Pages')->set('pagination', $this->pagination);
	 * </code>
	 *
	 * @param   mixed  $paths String or array of strings.
	 * @param   string $base  Base path.
	 *
	 * @return  KunenaLayout
	 * @throws Exception
	 * @since Kunena
	 */
	public static function factory($paths, $base = 'layouts')
	{
		$paths = (array) $paths;

		$app = Factory::getApplication();

		// Add all paths for the template overrides.
		if ($app->isClient('administrator'))
		{
			$template = KunenaFactory::getAdminTemplate();
		}
		else
		{
			$template = KunenaFactory::getTemplate();
		}

		$templatePaths = array();

		foreach ($paths as $path)
		{
			if (!$path)
			{
				continue;
			}

			$path   = (string) preg_replace('|\\\|', '/', strtolower($path));
			$lookup = $template->getTemplatePaths("{$base}/{$path}", true);

			foreach ($lookup as $loc)
			{
				array_unshift($templatePaths, $loc);
			}
		}

		// Go through all the matching layouts.
		$path = 'Undefined';

		foreach ($paths as $path)
		{
			if (!$path)
			{
				continue;
			}

			// Attempt to load layout class if it doesn't exist.
			$class = 'KunenaLayout' . (string) preg_replace('/[^A-Z0-9_]/i', '', $path);

			if (!class_exists($class))
			{
				$fpath    = (string) preg_replace('|\\\|', '/', strtolower($path));
				$filename = JPATH_BASE . "/components/com_kunena/layout/{$fpath}.php";

				if (!is_file($filename))
				{
					continue;
				}

				require_once $filename;
			}

			// Create layout object.
			return new $class($path, $templatePaths);
		}

		// Create default layout object.
		return new KunenaLayout($path, $templatePaths);
	}

	/**
	 * Display arbitrary MVC triad from current layout.
	 *
	 * By using $this->subRequest() instead of KunenaRequest::factory() you can make your template files both
	 * easier to read and gain some context awareness.
	 *
	 * @param   string              $path    path
	 * @param   \Joomla\Input\Input $input   input
	 * @param   mixed               $options options
	 *
	 * @return KunenaControllerDisplay|KunenaLayout
	 * @since Kunena
	 */
	public function subRequest($path, \Joomla\Input\Input $input = null, $options = null)
	{
		return KunenaRequest::factory($path . '/Display', $input, $options)
			->setLayout($this->getLayout());
	}
}
