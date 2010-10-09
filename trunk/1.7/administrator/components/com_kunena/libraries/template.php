<?php
/**
* @version $Id$
* Kunena Component - KunenaTemplate class
* @package Kunena
*
* @Copyright (C) 2010 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();
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
		$me = KunenaFactory::getUser();
		$jversion = new JVersion ();
		if ($jversion->RELEASE == 1.5) {
			jimport ( 'joomla.plugin.helper' );
			$mootools12 = JPluginHelper::isEnabled ( 'system', 'mtupgrade' ) || JPluginHelper::isEnabled ( 'system', 'mootools12' );
			if (! $mootools12) {
				$app = JFactory::getApplication ();
				if (!class_exists ( 'JHTMLBehavior' )) {
					if (is_dir ( JPATH_PLUGINS . DS . 'system' . DS . 'mtupgrade' )) {
						JHTML::addIncludePath ( JPATH_PLUGINS . DS . 'system' . DS . 'mtupgrade' );
					} elseif ($me->isAdmin()) {
						// TODO: translate
						$app->enqueueMessage ( 'Kunena: You do not have <em>System - Mootools Upgrade</em> plugin.', 'notice' );
						$app->enqueueMessage ( 'Many features, including Quick Reply and BBCode editor may be broken.', 'notice' );
					}
				} elseif ($me->isAdmin()) {
					// TODO: translate
					$app->enqueueMessage ( 'Kunena: You have a plugin that uses Mootools 1.1 or custom version of JHTMLBehavior.', 'notice' );
					$app->enqueueMessage ( 'Many features, including Quick Reply and BBCode editor may be broken.', 'notice' );
					$app->enqueueMessage ( 'Please enable <em>System - Mootools Upgrade</em> plugin.', 'notice' );
				}
			}
		}
		JHTML::_ ( 'behavior.mootools' );
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
		return KUNENA_COMPONENT_RELPATH."/{$path}/{$file}";
	}

	public function getSmileyPath($filename='') {
		if (!isset($this->smileyPath[$filename])) {
			$path = "{$this->getPath()}/images/emoticons/{$filename}";
			if (($filename && !is_file(KPATH_SITE .DS. $path)) || !is_dir(KPATH_SITE .DS. $path)) {
				$path = "{$this->getPath(true)}/images/emoticons/{$filename}";
			}
			$this->smileyPath[$filename] = $path;
		}
		return $this->smileyPath[$filename];
	}

	public function getRankPath($filename='') {
		if (!isset($this->rankPath[$filename])) {
			$path = "{$this->getPath()}/images/ranks/{$filename}";
			if (($filename && !is_file(KPATH_SITE .DS. $path)) || !is_dir(KPATH_SITE .DS. $path)) {
				$path = "{$this->getPath(true)}/images/ranks/{$filename}";
			}
			$this->rankPath[$filename] = $path;
		}
		return $this->rankPath[$filename];
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

	public function getTopicIconPath($index, $url = false) {
		if (empty($this->topicIcons)) {
			$curpath = $this->getPath();
			$defpath = $this->getPath(true);

			$path = $curpath;
			if (!is_file ( KPATH_SITE . "/{$path}/icons.php" )) {
				$path = $defpath;
			}
			$topic_emoticons = array();
			$this->topicIcons[0] = "/{$defpath}/images/icons/topic-default.gif";
			include KPATH_SITE . "/{$path}/icons.php";
			foreach ($topic_emoticons as $id=>$icon) {
				if (is_file( KPATH_SITE . "/{$curpath}/images/icons/{$icon}" )) {
					$this->topicIcons[$id] = "{$curpath}/images/icons/{$icon}";
				} elseif (is_file( KPATH_SITE . "/{$defpath}/images/icons/{$icon}" )) {
					$this->topicIcons[$id] = "{$defpath}/images/icons/{$icon}";
				}
			}
		}
		$base = '';
		if ($url) $base = KURL_SITE;
		return $base.(isset($this->topicIcons[$index]) ? $this->topicIcons[$index] : $this->topicIcons[0]);
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
			if ( $topic->moved == 0 ) $iconurl = $this->getTopicIconPath($topic->topic_emoticon, true);
			else $iconurl = $this->getMovedIconPath(true);
		} else {
			$icon = 'normal';
			if ($topic->msgcount < 2) $icon = 'unanswered';
			if ($topic->ordering) $icon = 'sticky';
			//if ($topic->myfavorite) $icon = 'favorite';
			if ($topic->locked) $icon = 'locked';
			if ($topic->moved) $icon = 'moved';
			if ($topic->hold == 1) $icon = 'unapproved';
			if ($topic->hold == 2) $icon = 'deleted';
			if ($topic->unread) $icon .= '_new';
			$iconurl = $this->getImagePath("topicicons/icon_{$icon}.png");
		}
		$html = '<img src="'.$iconurl.'" alt="emo" />';
		return $html;
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
