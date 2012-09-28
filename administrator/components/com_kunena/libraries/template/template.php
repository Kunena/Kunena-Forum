<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Template
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
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

		$this->xml_path = KPATH_SITE . "/template/{$name}/template.xml";
		$ini = KPATH_SITE . "/template/{$name}/params.ini";
		$content = '';
		if (is_readable( $ini ) ) {
			$this->paramstime = filemtime($ini);
			$content = file_get_contents($ini);
		}
		$this->name = $name;

		if (version_compare(JVERSION, '1.6', '>')) {
			// Joomla 1.6+
			$this->params = new JRegistry($content);
		} else {
			// Joomla 1.5
			$this->params = new JParameter($content);
		}
		// Load default values
		$this->xml = simplexml_load_file($this->xml_path);
		if ($this->xml) {
			foreach ($this->xml->xpath('params/param') as $node) {
				if (isset($node['name']) && isset($node['default'])) $this->params->def($node['name'], $node['default']);
			}
			// Generate CSS variables
			foreach ($this->params->toArray() as $key=>$value)  {
				if (substr($key,0,5) == 'style') {
					$this->style_variables[$key] = $value;
				}
			}
		}
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
		$this->createStyleSheets();
		$this->loadLanguage();
	}

	public function initializeBackend() {
		$this->createStyleSheets();
		$this->loadLanguage();
	}

	public function getUserClasses() {
		return $this->userClasses;
	}

	public function getButton($link, $name, $scope, $type, $id = null) {
		if ($link instanceof JUri && version_compare(JVERSION, '2.5', '<')) $link = $link->toString();

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

	public function getPaginationItemActive(&$item) {
		return '<a title="'.$item->text.'" href="'.$item->link.'" class="pagenav">'.$item->text.'</a>';
	}

	public function getPaginationItemInactive(&$item) {
		return '<span class="pagenav">'.$item->text.'</span>';
	}

	public function getClass($class, $class_sfx='') {
		return $class.($class_sfx ? " {$class}.{$class_sfx}" : '');
	}

	public function loadMootools() {
		if (version_compare(JVERSION, '1.6','>')) {
			// Joomla 1.6+
			JHTML::_ ( 'behavior.framework', true );
		} else {
			// Joomla 1.5
			jimport ( 'joomla.plugin.helper' );
			$mtupgrade = JPluginHelper::isEnabled ( 'system', 'mtupgrade' );
			if (! $mtupgrade) {
				$app = JFactory::getApplication ();
				if (!class_exists ( 'JHTMLBehavior' )) {
					if (is_dir ( JPATH_PLUGINS . '/system/mtupgrade' )) {
						JHTML::addIncludePath ( JPATH_PLUGINS . '/system/mtupgrade' );
					} else {
						KunenaError::warning ( JText::_('COM_KUNENA_LIB_TEMPLATE_MOOTOOLS_NO_UPGRADE').' '.JText::_('COM_KUNENA_LIB_TEMPLATE_MOOTOOLS_WARNING') );
					}
				}
			}
			JHTML::_ ( 'behavior.mootools' );
			// Get the MooTools version string
			$mtversion = preg_replace('/[^\d\.]/','', JFactory::getApplication()->get('MooToolsVersion'));
			if (version_compare($mtversion, '1.2.4', '<')) {
				KunenaError::warning ( JText::_('COM_KUNENA_LIB_TEMPLATE_MOOTOOLS_LEGACY').' '.JText::_('COM_KUNENA_LIB_TEMPLATE_MOOTOOLS_WARNING') );
			}
		}

		if (JDEBUG || KunenaFactory::getConfig()->debug) {
			// Debugging Mootools issues
			$this->addScript ( 'js/debug.js' );
		}
	}

	public function getStyleVariables() {
		if ($this->compiled_style_variables === null) {
			$variables = array();
			foreach ($this->style_variables as $name=>$value)  {
				if ($value != '')
					$variables[] = "\t{$name}:{$value};";
			}
			if ($variables) $this->compiled_style_variables = "@variables {\n".implode("\n", $variables)."\n}\n\n";
			else $this->compiled_style_variables = '';

		}
		return $this->compiled_style_variables;
	}

	public function getStyleVariable($name, $default='') {
		return isset($this->style_variables[$name]) ? $this->style_variables[$name] : $default;
	}

	public function setStyleVariable($name, $value) {
		$this->compiled_style_variables = null;
		return $this->style_variables[$name] = $value;
	}

	public function addStyleSheet($filename, $group='forum') {
		if ($this->css_compile) {
			// If template supports CSS compiler
			$source = $this->getFile($filename);
			if (!file_exists(JPATH_ROOT.'/'.$source)) return false;
			$sourcetime = filemtime(JPATH_ROOT.'/'.$source);
			$filename = $this->getCachePath($filename);
			if (!JFile::exists(JPATH_ROOT.'/'.$filename)
				|| $sourcetime > filemtime(JPATH_ROOT.'/'.$filename)
				|| ($this->paramstime && $this->paramstime > filemtime(JPATH_ROOT.'/'.$filename) )) {
				$this->compileStyleSheet($source, $filename);
			}
			$group = $group ? $group : '_none_';
			$this->stylesheets[$group][$filename] = $sourcetime;
			return true;
		} else {
			// For other templates use the old way
			$filemin = $filename = $this->getFile($filename);
			$filemin_path = preg_replace ( '/\.css$/u', '-min.css', $filename );
			if (!JDEBUG && !KunenaFactory::getConfig ()->debug && !KunenaForum::isDev () && JFile::exists(JPATH_ROOT."/$filemin_path")) {
				$filemin = preg_replace ( '/\.css$/u', '-min.css', $filename );
			}
			if (JFile::exists(JPATH_ROOT."/$filemin")) {
				$filename = $filemin;
			}
		}
		return JFactory::getDocument ()->addStyleSheet ( JURI::root(true)."/{$filename}" );
	}

	public function addIEStyleSheet($filename, $condition='IE') {
		$url = $this->getFile($filename, true);
		$stylelink = "<!--[if {$condition}]>\n";
		$stylelink .= '<link rel="stylesheet" href="'.$url.'" />' ."\n";
		$stylelink .= "<![endif]-->\n";
		JFactory::getDocument()->addCustomTag($stylelink);
	}

	public function createStyleSheets() {
		if (empty($this->stylesheets)) return;
		if (JDEBUG || KunenaFactory::getConfig ()->debug) {
			foreach ($this->stylesheets as $list) {
				foreach ($list as $stylesheet=>$time) {
					JFactory::getDocument ()->addStyleSheet ( JURI::root(true)."/{$stylesheet}" );
				}
			}
		} else {
			$path = $this->getCachePath();
			foreach ($this->stylesheets as $group=>$list) {
				if ($group[0] == '_') {
					foreach ($list as $stylesheet=>$time) {
						JFactory::getDocument ()->addStyleSheet ( JURI::root(true)."/{$stylesheet}" );
					}
				} else {
					$cssfile = "{$path}/{$group}.css";
					$hashfile = JPATH_ROOT."/{$path}/forum_hash.txt";
					$hash = md5(serialize($this->stylesheets).'-'.$this->paramstime);
					if (!JFile::exists($hashfile) || file_get_contents($hashfile) != $hash) {
						$buffer = '@charset "utf-8";
		/* This file is automatically generated. Please enable debug mode to see the uncompressed files. */

		';
						foreach ($list as $stylesheet=>$time) {
							$buffer .= file_get_contents(JPATH_ROOT.'/'.$stylesheet);
						}
						if ($buffer) {
							JFile::write(JPATH_ROOT.'/'.$cssfile, $buffer);
							JFile::write($hashfile, $hash);
						}
					}
					JFactory::getDocument ()->addStyleSheet ( JURI::root(true).'/'.$cssfile );
				}
			}
		}
	}

/*
	public function addStyleSheetDeclaration($string) {
		$this->stylesheets[] = $string;
	}
*/

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

	public function compileStyleSheet($source, $dest) {
		$buffer = $this->getStyleVariables();
		$buffer .= JFile::read(JPATH_ROOT.'/'.$source);

		if (JDEBUG || KunenaFactory::getConfig ()->debug) {
			$filters = array (
				'ImportImports' => array ('BasePath' => JPATH_ROOT.'/'.dirname($source) ),
				'RemoveComments' => false,
				'RemoveEmptyRulesets' => false,
				'RemoveEmptyAtBlocks' => false,
				'ConvertLevel3Properties' => true,
				'ConvertLevel3AtKeyframes' => array ('RemoveSource' => false ),
				'Variables' => true,
				'RemoveLastDelarationSemiColon' => false
			);
			$plugins = array (
				'Variables' => true,
				'ConvertFontWeight' => false,
				'ConvertHslColors' => false,
				'ConvertRgbColors' => false,
				'ConvertNamedColors' => false,
				'CompressColorValues' => false,
				'CompressUnitValues' => false,
				'CompressExpressionValues' => false
			);
			CssMin::setVerbose(1);
			$tokens = CssMin::minify($buffer, $filters, $plugins, false);
			$buffer = new CssKunenaFormatter($tokens, "\t");
		} else {
			$filters = array (
				'ImportImports' => array ('BasePath' => JPATH_ROOT.'/'.dirname($source) ),
				'RemoveComments' => true,
				'RemoveEmptyRulesets' => true,
				'RemoveEmptyAtBlocks' => true,
				'ConvertLevel3Properties' => true,
				'ConvertLevel3AtKeyframes' => array ('RemoveSource' => false ),
				'Variables' => true,
				'RemoveLastDelarationSemiColon' => true
			);
			$plugins = array (
				'Variables' => true,
				'ConvertFontWeight' => true,
				'ConvertHslColors' => true,
				'ConvertRgbColors' => true,
				'ConvertNamedColors' => true,
				'CompressColorValues' => true,
				'CompressUnitValues' => false, // There seems to be a bug with background-position: 0 0 -> 0 50%
				'CompressExpressionValues' => true
			);
			$buffer = CssMin::minify($buffer, $filters, $plugins);
		}

		$buffer = preg_replace_callback ( '/url\(([^\)]+)\)/u', array($this, 'findUrl'), $buffer );
		JFile::write(JPATH_ROOT.'/'.$dest, $buffer);
		unset($tokens, $buffer, $filters, $plugins);
		return $dest;
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
		$filemin_path = preg_replace ( '/\.css$/u', '-min.css', $filename );
		if (!JDEBUG && !KunenaFactory::getConfig ()->debug && !KunenaForum::isDev () && JFile::exists(JPATH_ROOT."/$filemin_path")) {
			// If we are in debug more, make sure we load the unpacked css
			$filename = preg_replace ( '/\.js$/u', '-min.js', $filename );
		}
		return JFactory::getDocument ()->addScript ( $this->getFile($filename, true, '', 'media/kunena', 'default') );
	}

	/*
	public function addScript($filename, $namespace='default') {
		$this->scripts[$namespace][] = file_get_contents(KPATH_SITE.'/'.$this->getFile($filename));
	}

	public function addScriptDeclaration($string, $namespace='default') {
		$this->scripts[$namespace][] = $string;
	}
	*/

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
		return ($url ? JURI::root(true).'/' : '').$this->filecache[$filepath];
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
			}
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
		return $this->xml;
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
			if (!file_exists(KPATH_SITE . "/template/{$templatename}/template.xml")) {
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

require_once KPATH_ADMIN.'/libraries/external/cssmin/jsmin.php';
require_once KPATH_ADMIN.'/libraries/external/cssmin/cssmin.php';

class CssKunenaFormatter extends aCssFormatter {
	public function __toString() {
		$r = array ();
		$level = 0;
		for($i = 0, $l = count ( $this->tokens ); $i < $l; $i ++) {
			$token = $this->tokens [$i];
			$class = get_class ( $token );
			$indent = str_repeat ( $this->indent, $level );
			if ($class === "CssCommentToken") {
				$lines = array_map ( "trim", explode ( "\n", $token->Comment ) );
				for($ii = 0, $ll = count ( $lines ); $ii < $ll; $ii ++) {
					$r [] = $indent . (substr ( $lines [$ii], 0, 1 ) == "*" ? " " : "") . $lines [$ii];
				}
			} elseif ($class === "CssAtCharsetToken") {
				$r [] = $indent . "@charset " . $token->Charset . ";";
			} elseif ($class === "CssAtFontFaceStartToken") {
				$r [] = $indent . "@font-face {";
				$level ++;
			} elseif ($class === "CssAtImportToken") {
				$r [] = $indent . "@import " . $token->Import . " " . implode ( ", ", $token->MediaTypes ) . ";";
			} elseif ($class === "CssAtKeyframesStartToken") {
				$r [] = $indent . "@keyframes \"" . $token->Name . "\" {";
				$level ++;
			} elseif ($class === "CssAtMediaStartToken") {
				$r [] = $indent . "@media " . implode ( ", ", $token->MediaTypes ) . " {";
				$level ++;
			} elseif ($class === "CssAtPageStartToken") {
				$r [] = $indent . "@page {";
				$level ++;
			} elseif ($class === "CssAtVariablesStartToken") {
				$r [] = $indent . "@variables " . implode ( ", ", $token->MediaTypes ) . " {";
				$level ++;
			} elseif ($class === "CssRulesetStartToken" || $class === "CssAtKeyframesRulesetStartToken") {
				$r [] = $indent . implode ( ",\n", $token->Selectors ) . " {";
				$level ++;
			} elseif ($class == "CssAtFontFaceDeclarationToken" || $class === "CssAtKeyframesRulesetDeclarationToken" || $class === "CssAtPageDeclarationToken" || $class == "CssAtVariablesDeclarationToken" || $class === "CssRulesetDeclarationToken") {
				$declaration = $indent . $token->Property . ": ";
				if ($this->padding) {
					$declaration = str_pad ( $declaration, $this->padding, " ", STR_PAD_RIGHT );
				}
				$r [] = $declaration . $token->Value . ($token->IsImportant ? " !important" : "") . ";";
			} elseif ($class === "CssAtFontFaceEndToken" || $class === "CssAtMediaEndToken" || $class === "CssAtKeyframesEndToken" || $class === "CssAtKeyframesRulesetEndToken" || $class === "CssAtPageEndToken" || $class === "CssAtVariablesEndToken" || $class === "CssRulesetEndToken") {
				$level--;
				$r[] = str_repeat($indent, $level) . "}";
			}
		}
		return implode("\n", $r);
	}
}
