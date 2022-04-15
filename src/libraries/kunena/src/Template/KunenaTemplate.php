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

namespace Kunena\Forum\Libraries\Template;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Icons\KunenaSvgIcons;
use Kunena\Forum\Libraries\Path\KunenaPath;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;
use SimpleXMLElement;
use StdClass;

/**
 * Kunena Template Class
 * Provides access to Kunena templates method handling
 *
 * @since   Kunena 6.0
 */
class KunenaTemplate
{
	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected static $_instances = [];

	/**
	 * @var     null|string
	 * @since   Kunena 6.0
	 */
	public $name = null;

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public $params = [];

	/**
	 * @var     boolean|integer
	 * @since   Kunena 6.0
	 */
	public $paramstime = false;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $topicIcons = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $categoryIcons = [];

	/**
	 * @var     KunenaConfig
	 * @since   Kunena 6.0
	 */
	public $config = null;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $pathTypes = [
		'emoticons'     => 'media/emoticons',
		'ranks'         => 'media/ranks',
		'icons'         => 'media/icons',
		'categoryIcons' => 'media/category_icons',
		'images'        => 'media/images',
		'js'            => 'media/js',
		'css'           => 'media/css',
	];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $pathTypeDefaults = [
		'avatars'       => 'media/avatars',
		'emoticons'     => 'media/emoticons',
		'ranks'         => 'media/ranks',
		'icons'         => 'media/icons',
		'topicIcons'    => 'media/topic_icons',
		'categoryIcons' => 'media/category_icons',
		'images'        => 'media/images',
		'js'            => 'media/js',
		'css'           => 'media/css',
	];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $pathTypeOld = [
		'avatars'    => 'images/avatars',
		'emoticons'  => 'images/emoticons',
		'ranks'      => 'images/ranks',
		'icons'      => 'images/icons',
		'topicIcons' => 'images/topicIcons',
		'images'     => 'images',
		'js'         => 'js',
		'css'        => 'css',
	];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $default = ['Aurelia'];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $paths = [];

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $css_compile = true;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $filecache = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $smileyPath = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $rankPath = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $userClasses = [
		'kwho-',
		'admin'     => 'kwho-admin',
		'globalmod' => 'kwho-globalmoderator',
		'moderator' => 'kwho-moderator',
		'user'      => 'kwho-user',
		'guest'     => 'kwho-guest',
		'banned'    => 'kwho-banned',
		'blocked'   => 'kwho-blocked',
	];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $stylesheets = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $style_variables = [];

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $compiled_style_variables = null;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $scripts = [];

	/**
	 * @var     null|SimpleXMLElement
	 * @since   Kunena 6.0
	 */
	protected $xml = null;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $map;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $hmvc;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $categoryIconset = '';

	/**
	 * Constructor
	 *
	 * @access    protected
	 *
	 * @param   null  $name  name
	 *
	 * @throws  Exception
	 * @since     Kunena 6.0
	 */
	public function __construct($name = null)
	{
		$this->config = KunenaFactory::getConfig();

		if (!$name)
		{
			$name = $this->config->template;
		}

		$name = KunenaPath::clean($name);

		// Create template inheritance
		if (!\is_array($this->default))
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

		$this->params = new Registry;
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

			// Generate CSS variables for scss compiler.
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

		if ($view == 'com_kunena' && Factory::getApplication()->isClient('site'))
		{
			// Set active class on menu item alias.
			if ($this->config->activeMenuItem)
			{
				$id = htmlspecialchars($this->config->activeMenuItem, ENT_COMPAT, 'UTF-8');
				$this->addScriptDeclaration(
					"
					document.addEventListener('DOMContentLoaded', () => {
						document.querySelector('" . $id . "').classList.add('active');
					});
				"
				);
			}
			else
			{
				$Itemid = KunenaRoute::fixMissingItemID();
				$items  = Factory::getApplication()->getMenu('site')->getItems('link', 'index.php?Itemid=' . $Itemid);

				if ($items)
				{
					$id = htmlspecialchars('.item-' . $items[0]->id, ENT_COMPAT, 'UTF-8');
					$this->addScriptDeclaration(
						"
						document.addEventListener('DOMContentLoaded', () => {
							document.querySelector('" . $id . "').classList.add('active');
						});
					"
					);
				}
			}
		}
	}

	/**
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function isHmvc()
	{
		$app = Factory::getApplication();

		if (\is_null($this->hmvc))
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
	 * Wrapper to addScript
	 *
	 * @param   string  $content  content
	 * @param   string  $type     type
	 *
	 * @return \Joomla\CMS\Document\Document|boolean
	 *
	 * @since   Kunena 6.0
	 * @throws \Exception
	 */
	public function addScriptDeclaration(string $content, $type = 'text/javascript')
	{
		$app    = Factory::getApplication();
		$format = $app->input->getCmd('format');

		if (!empty($format) && $format != 'html')
		{
			return false;
		}

		return Factory::getApplication()->getDocument()->addScriptDeclaration($content, $type);
	}

	/**
	 * Returns the global KunenaTemplate object, only creating it if it doesn't already exist.
	 *
	 * @access  public
	 *
	 * @param   int  $name  Template name or null for default/selected template in your configuration
	 *
	 * @return  KunenaTemplate    The template object.
	 *
	 * @throws  Exception
	 * @since   Kunena 1.6
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
				// If template xml doesn't exist, raise warning and use aurelia instead
				$file         = JPATH_THEMES . "/{$app->getTemplate()}/html/com_kunena/template.php";
				$templatename = 'aurelia';
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
					$classname = "KunenaTemplateAurelia";
					$file      = KPATH_SITE . "/template/aurelia/template.php";
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
	 * @return  array|false|string|string[]|null
	 *
	 * @since   Kunena 6.0
	 */
	public function getConfigXml()
	{
		// Find configuration file.
		$xml_path = KPATH_SITE . "/template/{$this->name}/config/config.xml";

		if (!is_file($xml_path))
		{
			return false;
		}

		$xml = file_get_contents($xml_path);

		if (!strstr($xml, '<config>'))
		{
			// Update old template files to new format.
			$xml = preg_replace(
				['|<params|', '|</params>|', '|<param\s+|', '|</param>|'],
				['<config', '</config>', '<field ', '</field>'],
				$xml
			);
		}

		return $xml;
	}

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function initialize(): void
	{
		$this->loadLanguage();

		if ($this->config->sef)
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
			$this->addScriptDeclaration(
				'
				document.addEventListener("DOMContentLoaded", () => {
					document.querySelector(".current").classList.add("active");
					document.querySelector(".current").classList.add("alias-parent-active");
					document.querySelector(".alias-parent-active").classList.add("active");
					document.querySelector(".alias-parent-active").classList.add("alias-parent-active");
				});
			'
			);
		}
	}

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function loadLanguage(): void
	{
		// Loading language strings for the template
		$lang = Factory::getApplication()->getLanguage();
		KunenaFactory::loadLanguage('com_kunena.templates', 'site');

		foreach (array_reverse($this->default) as $template)
		{
			$lang->load('kunena_tmpl_' . $template, JPATH_SITE)
			|| $lang->load('kunena_tmpl_' . $template, KPATH_SITE)
			|| $lang->load('kunena_tmpl_' . $template, KPATH_SITE . '/template/' . $template);
		}
	}

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function initializeBackend(): void
	{
		$this->loadLanguage();
	}

	/**
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function getUserClasses(): array
	{
		return $this->userClasses;
	}

	/**
	 * @param   string  $link   link
	 * @param   string  $name   name
	 * @param   string  $scope  scope
	 * @param   string  $type   type
	 * @param   null    $id     id
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getButton(string $link, string $name, string $scope, string $type, $id = null): string
	{
		$types = ['communication' => 'comm', 'user' => 'user', 'moderation' => 'mod'];
		$names = ['unsubscribe' => 'subscribe', 'unfavorite' => 'favorite', 'unsticky' => 'sticky', 'unlock' => 'lock', 'create' => 'newtopic',
		          'quickReply'  => 'reply', 'quote' => 'kquote', 'edit' => 'kedit', ];

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

		if ($name == 'quickReply')
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
	 * @param   string  $name   name
	 * @param   string  $title  title
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getIcon(string $name, $title = ''): string
	{
		return '<span class="kicon ' . $name . '" title="' . $title . '"></span>';
	}

	/**
	 * @param   string  $image  image
	 * @param   string  $alt    alt
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getImage(string $image, $alt = ''): string
	{
		return '<img loading=lazy src="' . $this->getImagePath($image) . '" alt="' . $alt . '" />';
	}

	/**
	 * @param   string  $filename  filename
	 * @param   bool    $url       url
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getImagePath($filename = '', $url = true): string
	{
		return $this->getFile($filename, $url, $this->pathTypes['images'], 'media/kunena/images');
	}

	/**
	 * @param   string  $file      file
	 * @param   bool    $url       url
	 * @param   string  $basepath  basepath
	 * @param   null    $default   default
	 * @param   null    $ignore    ignore
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getFile(string $file, $url = false, $basepath = '', $default = null, $ignore = null): string
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
	 * @param   mixed  $list  list
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getPaginationListFooter($list): string
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
	 * @param   mixed  $list  list
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getPaginationListRender($list): string
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
	 * @param   object  $item  item
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getPaginationItemActive(object $item): string
	{
		return '<a title="' . $item->text . '" href="' . $item->link . '" class="pagenav">' . $item->text . '</a>';
	}

	/**
	 * @param   object  $item  item
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getPaginationItemInactive(object $item): string
	{
		return '<span class="pagenav">' . $item->text . '</span>';
	}

	/**
	 * @param   string  $class      class
	 * @param   string  $class_sfx  class_sfx
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getClass(string $class, $class_sfx = ''): string
	{
		return $class . ($class_sfx ? " {$class}.{$class_sfx}" : '');
	}

	/**
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function getStyleVariables(): array
	{
		return $this->style_variables;
	}

	/**
	 * @param   string  $name     name
	 * @param   string  $default  default
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getStyleVariable(string $name, $default = ''): string
	{
		return isset($this->style_variables[$name]) ? $this->style_variables[$name] : $default;
	}

	/**
	 * @param   string   $name   name
	 * @param   integer  $value  value
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function setStyleVariable(string $name, int $value): int
	{
		$this->compiled_style_variables = null;

		return $this->style_variables[$name] = $value;
	}

	/**
	 * @param   string  $filename  filename
	 * @param   string  $group     group
	 *
	 * @return  mixed|void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function addStyleSheet(string $filename, $group = 'forum')
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
				$filename = preg_replace('|^css/|u', '', $filename);
				$filename = preg_replace('/^assets\//', '', $filename);
				$filename = $this->getFile($filename, false, $this->pathTypes['css'], 'components/com_kunena/template/' . $this->name . '/assets');
			}
			elseif (preg_match('/kunena.css/', $filename) || preg_match('/kunena-custom.css/', $filename))
			{
				$filename = preg_replace('|^css/|u', '', $filename);
				$filename = preg_replace('/^assets\//', '', $filename);
				$filename = $this->getFile($filename, false, $this->pathTypes['css'], 'media/kunena/cache/' . $this->name . '/css');
			}
			else
			{
				$filename = $this->getFile($filename, false, $this->pathTypes['css'], $this->pathTypes['css']);
			}

			$filemin      = $filename;
			$filemin_path = preg_replace('/\.css$/u', '-min.css', $filename);

			if (!JDEBUG && !$this->config->debug && !KunenaForum::isDev() && is_file(JPATH_ROOT . "/$filemin_path"))
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
	 * Set the Scss file into the document head
	 *
	 * @param   string  $filename  filename
	 *
	 * @return  mixed|void
	 *
	 * @throws Exception
	 * @since   Kunena 5.1.3
	 */
	public function addScssSheet(string $filename)
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
	 * @param   string  $style  style
	 *
	 * @return  string|void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function addStyleDeclaration(string $style)
	{
		$app    = Factory::getApplication();
		$format = $app->input->getCmd('format');

		if (!empty($format) && $format != 'html')
		{
			return false;
		}

		return Factory::getApplication()->getDocument()->addStyleDeclaration($style);
	}

	/**
	 * @param   string  $filename   filename
	 * @param   string  $condition  condition
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function addIEStyleSheet(string $filename, $condition = 'IE'): void
	{
		$filename  = preg_replace('|^css/|u', '', $filename);
		$url       = $this->getFile($filename, true, $this->pathTypes['css'], 'media/kunena/css');
		$stylelink = "<!--[if {$condition}]>\n";
		$stylelink .= '<link rel="stylesheet" href="' . $url . '" />' . "\n";
		$stylelink .= "<![endif]-->\n";
		Factory::getApplication()->getDocument()->addCustomTag($stylelink);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function clearCache(): void
	{
		$path = JPATH_ROOT . "/media/kunena/cache/{$this->name}";

		if (is_dir($path))
		{
			Folder::delete($path);
		}
	}

	/**
	 * @param   string  $filename  filename
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getCachePath($filename = ''): string
	{
		if ($filename)
		{
			$filename = '/' . $filename;
		}

		if (JDEBUG || $this->config->debug)
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
	 * @param   mixed  $matches  matches
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function findUrl($matches): string
	{
		$file = trim($matches[1], ' \'"');

		if (preg_match('#^../#', $file))
		{
			$file = $this->getFile(substr($file, 3), true, '', 'media/kunena');
		}

		return "url('{$file}')";
	}

	/**
	 * Add option for script
	 *
	 * @param   string  $key      Name in Storage
	 * @param   mixed   $options  Scrip options as array or string
	 * @param   bool    $merge    Whether merge with existing (true) or replace (false)
	 *
	 * @return \Joomla\CMS\Document\Document|boolean
	 *
	 * @throws \Exception
	 * @since   Kunena 3.5
	 */
	public function addScriptOptions(string $key, $options, $merge = true)
	{
		$app    = Factory::getApplication();
		$format = $app->input->getCmd('format');

		if (!empty($format) && $format != 'html')
		{
			return false;
		}

		return Factory::getApplication()->getDocument()->addScriptOptions($key, $options, $merge);
	}

	/**
	 * @param   string  $path  path
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function addPath(string $path): void
	{
		$this->paths[] = KunenaPath::clean("/$path");
	}

	/**
	 * @param   string  $path      path
	 * @param   bool    $fullpath  fullpath
	 *
	 * @return  array
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getTemplatePaths($path = '', $fullpath = false): array
	{
		$app = Factory::getApplication();

		if ($path)
		{
			$path = KunenaPath::clean("/$path");
		}

		$array = [];

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
	 * @param   string  $filename  filename
	 * @param   bool    $url       url
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getAvatarPath($filename = '', $url = false): string
	{
		return $this->getFile($filename, $url, $this->pathTypes['avatars'], 'media/kunena/avatars');
	}

	/**
	 * @param   string  $filename  filename
	 * @param   bool    $url       url
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getSmileyPath($filename = '', $url = false): string
	{
		return $this->getFile($filename, $url, $this->pathTypes['emoticons'], 'media/kunena/emoticons');
	}

	/**
	 * @param   string  $filename  filename
	 * @param   bool    $url       url
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getRankPath($filename = '', $url = false): string
	{
		return $this->getFile($filename, $url, $this->pathTypes['ranks'], 'media/kunena/ranks');
	}

	/**
	 * @param   mixed  $index  index
	 * @param   bool   $url    url
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getTopicIconIndexPath($index, $url = false): string
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
	 * @param   bool  $all      all
	 * @param   int   $checked  checked
	 *
	 * @return  array
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getTopicIcons($all = false, $checked = 0)
	{
		$categoryIconset = $this->categoryIconset;

		if (empty($this->topicIcons))
		{
			$xmlfile = JPATH_ROOT . '/media/kunena/topic_icons' . $categoryIconset . '/topicicons.xml';

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
							$icon->svg                   = (string) $attributes->svg;
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
			$icons = [];

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
	 * @param   string  $filename  filename
	 * @param   bool    $url       url
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getTopicIconPath($filename = '', $url = true): string
	{
		if ($this->config->topicIcons)
		{
			$categoryIconset = 'images/topic_icons';

			if (!file_exists($categoryIconset))
			{
				$categoryIconset = 'media/kunena/topic_icons' . $this->categoryIconset;
			}
		}
		else
		{
			$categoryIconset = 'images/topic_icons';

			if (!file_exists($categoryIconset))
			{
				$categoryIconset = 'media/kunena/topic_icons';
			}
		}

		return $this->getFile($filename, $url, $this->pathTypes['topicIcons'], $categoryIconset);
	}

	/**
	 * @param   KunenaTopic  $topic  topic
	 *
	 * @return  string
	 *
	 * @throws Exception
	 * @since    Kunena 6.0
	 *
	 * @internal param string $categoryIconset
	 */
	public function getTopicIcon(KunenaTopic $topic): string
	{
		$topicicontype   = $this->params->get('topicicontype');
		$categoryIconset = $topic->getCategory()->iconset;

		if ($this->config->topicIcons)
		{
			$xmlfile = JPATH_ROOT . '/media/kunena/topic_icons/' . $categoryIconset . '/topicicons.xml';

			if (!file_exists($xmlfile))
			{
				$xmlfile = JPATH_ROOT . '/media/kunena/topic_icons/default/topicicons.xml';
			}

			$xml  = simplexml_load_file($xmlfile);
			$icon = $this->get_xml_icon($xml, $topic->icon_id, $topicicontype);

			if ($topic->ordering)
			{
				$topic->icon_id = 504;
				$icon           = $this->get_xml_icon($xml, $topic->icon_id, $topicicontype);
			}

			if ($topic->locked)
			{
				$topic->icon_id = 505;
				$icon           = $this->get_xml_icon($xml, $topic->icon_id, $topicicontype);
			}

			if ($topic->ordering && $topic->locked)
			{
				$topic->icon_id = 503;
				$icon           = $this->get_xml_icon($xml, $topic->icon_id, $topicicontype);
			}

			if ($topic->hold == 2)
			{
				$topic->icon_id = 501;
				$icon           = $this->get_xml_icon($xml, $topic->icon_id, $topicicontype);
			}

			if ($topic->hold == 3)
			{
				$topic->icon_id = 501;
				$icon           = $this->get_xml_icon($xml, $topic->icon_id, $topicicontype);
			}

			if ($topic->moved_id > 0)
			{
				$topic->icon_id = 500;
				$icon           = $this->get_xml_icon($xml, $topic->icon_id, $topicicontype);
			}

			if ($topicicontype == 'svg')
			{
				if ($categoryIconset != 'default')
				{
					return KunenaSvgIcons::loadsvg($icon->svg, 'usertopicIcons', $categoryIconset . '/' . $icon->src);
				}

				return KunenaSvgIcons::loadsvg($icon->svg);
			}
			elseif ($topicicontype == 'fa')
			{
				return '<i class="fa fa-' . $icon->fa . ' fa-2x"></i>';
			}
			elseif ($topicicontype == 'image')
			{
				return '<img loading=lazy src="' . Uri::root() . 'media/kunena/topic_icons/' . $categoryIconset . '/' . $icon->src . '" alt="' . $icon->fa . '" />';
			}
			else
			{
				$iconurl = $this->getTopicIconPath("{$categoryIconset}/{$icon->src}", true);

				return '<img loading=lazy src="' . $iconurl . '" alt="Topic-icon" />';
			}
		}
		else
		{
			$xmlfile = JPATH_ROOT . '/media/kunena/topic_icons/' . $categoryIconset . '/systemicons.xml';

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

			if ($topicicontype == 'svg')
			{
				return KunenaSvgIcons::loadsvg($icon->svg, 'systemtopicIcons', $categoryIconset);
			}
			elseif ($topicicontype == 'fa')
			{
				return '<i class="fa fa-' . $icon->fa . ' fa-2x"></i>';
			}
			else
			{
				$file = JPATH_ROOT . '/media/kunena/topic_icons/' . $categoryIconset . '/system/normal.png';

				if (!file_exists($file))
				{
					$categoryIconset = 'default';
				}

				if (!empty($topic->unread))
				{
					$icon->src = $icon->new;
				}

				if (empty($icon->name))
				{
					$icon->name = "";
				}

				$iconurl = $this->getTopicIconPath("{$categoryIconset}/system/{$icon->src}", true);

				return '<img loading=lazy src="' . $iconurl . '" alt="' . $icon->name . '" />';
			}
		}
	}

	/**
	 * Return the icon attributes from the XML file given
	 *
	 * @param   mixed  $src  src
	 *
	 * @return  stdClass
	 *
	 * @since   Kunena 6.0
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
		$icon->svg  = (string) $attributes->svg;
		$icon->fa   = (string) $attributes->fa;
		$icon->src  = (string) $attributes->src;
		$icon->new  = (string) $attributes->new;

		return $icon;
	}

	/**
	 * @param   mixed   $src    src
	 * @param   int     $id     id
	 * @param   string  $style  style
	 *
	 * @return \StdClass
	 *
	 * @since      Kunena 5.0.0-Beta4
	 */
	public function get_xml_systemicon($src, $id = 0, $style = 'src'): StdClass
	{
		if (isset($src->icons))
		{
			$icon = $src->xpath('/kunena-systemicons/icons/icon[@id=' . $id . ']');

			if (!$icon)
			{
				$icon = $src->xpath('/kunena-systemicons/icons/icon[@id=' . $id . ']');
			}

			$icon = $this->getIconsAttributesSystem($icon);

			return $icon;
		}
	}

	/**
	 * Retrieve icons attributes
	 *
	 * @return stdClass
	 * @since Kunena 6.0
	 */
	private function getIconsAttributesSystem($icon)
	{
		$attributes = $icon[0]->attributes();
		$icon       = new stdClass;
		$icon->id   = (int) $attributes->id;
		$icon->name = (string) $attributes->name;
		$icon->svg  = (string) $attributes->svg;
		$icon->fa   = (string) $attributes->fa;
		$icon->src  = (string) $attributes->src;
		$icon->new  = (string) $attributes->new;

		return $icon;
	}

	/**
	 * @param   KunenaCategory  $category  category
	 *
	 * @return  string
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function getCategoryIcon(KunenaCategory $category): string
	{
		if ($this->config->categoryIcons)
		{
			$icon    = $category->icon_id;
			$iconurl = $this->getCategoryIconIndexPath($icon, true);
		}
		else
		{
			$icon    = 'folder';
			$iconurl = $this->getCategoryIconPath("system/{$icon}.png", true);
		}

		return '<img loading=lazy src="' . $iconurl . '" alt="emo" />';
	}

	/**
	 * @param   mixed  $index  index
	 * @param   bool   $url    url
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getCategoryIconIndexPath($index, $url = false): string
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
	 * @param   bool  $all      all
	 * @param   int   $checked  checked
	 *
	 * @return  array
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getCategoryIcons($all = false, $checked = 0)
	{
		if (empty($this->categoryIcons))
		{
			$xmlfile = $this->getCategoryIconPath('categoryIcons.xml', false);

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
			$icons = [];

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
	 * @param   string  $filename         filename
	 * @param   bool    $url              url
	 * @param   mixed   $categoryIconset  category
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getCategoryIconPath($filename = '', $url = true, $categoryIconset = 'default'): string
	{
		if (!$this->isHmvc())
		{
			$set             = '';
			$categoryIconset = 'default';
		}

		return $this->getFile($filename, $url, $this->pathTypes['categoryIcons'] . $set, 'media/kunena/category_icons/' . $categoryIconset);
	}

	/**
	 * @return  SimpleXMLElement
	 *
	 * @since   Kunena 6.0
	 */
	public function getTemplateDetails(): SimpleXMLElement
	{
		return simplexml_load_file(KPATH_SITE . "/template/{$this->name}/config/template.xml");
	}

	/**
	 * @param   string  $inputFile   input
	 * @param   string  $outputFile  output
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 * @throws \ScssPhp\ScssPhp\Exception\SassException
	 * @throws Exception
	 */
	public function compileScss(string $inputFile, string $outputFile): void
	{
		// Load the cache.
		$cacheDir = JPATH_SITE . '/cache/kunena';

		if (!is_dir($cacheDir))
		{
			Folder::create($cacheDir);
		}

		if (!is_file($inputFile))
		{
			$inputFile = KPATH_SITE . '/template/' . $this->name . '/' . $inputFile;
		}

		$outputDir = KPATH_MEDIA . "/cache/{$this->name}/css";

		if (!is_dir($outputDir))
		{
			Folder::create($outputDir);
		}

		$scss = new Compiler();
		$scss->addImportPath(\dirname($inputFile));
		$scss->setOutputStyle(OutputStyle::COMPRESSED);
		$scssContent = file_get_contents($inputFile);
		$style = $scss->compileString($scssContent, $inputFile);

		file_put_contents($outputDir . "/kunena.css", $style->getCss());
	}

	/**
	 * Set the category iconset value
	 *
	 * @param   string  $iconset  iconset
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function setCategoryIconset($iconset = '/default'): void
	{
		$this->categoryIconset = '/' . $iconset;
	}

	/**
	 * @param   mixed  $topic  topic
	 *
	 * @return \StdClass
	 *
	 * @since   Kunena 6.0
	 */
	public function getTopicLabel($topic): StdClass
	{
		$topicicontype = $this->params->get('topicicontype');
		$topiclabels   = $this->params->get('labels');

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

			return $this->get_xml_label($xml, $id, $topicicontype);
		}
	}

	/**
	 * @param   mixed   $src    src
	 * @param   int     $id     id
	 * @param   string  $style  style
	 *
	 * @return \StdClass
	 *
	 * @since   Kunena 6.0
	 */
	public function get_xml_label($src, $id = 0, $style = 'src'): StdClass
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
			$label->svg       = (string) $attributes->svg;
			$label->fa        = (string) $attributes->fa;
			$label->src       = (string) $attributes->src;
			$label->name      = (string) $attributes->name;
			$label->new       = (string) $attributes->new;
			$label->labeltype = (string) $attributes->labeltype;

			return $label;
		}
	}

	/**
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function borderless(): string
	{
		$borderless = $this->params->get('borderless');

		if ($borderless)
		{
			return '';
		}

		return ' table-bordered';
	}

	/**
	 * @param   bool  $class  class
	 *
	 * @return string
	 *
	 * @since   Kunena 6.0
	 */
	public function tooltips(bool $class = false): string
	{
		$tooltips = $this->params->get('tooltips');

		if ($tooltips)
		{
			if ($class)
			{
				return 'class="hasTooltip"';
			}

			return 'hasTooltip';
		}
	}

	/**
	 * @param   bool  $topic_ids  topics id's
	 *
	 * @return string
	 *
	 * @throws \Exception
	 * @since   Kunena 6.0
	 */
	public function recaptcha(bool $topic_ids = false): string
	{
		if (PluginHelper::isEnabled('captcha'))
		{
			PluginHelper::importPlugin('captcha');

			if ($topic_ids)
			{
				PluginHelper::importPlugin('captcha');
				Factory::getApplication()->triggerEvent('onInit', ['dynamic_recaptcha_' . $topic_ids]);
				$display = Factory::getApplication()->triggerEvent('onDisplay', [null, 'dynamic_recaptcha_' . $topic_ids, 'controls g-recaptcha']);
			}
			else
			{
				Factory::getApplication()->triggerEvent('onInit', ['dynamic_recaptcha_1']);
				$display = Factory::getApplication()->triggerEvent('onDisplay', [null, 'dynamic_recaptcha_1', 'controls g-recaptcha']);
			}

			return $display[0];
		}
	}

	/**
	 * Load fontawesome libraries with if enabled the compatibility layer for version 4.x
	 *
	 * @since Kunena 5.2
	 */
	public function loadFontawesome()
	{
		$fontawesome          = $this->params->get('fontawesome');
		$fontawesome_layer_v4 = $this->params->get('fontawesome_layer_v4');

		if ($fontawesome)
		{
			$this->addScript('https://use.fontawesome.com/releases/v5.15.4/js/all.js', [], ['defer' => true]);
		}

		if ($fontawesome && $fontawesome_layer_v4)
		{
			$this->addScript('https://use.fontawesome.com/releases/v5.15.4/js/v4-shims.js', [], ['defer' => true]);
		}
	}

	/**
	 * Wrapper to addScript
	 *
	 * @param   string  $filename  filename
	 * @param   array   $options   options
	 * @param   array   $attribs   attribs
	 *
	 * @return  Document|void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function addScript(string $filename, $options = [], $attribs = [])
	{
		$app    = Factory::getApplication();
		$format = $app->input->getCmd('format');

		if (!empty($format) && $format != 'html')
		{
			return false;
		}

		if (!preg_match('|https?://|', $filename))
		{
			if (preg_match('/assets/', $filename))
			{
				$filename = preg_replace('|^css/|u', '', $filename);
				$filename = preg_replace('/^assets\//', '', $filename);
				$filename = $this->getFile($filename, false, $this->pathTypes['js'], 'components/com_kunena/template/' . $this->name . '/assets');
			}
			else
			{
				$filename = $this->getFile($filename, false, $this->pathTypes['js'], $this->pathTypes['js']);
			}

			$filemin      = $filename;
			$filemin_path = preg_replace('/\.js$/u', '-min.js', $filename);

			if (!JDEBUG && !$this->config->debug && !KunenaForum::isDev() && is_file(JPATH_ROOT . "/$filemin_path"))
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
}
