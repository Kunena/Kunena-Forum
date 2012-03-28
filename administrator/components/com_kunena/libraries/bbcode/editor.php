<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage BBCode
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// TODO: add possibility to hide contents from these tags:
// [hide], [confidential], [spoiler], [attachment], [code]

/**
 * Kunena BBCode Editor Class
 *
 * @version		2.0
 */
class KunenaBbcodeEditor {

	protected $_insert = array( "after" => array(),
								"before" => array(),
								"afterall" => array());

	function __construct($config = array()) {
		$this->config = $config;
	}

	public function &getInstance($config = array()) {
		static $instance = false;
		if (! $instance) {
			$instance = new KunenaBbcodeEditor ($config);
		}
		return $instance;
	}

	public static function insertElement ($editor, $element, $pos=NULL, $where) {
		if ( is_subclass_of($element, 'KunenaBbcodeEditorElement') ) {
			return self::insertElements($editor, array($element), $pos, $where);
		}
		return false;
	}

	public static function insertElements (&$editor, $elements, $pos=NULL, $where) {
		$new_elements_keys = array();

		if (!is_array($elements)) {
			return false;
		}

		foreach ( $elements as $v) {
			$new_elements_keys[] = $v->_name;
		}

		$new_elements_values = array_values($elements);

		$editor_keys = array_keys($editor);
		$editor_values = array_values($editor);

		switch ($pos) {
			case 'after':
				if (($pos = array_search($where, $editor_keys)) === false)
					return false;
				$pos++;
				break;
			case 'before':
				$pos = array_search($where, $editor_keys);
				if ($pos === false)
					return false;
				break;
			default:
				$pos = count($editor_keys);
		}

		array_splice($editor_keys, $pos, 0, $new_elements_keys);
		array_splice($editor_values, $pos, 0, $new_elements_values);

		$editor = array_combine($editor_keys, $editor_values);
	}

	//TODO convert to class structure
	//TODO trigger ausführen
	//TODO JS zusammen bauen
	public function initialize($identifier ='class') {
		$js = "window.addEvent('domready', function() {
	kbbcode = new kbbcode('kbbcode-message', 'kbbcode-toolbar', {
		dispatchChangeEvent: true,
		changeEventDelay: 1000,
		interceptTab: true
});\n";
		$editor = simplexml_load_file(dirname(__FILE__).'/editor.xml');
		//Hook to manipulate the Editor XML like adding buttons
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');
		$dispatcher->trigger( 'onKunenaBbcodeEditorInit', array ( $this ) );

		foreach ($editor as $item) {
			if ( array_key_exists((string) $item['name'], $this->_insert['before'])) {
				foreach ( $this->_insert['before'][(string) $item['name']] as $insert_item ) {
					$js .= $this->editorItem( $insert_item, $identifier);
				}
				unset ( $this->_insert['before'][(string) $item['name']] );
			}

			$js .= $this->editorItem($item, $identifier);

			if ( array_key_exists((string) $item['name'], $this->_insert['after'])) {
				foreach ( $this->_insert['after'][(string) $item['name']] as $insert_item ) {
					$js .= $this->editorItem( $insert_item, $identifier);
				}
				unset ( $this->_insert['after'][(string) $item['name']] );
			}
		}
		foreach ( $this->_insert['afterall'] as $insert_item ) {
			$js .= $this->editorItem( $insert_item, $identifier);
		}
		$this->_insert['afterall'] = array();

		$js .= "});\n";
		$template = KunenaTemplate::getInstance();
		$template->addScript('js/editor.js');
		JFactory::getDocument()->addScriptDeclaration( "// <![CDATA[\n{$js}\n// ]]>");
	}

	/**
	 * Generate the addFunction call
	 *
	 * @param $item SimpleXMLElement which describes the functionality
	 * @param $identifier
	 * @return null|string JavaScript that needs to be added
	 */
	//TODO move into element class
	protected function editorItem($item, $identifier) {
		$js = '';
		if ($item['disabled'] == 'disabled') return null;
		switch ($item->getName()) {
			case 'button':
				// <button tag="i" name="italic" title="COM_KUNENA_EDITOR_ITALIC" alt="COM_KUNENA_EDITOR_HELPLINE_ITALIC">
				if ($item['config']) {
					$cfgVariable = (string) $item['config'];
					$cfgValue = intval($cfgVariable[0] != '!');
					if (!$cfgValue) $cfgVariable = substr($cfgVariable, 1);
					if (KunenaFactory::getConfig()->$cfgVariable != $cfgValue) continue;
				}
				$name = $item['name'] ? $item['name'] : ($item['tag'] ? $item['tag'] : '#');
				$class = $item['class'] ? $item['class'] : "kbbcode-{$name}-button";
				$js .= "\nkbbcode.addFunction('{$name}', function() {";
				$js .= $this->editorAction($name, $item);
				$js .= "\n}, {";
				foreach (array('title', 'alt') as $type) {
					if (isset($item[$type])) {
						$value = JText::_($item[$type], true);
						$js .= "\n	'{$type}': '{$value}',";
					}
				}
				if ($item['class']) {
					$js .= "\n	'class': '{$class}'";
				} else {
						$js .= "\n	'{$identifier}': '{$class}'";
				}
				$js .= "\n});\n";
				break;
			case 'seperator':
				$js .= "\nkbbcode.addFunction('#', function() {";
				$js .= "\n}, {";
				$js .= "\n	'class': 'kbbcode-separator'";
				$js .= "});\n";
				break;
		}

		return $js;
	}
 //TODO move into element class
	protected function editorAction($name, $item) {
		$js = '';
		foreach ($item as $action) {
			if ($action['disabled'] == 'disabled') continue;
			if (!empty($action['config'])) {
				$cfgVariable = (string) $action['config'];
				$cfgValue = intval($cfgVariable[0] != '!');
				if (!$cfgValue) $cfgVariable = substr($cfgVariable, 1);
				if (KunenaFactory::getConfig()->$cfgVariable != $cfgValue) continue;
			}
			$tag = $action['tag'] ? $action['tag'] : $item['tag'];
			switch ($action->getName()) {
				case 'display':
					// <display name="kbbcode-color-options" />
					if (!$tag) continue;
					if ($action['selection']) {
						$js .= "\n	sel = this.getSelection();\n	if (sel) {\n		document.id('{$action['selection']}').set('value', sel);\n	}";
					}
					$js .= "\n	kToggleOrSwap('kbbcode-{$name}-options');";

					break;
				case 'wrap-selection':
					// <wrap-selection />
					if (!$tag) continue;
					if (!$action['repeat']) {
						$js .= "\n	this.wrapSelection('[{$tag}]', '[/{$tag}]', false);";
					} else {
						$start = $action['start'] ? $action['start'] : "[{$action['tag']}]";
						$end =  $action['end'] ? $action['end'] : "[{$action['tag']}]";
						$js .= "\nselection = this.getSelection();
	if (selection) {
		this.processEachLine(function(line) {
				return '  {$start}' + line + '{$end}';
			}, false);
			this.insert('{$action['before']}', 'before', false);
			this.insert('{$action['after']}', 'after', true);
	} else {
			this.wrapSelection('{$action['empty_before']}', '{$action['empty_after']}', false);
	}";
					}
					break;
				case 'link':
					// <link url="http://docs.kunena.org/index.php/bbcode" />
					$js .= "\n	window.open('{$action['url']}');";
					break;
			}
		}
		return $js;
	}
}

abstract class KunenaBbcodeEditorElement {
	protected $_name;

	function _generateJs () {

	}
}

class KunenaBbcodeEditorButton extends KunenaBbcodeEditorElement {

}

class KunenaBbcodeEditorSeparator extends KunenaBbcodeEditorElement {

}