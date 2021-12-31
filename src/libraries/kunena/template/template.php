<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Template
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Object\CMSObject;

jimport('joomla.html.parameter');

/**
 * Kunena Users Table Class
 * Provides access to the #__kunena_users table
 * @since Kunena
 */
class KunenaTemplate extends CMSObject
{
	/**
	 * @var array
	 * @since Kunena
	 */
	protected static $_instances = array();

	/**
	 * @var null|string
	 * @since Kunena
	 */
	public $name = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $params = null;

	/**
	 * @var bool|int
	 * @since Kunena
	 */
	public $paramstime = false;

	/**
	 * @var array
	 * @since Kunena
	 */
	public $topicIcons = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	public $categoryIcons = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $pathTypes = array(
		'emoticons'     => 'media/emoticons',
		'ranks'         => 'media/ranks',
		'icons'         => 'media/icons',
		'categoryicons' => 'media/category_icons',
		'images'        => 'media/images',
		'js'            => 'media/kunena/core/js',
		'css'           => 'media/kunena/core/css',
	);

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $pathTypeDefaults = array(
		'avatars'       => 'media/avatars',
		'emoticons'     => 'media/emoticons',
		'ranks'         => 'media/ranks',
		'icons'         => 'media/icons',
		'topicicons'    => 'media/topic_icons',
		'categoryicons' => 'media/category_icons',
		'images'        => 'media/images',
		'js'            => 'media/js',
		'css'           => 'media/css',
	);

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $pathTypeOld = array(
		'avatars'    => 'images/avatars',
		'emoticons'  => 'images/emoticons',
		'ranks'      => 'images/ranks',
		'icons'      => 'images/icons',
		'topicicons' => 'images/topicicons',
		'images'     => 'images',
		'js'         => 'js',
		'css'        => 'css',
	);

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $default = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $paths = array();

	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected $css_compile = true;

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $filecache = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $smileyPath = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $rankPath = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $userClasses = array(
		'kwho-',
		'admin'     => 'kwho-admin',
		'globalmod' => 'kwho-globalmoderator',
		'moderator' => 'kwho-moderator',
		'user'      => 'kwho-user',
		'guest'     => 'kwho-guest',
		'banned'    => 'kwho-banned',
		'blocked'   => 'kwho-blocked',
	);

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $stylesheets = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $style_variables = array();

	/**
	 * @var null
	 * @since Kunena
	 */
	protected $compiled_style_variables = null;

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $scripts = array();

	/**
	 * @var null|SimpleXMLElement
	 * @since Kunena
	 */
	protected $xml = null;

	/**
	 * @var string
	 * @since Kunena
	 */
	protected $map;

	/**
	 * @var string
	 * @since Kunena
	 */
	protected $hmvc;

	/**
	 * @var string
	 * @since Kunena
	 */
	protected $category_iconset = '';

	/**
	 * Constructor
	 *
	 * @access    protected
	 *
	 * @param   null $name name
	 *
	 * @throws Exception
	 * @since     Kunena
	 */
	public function __construct($name = null)
	{
		if (!$name)
		{
			$name = KunenaFactory::getConfig()->template;
		}

		$name = KunenaPath::clean($name);

		// Create template inheritance
		if (!is_array($this->default))
		{
			$this->default = (array) $this->default;
		}

		array_unshift($this->default, $name);
		$this->default[] = 'system';

		$this->default = array_unique($this->default);

		// Find configuration file.
		$xml_path = KPATH_SITE . "/template/{$name}/config/config.xml";

		if (!is_file($xml_path))
		{
			// Configuration file was not found - legacy template support.
			$xml_path = KPATH_SITE . "/template/{$name}/config/template.xml";
		}

		$ini     = KPATH_SITE . "/template/{$name}/config/params.ini";
		$content = '';
		$format  = 'INI';

		if (is_readable($ini))
		{
			$this->paramstime = filemtime($ini);
			$content          = file_get_contents($ini);

			// Workaround a bug in previous versions (file may contain JSON).

			if ($content && $content[0] == '{')
			{
				$format = 'JSON';
			}
		}

		$this->name = $name;

		$this->params = new \Joomla\Registry\Registry;
		$this->params->loadString($content, $format);

		// Load default values from configuration definition file.
		$this->xml = simplexml_load_file($xml_path);

		if ($this->xml)
		{
			foreach ($this->xml->xpath('//field') as $node)
			{
				if (isset($node['name']) && isset($node['default']))
				{
					$this->params->def($node['name'], (string) $node['default']);
				}
			}

			// Generate CSS variables for less compiler.
			foreach ($this->params->toArray() as $key => $value)
			{
				if (substr($key, 0, 5) == 'style' && $value)
				{
					$this->style_variables[$key] = $value;
				}
			}
		}

		// Set lookup paths.
		$this->pathTypes += $this->isHmvc() ? $this->pathTypeDefaults : $this->pathTypeOld;

		$view = Factory::getApplication()->input->get('option');

		if ($view == 'com_kunena')
		{
			// Set active class on menu item alias.
			if (KunenaConfig::getInstance()->activemenuitem)
			{
				$id = htmlspecialchars(KunenaConfig::getInstance()->activemenuitem, ENT_COMPAT, 'UTF-8');
				$this->addScriptDeclaration("
		jQuery(function($){ $(\"$id\").addClass('active')});");
			}
			else
			{
				$Itemid = KunenaRoute::fixMissingItemID();
				$items  = Factory::getApplication()->getMenu('site')->getItems('link', 'index.php?Itemid=' . $Itemid);

				if ($items)
				{
					$id = htmlspecialchars('.item-' . $items[0]->id, ENT_COMPAT, 'UTF-8');
					$this->addScriptDeclaration("
		jQuery(function($){ $(\"$id\").addClass('active')});");
				}
			}
		}
	}

	/**
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function isHmvc()
	{
		$app = Factory::getApplication();

		if (is_null($this->hmvc))
		{
			if (is_dir(JPATH_THEMES . "/{$app->getTemplate()}/com_kunena/pages"))
			{
				$this->hmvc = is_dir(JPATH_THEMES . "/{$app->getTemplate()}/com_kunena/pages");
			}
			else
			{
				$this->hmvc = is_dir(KPATH_SITE . "/template/{$this->name}/pages");
			}
		}

		return $this->hmvc;
	}

	/**
	 * Returns the global KunenaTemplate object, only creating it if it doesn't already exist.
	 *
	 * @access    public
	 *
	 * @param   int $name Template name or null for default/selected template in your configuration
	 *
	 * @return    KunenaTemplate    The template object.
	 * @throws Exception
	 * @since     1.6
	 */
	public static function getInstance($name = null)
	{
		$app = Factory::getApplication();

		if (!$name)
		{
			$name = Factory::getApplication()->input->cookie->getString('kunena_template', KunenaFactory::getConfig()->template);
		}

		$name = KunenaPath::clean($name);

		if (empty(self::$_instances[$name]))
		{
			// Find overridden template class (use $templatename to avoid creating new objects if the template doesn't exist)
			$templatename = $name;
			$classname    = "KunenaTemplate{$templatename}";

			if (!is_file(KPATH_SITE . "/template/{$templatename}/config/template.xml")
				&& !is_file(KPATH_SITE . "/template/{$templatename}/config/config.xml")
			)
			{
				// If template xml doesn't exist, raise warning and use Crypsis instead
				$file         = JPATH_THEMES . "/{$app->getTemplate()}/html/com_kunena/template.php";
				$templatename = 'crypsis';
				$classname    = "KunenaTemplate{$templatename}";

				if (is_dir(KPATH_SITE . "/template/{$templatename}"))
				{
					KunenaError::warning(Text::sprintf('COM_KUNENA_LIB_TEMPLATE_NOTICE_INCOMPATIBLE', $name, $templatename));
				}
			}

			if (!class_exists($classname) && $app->isClient('site'))
			{
				$file = KPATH_SITE . "/template/{$templatename}/template.php";

				if (!is_file($file))
				{
					$classname = "KunenaTemplateCrypsis";
					$file      = KPATH_SITE . "/template/crypsis/template.php";
				}

				if (is_file($file))
				{
					require_once $file;
				}
			}

			if (class_exists($classname))
			{
				self::$_instances [$name] = new $classname($templatename);
			}
			else
			{
				self::$_instances [$name] = new KunenaTemplate($templatename);
			}
		}

		return self::$_instances [$name];
	}

	/**
	 * getconfigxml
	 *
	 * @return boolean|mixed|string
	 * @since Kunena
	 */
	public function getConfigXml()
	{
		// Find configuration file.
		$xml_path = KPATH_SITE . "/template/{$this->name}/config/config.xml";

		if (!is_file($xml_path))
		{
			$this->xml_path = KPATH_SITE . "/template/{$this->name}/config/template.xml";

			return false;
		}

		$xml = file_get_contents($xml_path);

		if (!strstr($xml, '<config>'))
		{
			// Update old template files to new format.
			$xml = preg_replace(
				array('|<params|', '|</params>|', '|<param\s+|', '|</param>|'),
				array('<config', '</config>', '<field ', '</field>'), $xml
			);
		}

		return $xml;
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @return void
	 */
	public function initialize()
	{
		$this->loadLanguage();
		$config = KunenaFactory::getConfig();

		if ($config->sef)
		{
			$sef = '/forum';
		}
		else
		{
			$sef = '/index.php?option=com_kunena';
		}

		if (strpos($_SERVER['REQUEST_URI'], $sef) !== false)
		{
			$isForumActive = true;
		}
		else
		{
			$isForumActive = false;
		}

		if ($isForumActive)
		{
			$this->addScriptDeclaration('jQuery(document).ready(function ($) {
				$(".current").addClass("active alias-parent-active");
				$(".alias-parent-active").addClass("active alias-parent-active");
			});
			');
		}
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @return void
	 */
	public function loadLanguage()
	{
		// Loading language strings for the template
		$lang = Factory::getLanguage();
		KunenaFactory::loadLanguage('com_kunena.templates', 'site');

		foreach (array_reverse($this->default) as $template)
		{
			$lang->load('kunena_tmpl_' . $template, JPATH_SITE)
			|| $lang->load('kunena_tmpl_' . $template, KPATH_SITE)
			|| $lang->load('kunena_tmpl_' . $template, KPATH_SITE . '/template/' . $template);
		}
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @return void
	 */
	public function initializeBackend()
	{
		$this->loadLanguage();
	}

	/**
	 * @return array
	 * @since Kunena
	 */
	public function getUserClasses()
	{
		return $this->userClasses;
	}

	/**
	 * @param   string $link  link
	 * @param   string $name  name
	 * @param   string $scope scope
	 * @param   string $type  type
	 * @param   null   $id    id
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getButton($link, $name, $scope, $type, $id = null)
	{
		$types = array('communication' => 'comm', 'user' => 'user', 'moderation' => 'mod');
		$names = array('unsubscribe' => 'subscribe', 'unfavorite' => 'favorite', 'unsticky' => 'sticky', 'unlock' => 'lock', 'create' => 'newtopic',
		               'quickreply'  => 'reply', 'quote' => 'kquote', 'edit' => 'kedit', );

		$text  = Text::_("COM_KUNENA_BUTTON_{$scope}_{$name}");
		$title = Text::_("COM_KUNENA_BUTTON_{$scope}_{$name}_LONG");

		if ($title == "COM_KUNENA_BUTTON_{$scope}_{$name}_LONG")
		{
			$title = '';
		}

		if ($id)
		{
			$id = 'id="' . $id . '"';
		}

		if (isset($types[$type]))
		{
			$type = $types[$type];
		}

		if ($name == 'quickreply')
		{
			$type .= ' kqreply';
		}

		if (isset($names[$name]))
		{
			$name = $names[$name];
		}

		return <<<HTML
					<a {$id} class="kicon-button kbutton{$type} btn-left" href="{$link}" rel="nofollow" title="{$title}">
						<span class="{$name}"><span>{$text}</span></span>
					</a>
HTML;
	}

	/**
	 * @param   string $name  name
	 * @param   string $title title
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getIcon($name, $title = '')
	{
		return '<span class="kicon ' . $name . '" title="' . $title . '"></span>';
	}

	/**
	 * @param   string $image image
	 * @param   string $alt   alt
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getImage($image, $alt = '')
	{
		return '<img src="' . $this->getImagePath($image) . '" alt="' . $alt . '" />';
	}

	/**
	 * @param   string $filename filename
	 * @param   bool   $url      url
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getImagePath($filename = '', $url = true)
	{
		return $this->getFile($filename, $url, $this->pathTypes['images'], 'media/kunena/images');
	}

	/**
	 * @param   string $file     file
	 * @param   bool   $url      url
	 * @param   string $basepath basepath
	 * @param   null   $default  default
	 * @param   null   $ignore   ignore
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getFile($file, $url = false, $basepath = '', $default = null, $ignore = null)
	{
		if ($basepath)
		{
			$basepath = '/' . $basepath;
		}

		$filepath = "{$basepath}/{$file}";

		if (!isset($this->filecache[$filepath]))
		{
			$this->filecache[$filepath] = $default ? "{$default}/{$file}" : KPATH_COMPONENT_RELATIVE . "/template/{$this->name}/{$file}";

			foreach ($this->default as $template)
			{
				if ($template == $ignore)
				{
					continue;
				}

				$path = "template/{$template}{$basepath}";

				if (is_file(KPATH_SITE . "/{$path}/{$file}"))
				{
					$this->filecache[$filepath] = KPATH_COMPONENT_RELATIVE . "/{$path}/{$file}";
					break;
				}
			}
		}

		return ($url ? Uri::root(true) . '/' : '') . $this->filecache[$filepath];
	}

	/**
	 * @param   mixed $list list
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getPaginationListFooter($list)
	{
		$html = '<div class="list-footer">';
		$html .= '<div class="limit">' . Text::_('COM_KUNENA_LIB_HTML_DISPLAY_NUM') . ' ' . $list['limitfield'] . '</div>';
		$html .= $list['pageslinks'];
		$html .= '<div class="counter">' . $list['pagescounter'] . '</div>';
		$html .= '<input type="hidden" name="' . $list['prefix'] . 'limitstart" value="' . $list['limitstart'] . '" />';
		$html .= '</div>';

		return $html;
	}

	/**
	 * @param   mixed $list list
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getPaginationListRender($list)
	{
		$html = '<ul class="kpagination">';
		$html .= '<li class="page">' . Text::_('COM_KUNENA_PAGE') . '</li>';
		$last = 0;

		foreach ($list['pages'] as $i => $page)
		{
			if ($last + 1 != $i)
			{
				$html .= '<li>...</li>';
			}

			$html .= '<li>' . $page['data'] . '</li>';
			$last = $i;
		}

		$html .= '</ul>';

		return $html;
	}

	/**
	 * @param   string $item item
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getPaginationItemActive($item)
	{
		return '<a title="' . $item->text . '" href="' . $item->link . '" class="pagenav">' . $item->text . '</a>';
	}

	/**
	 * @param   string $item item
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getPaginationItemInactive($item)
	{
		return '<span class="pagenav">' . $item->text . '</span>';
	}

	/**
	 * @param   string $class     class
	 * @param   string $class_sfx class_sfx
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getClass($class, $class_sfx = '')
	{
		return $class . ($class_sfx ? " {$class}.{$class_sfx}" : '');
	}

	/**
	 * @return array
	 * @since Kunena
	 */
	public function getStyleVariables()
	{
		return $this->style_variables;
	}

	/**
	 * @param   string $name    name
	 * @param   string $default default
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getStyleVariable($name, $default = '')
	{
		return isset($this->style_variables[$name]) ? $this->style_variables[$name] : $default;
	}

	/**
	 * @param   string  $name  name
	 * @param   integer $value value
	 *
	 * @return mixed
	 * @since Kunena
	 */
	public function setStyleVariable($name, $value)
	{
		$this->compiled_style_variables = null;

		return $this->style_variables[$name] = $value;
	}

	/**
	 * @param   string $filename filename
	 * @param   string $group    group
	 *
	 * @return mixed
	 * @throws Exception
	 * @since Kunena
	 */
	public function addStyleSheet($filename, $group = 'forum')
	{
		$app    = Factory::getApplication();
		$format = $app->input->getCmd('format');

		if (!empty($format) && $format != 'html')
		{
			return;
		}

		if (!preg_match('|https?://|', $filename) && !preg_match('|media/jui|', $filename))
		{
			if (preg_match('/assets/', $filename))
			{
				$filename     = preg_replace('|^css/|u', '', $filename);
				$filename     = preg_replace('/^assets\//', '', $filename);
				$filename     = $this->getFile($filename, false, $this->pathTypes['css'], 'components/com_kunena/template/' . $this->name . '/assets');
			}
			elseif (preg_match('/kunena.css/', $filename) || preg_match('/kunena-custom.css/', $filename))
			{
				$filename     = preg_replace('|^css/|u', '', $filename);
				$filename     = preg_replace('/^assets\//', '', $filename);
				$filename = $this->getFile($filename, false, $this->pathTypes['css'], 'media/kunena/cache/' . $this->name . '/css');
			}
			else
			{
				$filename = $this->getFile($filename, false, $this->pathTypes['css'],  $this->pathTypes['css']);
			}

			$filemin      = $filename;
			$filemin_path = preg_replace('/\.css$/u', '-min.css', $filename);

			if (!JDEBUG && !KunenaFactory::getConfig()->debug && !KunenaForum::isDev() && is_file(JPATH_ROOT . "/$filemin_path"))
			{
				$filemin = preg_replace('/\.css$/u', '-min.css', $filename);
			}

			if (file_exists(JPATH_ROOT . "/$filemin"))
			{
				$filename = $filemin;
			}

			$filename = Uri::root(false) . $filename;
		}

		return HTMLHelper::_('stylesheet', $filename);
	}

	/**
	 * Set the LESS file into the document head
	 *
	 * @param   string $filename filename
	 *
	 * @return mixed
	 * @throws Exception
	 * @since Kunena 5.1.3
	 */
	public function addLessSheet($filename)
	{
		$app    = Factory::getApplication();
		$format = $app->input->getCmd('format');

		if (!empty($format) && $format != 'html')
		{
			return;
		}

		$filename = $this->getFile($filename, false, '', "media/kunena/cache/{$this->name}/css");

		return HTMLHelper::_('stylesheet', $filename);
	}

	/**
	 * @param $style
	 *
	 * @return string
	 *
	 * @throws Exception
	 * @since version
	 */
	public function addStyleDeclaration($style)
	{
		$app    = Factory::getApplication();
		$format = $app->input->getCmd('format');

		if (!empty($format) && $format != 'html')
		{
			return;
		}

		return Factory::getDocument()->addStyleDeclaration($style);
	}

	/**
	 * @param   string $filename  filename
	 * @param   string $condition condition
	 *
	 * @since Kunena
	 * @return void
	 */
	public function addIEStyleSheet($filename, $condition = 'IE')
	{
		$filename  = preg_replace('|^css/|u', '', $filename);
		$url       = $this->getFile($filename, true, $this->pathTypes['css'], 'media/kunena/css');
		$stylelink = "<!--[if {$condition}]>\n";
		$stylelink .= '<link rel="stylesheet" href="' . $url . '" />' . "\n";
		$stylelink .= "<![endif]-->\n";
		Factory::getDocument()->addCustomTag($stylelink);
	}

	/**
	 * @since Kunena
	 * @return void
	 */
	public function clearCache()
	{
		$path = JPATH_ROOT . "/media/kunena/cache/{$this->name}";

		if (is_dir($path))
		{
			KunenaFolder::delete($path);
		}
	}

	/**
	 * @param   string $filename filename
	 *
	 * @return string
	 * @throws Exception
	 * @since Kunena
	 */
	public function getCachePath($filename = '')
	{
		if ($filename)
		{
			$filename = '/' . $filename;
		}

		if (JDEBUG || KunenaFactory::getConfig()->debug)
		{
			$filename = "media/kunena/cache/{$this->name}/debug{$filename}";
		}
		else
		{
			$filename = "media/kunena/cache/{$this->name}{$filename}";
		}

		return $filename;
	}

	/**
	 * @param   mixed $matches matches
	 *
	 * @return string
	 * @since Kunena
	 */
	public function findUrl($matches)
	{
		$file = trim($matches[1], ' \'"');

		if (preg_match('#^../#', $file))
		{
			$file = $this->getFile(substr($file, 3), true, '', 'media/kunena');
		}

		return "url('{$file}')";
	}

	/**
	 * Wrapper to addScript
	 *
	 * @param   string $content content
	 * @param   string $type    type
	 *
	 * @return \Joomla\CMS\Document\Document
	 * @throws Exception
	 * @since Kunena
	 */
	public function addScriptDeclaration($content, $type = 'text/javascript')
	{
		$app    = Factory::getApplication();
		$format = $app->input->getCmd('format');

		if (!empty($format) && $format != 'html')
		{
			return;
		}

		return Factory::getDocument()->addScriptDeclaration($content, $type);
	}

	/**
	 * Wrapper to addScript
	 *
	 * @param   string $filename filename
	 *
	 * @param array    $options
	 * @param array    $attribs
	 *
	 * @return \Joomla\CMS\Document\Document
	 * @throws Exception
	 * @since Kunena
	 */
	public function addScript($filename, $options = array(), $attribs = array())
	{
		$app    = Factory::getApplication();
		$format = $app->input->getCmd('format');

		if (!empty($format) && $format != 'html')
		{
			return;
		}

		if (!preg_match('|https?://|', $filename))
		{
			if (preg_match('/assets/', $filename))
			{
				$filename     = preg_replace('|^js/|u', '', $filename);
				$filename     = preg_replace('/^assets\//', '', $filename);
				$filename     = $this->getFile($filename, false, $this->pathTypes['js'], 'components/com_kunena/template/' . $this->name . '/assets');
			}
			else
			{
				$filename = $this->getFile($filename, false, $this->pathTypes['js'],  $this->pathTypes['js']);
			}

			$filemin      = $filename;
			$filemin_path = preg_replace('/\.js$/u', '-min.js', $filename);

			if (!JDEBUG && !KunenaFactory::getConfig()->debug && !KunenaForum::isDev() && is_file(JPATH_ROOT . "/$filemin_path"))
			{
				$filemin = preg_replace('/\.js$/u', '-min.js', $filename);
			}

			if (file_exists(JPATH_ROOT . "/$filemin"))
			{
				$filename = $filemin;
			}

			$filename = Uri::root(false) . $filename;
		}

		return HTMLHelper::_('script', $filename, $options, $attribs);
	}

	/**
	 * Add option for script
	 *
	 * @param   string $key     Name in Storage
	 * @param   mixed  $options Scrip options as array or string
	 * @param   bool   $merge   Whether merge with existing (true) or replace (false)
	 *
	 * @return \Joomla\CMS\Document\Document
	 *
	 * @throws Exception
	 * @since   3.5
	 */
	public function addScriptOptions($key, $options, $merge = true)
	{
		$app    = Factory::getApplication();
		$format = $app->input->getCmd('format');

		if (!empty($format) && $format != 'html')
		{
			return;
		}

		return Factory::getDocument()->addScriptOptions($key, $options, $merge);
	}

	/**
	 * @param   string $path path
	 *
	 * @since Kunena
	 * @return void
	 */
	public function addPath($path)
	{
		$this->paths[] = KunenaPath::clean("/$path");
	}

	/**
	 * @param   string $path     path
	 * @param   bool   $fullpath fullpath
	 *
	 * @return array
	 * @throws Exception
	 * @since Kunena
	 */
	public function getTemplatePaths($path = '', $fullpath = false)
	{
		$app = Factory::getApplication();

		if ($path)
		{
			$path = KunenaPath::clean("/$path");
		}

		$array = array();

		foreach (array_reverse($this->default) as $template)
		{
			$array[] = ($fullpath ? KPATH_SITE : KPATH_COMPONENT_RELATIVE) . "/template/" . $template . $path;
			$array[] = ($fullpath ? JPATH_ROOT : JPATH_SITE) . "/templates/{$app->getTemplate()}/html/com_kunena" . $path;
		}

		foreach (array_reverse($this->paths) as $template)
		{
			$array[] = ($fullpath ? JPATH_SITE : '') . $template . $path;
		}

		return $array;
	}

	/**
	 * @param   string $filename filename
	 * @param   bool   $url      url
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getAvatarPath($filename = '', $url = false)
	{
		return $this->getFile($filename, $url, $this->pathTypes['avatars'], 'media/kunena/avatars');
	}

	/**
	 * @param   string $filename filename
	 * @param   bool   $url      url
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getSmileyPath($filename = '', $url = false)
	{
		return $this->getFile($filename, $url, $this->pathTypes['emoticons'], 'media/kunena/emoticons');
	}

	/**
	 * @param   string $filename filename
	 * @param   bool   $url      url
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getRankPath($filename = '', $url = false)
	{
		return $this->getFile($filename, $url, $this->pathTypes['ranks'], 'media/kunena/ranks');
	}

	/**
	 * @param   mixed $index index
	 * @param   bool  $url   url
	 *
	 * @return string
	 * @throws Exception
	 * @since Kunena
	 */
	public function getTopicIconIndexPath($index, $url = false)
	{
		if (empty($this->topicIcons))
		{
			$this->getTopicIcons(false, 0);
		}

		if (empty($this->topicIcons[$index]->published))
		{
			$index = 0;
		}

		$icon = $this->topicIcons[$index];

		return $this->getTopicIconPath($icon->filename, $url);
	}

	/**
	 * @param   bool $all     all
	 * @param   int  $checked checked
	 *
	 * @return array|SimpleXMLElement
	 * @throws Exception
	 * @since Kunena
	 */
	public function getTopicIcons($all = false, $checked = 0)
	{
		$category_iconset = $this->category_iconset;

		if (empty($this->topicIcons))
		{
			$xmlfile = JPATH_ROOT . '/media/kunena/topic_icons/' . $category_iconset . '/topicicons.xml';

			if (is_file($xmlfile))
			{
				$xml = simplexml_load_file($xmlfile);

				if (isset($xml->icons))
				{
					foreach ($xml->icons as $icons)
					{
						$type   = (string) $icons->attributes()->type;
						$width  = (int) $icons->attributes()->width;
						$height = (int) $icons->attributes()->height;

						foreach ($icons->icon as $icon)
						{
							$attributes = $icon->attributes();
							$icon       = new stdClass;
							$icon->id   = (int) $attributes->id;
							$icon->type = (string) $attributes->type ? (string) $attributes->type : $type;
							$icon->name = (string) $attributes->name;

							if ($icon->type != 'user')
							{
								$icon->id = $icon->type . '_' . $icon->name;
							}

							$icon->published             = (int) $attributes->published;
							$icon->title                 = (string) $attributes->title;
							$icon->b2                    = (string) $attributes->b2;
							$icon->b3                    = (string) $attributes->b3;
							$icon->b4                    = (string) $attributes->b4;
							$icon->fa                    = (string) $attributes->fa;
							$icon->filename              = (string) $attributes->src;
							$icon->width                 = (int) $attributes->width ? (int) $attributes->width : $width;
							$icon->height                = (int) $attributes->height ? (int) $attributes->height : $height;
							$icon->relpath               = $this->getTopicIconPath("{$icon->filename}", false);
							$this->topicIcons[$icon->id] = $icon;
						}
					}
				}
			}

			// Make sure that default icon exists (use user/default.png in current template)
			if (!isset($this->topicIcons[0]))
			{
				$icon                = new StdClass;
				$icon->id            = 0;
				$icon->type          = 'user';
				$icon->name          = 'default';
				$icon->published     = 0;
				$icon->title         = 'Default';
				$icon->filename      = 'default.png';
				$icon->width         = 48;
				$icon->height        = 48;
				$icon->relpath       = $this->getTopicIconPath("user/{$icon->filename}", false);
				$this->topicIcons[0] = $icon;
			}
		}

		if ($all)
		{
			$icons = $this->topicIcons;
		}
		else
		{
			$icons = array();

			foreach ($this->topicIcons as $icon)
			{
				if ($icon->published && is_numeric($icon->id))
				{
					$icons[$icon->id]          = clone $icon;
					$icons[$icon->id]->checked = ($checked == $icon->id);
				}
			}
		}

		return $icons;
	}

	/**
	 * @param   string $filename filename
	 * @param   bool   $url      url
	 *
	 * @return string
	 * @throws Exception
	 * @since Kunena
	 */
	public function getTopicIconPath($filename = '', $url = true)
	{
		$config = KunenaFactory::getConfig();

		if ($config->topicicons)
		{
			$category_iconset = 'images/topic_icons';

			if (!file_exists($category_iconset))
			{
				$category_iconset = 'media/kunena/topic_icons' . $this->category_iconset;
			}
		}
		else
		{
			$category_iconset = 'images/topic_icons';

			if (!file_exists($category_iconset))
			{
				$category_iconset = 'media/kunena/topic_icons';
			}
		}

		return $this->getFile($filename, $url, $this->pathTypes['topicicons'], $category_iconset);
	}

	/**
	 * @param   KunenaForumTopic $topic topic
	 *
	 * @return string
	 * @throws Exception
	 * @internal param string $category_iconset
	 * @since    Kunena
	 */
	public function getTopicIcon($topic)
	{
		$config           = KunenaFactory::getConfig();
		$this->ktemplate  = KunenaFactory::getTemplate();
		$topicicontype    = $this->ktemplate->params->get('topicicontype');
		$category_iconset = $topic->getCategory()->iconset;

		if ($config->topicicons)
		{
			$xmlfile = JPATH_ROOT . '/media/kunena/topic_icons/' . $category_iconset . '/topicicons.xml';

			if (!file_exists($xmlfile))
			{
				$xmlfile = JPATH_ROOT . '/media/kunena/topic_icons/default/topicicons.xml';
			}

			$xml  = simplexml_load_file($xmlfile);
			$icon = $this->get_xml_icon($xml, $topic->icon_id, $topicicontype);

			if ($topic->ordering)
			{
				$topic->icon_id = 504;
				$icon           = $this->get_xml_systemicon($xml, $topic->icon_id, $topicicontype);
			}

			if ($topic->locked)
			{
				$topic->icon_id = 505;
				$icon           = $this->get_xml_systemicon($xml, $topic->icon_id, $topicicontype);
			}

			if ($topic->ordering && $topic->locked)
			{
				$topic->icon_id = 503;
				$icon           = $this->get_xml_systemicon($xml, $topic->icon_id, $topicicontype);
			}

			if ($topic->hold == 2)
			{
				$topic->icon_id = 501;
				$icon           = $this->get_xml_systemicon($xml, $topic->icon_id, $topicicontype);
			}

			if ($topic->hold == 3)
			{
				$topic->icon_id = 501;
				$icon           = $this->get_xml_systemicon($xml, $topic->icon_id, $topicicontype);
			}

			if ($topic->moved_id > 0)
			{
				$topic->icon_id = 500;
				$icon           = $this->get_xml_systemicon($xml, $topic->icon_id, $topicicontype);
			}

			if ($topicicontype == 'B2')
			{
				return '<span class="icon-topic icon icon-' . $icon->b2 . '"></span>';
			}
			elseif ($topicicontype == 'B3')
			{
				return '<span class="glyphicon-topic glyphicon glyphicon-' . $icon->b3 . '"></span>';
			}
			elseif ($topicicontype == 'B4')
			{
				return '<img src="' . Uri::root() . 'media/kunena/topic_icons/' . $category_iconset . '/user/svg/' . $icon->b4 . '.svg" alt="' . $icon->b4 . '" width="32" height="32" />';
			}
			elseif ($topicicontype == 'fa')
			{
				return '<i class="fa fa-' . $icon->fa . ' fa-2x"></i>';
			}
			elseif ($topicicontype == 'image')
			{
				return '<img src="' . Uri::root() . 'media/kunena/topic_icons/' . $category_iconset . '/' . $icon->src . '" alt="' . $icon->fa . '" />';
			}
			else
			{
				$iconurl = $this->getTopicIconPath("{$category_iconset}/{$icon->src}", true);

				return '<img src="' . $iconurl . '" alt="Topic-icon" />';
			}
		}
		else
		{
			$xmlfile = JPATH_ROOT . '/media/kunena/topic_icons/' . $category_iconset . '/systemicons.xml';

			if (!file_exists($xmlfile))
			{
				$xmlfile = JPATH_ROOT . '/media/kunena/topic_icons/default/systemicons.xml';
			}

			$iconid = 0;

			if ($topic->posts < 2)
			{
				$iconid = 6;
			}

			if ($topic->ordering)
			{
				$iconid = 3;
			}

			if ($topic->locked)
			{
				$iconid = 4;
			}

			if ($topic->ordering && $topic->locked)
			{
				$iconid = 7;
			}

			if ($topic->moved_id)
			{
				$iconid = 5;
			}

			if ($topic->hold == 1)
			{
				$iconid = 1;
			}

			if ($topic->hold == 2)
			{
				$iconid = 2;
			}

			if ($topic->hold == 3)
			{
				$iconid = 2;
			}

			$xml  = simplexml_load_file($xmlfile);
			$icon = $this->get_xml_systemicon($xml, $iconid, $topicicontype);

			if ($topicicontype == 'B2')
			{
				return '<span class="icon-topic icon icon-' . $icon->b2 . '"></span>';
			}
			elseif ($topicicontype == 'B3')
			{
				return '<span class="glyphicon-topic glyphicon glyphicon-' . $icon->b3 . '"></span>';
			}
			elseif ($topicicontype == 'B4')
			{
				return '<img src="' . Uri::root() . '/media/kunena/topic_icons/' . $category_iconset . '/system/' . $icon->b4 . '" width="32" height="32" />';
			}
			elseif ($topicicontype == 'fa')
			{
				return '<i class="fa fa-' . $icon->fa . ' fa-2x"></i>';
			}
			else
			{
				$file = JPATH_ROOT . '/media/kunena/topic_icons/' . $category_iconset . '/system/normal.png';

				if (!file_exists($file))
				{
					$category_iconset = 'default';
				}

				if (!empty($topic->unread))
				{
					$icon->src = $icon->new;
				}

				if (empty($icon->name))
				{
					$icon->name = "";
				}

				$iconurl = $this->getTopicIconPath("{$category_iconset}/system/{$icon->src}", true);

				return '<img src="' . $iconurl . '" alt="' . $icon->name . '" />';
			}
		}
	}

	/**
	 * Retrieve icons attributes
	 *
	 * @return stdClass
	 * @since Kunena 5.2
	 */
	private function getIconsAttributes($icon)
	{
		$attributes = $icon[0]->attributes();
		$icon       = new stdClass;
		$icon->id   = (int) $attributes->id;
		$icon->name = (string) $attributes->name;
		$icon->b2   = (string) $attributes->b2;
		$icon->b3   = (string) $attributes->b3;
		$icon->b4   = (string) $attributes->b4;
		$icon->fa   = (string) $attributes->fa;
		$icon->src  = (string) $attributes->src;
		$icon->new  = (string) $attributes->new;

		return $icon;
	}

	/**
	 * @param   mixed  $src   src
	 * @param   int    $id    id
	 * @param   string $style style
	 *
	 * @return stdClass
	 * @since Kunena
	 */
	public function get_xml_icon($src, $id = 0, $style = 'src')
	{
		if (isset($src->icons))
		{
			$icon = $src->xpath('/kunena-topicicons/icons/icon[@id=' . $id . ']');

			if (!$icon)
			{
				$icon = $src->xpath('/kunena-topicicons/icons/icon[@id=0]');
			}

			$icon = $this->getIconsAttributes($icon);

			return $icon;
		}
	}

	/**
	 * @param   mixed  $src   src
	 * @param   int    $id    id
	 * @param   string $style style
	 *
	 * @return stdClass
	 * @since Kunena
	 */
	public function get_xml_systemicon($src, $id = 0, $style = 'src')
	{
		if (isset($src->icons))
		{
			$icon = $src->xpath('/kunena-systemicons/icons/icon[@id=' . $id . ']');

			if (!$icon)
			{
				$icon = $src->xpath('/kunena-topicicons/icons/icon[@id=' . $id . ']');

				if (!$icon)
				{
					$icon = $src->xpath('/kunena-topicicons/icons/icon[@id=0]');
				}
			}

			$icon = $this->getIconsAttributes($icon);

			return $icon;
		}
	}

	/**
	 * @param   KunenaForumCategory $category category
	 *
	 * @return string
	 * @throws Exception
	 * @since Kunena
	 */
	public function getCategoryIcon($category)
	{
		$config = KunenaFactory::getConfig();

		if ($config->categoryicons)
		{
			$icon    = $category->icon_id;
			$iconurl = $this->getCategoryIconIndexPath($icon, true);
		}
		else
		{
			$icon    = 'folder';
			$iconurl = $this->getCategoryIconPath("system/{$icon}.png", true);
		}

		$html = '<img src="' . $iconurl . '" alt="emo" />';

		return $html;
	}

	/**
	 * @param   mixed $index index
	 * @param   bool  $url   url
	 *
	 * @return string
	 * @throws Exception
	 * @since Kunena
	 */
	public function getCategoryIconIndexPath($index, $url = false)
	{
		if (empty($this->categoryIcons))
		{
			$this->getCategoryIcons();
		}

		if (empty($this->categoryIcons[$index]->published))
		{
			$index = 0;
		}

		$icon = $this->categoryIcons[$index];

		return $this->getCategoryIconPath($icon->filename, $url);
	}

	/**
	 * @param   bool $all     all
	 * @param   int  $checked checked
	 *
	 * @return array|SimpleXMLElement
	 * @throws Exception
	 * @since Kunena
	 */
	public function getCategoryIcons($all = false, $checked = 0)
	{
		if (empty($this->categoryIcons))
		{
			$xmlfile = $this->getCategoryIconPath('categoryicons.xml', false);

			if (is_file($xmlfile))
			{
				$xml = simplexml_load_file($xmlfile);

				if (isset($xml->icons))
				{
					foreach ($xml->icons as $icons)
					{
						$type   = (string) $icons->attributes()->type;
						$width  = (int) $icons->attributes()->width;
						$height = (int) $icons->attributes()->height;

						foreach ($icons->icon as $icon)
						{
							$attributes = $icon->attributes();
							$icon       = new stdClass;
							$icon->id   = (int) $attributes->id;
							$icon->type = (string) $attributes->type ? (string) $attributes->type : $type;
							$icon->name = (string) $attributes->name;

							if ($icon->type != 'user')
							{
								$icon->id = $icon->type . '_' . $icon->name;
							}

							$icon->published                = (int) $attributes->published;
							$icon->title                    = (string) $attributes->title;
							$icon->filename                 = (string) $attributes->src;
							$icon->width                    = (int) $attributes->width ? (int) $attributes->width : $width;
							$icon->height                   = (int) $attributes->height ? (int) $attributes->height : $height;
							$this->categoryIcons[$icon->id] = $icon;
						}
					}
				}
			}

			// Make sure that default icon exists (use user/default.png in current template)
			if (!isset($this->categoryIcons[0]))
			{
				$icon                   = new StdClass;
				$icon->id               = 0;
				$icon->type             = 'user';
				$icon->name             = 'default';
				$icon->published        = 0;
				$icon->title            = 'Default';
				$icon->filename         = 'default.png';
				$icon->width            = 48;
				$icon->height           = 48;
				$icon->relpath          = $this->getCategoryIconPath("user/{$icon->filename}", false);
				$this->categoryIcons[0] = $icon;
			}
		}

		if ($all)
		{
			$icons = $this->categoryIcons;
		}
		else
		{
			$icons = array();

			foreach ($this->categoryIcons as $icon)
			{
				if ($icon->published && is_numeric($icon->id))
				{
					$icons[$icon->id]          = clone $icon;
					$icons[$icon->id]->checked = ($checked == $icon->id);
				}
			}
		}

		return $icons;
	}

	/**
	 * @param   string $filename         filename
	 * @param   bool   $url              url
	 * @param   mixed  $category_iconset category
	 *
	 * @return string
	 * @throws Exception
	 * @since Kunena
	 */
	public function getCategoryIconPath($filename, $url, $category_iconset)
	{
		if (!$this->isHmvc())
		{
			$set              = '';
			$category_iconset = 'default';
		}

		return $this->getFile($filename, $url, $this->pathTypes['categoryicons'] . $set, 'media/kunena/category_icons/' . $category_iconset);
	}

	/**
	 * @param   string $filename filename
	 *
	 * @return string
	 * @deprecated K4.0
	 * @since      Kunena
	 */
	public function getTopicsIconPath($filename)
	{
		if (empty($filename))
		{
			return false;
		}

		return "media/kunena/topicicons/{$filename}";
	}

	/**
	 * @return SimpleXMLElement
	 * @since Kunena
	 */
	public function getTemplateDetails()
	{
		$xml = simplexml_load_file(KPATH_SITE . "/template/{$this->name}/config/template.xml");

		return $xml;
	}

	/**
	 * @param   string $inputFile  input
	 * @param   string $outputFile output
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function compileLess($inputFile, $outputFile)
	{
		if (!class_exists('lessc'))
		{
			require_once KPATH_FRAMEWORK . '/external/lessc/lessc.php';
		}

		// Load the cache.
		$cacheDir = JPATH_CACHE . '/kunena';

		if (!is_dir($cacheDir))
		{
			KunenaFolder::create($cacheDir);
		}

		$cacheFile = "{$cacheDir}/kunena.{$this->name}.{$inputFile}.cache";

		if (is_file($cacheFile))
		{
			$cache = unserialize(file_get_contents($cacheFile));
		}
		else
		{
			$cache = JPATH_SITE . '/' . $this->getFile($inputFile, false, 'less');
		}

		$outputDir = KPATH_MEDIA . "/cache/{$this->name}/css";

		if (!is_dir($outputDir))
		{
			KunenaFolder::create($outputDir);
		}

		$outputFile = "{$outputDir}/{$outputFile}";

		$less  = new lessc;
		$class = $this;
		$less->registerFunction('url', function ($arg) use ($class) {
			list($type, $q, $values) = $arg;
			$value = reset($values);

			return "url({$q}{$class->getFile($value, true, 'media', '')}{$q})";
		}
		);
		$less->setVariables($this->style_variables);
		$newCache = $less->cachedCompile($cache);

		if (!is_array($cache) || $newCache['updated'] > $cache['updated'] || !is_file($outputFile))
		{
			$cache = serialize($newCache);
			KunenaFile::write($cacheFile, $cache);
			KunenaFile::write($outputFile, $newCache['compiled']);
		}
	}

	/**
	 * Legacy template support.
	 *
	 * @param   string $search search
	 *
	 * @return array
	 * @deprecated K4.0
	 * @since      Kunena
	 */
	public function mapLegacyView($search)
	{
		if (!isset($this->map))
		{
			$file = JPATH_SITE . '/' . $this->getFile('mapping.php');

			if (is_file($file))
			{
				include $file;
			}
		}

		$search = rtrim($search, '_');

		if (isset($this->map[$search]))
		{
			return $this->map[$search];
		}

		return array($search, 'default');
	}

	/**
	 * Set the category iconset value
	 *
	 * @param   string $iconset iconset
	 *
	 * @since Kunena
	 * @return void
	 */
	public function setCategoryIconset($iconset = '/default')
	{
		$this->category_iconset = '/' . $iconset;
	}

	/**
	 * @param   mixed $topic topic
	 *
	 * @return stdClass
	 * @throws Exception
	 * @since Kunena
	 */
	public function getTopicLabel($topic)
	{
		$ktemplate = KunenaFactory::getTemplate();

		$topicicontype = $ktemplate->params->get('topicicontype');
		$topiclabels   = $ktemplate->params->get('labels');

		if ($topiclabels != 0)
		{
			$xmlfile = JPATH_ROOT . '/components/com_kunena/template/' . $this->name . '/config/labels.xml';

			if (!file_exists($xmlfile))
			{
				$xmlfile = JPATH_ROOT . '/media/kunena/labels/labels.xml';
			}

			$xml = simplexml_load_file($xmlfile);

			if ($topiclabels == 1)
			{
				$id = $topic->icon_id;
			}
			else
			{
				$id = $topic->label_id;
			}

			$icon = $this->get_xml_label($xml, $id, $topicicontype);

			return $icon;
		}
	}

	/**
	 * @param   mixed  $src   src
	 * @param   int    $id    id
	 * @param   string $style style
	 *
	 * @return stdClass
	 * @since Kunena
	 */
	public function get_xml_label($src, $id = 0, $style = 'src')
	{
		if (isset($src->labels))
		{
			$label = $src->xpath('/kunena-topiclabels/labels/label[@id=' . $id . ']');

			if (!$label)
			{
				$label = $src->xpath('/kunena-topiclabels/labels/label[@id=0]');
			}

			$attributes       = $label[0]->attributes();
			$label            = new stdClass;
			$label->id        = (int) $attributes->id;
			$label->b2        = (string) $attributes->b2;
			$label->b3        = (string) $attributes->b3;
			$label->b4        = (string) $attributes->b4;
			$label->fa        = (string) $attributes->fa;
			$label->src       = (string) $attributes->src;
			$label->name      = (string) $attributes->name;
			$label->new       = (string) $attributes->new;
			$label->labeltype = (string) $attributes->labeltype;

			return $label;
		}
	}

	/**
	 * @return string
	 * @throws Exception
	 * @since Kunena
	 */
	public function borderless()
	{
		$ktemplate  = KunenaFactory::getTemplate();
		$borderless = $ktemplate->params->get('borderless');

		if ($borderless)
		{
			return '';
		}
		else
		{
			return ' table-bordered';
		}
	}

	/**
	 * @param   bool $class class
	 *
	 * @return string
	 *
	 * @since version
	 * @throws Exception
	 */
	public function tooltips($class = false)
	{
		$ktemplate = KunenaFactory::getTemplate();
		$tooltips  = $ktemplate->params->get('tooltips');

		if ($tooltips)
		{
			if ($class)
			{
				return 'class="hasTooltip"';
			}
			else
			{
				return 'hasTooltip';
			}
		}
	}

	/**
	 * Load fontawesome libraries with if enabled the compatibility layer for version 4.x
	 *
	 * @since Kunena 5.2
	 */
	public function loadFontawesome()
	{
		$ktemplate = KunenaFactory::getTemplate();
		$fontawesome = $ktemplate->params->get('fontawesome');
		$fontawesome_layer_v4 = $ktemplate->params->get('fontawesome_layer_v4');

		if ($fontawesome)
		{
			$this->addScript('https://use.fontawesome.com/releases/v5.15.4/js/all.js', array(), array('defer' => true));
		}
		
		if ($fontawesome && $fontawesome_layer_v4)
		{
			$this->addScript('https://use.fontawesome.com/releases/v5.15.4/js/v4-shims.js', array(), array('defer' => true));
		}
	}
}
