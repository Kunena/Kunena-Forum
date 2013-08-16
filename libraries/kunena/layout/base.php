<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Categories
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
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
	protected $name = '';

	/**
	 * The view layout.
	 *
	 * @var    string
	 */
	protected $layout = 'default';

	/**
	 * The paths queue.
	 *
	 * @var    SplPriorityQueue
	 */
	protected $paths;

	/**
	 * Support for closure variables.
	 *
	 * @var array
	 */
	protected $closures = array();

	/**
	 * Content to be appended after the main output.
	 *
	 * @var array
	 */
	protected $after = array();

	/**
	 * Method to instantiate the layout.
	 *
	 * @param	string			$name
	 * @param   SplPriorityQueue  $paths  The paths queue.
	 */
	public function __construct($name, SplPriorityQueue $paths = null)
	{
		// Setup dependencies.
		$this->name = $name;
		$this->paths = isset($paths) ? $paths : $this->loadPaths();
	}

	/**
	 * Magic toString method that is a proxy for the render method.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		try {
			return (string) $this->render();
		} catch (Exception $e) {
			// Exceptions aren't allowed in string conversion, log the error and output it as a string.
			$trace = $e->getTrace();
			foreach ($trace as $caller) {
				if (isset($caller['class']) && isset($caller['function'])
					&& $caller['function'] == '__toString' && $caller['class'] == __CLASS__) {
					break;
				}
			}

			$error  = "Fatal Error in layout {$this->name}: {$e->getMessage()}";
			$error .= " in {$trace[0]['file']} on line {$trace[0]['line']}";
			if (isset($caller['file'])) $error .= " called from {$caller['file']} on line {$caller['line']}";
			JLog::add($error, JLog::CRITICAL, 'kunena');

			$error = "<b>Fatal Error</b> in layout <b>{$this->name}</b>: {$e->getMessage()}";
			if (JDEBUG) {
				$error .= " in <b>{$trace[0]['file']}</b> on line {$trace[0]['line']}<br />";
				if (isset($caller['file'])) $error .= "Layout was rendered in <b>{$caller['file']}</b> on line {$caller['line']}";
			} else {
				$error .= '. Please enable debug mode for more information.';
			}
			return '<br />'.$error.'<br />';
		}
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
		KUNENA_PROFILER ? KunenaProfiler::instance()->start("render layout '{$this->name}'") : null;

		// Get the layout path.
		if (!$layout) $layout = $this->getLayout();
		$path = $this->getPath($layout);

		// Check if the layout path was found.
		if (!$path) {
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop("render layout '{$this->name}'") : null;
			throw new RuntimeException("Layout Path For '{$this->name}:{$layout}' Not Found");
		}

		try {
			// Start an output buffer.
			ob_start();

			// Load the layout.
			include $path;

			// And get the contents.
			$output = ob_get_clean();

		} catch (Exception $e) {
			// Flush the contents and re-throw the exception.
			ob_end_clean();
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop("render layout '{$this->name}'") : null;
			throw $e;
		}

		if (JDEBUG || KunenaConfig::getInstance()->get('debug')) {
			$output = trim($output);
			$output = "\n<!-- START {$path} -->\n{$output}\n<!-- END {$path} -->\n";
		}

		foreach ($this->after as $content) {
			$output .= (string) $content;
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop("render layout '{$this->name}'") : null;
		return $output;
	}

	/**
	 * Load a template file.
	 *
	 * @param   string  $tpl  The name of the template source file.
	 *
	 * @return  string  The output of the the template file.
	 *
	 * @throws  Exception
	 */
	public function loadTemplate($tpl = null)
	{
		return $this->render("{$this->name}_{$tpl}");
	}

	/**
	 * Append HTML after the layout content.
	 *
	 * @param  string  $content
	 */
	public function appendAfter($content) {
		$this->after[] = $content;
	}

	/**
	 * Add stylesheet to the document.
	 *
	 * @param $filename
	 * @return mixed
	 */
	public function addStyleSheet($filename) {
		return KunenaFactory::getTemplate()->addStyleSheet ( $filename );
	}

	/**
	 * Add script to the document.
	 *
	 * @param $filename
	 * @return mixed
	 */
	public function addScript($filename) {
		return KunenaFactory::getTemplate()->addScript ( $filename );
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
		if (!$layout) {
			$layout = $this->getLayout();
		}

		$paths = array();
		foreach (clone $this->paths as $path) {
			$paths[] = $path;
		}
		// Find the layout file path.
		$path = JPath::find($paths, "{$layout}.php");
		if (!$path) $path = JPath::find($paths, 'default.php');

		return $path;
	}

	/**
	 * Method to get the view paths.
	 *
	 * @return  SplPriorityQueue  The paths queue.
	 */
	public function getPaths()
	{
		return $this->paths;
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
	 * @param   SplPriorityQueue  $paths  The paths queue.
	 *
	 * @return  KunenaLayout  Method supports chaining.
	 */
	public function setPaths(SplPriorityQueue $paths)
	{
		$this->paths = $paths;

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
		if ($isFactory) {
			$this->closures[$property] = $value;
		} else {
			$this->$property = $value;
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
		 if (!array_key_exists($property, $this->closures)) {
			 if (JDEBUG) {
			 	throw new InvalidArgumentException(sprintf('Property "%s" is not defined', $property));
		 	} else {
				 return null;
		 	}
        }

        return $this->closures[$property]();
	}

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
		if (!is_array($properties) && !is_object($properties)) {
			throw new \InvalidArgumentException('Parameter should be either array or an object.');
		}

		foreach ((array) $properties as $k => $v) {
			// Use the set function which might be overridden.
			if ($k[0] != "\0") $this->set($k, $v);
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
		foreach ($properties as $property=>$value) {
			if ($property[0] != "\0") $list[$property] = $value;
		}
		return $list;
	}

	/**
	 * Method to load the paths queue.
	 *
	 * @return  SplPriorityQueue  The paths queue.
	 */
	protected function loadPaths()
	{
		return new SplPriorityQueue();
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
		return self::factory($path)->setLayout($this->getLayout());
	}

	/**
	 * Display arbitrary MVC triad from current layout.
	 *
	 * By using $this->subLayout() instead of KunenaRequest::factory() you can make your template files both
	 * easier to read and gain some context awareness.
	 *
	 * @param   $path
	 * @return  KunenaControllerDisplay
	 */
	public function subRequest($path)
	{
		return KunenaRequest::factory($path.'/Display')->set('layout', $this->getLayout());
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
	 * @return  KunenaLayout
	 */
	public static function factory($paths) {
		$paths = (array) $paths;

		$app = JFactory::getApplication();
		// Add all paths for the template overrides.
		$templatePaths = new SplPriorityQueue();
		if ($app->isAdmin()) {
			$template = KunenaFactory::getAdminTemplate();
		} else {
			$template = KunenaFactory::getTemplate();
		}
		$base = 'layouts';

		foreach ($paths as $path) {
			if (!$path) continue;

			$path = (string) preg_replace('|\\\|', '/', strtolower($path));
			$lookup = $template->getTemplatePaths("{$base}/{$path}", true);
			foreach ($lookup as $loc) {
				$templatePaths->insert($loc, 1);
			}
		}

		// Go through all the matching layouts.
		$path = 'Undefined';
		foreach ($paths as $path) {
			if (!$path) continue;

			// Attempt to load layout class if it doesn't exist.
			$class = 'KunenaLayout' . (string) preg_replace('/[^A-Z0-9_]/i', '', $path);
			if (!class_exists($class)) {
				$fpath = (string) preg_replace('|\\\|', '/', strtolower($path));
				$filename = JPATH_BASE . "/components/com_kunena/layout/{$fpath}.php";
				if (!is_file($filename)) {
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
