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

kimport ('kunena.bbcode');

// TODO: add possibility to hide contents from these tags:
// [hide], [confidential], [spoiler], [attachment], [code]

/**
 * Kunena BBCode Editor Class
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		2.0
 */
class KunenaBBCodeEditor {
	function __construct($config = array()) {
		$this->config = $config;
	}

	public function &getInstance($config = array()) {
		static $instance = false;
		if (! $instance) {
			$instance = new KunenaBBCodeEditor ($config);
		}
		return $instance;
	}

	public function initialize($identifier ='class') {
		$js = "window.addEvent('domready', function() {
	kbbcode = new kbbcode('kbbcode-message', 'kbbcode-toolbar', {
		dispatchChangeEvent: true,
		changeEventDelay: 1000,
		interceptTab: true
});\n";
		$editor = simplexml_load_file(dirname(__FILE__).'/editor.xml');
		foreach ($editor as $item) {
			if ($item['disabled'] == 'disabled') continue;
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
			}
		}
		$js .= "});\n";
		$template = KunenaTemplate::getInstance();
		$template->addScript('js/editor.js');
		JFactory::getDocument()->addScriptDeclaration( "// <![CDATA[\n{$js}\n// ]]>");
	}

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