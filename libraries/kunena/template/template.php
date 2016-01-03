<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Template
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

jimport('joomla.html.parameter');

/**
 * Kunena Users Table Class
 * Provides access to the #__kunena_users table
 */
class KunenaTemplate extends JObject
{
	// Global for every instance
	protected static $_instances = array();

	public $name = null;
	public $params = null;
	public $paramstime = false;

	protected $pathTypes = array();
	protected $pathTypeDefaults = array(
		'avatars'       => 'media/avatars',
		'emoticons'     => 'media/emoticons',
		'ranks'         => 'media/ranks',
		'icons'         => 'media/icons',
		'topicicons'    => 'media/topic_icons',
		'categoryicons' => 'media/category_icons',
		'images'        => 'media/images',
		'js'            => 'media/js',
		'css'           => 'media/css'
	);
	protected $pathTypeOld = array(
		'avatars'    => 'images/avatars',
		'emoticons'  => 'images/emoticons',
		'ranks'      => 'images/ranks',
		'icons'      => 'images/icons',
		'topicicons' => 'images/topicicons',
		'images'     => 'images',
		'js'         => 'js',
		'css'        => 'css'
	);
	protected $default = array();
	protected $paths = array();
	protected $css_compile = true;
	protected $filecache = array();
	protected $smileyPath = array();
	protected $rankPath = array();
	protected $userClasses = array(
		'kuser-',
		'admin'      => 'kuser-admin',
		'localadmin' => 'kuser-admin',
		'globalmod'  => 'kuser-globalmod',
		'moderator'  => 'kuser-moderator',
		'user'       => 'kuser-user',
		'guest'      => 'kuser-guest',
		'banned'     => 'kuser-banned',
		'blocked'    => 'kuser-blocked'
	);
	public $topicIcons = array();
	public $categoryIcons = array();

	protected $stylesheets = array();
	protected $style_variables = array();
	protected $compiled_style_variables = null;
	protected $scripts = array();
	protected $xml = null;
	protected $map;
	protected $hmvc;

	/**
	 * @var string
	 */
	protected $category_iconset = '';

	/**
	 * Constructor
	 *
	 * @access    protected
	 *
	 * @param null $name
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
		$this->xml_path = KPATH_SITE . "/template/{$name}/config.xml";

		if (!is_file($this->xml_path))
		{
			// Configuration file was not found - legacy template support.
			$this->xml_path = KPATH_SITE . "/template/{$name}/template.xml";
		}

		// TODO: move configuration out of filesystem (keep on legacy).
		$ini     = KPATH_SITE . "/template/{$name}/params.ini";
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

		$this->params = new JRegistry();
		$this->params->loadString($content, $format);

		// Load default values from configuration definition file.
		$this->xml = simplexml_load_file($this->xml_path);

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
	}

	public function getConfigXml()
	{
		// Find configuration file.
		$this->xml_path = KPATH_SITE . "/template/{$this->name}/config.xml";

		if (!is_file($this->xml_path))
		{
			$this->xml_path = KPATH_SITE . "/template/{$this->name}/template.xml";

			return false;
		}

		$xml = file_get_contents($this->xml_path);

		if (!strstr($xml, '<config>'))
		{
			// Update old template files to new format.
			$xml = preg_replace(
				array('|<params|', '|</params>|', '|<param\s+|', '|</param>|'),
				array('<config', '</config>', '<field ', '</field>'),
				$xml);
		}

		return $xml;
	}

	public function loadLanguage()
	{
		// Loading language strings for the template
		$lang = JFactory::getLanguage();
		KunenaFactory::loadLanguage('com_kunena.templates', 'site');

		foreach (array_reverse($this->default) as $template)
		{
			// Try to load language file for legacy templates
			if ($lang->load('com_kunena.tpl_' . $template, JPATH_SITE)
				|| $lang->load('com_kunena.tpl_' . $template, KPATH_SITE)
				|| $lang->load('com_kunena.tpl_' . $template, KPATH_SITE . '/template/' . $template)
			)
			{
				$lang->load('com_kunena.tpl_' . $template, JPATH_SITE)
				|| $lang->load('com_kunena.tpl_' . $template, KPATH_SITE)
				|| $lang->load('com_kunena.tpl_' . $template, KPATH_SITE . '/template/' . $template);
			}
			else
			{
				$lang->load('kunena_tmpl_' . $template, JPATH_SITE)
				|| $lang->load('kunena_tmpl_' . $template, KPATH_SITE)
				|| $lang->load('kunena_tmpl_' . $template, KPATH_SITE . '/template/' . $template);
			}
		}
	}

	public function initialize()
	{
		$this->loadLanguage();
	}

	public function initializeBackend()
	{
		$this->loadLanguage();
	}

	public function getUserClasses()
	{
		return $this->userClasses;
	}

	public function getButton($link, $name, $scope, $type, $id = null)
	{
		$types = array('communication' => 'comm', 'user' => 'user', 'moderation' => 'mod');
		$names = array('unsubscribe' => 'subscribe', 'unfavorite' => 'favorite', 'unsticky' => 'sticky', 'unlock' => 'lock', 'create' => 'newtopic',
		               'quickreply'  => 'reply', 'quote' => 'kquote', 'edit' => 'kedit');

		$text  = JText::_("COM_KUNENA_BUTTON_{$scope}_{$name}");
		$title = JText::_("COM_KUNENA_BUTTON_{$scope}_{$name}_LONG");

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
<a $id class="kicon-button kbutton{$type} btn-left" href="{$link}" rel="nofollow" title="{$title}">
	<span class="{$name}"><span>{$text}</span></span>
</a>
HTML;
	}

	public function getIcon($name, $title = '')
	{
		return '<span class="kicon ' . $name . '" title="' . $title . '"></span>';
	}

	public function getImage($image, $alt = '')
	{
		return '<img src="' . $this->getImagePath($image) . '" alt="' . $alt . '" />';
	}

	public function getPaginationListFooter($list)
	{
		$html = '<div class="list-footer">';
		$html .= '<div class="limit">' . JText::_('COM_KUNENA_LIB_HTML_DISPLAY_NUM') . ' ' . $list['limitfield'] . '</div>';
		$html .= $list['pageslinks'];
		$html .= '<div class="counter">' . $list['pagescounter'] . '</div>';
		$html .= '<input type="hidden" name="' . $list['prefix'] . 'limitstart" value="' . $list['limitstart'] . '" />';
		$html .= '</div>';

		return $html;
	}

	public function getPaginationListRender($list)
	{
		$html = '<ul class="kpagination">';
		$html .= '<li class="page">' . JText::_('COM_KUNENA_PAGE') . '</li>';
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

	public function getPaginationItemActive($item)
	{
		return '<a title="' . $item->text . '" href="' . $item->link . '" class="pagenav">' . $item->text . '</a>';
	}

	public function getPaginationItemInactive($item)
	{
		return '<span class="pagenav">' . $item->text . '</span>';
	}

	public function getClass($class, $class_sfx = '')
	{
		return $class . ($class_sfx ? " {$class}.{$class_sfx}" : '');
	}

	public function loadMootools()
	{
		JHtml::_('behavior.framework', true);

		if (JDEBUG || KunenaFactory::getConfig()->debug)
		{
			// Debugging Mootools issues
			$this->addScript('debug.js');
		}
	}

	public function getStyleVariables()
	{
		return $this->style_variables;
	}

	public function getStyleVariable($name, $default = '')
	{
		return isset($this->style_variables[$name]) ? $this->style_variables[$name] : $default;
	}

	public function setStyleVariable($name, $value)
	{
		$this->compiled_style_variables = null;

		return $this->style_variables[$name] = $value;
	}

	public function addStyleSheet($filename, $group = 'forum')
	{
		if (!preg_match('|https?://|', $filename))
		{
			$filename     = preg_replace('|^css/|u', '', $filename);
			$filemin      = $filename = $this->getFile($filename, false, $this->pathTypes['css'], 'media/kunena/css');
			$filemin_path = preg_replace('/\.css$/u', '-min.css', $filename);

			if (!JDEBUG && !KunenaFactory::getConfig()->debug && !KunenaForum::isDev() && is_file(JPATH_ROOT . "/$filemin_path"))
			{
				$filemin = preg_replace('/\.css$/u', '-min.css', $filename);
			}

			if (file_exists(JPATH_ROOT . "/$filemin"))
			{
				$filename = $filemin;
			}

			$filename = JUri::root(true) . "/{$filename}";
		}

		return JFactory::getDocument()->addStyleSheet($filename);
	}

	public function addIEStyleSheet($filename, $condition = 'IE')
	{
		$filename  = preg_replace('|^css/|u', '', $filename);
		$url       = $this->getFile($filename, true, $this->pathTypes['css'], 'media/kunena/css');
		$stylelink = "<!--[if {$condition}]>\n";
		$stylelink .= '<link rel="stylesheet" href="' . $url . '" />' . "\n";
		$stylelink .= "<![endif]-->\n";
		JFactory::getDocument()->addCustomTag($stylelink);
	}

	public function clearCache()
	{
		$path = JPATH_ROOT . "/media/kunena/cache/{$this->name}";

		if (is_dir($path))
		{
			KunenaFolder::delete($path);
		}
	}

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

	function findUrl($matches)
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
	 * @param        $content
	 * @param string $type
	 *
	 * @return JDocument
	 */
	function addScriptDeclaration($content, $type = 'text/javascript')
	{
		return JFactory::getDocument()->addScriptDeclaration($content, $type);
	}

	/**
	 * Wrapper to addScript
	 *
	 * @param $filename
	 *
	 * @return JDocument
	 */
	function addScript($filename)
	{
		if (!preg_match('|https?://|', $filename))
		{
			$filename     = preg_replace('|^js/|u', '', $filename);
			$filemin_path = preg_replace('/\.js$/u', '-min.js', $filename);

			if (!JDEBUG && !KunenaFactory::getConfig()->debug && !KunenaForum::isDev() && is_file(JPATH_ROOT . "/media/kunena/$filemin_path"))
			{
				// If we are in debug more, make sure we load the unpacked css
				$filename = preg_replace('/\.js$/u', '-min.js', $filename);
			}

			$filename = $this->getFile($filename, true, $this->pathTypes['js'], 'media/kunena/js', 'default');
		}

		return JFactory::getDocument()->addScript($filename);
	}

	public function addPath($path)
	{
		$this->paths[] = KunenaPath::clean("/$path");
	}

	public function getTemplatePaths($path = '', $fullpath = false)
	{
		$app = JFactory::getApplication();

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

	public function getFile($file, $url = false, $basepath = '', $default = null, $ignore = null)
	{
		if ($basepath)
		{
			$basepath = '/' . $basepath;
		}

		$filepath = "{$basepath}/{$file}";

		if (!isset($this->filecache[$filepath]))
		{
			$this->filecache[$filepath] = $default ? "{$default}/{$file}" : KPATH_COMPONENT_RELATIVE . "/template/blue_eagle/{$file}";
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

		return ($url ? JUri::root(true) . '/' : '') . $this->filecache[$filepath];
	}

	public function getAvatarPath($filename = '', $url = false)
	{
		return $this->getFile($filename, $url, $this->pathTypes['avatars'], 'media/kunena/avatars');
	}

	public function getSmileyPath($filename = '', $url = false)
	{
		return $this->getFile($filename, $url, $this->pathTypes['emoticons'], 'media/kunena/emoticons');
	}

	public function getRankPath($filename = '', $url = false)
	{
		return $this->getFile($filename, $url, $this->pathTypes['ranks'], 'media/kunena/ranks');
	}

	public function getTopicIconPath($filename = '', $url = true)
	{
		$config = KunenaFactory::getConfig();

		if ($config->topicicons)
		{
			if ($this->isHMVC())
			{
				$category_iconset = 'images/topic_icons/';
				if (!file_exists($category_iconset))
				{
					$category_iconset = 'media/kunena/topic_icons' . $this->category_iconset;
				}
			}
			else
			{
				$category_iconset = 'images/topicicons/';
				if (!file_exists($category_iconset))
				{
					$category_iconset = 'media/kunena/topicicons/default';
					if (!file_exists($category_iconset))
					{
						$category_iconset = 'media/kunena/topic_icons/default';
					}
				}
			}
		}
		else
		{
			if ($this->isHMVC())
			{
				$category_iconset = 'images/topic_icons';
				if (!file_exists($category_iconset))
				{
					$category_iconset = 'media/kunena/topic_icons';
				}
			}
			else
			{
				$category_iconset = 'images/topicicons';
				if (!file_exists($category_iconset))
				{
					$category_iconset = 'media/kunena/topicicons';
					if (!file_exists($category_iconset))
					{
						$category_iconset = 'media/kunena/topic_icons';
					}
				}
			}
		}

		return $this->getFile($filename, $url, $this->pathTypes['topicicons'], $category_iconset);
	}

	public function getCategoryIconPath($filename = '', $url = true, $category_iconset)
	{
		if (!$this->isHmvc())
		{
			$set              = '';
			$category_iconset = 'default';
		}

		return $this->getFile($filename, $url, $this->pathTypes['categoryicons'] . $set, 'media/kunena/category_icons/' . $category_iconset);
	}

	public function getImagePath($filename = '', $url = true)
	{
		return $this->getFile($filename, $url, $this->pathTypes['images'], 'media/kunena/images');
	}

	public function getTopicIcons($all = false, $checked = 0)
	{
		if ($this->isHMVC())
		{
			$category_iconset = $this->category_iconset;
		}
		else
		{
			$category_iconset = 'default';
		}

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
							$icon       = new stdClass();
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
							$icon->fa                    = (string) $attributes->fa;
							$icon->filename              = (string) $attributes->src;
							$icon->width                 = (int) $attributes->width ? (int) $attributes->width : $width;
							$icon->height                = (int) $attributes->height ? (int) $attributes->height : $height;
							$icon->relpath               = $this->getTopicIconPath("{$icon->filename}", false, $category_iconset);
							$this->topicIcons[$icon->id] = $icon;
						}
					}
				}
			}

			// Make sure that default icon exists (use user/default.png in current template)
			if (!isset($this->topicIcons[0]))
			{
				$icon                = new StdClass();
				$icon->id            = 0;
				$icon->type          = 'user';
				$icon->name          = 'default';
				$icon->published     = 0;
				$icon->title         = 'Default';
				$icon->filename      = 'default.png';
				$icon->width         = 48;
				$icon->height        = 48;
				$icon->relpath       = $this->getTopicIconPath("user/{$icon->filename}", false, $category_iconset);
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
							$icon       = new stdClass();
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
				$icon                   = new StdClass();
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

	public function getTopicIconIndexPath($index, $url = false)
	{
		if (empty($this->topicIcons))
		{
			$this->getTopicIcons(false, 0, $this->category_iconset);
		}

		if (empty($this->topicIcons[$index]->published))
		{
			$index = 0;
		}

		$icon = $this->topicIcons[$index];

		return $this->getTopicIconPath($icon->filename, $url);
	}

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
	 * Get the th topic icon depending on template settings
	 *
	 * @param KunenaForumTopic $topic
	 * @param $category_iconset
	 *
	 * @return string
	 */
	public function getTopicIcon($topic, $category_iconset = '')
	{
		$config = KunenaFactory::getConfig();
		$this->ktemplate = KunenaFactory::getTemplate();

		if ($this->isHMVC())
		{
			$topicicontype = $this->ktemplate->params->get('topicicontype');
		}
		else
		{
			$topicicontype = '0';
		}

		if ($this->isHMVC() && !empty($category_iconset))
		{
			$this->category_iconset = '/' . $category_iconset;
		}
		else
		{
			if ($config->topicicons)
			{
				$this->category_iconset = '/default';
			}
		}

		if ($config->topicicons)
		{
			if ($topic->icon_id == 5 || $topic->ordering)
			{
				if ($topicicontype == 'B2' || $topicicontype == 'B3')
				{
					$icon = 'pushpin';
				}
				else if ($topicicontype == 'fa')
				{
					$icon = 'thumb-tack';
				}
			}
			elseif ($topic->icon_id == 1)
			{
				if ($topicicontype == 'B2')
				{
					$icon = 'notification-circle';
				}
				else if ($topicicontype == 'B3')
				{
					$icon = 'exclamation-sign';
				}
				else if ($topicicontype == 'fa')
				{
					$icon = 'exclamation-circle';
				}
			}
			elseif ($topic->icon_id == 2)
			{
				if ($topicicontype == 'B2' || $topicicontype == 'B3')
				{
					$icon = 'question-sign';
				}
				else if ($topicicontype == 'fa')
				{
					$icon = 'question-circle';
				}
			}
			elseif ($topic->icon_id == 3)
			{
				if ($topicicontype == 'B2' || $topicicontype == 'B3')
				{
					$icon = 'lamp';
				}
				else if ($topicicontype == 'fa')
				{
					$icon = 'lightbulb-o';
				}
			}
			elseif ($topic->icon_id == 4)
			{
				$icon = 'heart';
			}
			elseif ($topic->icon_id == 5)
			{
				$icon = 'heart';
			}
			elseif ($topic->icon_id == 6)
			{
				$icon = 'heart';
			}
			elseif ($topic->icon_id == 7)
			{
				$icon = 'heart';
			}
			elseif ($topic->icon_id == 8)
			{
				if ($topicicontype == 'B2' || $topicicontype == 'B3')
				{
					$icon = 'ok';
				}
				else if ($topicicontype == 'fa')
				{
					$icon = 'check';
				}
			}
			elseif ($topic->icon_id == 9)
			{
				if ($topicicontype == 'B3')
				{
					$icon = 'resize-small';
				}
				else if ($topicicontype == 'B2')
				{
					$icon = 'contract';
				}
				else if ($topicicontype == 'fa')
				{
					$icon = 'compress';
				}
			}
			elseif ($topic->icon_id == 10)
			{
				if ($topicicontype == 'B3' || $topicicontype == 'B2')
				{
					$icon = 'remove';
				}
				else if ($topicicontype == 'fa')
				{
					$icon = 'times';
				}
			}
			elseif ($topic->locked)
			{
				if ($topicicontype == 'B2')
				{
					$icon = 'locked';
				}
				else if ($topicicontype == 'B3' || $topicicontype == 'fa')
				{
					$icon = 'lock';
				}
			}
			elseif ($topic->icon_id == 5 || $topic->ordering && $topic->locked)
			{
				$icon = 'pushpin';
			}
			else
			{
				$icon = 'file';
			}

			if ($topicicontype == '0' || !$topicicontype)
			{
				// TODO: use xml file instead
				if ($topic->moved_id)
				{
					$icon = 'system_moved';
				}
				elseif ($topic->hold == 2 || $topic->hold == 3)
				{
					$icon = 'system_deleted';
				}
				elseif ($topic->hold == 1)
				{
					$icon = 'system_unapproved';
				}
				elseif ($topic->ordering && $topic->locked)
				{
					$icon = 'system_sticky_locked';
				}
				elseif ($topic->ordering)
				{
					$icon = 'system_sticky';
				}
				elseif ($topic->locked)
				{
					$icon = 'system_locked';
				}
				else
				{
					$icon = $topic->icon_id;
				}

				$iconurl = $this->getTopicIconIndexPath($icon, true);
			}
		}
		else
		{
			$icon = 'normal';
			if ($topic->posts < 2)
			{
				$icon = 'unanswered';
			}

			if ($topic->ordering)
			{
				$icon = 'sticky';
			}

			//if ($topic->myfavorite) $icon = 'favorite';
			if ($topic->locked)
			{
				$icon = 'locked';
			}

			if ($topic->ordering && $topic->locked)
			{
				$icon = 'sticky_and_locked';
			}

			if ($topic->hold == 1)
			{
				$icon = 'unapproved';
			}

			if ($topic->hold == 2)
			{
				$icon = 'deleted';
			}

			if ($topic->moved_id)
			{
				$icon = 'moved';
			}

			if (!empty($topic->unread))
			{
				$icon .= '_new';
			}

			$iconurl = $this->getTopicIconPath("system/{$icon}.png", true);
		}

		if ($topicicontype == 'B2')
		{
			if ($config->topicicons)
			{
				$html = '<span class="icon icon-' . $icon . ' icon-topic" aria-hidden="true"></span>';
			}
			else {
				$html = '<img src="' . $iconurl . '" alt="emo" />';
			}
		}
		elseif ($topicicontype == 'B3')
		{
			if ($config->topicicons)
			{
				$html = '<span class="glyphicon glyphicon-' . $icon . ' glyphicon-topic" aria-hidden="true"></span>';
			}
			else
			{
				$html = '<img src="' . $iconurl . '" alt="emo" />';
			}
		}
		elseif ($topicicontype == 'fa')
		{
			if ($config->topicicons)
			{
				$html = '<i class="fa fa-' . $icon . ' fa-2x"></i>';
			}
			else
			{
				$html = '<img src="' . $iconurl . '" alt="emo" />';
			}
		}
		elseif ($topicicontype == '0' || !$topicicontype)
		{
			$html = '<img src="' . $iconurl . '" alt="emo" />';
		}

		return $html;
	}

	/**
	 * @param KunenaForumCategory $category
	 *
	 * @return string
	 */
	public function getCategoryIcon($category)
	{
		$config = KunenaFactory::getConfig();
		if ($config->categoryicons)
		{
			// TODO: use xml file instead
			$icon    = $category->icon_id;
			$iconurl = $this->getCategoryIconIndexPath($icon, true);
		}
		else
		{
			$icon = 'folder';
			// FIXME: hardcoded to system type...
			$iconurl = $this->getCategoryIconPath("system/{$icon}.png", true);
		}
		$html = '<img src="' . $iconurl . '" alt="emo" />';

		return $html;
	}

	/**
	 * @param $filename
	 *
	 * @return string
	 * @deprecated K4.0
	 */
	public function getTopicsIconPath($filename)
	{
		if (empty($filename))
		{
			return;
		}

		return "media/kunena/topicicons/{$filename}";
	}

	public function getTemplateDetails()
	{
		$xml = simplexml_load_file(KPATH_SITE . "/template/{$this->name}/template.xml");

		return $xml;
	}

	function compileLess($inputFile, $outputFile)
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
		$less->registerFunction('url', function ($arg) use ($class)
		{
			list($type, $q, $values) = $arg;
			$value = reset($values);

			return "url({$q}{$class->getFile($value, true, 'media', 'media/kunena')}{$q})";
		});
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
	 * @param $search
	 *
	 * @return array
	 * @deprecated K4.0
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

	public function isHmvc()
	{
		$app = JFactory::getApplication();
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
	 * Set the category iconset value
	 *
	 * @return void
	 */
	public function setCategoryIconset($iconset = '/default')
	{
		$this->category_iconset = '/' . $iconset;
	}

	/**
	 * Returns the global KunenaTemplate object, only creating it if it doesn't already exist.
	 *
	 * @access public
	 *
	 * @param int $name Template name or null for default/selected template in your configuration
	 *
	 * @return KunenaTemplate The template object.
	 * @since  1.6
	 */
	public static function getInstance($name = null)
	{
		$app = JFactory::getApplication();
		if (!$name)
		{

			$name = JFactory::getApplication()->input->cookie->getString('kunena_template', KunenaFactory::getConfig()->template);
		}

		$name = KunenaPath::clean($name);
		if (empty(self::$_instances[$name]))
		{
			// Find overridden template class (use $templatename to avoid creating new objects if the template doesn't exist)
			$templatename = $name;
			// directories in lower case
			$relTmplDirName = strtolower($templatename);
			// classnames perhaps in CamelCase
			$classname = "KunenaTemplate{$templatename}";

			if (!is_file(KPATH_SITE . "/template/{$relTmplDirName}/template.xml") && !is_file(KPATH_SITE . "/template/{$relTmplDirName}/config.xml"))
			{
				// If template xml doesn't exist, raise warning and use blue eagle instead
				$file         = JPATH_THEMES . "/{$app->getTemplate()}/html/com_kunena/template.php";
				$templatename = 'blue_eagle';
				$classname    = "KunenaTemplate{$templatename}";

				if (is_dir(KPATH_SITE . "/template/{$relTmplDirName}"))
				{
					KunenaError::warning(JText::sprintf('COM_KUNENA_LIB_TEMPLATE_NOTICE_INCOMPATIBLE', $name, $templatename));
				}
			}

			if (!class_exists($classname) && $app->isSite())
			{

				$file = KPATH_SITE . "/template/{$relTmplDirName}/template.php";

				if (!is_file($file))
				{
					$classname = "KunenaTemplateBlue_Eagle";
					$file      = KPATH_SITE . "/template/blue_eagle/template.php";
				}

				if (is_file($file))
				{
					require_once $file;
				}
			}
			if (class_exists($classname))
			{
				self::$_instances [$name] = new $classname ($relTmplDirName);
			}
			else
			{
				self::$_instances [$name] = new KunenaTemplate ($relTmplDirName);
			}
		}

		return self::$_instances [$name];
	}
}
