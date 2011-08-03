<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Template
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport('joomla.html.parameter');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class KunenaParameter extends JParameter {
	public function getXml() {
		return $this->_xml;
	}
}
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

	protected $default = 'blue_eagle';
	protected $css_compile = true;
	protected $smileyPath = array();
	protected $rankPath = array();
	public $topicIcons = array();

	protected $stylesheets = array();
	protected $style_variables = array();
	protected $compiled_style_variables = null;
	protected $scripts = array();

	/**
	* Constructor
	*
	* @access	protected
	*/
	public function __construct($name=null) {
		if (!$name) {
			$config = KunenaFactory::getConfig();
			$name = $config->template;
		}
		$xml = KPATH_SITE . "/template/{$name}/template.xml";
		if (!is_readable ( $xml )) {
			$name = 'blue_eagle';
			$xml = KPATH_SITE . "/template/{$name}/template.xml";
		}
		$this->xml_path = $xml;
		$ini = KPATH_SITE . "/template/{$name}/params.ini";
		$content = '';
		if (is_readable( $ini ) ) {
			$this->paramstime = filemtime($ini);
			$content = file_get_contents($ini);
		}
		$this->name = $name;
		$this->params = new KunenaParameter($content, $xml);

		$xml = $this->params->getXml();
		foreach ($xml['_default']->children() as $param)  {
			if ($param->attributes('type') == 'spacer') continue;
			$this->params->def($param->attributes('name'), $param->attributes('default'));
			$name = $param->attributes('name');
			if (substr($name,0,5) == 'style') {
				$this->style_variables[$name] = $this->params->get($name);
			}
		}
	}

	public function initialize() {}

	public function getButton($name, $text) {
		return '<span class="'.$name.'"><span>'.$text.'</span></span>';
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
			// FIXME: add Joomla 1.6 support
			$xml = $this->params->getXml();
			$variables = array();
			foreach ($this->style_variables as $name=>$value)  {
				$variables[] = "\t{$name}:{$this->params->get($name)};";
			}
			$this->compiled_style_variables = "@variables {\n".implode("\n", $variables)."\n}\n\n";

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

	public function addStyleSheet($filename) {
		if ($this->css_compile) {
			// If template supports CSS compiler
			$source = $this->getFile($filename);
			$filename = $this->getCachePath($filename);
			if (!JFile::exists(JPATH_ROOT.'/'.$filename)
				|| filemtime(JPATH_ROOT.'/'.$source) > filemtime(JPATH_ROOT.'/'.$filename)
				|| ($this->paramstime && $this->paramstime > filemtime(JPATH_ROOT.'/'.$filename) )) {
				$this->compileStyleSheet($source, $filename);
			}
		} else {
			// For other templates use the old way
			$filename = $this->getFile($filename);
			$filemin = preg_replace ( '/\.css$/u', '-min.css', $filename );
			if (JFile::exists(JPATH_ROOT."/$filemin")) {
				$filename = $filemin;
			}
		}
		return JFactory::getDocument ()->addStyleSheet ( JURI::root(true)."/{$filename}" );
	}

/*
	public function addStyleSheetDeclaration($string) {
		$this->stylesheets[] = $string;
	}
*/

	public function getCachePath($filename) {
		if (JDEBUG || KunenaFactory::getConfig ()->debug) {
			$filename = "media/kunena/cache/{$this->name}/debug/{$filename}";
		} else {
			$filename = "media/kunena/cache/{$this->name}/{$filename}";
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
				'CompressUnitValues' => true,
				'CompressExpressionValues' => true
			);
			$buffer = CssMin::minify($buffer, $filters, $plugins);
		}

		$buffer = preg_replace ( '/url\(([\'"]?)\.\./u', 'url(\\1'.JURI::root(true)."/components/com_kunena/template/{$this->name}", $buffer );
		JFile::write(JPATH_ROOT.'/'.$dest, $buffer);
		return $dest;
	}

	/**
	 * Wrapper to addScript
	 */
	function addScript($filename) {
		if (!JDEBUG && !KunenaFactory::getConfig ()->debug && !KunenaForum::isSvn ()) {
			// If we are in debug more, make sure we load the unpacked css
			$filename = preg_replace ( '/\.js$/u', '-min.js', $filename );
		}
		return JFactory::getDocument ()->addScript ( JURI::root(true).'/'.$this->getFile($filename) );
	}

	/*
	public function addScript($filename, $namespace='default') {
		$this->scripts[$namespace][] = file_get_contents(KPATH_SITE.'/'.$this->getFile($filename));
	}

	public function addScriptDeclaration($string, $namespace='default') {
		$this->scripts[$namespace][] = $string;
	}
	*/

	public function getPath($default = false) {
		if ($default) return "template/{$this->default}";
		return "template/{$this->name}";
	}

	public function getFile($file) {
		$path = $this->getPath();
		if (!is_file(KPATH_SITE . "/{$path}/{$file}")) {
			$path = $this->getPath(true);
		}
		return KPATH_COMPONENT_RELATIVE."/{$path}/{$file}";
	}

	public function getSmileyPath($filename='', $url = false) {
		if (!isset($this->smileyPath[$filename])) {
			$path = "{$this->getPath()}/images/emoticons/{$filename}";
			if (($filename && !is_file(KPATH_SITE . "/{$path}")) || !is_dir(KPATH_SITE . "/{$path}")) {
				$path = "{$this->getPath(true)}/images/emoticons/{$filename}";
			}
			$this->smileyPath[$filename] = $path;
		}
		$base = '';
		if ($url) $base = JURI::root(true).'/'.KPATH_COMPONENT_RELATIVE.'/';
		return $base.$this->smileyPath[$filename];
	}

	public function getRankPath($filename='', $url = false) {
		if (!isset($this->rankPath[$filename])) {
			$path = "{$this->getPath()}/images/ranks/{$filename}";
			if (($filename && !is_file(KPATH_SITE . "/{$path}")) || !is_dir(KPATH_SITE . "/{$path}")) {
				$path = "{$this->getPath(true)}/images/ranks/{$filename}";
			}
			$this->rankPath[$filename] = $path;
		}
		$base = '';
		if ($url) $base = JURI::root(true).'/'.KPATH_COMPONENT_RELATIVE.'/';
		return $base.$this->rankPath[$filename];
	}

	public function getImagePath($image, $url = true) {
		$path = $this->getPath();
		if (!is_file(KPATH_SITE . "/{$path}/images/{$image}")) {
			$path = $this->getPath(true);
		}
		$base = '';
		if ($url) $base = JURI::root(true).'/'.KPATH_COMPONENT_RELATIVE.'/';
		return "{$base}{$path}/images/{$image}";
	}

	public function getTopicIcons($all = false) {
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
				$default = !is_file( KPATH_SITE . "/{$this->getPath()}/images/topicicons/user/{$icon->filename}" );
				$icon->relpath = KPATH_COMPONENT_RELATIVE."/{$this->getPath($default)}/images/topicicons/user/{$icon->filename}";
				$this->topicIcons[0] = $icon;
			}
		}
		if ($all) {
			$icons = $this->topicIcons;
		} else {
			$icons = array();
			foreach ($this->topicIcons as $icon) {
				if ($icon->published && is_numeric($icon->id)) {
					$icons[$icon->id] = $icon;
				}
			}
		}
		return $icons;
	}

	public function getTopicIconPath($index, $url = false) {
		if (empty($this->topicIcons)) {
			$this->getTopicIcons();
		}
		if (empty($this->topicIcons[$index]->published)) {
			$index = 0;
		}
		$icon = $this->topicIcons[$index];
		if (!isset($icon->relpath)) {
			$curpath = $this->getPath();
			$defpath = $this->getPath(true);
			if (is_file( KPATH_SITE . "/{$curpath}/images/topicicons/default/{$icon->filename}" )) {
				$icon->relpath = KPATH_COMPONENT_RELATIVE."/{$curpath}/images/topicicons/default/{$icon->filename}";
			} elseif (is_file( KPATH_SITE . "/{$defpath}/images/topicicons/default/{$icon->filename}" )) {
				$icon->relpath = KPATH_COMPONENT_RELATIVE."/{$defpath}/images/topicicons/default/{$icon->filename}";
			} else {
				$icon->relpath = "media/kunena/topicicons/default/{$icon->filename}";
			}
		}
		return ($url ? JURI::root(true).'/' : '') . $icon->relpath;
	}

	public function getMovedIconPath($url = false) {
		static $moved = false;
		if ($moved === false) {
			$path = $this->getPath();
			if (!is_file(KPATH_SITE . "/{$path}/images/topicicons/user/moved.png")) {
				$path = $this->getPath(true);
			}
			$moved =  "{$path}/images/topicicons/user/moved.png";
		}

		$base = '';
		if ($url) $base = JURI::root(true).'/'.KPATH_COMPONENT_RELATIVE.'/';
		return $base.$moved;
	}

	public function getTopicIcon($topic ) {
		$config = KunenaFactory::getConfig ();
		if ($config->topicicons) {
			if ( $topic->moved_id == 0 ) $iconurl = $this->getTopicIconPath($topic->icon_id, true);
			else $iconurl = $this->getMovedIconPath(true);
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
			$iconurl = $this->getImagePath("topicicons/system/{$icon}.png");
		}
		$html = '<img src="'.$iconurl.'" alt="emo" />';
		return $html;
	}

	// FIXME: remove:
	public function getTopicsIconPath($filename) {
		if ( empty($filename) ) return;

		return "media/kunena/topicicons/{$filename}";
	}

	public function getTemplateDetails() {
		$templatedetails = new stdClass();
		$xml_tmpl = JFactory::getXMLparser('Simple');
		$xml_tmpl->loadFile($this->xml_path);

		$templatedetails->creationDate = $xml_tmpl->document->creationDate[0]->data();
		$templatedetails->author = $xml_tmpl->document->author[0]->data();
		$templatedetails->version = $xml_tmpl->document->version[0]->data();
		$templatedetails->name = $xml_tmpl->document->name[0]->data();

		return $templatedetails;
	}

	static public function loadTemplate($file) {
		$path = self::getInstance()->getPath();
		if (!is_file(KPATH_SITE . "/{$path}/{$file}")) {
			$path = self::getInstance()->getPath(true);
		}
		include KPATH_SITE . "/{$path}/{$file}";
	}

	/**
	 * Returns the global KunenaTemplate object, only creating it if it doesn't already exist.
	 *
	 * @access	public
	 * @param	int	$name		Template name or null for default/selected template in your configuration
	 * @return	KunenaTemplate	The template object.
	 * @since	1.6
	 */
	static public function getInstance($name=null) {
		if (!$name) {
			$config = KunenaFactory::getConfig();
			$name = $config->template;
		}
		if (empty(self::$_instances[$name])) {
			// Find overridden template class
			$classname = "KunenaTemplate{$name}";
			if (!class_exists($classname)) {
				$file = KPATH_SITE."/template/{$name}/template.php";
				if (!file_exists($file)) {
					$classname = "KunenaTemplateBlue_Eagle";
					$file = KPATH_SITE."/template/blue_eagle/template.php";
				}
				if (file_exists($file)) {
					require_once $file;
				}
			}
			if (class_exists ( $classname )) {
				self::$_instances [$name] = new $classname ( $name );
			} else {
				self::$_instances [$name] = new KunenaTemplate ( $name );
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