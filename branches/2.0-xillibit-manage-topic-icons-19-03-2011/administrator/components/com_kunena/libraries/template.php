<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport('joomla.html.parameter');

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

	protected $smileyPath = array();
	protected $rankPath = array();
	public $topicIcons = array();

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
			$name = 'default';
			$xml = KPATH_SITE . "/template/{$name}/template.xml";
		}
		$this->xml_path = $xml;
		$ini = KPATH_SITE . "/template/{$name}/params.ini";
		$content = '';
		if (is_readable( $ini ) ) {
			$content = file_get_contents($ini);
		}
		$this->name = $name;
		$this->params = new KunenaParameter($content, $xml);

		$xml = $this->params->getXml();
		foreach ($xml['_default']->children() as $param)  {
			if ($param->attributes('type') != 'spacer') $this->params->def($param->attributes('name'), $param->attributes('default'));
		}
		$this->getTopicIconPath(0);
	}

	public function loadMootools() {
		$jversion = new JVersion ();
		if ($jversion->RELEASE == '1.5') {
			jimport ( 'joomla.plugin.helper' );
			$mtupgrade = JPluginHelper::isEnabled ( 'system', 'mtupgrade' );
			if (! $mtupgrade) {
				$app = JFactory::getApplication ();
				if (!class_exists ( 'JHTMLBehavior' )) {
					if (is_dir ( JPATH_PLUGINS . DS . 'system' . DS . 'mtupgrade' )) {
						JHTML::addIncludePath ( JPATH_PLUGINS . DS . 'system' . DS . 'mtupgrade' );
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
		} else {
			// Joomla 1.6+
			JHTML::_ ( 'behavior.framework' );
		}

		if (JDEBUG || KunenaFactory::getConfig()->debug) {
			// Debugging Mootools issues
			$this->addScript ( 'js/debug-min.js' );
		}
	}

	/**
	 * Wrapper to addStyleSheet
	 */
	function addStyleSheet($filename) {
		if (JDEBUG || KunenaFactory::getConfig ()->debug || KunenaForum::isSvn ()) {
			// If we are in debug more, make sure we load the unpacked css
			$filename = preg_replace ( '/\-min\./u', '.', $filename );
		}
		return JFactory::getDocument ()->addStyleSheet ( JURI::root(true).'/'.$this->getFile($filename) );
	}

	/**
	 * Wrapper to addScript
	 */
	function addScript($filename) {
		if (JDEBUG || KunenaFactory::getConfig ()->debug || KunenaForum::isSvn ()) {
			// If we are in debug more, make sure we load the unpacked css
			$filename = preg_replace ( '/\-min\./u', '.', $filename );
		}
		return JFactory::getDocument ()->addScript ( JURI::root(true).'/'.$this->getFile($filename) );
	}

	public function getPath($default = false) {
		if ($default) return "template/default";
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
			if (($filename && !is_file(KPATH_SITE .DS. $path)) || !is_dir(KPATH_SITE .DS. $path)) {
				$path = "{$this->getPath(true)}/images/emoticons/{$filename}";
			}
			$this->smileyPath[$filename] = $path;
		}
		$base = '';
		if ($url) $base = KURL_SITE;
		return $base.$this->smileyPath[$filename];
	}

	public function getRankPath($filename='', $url = false) {
		if (!isset($this->rankPath[$filename])) {
			$path = "{$this->getPath()}/images/ranks/{$filename}";
			if (($filename && !is_file(KPATH_SITE .DS. $path)) || !is_dir(KPATH_SITE .DS. $path)) {
				$path = "{$this->getPath(true)}/images/ranks/{$filename}";
			}
			$this->rankPath[$filename] = $path;
		}
		$base = '';
		if ($url) $base = KURL_SITE;
		return $base.$this->rankPath[$filename];
	}

	public function getImagePath($image, $url = true) {
		$path = $this->getPath();
		if (!is_file(KPATH_SITE . "/{$path}/images/{$image}")) {
			$path = $this->getPath(true);
		}
		$base = '';
		if ($url) $base = KURL_SITE;
		return "{$base}{$path}/images/{$image}";
	}

	public function getTopicIcons() {
		$db = JFactory::getDBO ();
		if (empty($this->topicIcons)) {
			$path = $this->getPath();

			$query = "SELECT * FROM #__kunena_topics_icons WHERE published='1' ORDER BY ordering";
			$db->setQuery ( $query );
			$topicicons = $db->loadObjectlist();
			if (KunenaError::checkDatabaseError()) return;

			if ( empty($topicicons) ) return $this->topicIcons;

			$topic_emoticons = array();
			foreach ($topicicons as $icon) {
				if (is_file( KPATH_SITE . "/{$path}/images/icons/{$icon->filename}" )) {
					$this->topicIcons[$icon->id] = "{$path}/images/icons/{$icon->filename}";
				}
			}
		}

		return $this->topicIcons;
	}

	public function getTopicIconPath($index, $url = false) {
		if (empty($this->topicIcons)) {
			$this->getTopicIcons();
		}
		$base = '';
		if ($url) $base = KURL_SITE;

		// if index =0, so we get the topic icon set by default
		if ( $index == 0 ) {
			$this->topicIcons[0] = $this->getTopicIconDefault();
		}

		return $base.(isset($this->topicIcons[$index]) ? $this->topicIcons[$index] : $this->getTopicIconDefault());
	}

	public function getTopicIconDefault() {
			$path = $this->getPath();
		$db = JFactory::getDBO ();

		$query = "SELECT filename FROM #__kunena_topics_icons WHERE published='1' AND isdefault='1'";
		$db->setQuery ( $query );
		$defautltopicicon = $db->loadResult();
		if (KunenaError::checkDatabaseError()) return;

		return "/{$path}/images/icons/{$defautltopicicon}";
	}

	public function getMovedIconPath($url = false) {
		static $moved = false;
		if ($moved === false) {
			$path = $this->getPath();
			if (!is_file(KPATH_SITE . "/{$path}/images/icons/topic-arrow.png")) {
				$path = $this->getPath(true);
			}
			$moved =  "/{$path}/images/icons/topic-arrow.png";
		}

		$base = '';
		if ($url) $base = KURL_SITE;
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
			if ($topic->moved_id) $icon = 'moved';
			if ($topic->hold == 1) $icon = 'unapproved';
			if ($topic->hold == 2) $icon = 'deleted';
			if ($topic->unread) $icon .= '_new';
			$iconurl = $this->getImagePath("topicicons/icon_{$icon}.png");
		}
		$html = '<img src="'.$iconurl.'" alt="emo" />';
		return $html;
	}

	public function getTopicsIconPath($filename) {
		if ( empty($filename) ) return;

		$path = $this->getPath();

		return  "/{$path}/images/icons/{$filename}";
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
	static public function getInstance($name=null)
	{
		if (!$name) {
			$config = KunenaFactory::getConfig();
			$name = $config->template;
		}
		if (empty(self::$_instances[$name])) {
			self::$_instances[$name] = new KunenaTemplate($name);
		}

		return self::$_instances[$name];
	}
}
