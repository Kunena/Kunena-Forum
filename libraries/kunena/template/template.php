<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Template
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport('joomla.html.parameter');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.path');

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

	protected $default = array();
	protected $css_compile = true;
	protected $filecache = array();
	protected $smileyPath = array();
	protected $rankPath = array();
	protected $userClasses = array(
		'kuser-',
		'admin'=>'kuser-admin',
		'globalmod'=>'kuser-globalmod',
		'moderator'=>'kuser-moderator',
		'user'=>'kuser-user',
		'guest'=>'kuser-guest',
		'banned'=>'kuser-banned',
		'blocked'=>'kuser-blocked'
	);
	public $topicIcons = array();

	protected $stylesheets = array();
	protected $style_variables = array();
	protected $compiled_style_variables = null;
	protected $scripts = array();
	protected $xml = null;

	/**
	* Constructor
	*
	* @access	protected
	*/
	public function __construct($name=null) {
		if (!$name) {
			$name = KunenaFactory::getConfig()->template;
		}
		$name = JPath::clean($name);

		// Create template inheritance
		if (!is_array($this->default)) $this->default = (array) $this->default;
		array_unshift($this->default, $name);
		$this->default = array_unique($this->default);

		// Find configuration file.
		$this->xml_path = KPATH_SITE . "/template/{$name}/config.xml";
		if (!file_exists($this->xml_path)) {
			// Configuration file was not found - legacy template support.
			$this->xml_path = KPATH_SITE . "/template/{$name}/template.xml";
		}

		// TODO: move configuration out of filesystem (keep on legacy).
		$ini = KPATH_SITE . "/template/{$name}/params.ini";
		$content = '';
		$format = 'INI';
		if (is_readable( $ini ) ) {
			$this->paramstime = filemtime($ini);
			$content = file_get_contents($ini);
			// Workaround a bug in previous versions (file may contain JSON).
			if ($content && $content[0] == '{') $format = 'JSON';
		}
		$this->name = $name;

		$this->params = new JRegistry();
		$this->params->loadString($content, $format);

		// Load default values from configuration definition file.
		$this->xml = simplexml_load_file($this->xml_path);
		if ($this->xml) {
			foreach ($this->xml->xpath('//field') as $node) {
				if (isset($node['name']) && isset($node['default'])) $this->params->def($node['name'], (string)$node['default']);
			}
			// Generate CSS variables for less compiler.
			foreach ($this->params->toArray() as $key=>$value)  {
				if (substr($key,0,5) == 'style' && $value) {
					$this->style_variables[$key] = $value;
				}
			}
		}
	}

	public function getConfigXml() {
		// Find configuration file.
		$this->xml_path = KPATH_SITE . "/template/{$this->name}/config.xml";
		if (!file_exists($this->xml_path)) {
			$this->xml_path = KPATH_SITE . "/template/{$this->name}/template.xml";
		}
		if (!file_exists($this->xml_path)) return false;

		$xml = file_get_contents($this->xml_path);
		if (!strstr($xml, '<config>')) {
			// Update old template files to new format.
			$xml = preg_replace(
					array('|<params|', '|</params>|', '|<param\s+|', '|</param>|'),
					array('<config', '</config>','<field ', '</field>'),
					$xml);
		}
		return $xml;
	}

	public function loadLanguage() {
		// Loading language strings for the template
		$lang = JFactory::getLanguage();
		KunenaFactory::loadLanguage('com_kunena.templates', 'site');
		foreach (array_reverse($this->default) as $template) {
			$file = 'com_kunena.tpl_'.$template;
			$lang->load($file, JPATH_SITE)
				|| $lang->load($file, KPATH_SITE)
				|| $lang->load($file, KPATH_SITE.'/template/'.$template);
		}
	}

	public function initialize() {
		$this->loadLanguage();
	}

	public function initializeBackend() {
		$this->loadLanguage();
	}

	public function getUserClasses() {
		return $this->userClasses;
	}

	public function getButton($link, $name, $scope, $type, $id = null) {
		$types = array('communication'=>'comm', 'user'=>'user', 'moderation'=>'mod');
		$names = array('unsubscribe'=>'subscribe', 'unfavorite'=>'favorite', 'unsticky'=>'sticky', 'unlock'=>'lock', 'create'=>'newtopic',
				'quickreply'=>'reply', 'quote'=>'kquote', 'edit'=>'kedit');

		$text = JText::_("COM_KUNENA_BUTTON_{$scope}_{$name}");
		$title = JText::_("COM_KUNENA_BUTTON_{$scope}_{$name}_LONG");
		if ($title == "COM_KUNENA_BUTTON_{$scope}_{$name}_LONG") $title = '';
		if ($id) $id = 'id="'.$id.'"';

		if (isset($types[$type])) $type = $types[$type];
		if ($name == 'quickreply') $type .= ' kqreply';
		if (isset($names[$name])) $name = $names[$name];

		return <<<HTML
<a $id class="kicon-button kbutton{$type} btn-left" href="{$link}" rel="nofollow" title="{$title}">
	<span class="{$name}"><span>{$text}</span></span>
</a>
HTML;
	}

	public function getIcon($name, $title='') {
		return '<span class="kicon '.$name.'" title="'.$title.'"></span>';
	}

	public function getImage($image, $alt='') {
		return '<img src="'.$this->getImagePath($image).'" alt="'.$alt.'" />';
	}

	public function getPaginationListFooter($list) {
		$html = '<div class="list-footer">';
		$html .= '<div class="limit">'.JText::_('COM_KUNENA_LIB_HTML_DISPLAY_NUM').' '.$list['limitfield'].'</div>';
		$html .= $list['pageslinks'];
		$html .= '<div class="counter">'.$list['pagescounter'].'</div>';
		$html .= '<input type="hidden" name="' . $list['prefix'] . 'limitstart" value="'.$list['limitstart'].'" />';
		$html .= '</div>';
		return $html;
	}

	public function getPaginationListRender($list) {
		$html = '<ul class="kpagination">';
		$html .= '<li class="page">'.JText::_('COM_KUNENA_PAGE').'</li>';
		$last = 0;
		foreach($list['pages'] as $i=>$page) {
			if ($last+1 != $i) $html .= '<li>...</li>';
			$html .= '<li>'.$page['data'].'</li>';
			$last = $i;
		}
		$html .= '</ul>';
		return $html;
	}

	public function getPaginationItemActive($item) {
		return '<a title="'.$item->text.'" href="'.$item->link.'" class="pagenav">'.$item->text.'</a>';
	}

	public function getPaginationItemInactive($item) {
		return '<span class="pagenav">'.$item->text.'</span>';
	}

	public function getClass($class, $class_sfx='') {
		return $class.($class_sfx ? " {$class}.{$class_sfx}" : '');
	}

	public function loadMootools() {
		JHtml::_ ( 'behavior.framework', true );

		if (JDEBUG || KunenaFactory::getConfig()->debug) {
			// Debugging Mootools issues
			$this->addScript ( 'js/debug.js' );
		}
	}

	public function getStyleVariables() {
		return $this->style_variables;
	}

	public function getStyleVariable($name, $default='') {
		return isset($this->style_variables[$name]) ? $this->style_variables[$name] : $default;
	}

	public function setStyleVariable($name, $value) {
		$this->compiled_style_variables = null;
		return $this->style_variables[$name] = $value;
	}

	public function addStyleSheet($filename, $group='forum') {
		$filemin = $filename = $this->getFile($filename);
		$filemin_path = preg_replace ( '/\.css$/u', '-min.css', $filename );
		if (!JDEBUG && !KunenaFactory::getConfig ()->debug && !KunenaForum::isDev () && JFile::exists(JPATH_ROOT."/$filemin_path")) {
			$filemin = preg_replace ( '/\.css$/u', '-min.css', $filename );
		}
		if (JFile::exists(JPATH_ROOT."/$filemin")) {
			$filename = $filemin;
		}
		return JFactory::getDocument ()->addStyleSheet ( JUri::root(true)."/{$filename}" );
	}

	public function addIEStyleSheet($filename, $condition='IE') {
		$url = $this->getFile($filename, true);
		$stylelink = "<!--[if {$condition}]>\n";
		$stylelink .= '<link rel="stylesheet" href="'.$url.'" />' ."\n";
		$stylelink .= "<![endif]-->\n";
		JFactory::getDocument()->addCustomTag($stylelink);
	}

	public function clearCache() {
		$path = JPATH_ROOT."/media/kunena/cache/{$this->name}";
		if (JFolder::exists($path)) JFolder::delete($path);
	}

	public function getCachePath($filename='') {
		if ($filename) $filename = '/'.$filename;
		if (JDEBUG || KunenaFactory::getConfig ()->debug) {
			$filename = "media/kunena/cache/{$this->name}/debug{$filename}";
		} else {
			$filename = "media/kunena/cache/{$this->name}{$filename}";
		}
		return $filename;
	}

	function findUrl($matches) {
		$file = trim($matches[1],' \'"');
		if (preg_match('#^../#', $file)) {
			$file = $this->getFile(substr($file, 3), true, '', 'media/kunena');
		}
		return "url('{$file}')";
	}

	/**
	 * Wrapper to addScript
	 */
	function addScript($filename) {
		$filemin_path = preg_replace ( '/\.js$/u', '-min.js', $filename );
		if (!JDEBUG && !KunenaFactory::getConfig ()->debug && !KunenaForum::isDev () && JFile::exists(JPATH_ROOT."/$filemin_path")) {
			// If we are in debug more, make sure we load the unpacked css
			$filename = preg_replace ( '/\.js$/u', '-min.js', $filename );
		}
		return JFactory::getDocument ()->addScript ( $this->getFile($filename, true, '', 'media/kunena', 'default') );
	}

	public function getTemplatePaths($path = '', $fullpath = false) {
		if ($path) $path = JPath::clean("/$path");
		$array = array();
		foreach (array_reverse($this->default) as $template) {
			$array[] = ($fullpath ? KPATH_SITE : KPATH_COMPONENT_RELATIVE).'/template/'.$template.$path;
		}
		return $array;
	}

	public function getFile($file, $url = false, $basepath = '', $default = null, $ignore = null) {
		if ($basepath) $basepath = '/' . $basepath;
		$filepath = "{$basepath}/{$file}";
		if (!isset($this->filecache[$filepath])) {
			$this->filecache[$filepath] = $default ? "{$default}/{$file}" : KPATH_COMPONENT_RELATIVE."/template/blue_eagle/{$file}";
			foreach ($this->default as $template) {
				if ($template == $ignore) continue;
				$path = "template/{$template}{$basepath}";
				if (file_exists(KPATH_SITE . "/{$path}/{$file}")) {
					$this->filecache[$filepath] = KPATH_COMPONENT_RELATIVE."/{$path}/{$file}";
					break;
				}
			}
		}
		return ($url ? JUri::root(true).'/' : '').$this->filecache[$filepath];
	}

	public function getSmileyPath($filename='', $url = false) {
		return $this->getFile($filename, $url, 'images/emoticons', 'media/kunena/emoticons');
	}

	public function getRankPath($filename='', $url = false) {
		return $this->getFile($filename, $url, 'images/ranks', 'media/kunena/ranks');
	}

	public function getTopicIconPath($filename='', $url = true) {
		return $this->getFile($filename, $url, 'images/topicicons', 'media/kunena/topicicons');
	}

	public function getImagePath($filename='', $url = true) {
		return $this->getFile($filename, $url, 'images', 'media/kunena/images');
	}

	public function getTopicIcons($all = false, $checked = 0) {
		if (empty($this->topicIcons)) {
			$xmlfile = JPATH_ROOT.'/media/kunena/topicicons/default/topicicons.xml';
			if (file_exists($xmlfile)) {
				$xml = simplexml_load_file($xmlfile);
				if (isset($xml->icons)) {
					foreach($xml->icons as $icons) {
						$type = (string) $icons->attributes()->type;
						$width = (int) $icons->attributes()->width;
						$height = (int) $icons->attributes()->height;
						foreach($icons->icon as $icon) {
							$attributes = $icon->attributes();
							$icon = new stdClass();
							$icon->id = (int) $attributes->id;
							$icon->type = (string) $attributes->type ? (string) $attributes->type : $type;
							$icon->name = (string) $attributes->name;
							if ($icon->type != 'user') {
								$icon->id = $icon->type.'_'.$icon->name;
							}
							$icon->published = (int) $attributes->published;
							$icon->title = (string) $attributes->title;
							$icon->filename = (string) $attributes->src;
							$icon->width = (int) $attributes->width ? (int) $attributes->width : $width;
							$icon->height = (int) $attributes->height ? (int) $attributes->height : $height;
							$this->topicIcons[$icon->id] = $icon;
						}
					}
				}
			}
			// Make sure that default icon exists (use user/default.png in current template)
			if (!isset($this->topicIcons[0])) {
				$icon = new StdClass();
				$icon->id = 0;
				$icon->type = 'user';
				$icon->name = 'default';
				$icon->published = 0;
				$icon->title = 'Default';
				$icon->filename = 'default.png';
				$icon->width = 48;
				$icon->height = 48;
				$icon->relpath = $this->getTopicIconPath("user/{$icon->filename}", false);
				$this->topicIcons[0] = $icon;
			}
		}
		if ($all) {
			$icons = $this->topicIcons;
		} else {
			$icons = array();
			foreach ($this->topicIcons as $icon) {
				if ($icon->published && is_numeric($icon->id)) {
					$icons[$icon->id] = clone $icon;
					$icons[$icon->id]->checked = ($checked == $icon->id);
				}
			}
		}
		return $icons;
	}

	public function getTopicIconIndexPath($index, $url = false) {
		if (empty($this->topicIcons)) {
			$this->getTopicIcons();
		}
		if (empty($this->topicIcons[$index]->published)) {
			$index = 0;
		}
		$icon = $this->topicIcons[$index];
		return $this->getTopicIconPath("default/{$icon->filename}", $url);
	}

	public function getTopicIcon($topic ) {
		$config = KunenaFactory::getConfig ();
		if ($config->topicicons) {
			// TODO: use xml file instead
			if ($topic->moved_id) $icon = 'system_moved';
			elseif ($topic->hold == 2 || $topic->hold == 3) $icon = 'system_deleted';
			elseif ($topic->hold == 1) $icon = 'system_unapproved';
			elseif ($topic->ordering && $topic->locked) $icon = 'system_sticky_locked';
			elseif ($topic->ordering) $icon = 'system_sticky';
			elseif ($topic->locked) $icon = 'system_locked';
			else $icon = $topic->icon_id;
			$iconurl = $this->getTopicIconIndexPath($icon, true);
		} else {
			$icon = 'normal';
			if ($topic->posts < 2) $icon = 'unanswered';
			if ($topic->ordering) $icon = 'sticky';
			//if ($topic->myfavorite) $icon = 'favorite';
			if ($topic->locked) $icon = 'locked';
			if ($topic->hold == 1) $icon = 'unapproved';
			if ($topic->hold == 2) $icon = 'deleted';
			if ($topic->moved_id) $icon = 'moved';
			if (!empty($topic->unread)) $icon .= '_new';
			$iconurl = $this->getTopicIconPath("system/{$icon}.png", true);
		}
		$html = '<img src="'.$iconurl.'" alt="emo" />';
		return $html;
	}

	// TODO: remove in the future
	public function getTopicsIconPath($filename) {
		if ( empty($filename) ) return;

		return "media/kunena/topicicons/{$filename}";
	}

	public function getTemplateDetails() {
		$xml = simplexml_load_file(KPATH_SITE . "/template/{$this->name}/template.xml");
		return $xml;
	}

	function compileLess($inputFile, $outputFile) {
		if ( !class_exists( 'lessc' ) ) {
			require_once KPATH_FRAMEWORK . '/external/lessc/lessc.php';
		}

		// Load the cache.
		$cacheDir = JPATH_CACHE.'/kunena';
		if (!is_dir($cacheDir)) JFolder::create($cacheDir);
		$cacheFile = "{$cacheDir}/kunena.{$this->name}.{$inputFile}.cache";
		if ( file_exists( $cacheFile ) ) {
			$cache = unserialize( file_get_contents( $cacheFile ) );
		} else {
			$cache = JPATH_SITE.'/'.$this->getFile($inputFile, false, 'less');
		}
		$outputDir = KPATH_MEDIA."/cache/{$this->name}/css";
		if (!is_dir($outputDir)) JFolder::create($outputDir);
		$outputFile = "{$outputDir}/{$outputFile}";

		$less = new lessc;
		$less->setVariables($this->style_variables);
		$newCache = $less->cachedCompile( $cache );
		if ( !is_array( $cache ) || $newCache['updated'] > $cache['updated'] || !is_file($outputFile) ) {
			$cache = serialize( $newCache );
			JFile::write( $cacheFile, $cache );
			JFile::write( $outputFile, $newCache['compiled'] );
		}
	}

	/**
	 * Returns the global KunenaTemplate object, only creating it if it doesn't already exist.
	 *
	 * @access	public
	 * @param	int	$name		Template name or null for default/selected template in your configuration
	 * @return	KunenaTemplate	The template object.
	 * @since	1.6
	 */
	public static function getInstance($name=null) {
		$app = JFactory::getApplication();
		if (!$name) {
			$name = JRequest::getString ( 'kunena_template', KunenaFactory::getConfig()->template, 'COOKIE' );
		}
		$name = JPath::clean($name);
		if (empty(self::$_instances[$name])) {
			// Find overridden template class (use $templatename to avoid creating new objects if the template doesn't exist)
			$templatename = $name;
			$classname = "KunenaTemplate{$templatename}";
			if (!file_exists(KPATH_SITE . "/template/{$templatename}/template.xml")
				&& !file_exists(KPATH_SITE . "/template/{$templatename}/config.xml")) {
				// If template xml doesn't exist, raise warning and use blue eagle instead
				$templatename = 'blue_eagle';
				$classname = "KunenaTemplate{$templatename}";

				if (is_dir(KPATH_SITE . "/template/{$templatename}")) KunenaError::warning(JText::sprintf('COM_KUNENA_LIB_TEMPLATE_NOTICE_INCOMPATIBLE', $name, $templatename));
			}
			if (!class_exists($classname) && $app->isSite()) {
				$file = KPATH_SITE."/template/{$templatename}/template.php";
				if (!file_exists($file)) {
					$classname = "KunenaTemplateBlue_Eagle";
					$file = KPATH_SITE."/template/blue_eagle/template.php";
				}
				if (file_exists($file)) {
					require_once $file;
				}
			}
			if (class_exists ( $classname )) {
				self::$_instances [$name] = new $classname ( $templatename );
			} else {
				self::$_instances [$name] = new KunenaTemplate ( $templatename );
			}
		}

		return self::$_instances [$name];
	}
}
