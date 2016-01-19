<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Categories
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Implements Kunena layouts for the views.
 *
 * This class is part of Kunena HMVC implementation, allowing calls to
 * any layout file.
 *
 * <code>
 *	echo KunenaLayout::factory('Pagination')->set('pagination', $this->pagination);
 *	echo KunenaLayout::factory('Pagination/Footer')->set('pagination', $this->pagination);
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
 *	echo KunenaLayout::factory('Pagination')->set('pagination', $this->pagination)->setLayout('mini');
 * </code>
 *
 * @see KunenaRequest
 */
class KunenaLayoutBase extends KunenaCompatLayoutBase
{
	/**
	 * Layout name.
	 * @var string
	 */
	protected $_name = '';

	/**
	 * The view layout.
	 *
	 * @var    string
	 */
	protected $layout = 'default';

	/**
	 * The paths queue.
	 *
	 * @var    array
	 */
	protected $includePaths;

	/**
	 * Support for closure variables.
	 *
	 * @var array
	 */
	protected $closures = array();
	protected $debug;

	/**
	 * Method to instantiate the layout.
	 *
	 * @param	string			$name
	 * @param   array  $paths  The paths queue.
	 */
	public function __construct($name, array $paths = null)
	{
		// Setup dependencies.
		$this->_name = $name;
		$this->includePaths = isset($paths) ? $paths : $this->loadPaths();
		$this->debug = JDEBUG || KunenaConfig::getInstance()->get('debug');
	}

	/**
	 * Magic toString method that is a proxy for the render method.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		try
		{
			return (string) $this->render();
		}
		catch (Exception $e)
		{
			return $this->renderError($e);
		}
	}

	/**
	 * Dirty function to debug layout/path errors
	 *
	 * @return  string
	 */
	public function debugInfo()
	{
		$rawPath  = strtolower(str_replace('.', '/', $this->_name)) .'/'. $this->layout . '.php';

		$html = "<pre>";
		$html .= '<strong>Layout:</strong> ' . $this->_name . '<br />';
		$html .= '<strong>Template:</strong> ' . $this->layout . '.php<br />';
		$html .= '<strong>RAW Layout path:</strong> ' . $rawPath . '<br>';
		$html .= '<strong>includePaths:</strong> ';
		$html .= print_r($this->includePaths, true);
		$html .= '<strong>Checking paths:</strong> <br />';

		foreach ($this->includePaths as $path)
		{
			$file = $path .'/'. $this->layout . '.php';;

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
	 * Method to render the view.
	 *
	 * @param   string  Layout.
	 *
	 * @return  string  The rendered view.
	 *
	 * @throws  Exception|RunTimeException
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
	 * Set/override debug mode.
	 *
	 * @param array $data
	 *
	 * @return KunenaLayoutBase Instance of $this to allow chaining.
	 * @throws Exception
	 * @internal param bool $value
	 *
	 */
	public function debug($data = array())
	{
		$this->debug = (bool) $data;

		return $this;
	}

	public function renderError(Exception $e)
	{
		// Exceptions aren't allowed in string conversion, log the error and output it as a string.
		$trace = $e->getTrace();
		$location = null;

		foreach ($trace as $caller)
		{
			if (!$location && isset($caller['file']) && !strstr($caller['file'], '/libraries/'))
			{
				$location = $caller;
			}

			if (isset($caller['class']) && isset($caller['function'])
				&& $caller['function'] == '__toString' && $caller['class'] == __CLASS__)
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

		$error  = "Rendering Error in layout {$this->_name}: {$e->getMessage()}";
		$error .= " in {$location['file']} on line {$location['line']}";

		if (isset($caller['file']))
		{
			$error .= " called from {$caller['file']} on line {$caller['line']}";
		}

		JLog::add($error, JLog::CRITICAL, 'kunena');

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

		return '<br />'.$error.'<br />';
	}

	/**
	 * Load a template file.
	 *
	 * @param   string  $tpl  The name of the template source file.
	 *
	 * @return  string  The output of the the template file.
	 *
	 * @throws  Exception
	 * @deprecated
	 */
	public function loadTemplate($tpl = null)
	{
		return $this->render("{$this->_name}_{$tpl}");
	}

	/**
	 * Add stylesheet to the document.
	 *
	 * @param $filename
	 * @return mixed
	 */
	public function addStyleSheet($filename)
	{
		return KunenaFactory::getTemplate()->addStyleSheet($filename);
	}

	/**
	 * Add script to the document.
	 *
	 * @param $filename
	 * @return mixed
	 */
	public function addScript($filename)
	{
		return KunenaFactory::getTemplate()->addScript($filename);
	}

	/**
	 * Add script declaration to the document.
	 *
	 * @param        $content
	 * @param string $type
	 *
	 * @return mixed
	 * @internal param $filename
	 */
	public function addScriptDeclaration($content, $type = 'text/javascript')
	{
		return KunenaFactory::getTemplate()->addScriptDeclaration($content, $type);
	}

	/**
	 * Method to get the view layout.
	 *
	 * @return  string  The layout name.
	 */
	public function getLayout()
	{
		$layout = preg_replace('/[^a-z0-9_]/', '', strtolower($this->layout));

		return $layout ? $layout : 'default';
	}

	/**
	 * Method to get the layout path. If layout file isn't found, fall back to default layout.
	 *
	 * @param   string  $layout  The layout name, defaulting to the current one.
	 *
	 * @return  mixed  The layout file name if found, false otherwise.
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
	 * Method to get the view paths.
	 *
	 * @return  array  The paths queue.
	 */
	public function getPaths()
	{
		return $this->includePaths;
	}

	/**
	 * Method to set the view layout.
	 *
	 * @param   string  $layout  The layout name.
	 *
	 * @return  KunenaLayout  Method supports chaining.
	 */
	public function setLayout($layout)
	{
		if (!$layout) $layout = 'default';
		$this->layout = $layout;

		return $this;
	}

	/**
	 * Method to set the view paths.
	 *
	 * @param   string  $path  The paths queue.
	 *
	 * @return  KunenaLayout  Method supports chaining.
	 */
	public function setPath($path)
	{
		array_unshift($this->includePaths, $path);

		return $this;
	}

	/**
	 * Method to set the view paths.
	 *
	 * @param   array  $paths  The paths queue.
	 *
	 * @return  KunenaLayout  Method supports chaining.
	 */
	public function setPaths(array $paths)
	{
		$this->includePaths = $paths;

		return $this;
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $value     The value of the property to set.
	 *
	 * @return  KunenaLayout  Method supports chaining.
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
	 * Property overloading.
	 *
	 * @param $property
	 * @param $value
	 */
	public function __set($property, $value)
	{
		$this->set($property, $value);
	}

	/**
	 * Property overloading.
	 *
	 * @param $property
	 * @return mixed
	 * @throws InvalidArgumentException
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
				 return null;
			}
		}

		return $this->closures[$property]();
	}

	/**
	 * @param $name
	 * @param $arguments
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	public function __call($name, $arguments)
	{
		throw new InvalidArgumentException(sprintf('Method %s() is not defined', $name));
	}

	/**
	 * Property overloading.
	 *
	 * @param $property
	 * @return bool
	 */
	public function __isset($property)
	{
		return array_key_exists($property, $this->closures);
	}

	/**
	 * Set the object properties based on a named array/hash.
	 *
	 * @param   mixed  $properties  Either an associative array or another object.
	 *
	 * @return  KunenaLayout  Method supports chaining.
	 *
	 * @see     set()
	 * @throws \InvalidArgumentException
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
	 */
	public function getProperties()
	{
		$properties = (array) $this;
		$list = array();

		foreach ($properties as $property=>$value)
		{
			if ($property[0] != "\0")
			{
				$list[$property] = $value;
			}
		}

		return $list;
	}

	/**
	 * Method to load the paths queue.
	 *
	 * @return  array  The paths queue.
	 */
	protected function loadPaths()
	{
		return array();
	}

	/**
	 * Display layout from current layout.
	 *
	 * By using $this->subLayout() instead of KunenaLayout::factory() you can make your template files both
	 * easier to read and gain some context awareness -- for example possibility to use setLayout().
	 *
	 * @param   $path
	 * @return  KunenaLayout
	 */
	public function subLayout($path)
	{
		return self::factory($path)
			->setLayout($this->getLayout())
			->setOptions($this->getOptions());
	}

	/**
	 * Display arbitrary MVC triad from current layout.
	 *
	 * By using $this->subRequest() instead of KunenaRequest::factory() you can make your template files both
	 * easier to read and gain some context awareness.
	 *
	 * @param   $path
	 * @param   $input
	 * @param   $options
	 *
	 * @return  KunenaControllerDisplay
	 */
	public function subRequest($path, Jinput $input = null, $options = null)
	{
		return KunenaRequest::factory($path.'/Display', $input, $options)
			->setLayout($this->getLayout());
	}

	/**
	 * Returns layout class.
	 *
	 * <code>
	 *	// Output pagination/pages layout with current cart instance.
	 *	echo KunenaLayout::factory('Pagination/Pages')->set('pagination', $this->pagination);
	 * </code>
	 *
	 * @param   mixed $paths String or array of strings.
	 * @param   string $base Base path.
	 * @return  KunenaLayout
	 */
	public static function factory($paths, $base = 'layouts')
	{
		$paths = (array) $paths;

		$app = JFactory::getApplication();

		// Add all paths for the template overrides.
		if ($app->isAdmin())
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

			$path = (string) preg_replace('|\\\|', '/', strtolower($path));
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
				$fpath = (string) preg_replace('|\\\|', '/', strtolower($path));
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
}
