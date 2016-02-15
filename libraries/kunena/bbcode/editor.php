<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage BBCode
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// TODO: add possibility to hide contents from these tags:
// [hide], [confidential], [spoiler], [attachment], [code]

/**
 * Kunena BBCode Editor Class
 *
 * @version  2.0
 */
class KunenaBbcodeEditor
{
	var $editor_elements = array();

	/**
	 * @param array $config
	 */
	function __construct($config = array())
	{
		$this->config = $config;
	}

	/**
	 * @param array $config
	 * @return KunenaBbcodeEditor
	 */
	public static function getInstance($config = array())
	{
		static $instance = false;

		if (! $instance)
		{
			$instance = new KunenaBbcodeEditor ($config);
		}

		return $instance;
	}

	/**
	 * Inserts a button or another element at the specified location. See insertElements for details.
	 *
	 * @param $element
	 * @param null $pos
	 * @param $where
	 * @return bool
	 */
	public function insertElement ($element, $pos = NULL, $where = NULL)
	{
		if (is_subclass_of($element, 'KunenaBbcodeEditorElement'))
		{
			$this->insertElements(array($element), $pos, $where);
		}
	}

	/**
	 * Inserts a list of buttons or other elements at the specified location. The position $pos can be
	 * specified as NULL, 'after' or 'before. In the case of 'after' and 'before' the element will be
	 * inserted after/before the element named $where. If NULL is specified then it is inserted after
	 * all other elements.
	 *
	 * @param $elements
	 * @param null $pos
	 * @param $where
	 * @return bool
	 */
	public function insertElements ($elements, $pos = NULL, $where = NULL)
	{
		$new_elements_keys = array();

		if (!is_array($elements))
		{
			return false;
		}

		foreach ($elements as $v)
		{
			$new_elements_keys[] = $v->name;
		}

		$new_elements_values = array_values($elements);

		$editor_keys = array_keys($this->editor_elements);
		$editor_values = array_values($this->editor_elements);

		switch ($pos)
		{
			case 'after':
				if (($pos = array_search($where, $editor_keys)) === false)
				{
					return false;
				}

				$pos++;
				break;
			case 'before':
				$pos = array_search($where, $editor_keys);

				if ($pos === false)
				{
					return false;
				}

				break;
			default:
				$pos = count($editor_keys);
		}

		array_splice($editor_keys, $pos, 0, $new_elements_keys);
		array_splice($editor_values, $pos, 0, $new_elements_values);

		$this->editor_elements = array_combine($editor_keys, $editor_values);
	}

	/**
	 * Parses an XML description of the buttons into the internal object representation.
	 *
	 * @param   SimpleXMLElement  $xml          The XML object to parse
	 * @param   string            $parseMethod  The parse method name to call
	 *
	 * @return array
	 */
	public static function parseXML (SimpleXMLElement $xml, $parseMethod)
	{
		$elements = array();

		foreach ($xml as $xml_item)
		{
			if ($xml_item['config'])
			{
				$cfgVariable = (string) $xml_item['config'];
				$cfgValue = intval($cfgVariable[0] != '!');

				if (!$cfgValue)
				{
					$cfgVariable = substr($cfgVariable, 1);
				}

				if (KunenaFactory::getConfig()->$cfgVariable != $cfgValue)
				{
					continue;
				}
			}

			$class = "KunenaBbcodeEditor" . strtoupper($xml_item->getName());

			$item = call_user_func(array($class, $parseMethod), $xml_item);

			$elements[$item->name] = $item;
		}

		return $elements;
	}

	/**
	 * Initialize editor by calling HMVC version or legacy one
	 *
	 * @param   string  $identifier  The class to pass for legacy editor
	 *
	 * @return void
	 */
	public function initialize($identifier='class')
	{
		$template = KunenaFactory::getTemplate();

		$this->isHMVC = $template->isHmvc();

		if ( $this->isHMVC )
		{
			$this->initializeHMVC();
		}
		else
		{
			$this->initializeLegacy($identifier);
		}
	}

	/**
	 * Initialize HMVC editor
	 *
	 * @return void
	 */
	public function initializeHMVC()
	{
		$xml_file = simplexml_load_file(dirname(__FILE__) . '/crypsis_editor.xml');

		$this->editor_elements = self::parseXML($xml_file, 'parseHMVCXML');

		// Hook to manipulate the Editor XML like adding buttons
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');
		$dispatcher->trigger('onKunenaBbcodeEditorInit', array($this));

		$js = "bbcodeSettings = {
		previewParserPath:	'',
		markupSet: [";

		$itemjs = array();

		foreach ($this->editor_elements as $item)
		{
			$itemjs[] = $item->generateHMVCJs();
		}

		$itemjs = implode(',', $itemjs);

		$js .= $itemjs;

		$js .=	']};';

		// Write the js elements into editor.markitup.js file
		file_put_contents(KPATH_SITE . '/template/crypsis/media/js/markitup.editor.js', $js);
	}

	/**
	 * Initialize legacy editor
	 *
	 * @param string $identifier
	 *
	 * @return void
	 */
	public function initializeLegacy($identifier='class')
	{
		$js = "window.addEvent('domready', function() {
	kbbcode = new kbbcode('kbbcode-message', 'kbbcode-toolbar', {
		dispatchChangeEvent: true,
		changeEventDelay: 1000,
		interceptTab: true
});\n";
		$xml_file = simplexml_load_file(dirname(__FILE__).'/editor.xml');

		$this->editor_elements = self::parseXML($xml_file, 'parseXML');

		// Hook to manipulate the Editor XML like adding buttons
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');
		$dispatcher->trigger( 'onKunenaBbcodeEditorInit', array ( $this ) );

		foreach ($this->editor_elements as $item)
		{
			$js .= $item->generateJs($identifier);
		}

		$js .= "});\n";
		$template = KunenaTemplate::getInstance();
		$template->addScript('editor.js');
		JFactory::getDocument()->addScriptDeclaration( "// <![CDATA[\n{$js}\n// ]]>");
	}
}

/**
 * Class KunenaBbcodeEditorElement
 */
abstract class KunenaBbcodeEditorElement
{
	var $name;

	/**
	 * Constructor for the base class for editor elements.
	 *
	 * @param $name
	 */
	function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * Generate and creates the JavaScript code required to show the buttons.
	 *
	 * @abstract
	 * @param $identifier
	 */
	abstract function generateJs ($identifier);

	/**
	 * Internal function that is used to parse an XML representation of an element.
	 *
	 * @static
	 * @abstract
	 * @param $xml
	 */
	public static function parseXML (SimpleXMLElement $xml)
	{

	}

	/**
	 * Internal function that is used to parse an XML representation of an element.
	 *
	 * @static
	 * @abstract
	 * @param $xml
	 */
	public static function parseHMVCXML (SimpleXMLElement $xml)
	{

	}
}

/**
 * Class KunenaBbcodeEditorButton
 */
class KunenaBbcodeEditorButton extends KunenaBbcodeEditorElement
{
	protected $tag;
	protected $config;
	protected $title;
	protected $alt;
	protected $class;
	protected $actions = array();

	/**
	 * Create a button that can be added to the BBCode Editor.
	 *
	 * @param $name
	 * @param $class
	 * @param $tag
	 * @param $title
	 * @param $alt
	 */
	function __construct($name, $class, $tag, $title, $alt)
	{
		parent::__construct($name);

		$this->tag = $tag;
		$this->title = $title;
		$this->alt = $alt;
		$this->class = $class;
	}

	public static function parseXML(SimpleXMLElement $xml)
	{
		$obj = new KunenaBbcodeEditorButton((string)$xml['name'], (string)$xml['class'], (string)$xml['tag'], (string)$xml['title'], (string)$xml['alt']);

		foreach ($xml as $xml_item)
		{
			$item = array();
			$item['type'] = $xml_item->getName();
			$item['tag'] = (string)$xml_item['tag'];

			if ($xml_item['disabled'] == 'disabled')
			{
				continue;
			}

			if ($xml_item['config'])
			{
				$cfgVariable = (string) $xml_item['config'];
				$cfgValue = intval($cfgVariable[0] != '!');

				if (!$cfgValue)
				{
					$cfgVariable = substr($cfgVariable, 1);
				}

				if (KunenaFactory::getConfig()->$cfgVariable != $cfgValue)
				{
					continue;
				}
			}

			switch ($item['type'])
			{
				case 'wrap-selection':
					$item['empty_before'] = (string)$xml_item['empty_before'];
					$item['empty_after'] = (string)$xml_item['empty_after'];
					$item['repeat'] = (string)$xml_item['repeat'];
					$item['start'] = (string)$xml_item['start'];
					$item['end'] = (string)$xml_item['end'];
					$item['before'] = (string)$xml_item['before'];
					$item['after'] = (string)$xml_item['after'];

					break;
				case 'display':
					$item['selection'] = (string)$xml_item['selection'];
					$item['class'] = (string)$xml_item['class'];
					break;
				case 'link':
					$item['url'] = (string)$xml_item['url'];
					break;
			}

			$obj->actions[] = $item;
		}

		return $obj;
	}

	/**
	 *
	 * @param SimpleXMLElement $xml
	 * @return KunenaBbcodeEditorButton
	 */
	public static function parseHMVCXML(SimpleXMLElement $xml)
	{
		$obj = new KunenaBbcodeEditorButton((string)$xml['name'], (string)$xml['class'], (string)$xml['tag'], (string)$xml['title'], (string)$xml['alt']);

		foreach ($xml as $xml_item)
		{
			$item = array();
			$item['type'] = $xml_item->getName();
			$item['tag'] = (string)$xml_item['tag'];

			if ($xml_item['disabled'] == 'disabled')
			{
				continue;
			}

			if ($xml_item['config'])
			{
				$cfgVariable = (string) $xml_item['config'];
				$cfgValue = intval($cfgVariable[0] != '!');

				if (!$cfgValue)
				{
					$cfgVariable = substr($cfgVariable, 1);
				}

				if (KunenaFactory::getConfig()->$cfgVariable != $cfgValue)
				{
					continue;
				}
			}

			switch ($item['type'])
			{
				case 'wrap-selection':
					$item['empty_before'] = (string)$xml_item['empty_before'];
					$item['empty_after'] = (string)$xml_item['empty_after'];
					$item['repeat'] = (string)$xml_item['repeat'];
					$item['start'] = (string)$xml_item['start'];
					$item['end'] = (string)$xml_item['end'];
					$item['before'] = (string)$xml_item['before'];
					$item['after'] = (string)$xml_item['after'];
					$item['class'] = (string)$xml_item['class'];
					$item['key'] = (string)$xml_item['key'];
					$item['name'] = (string)$xml_item['name'];

					break;
				case 'dropdown':
					$item['start'] = (string)$xml_item['start'];
					$item['end'] = (string)$xml_item['end'];
					$item['selection'] = (string)$xml_item['selection'];
					$item['class'] = (string)$xml_item['class'];
					$item['key'] = (string)$xml_item['key'];
					$item['name'] = (string)$xml_item['name'];
					break;
				case 'modal':
					$item['key'] = (string)$xml_item['key'];
					$item['start'] = (string)$xml_item['start'];
					$item['end'] = (string)$xml_item['end'];
					$item['class'] = (string)$xml_item['class'];
					$item['name'] = (string)$xml_item['name'];
					break;
				case 'link':
					$item['url'] = (string)$xml_item['url'];
					break;
			}

			$obj->actions[] = $item;
		}

		return $obj;
	}

	/**
	 * Generate the JavaScript for each of the actions that the button has.
	 *
	 * @param $name
	 * @return string
	 */
	protected function editorActionJs($name)
	{
		$js = '';

		foreach ($this->actions as $action)
		{
			$tag = $action['tag'] ? $action['tag'] : $this->tag;

			switch ($action['type'])
			{
				case 'display':
					// <display name="kbbcode-color-options" />
					if (!$tag)
					{
						continue;
					}

					if ($action['selection'])
					{
						$js .= "\n	sel = this.focus().getSelection(); if (sel) { document.id('{$action['selection']}').set('value', sel); }";
					}

					$js .= "\n	kToggleOrSwap('kbbcode-{$name}-options');";

					break;
				case 'wrap-selection':
					// <wrap-selection />
					if (!$tag)
					{
						continue;
					}

					if (!$action['repeat'])
					{
						$js .= "\n	this.focus().wrapSelection('[{$tag}]', '[/{$tag}]', true);";
					}
					else
					{
						$start = $action['start'] ? $action['start'] : "[{$action['tag']}]";
						$end =  $action['end'] ? $action['end'] : "[/{$action['tag']}]";
						$js .= "\nselection = this.focus().getSelection();
	if (selection) {
		this.processEachLine(function(line) {
			return '  {$start}' + line + '{$end}';
		}, false);
		this.wrapSelection('{$action['before']}', '{$action['after']}', false);
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

	/**
	 *
	 * @return string
	 */
	protected function editorActionHMVCJs()
	{
		$js = '';

		foreach ($this->actions as $action)
		{
			switch ($action['type'])
			{
				case 'wrap-selection':
					$selection = array();

					$classname = '';

					if ( !empty($action['class']) )
					{
						$selection[]="className: '" . $action['class'] . "'";
					}

					$name = '';

					if ( !empty($action['name']) )
					{
						$selection[]="name: '" . $action['name'] . "'";
					}

					$key = '';

					if ( !empty($action['key']) )
					{
						$selection[]="key: '" . $action['key'] . "'";
					}

					$start = '';

					if ( !empty($action['start']) )
					{
						$selection[]="openWith: '" . $action['start'] . "'";
					}

					$end = '';

					if ( !empty($action['end']) )
					{
						$selection[]="closeWith: '" . $action['end'] . "'";
					}

					$selection = implode(',',$selection);

					$js = "{".$selection."}";
					break;
				case 'dropdown':
					if ($action['name'] == "Size")
					{
						$js = "{className: '" . $action['class'] . "', name:'" . $action['name'] . "', key:'" .$action['key']. "', openWith:'" . $action['start'] . "', closeWith:'" . $action['end'] . "',	dropMenu :[
						{name:'Very very small', openWith:'[size=1]', 	closeWith:'[/size]' },
						{name:'Very Small', openWith:'[size=2]', 	closeWith:'[/size]' },
						{name:'Small', openWith:'[size=3]', closeWith:'[/size]' },
						{name:'Normal', openWith:'[size=4]', closeWith:'[/size]' },
						{name:'Big', openWith:'[size=5]', closeWith:'[/size]' },
						{name:'Super Bigger', openWith:'[size=6]', closeWith:'[/size]' }
						]}";
					}
					elseif ($action['name'] == "videodropdownbutton")
					{
						$js = "{name:'Video', className: 'videodropdownbutton', dropMenu: [{name: '" . $action['class'] . "', className: '" . $action['class'] . "', beforeInsert:function() {
							jQuery('#videosettings-modal-submit').click(function(event) {
								event.preventDefault();

								jQuery('#modal-video-settings').modal('hide');
							});

							jQuery('#modal-video-settings').modal(
								{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
									dialog.overlay.fadeIn('slow', function () {
										dialog.container.slideDown('slow', function () {
											dialog.data.fadeIn('slow');
										});
									});
								}});
							} },
						{name: 'Video Provider URL', className: 'videoURLbutton', beforeInsert:function() {
							jQuery('#videourlprovider-modal-submit').click(function(event) {
								event.preventDefault();

								jQuery('#modal-video-urlprovider').modal('hide');
							});

							jQuery('#modal-video-urlprovider').modal(
								{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
									dialog.overlay.fadeIn('slow', function () {
										dialog.container.slideDown('slow', function () {
											dialog.data.fadeIn('slow');
										});
									});
								}});
							} }
						]}";
					}
					elseif ($action['name'] == "Colors")
					{
						$js = "{className: '" . $action['class'] . "', name:'" . $action['name'] . "', key:'" . $action['key'] . "', openWith:'" . $action['start'] . "', closeWith:'" . $action['end'] . "',dropMenu: [
						{name:'Black',	openWith:'[color=black]', 	closeWith:'[/color]', className:'col1-1' },
						{name:'Orange',	openWith:'[color=orange]', 	closeWith:'[/color]', className:'col1-2' },
						{name:'Red', 	openWith:'[color=red]', 	closeWith:'[/color]', className:'col1-3' },

						{name:'Blue', 	openWith:'[color=blue]', 	closeWith:'[/color]', className:'col2-1' },
						{name:'Purple', openWith:'[color=purple]', 	closeWith:'[/color]', className:'col2-2' },
						{name:'Green', 	openWith:'[color=green]', 	closeWith:'[/color]', className:'col2-3' },

						{name:'White', 	openWith:'[color=white]', 	closeWith:'[/color]', className:'col3-1' },
						{name:'Gray', 	openWith:'[color=gray]', 	closeWith:'[/color]', className:'col3-2' }
						]}";
					}
					break;
				case 'modal':
					$js = "{name:'" . $action['name'] . "', className: '" . $action['class'] . "', beforeInsert:function() {
						jQuery('#" . $action['name'] . "-modal-submit').click(function(event) {
							event.preventDefault();

							jQuery('#modal-" . $action['name'] . "').modal('hide');
						});

						jQuery('#modal-" . $action['name'] . "').modal(
							{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
								dialog.overlay.fadeIn('slow', function () {
									dialog.container.slideDown('slow', function () {
										dialog.data.fadeIn('slow');
									});
								});
							}});
						}
					}";
					break;
			}
		}

		return $js;
	}

	/**
	 *
	 * @return string
	 */
	public function generateHMVCJs()
	{
		$js = $this->editorActionHMVCJs($this->name);

		return $js;
	}

	function generateJs ($identifier)
	{
		// <button tag="i" name="italic" title="COM_KUNENA_EDITOR_ITALIC" alt="COM_KUNENA_EDITOR_HELPLINE_ITALIC">
		$name = $this->name ? $this->name : ($this->tag ? $this->tag : '#');
		$class = $this->class ? $this->class : "kbbcode-{$name}-button";
		$js = "\nkbbcode.addFunction('{$name}', function() {";
		$js .= $this->editorActionJs($name);
		$js .= "\n}, {";

		foreach (array('title', 'alt') as $type)
		{
			if ($this->$type)
			{
				$value = JText::_($this->$type, true);
				$js .= "'{$type}': '{$value}', ";
			}
		}

		$js .= "'{$identifier}': '{$class}'";
		$js .= "\n});\n";

		return $js;
	}

	/**
	 * Add a new display action. This can be used to show a button specific action area.
	 *
	 * @param $selection
	 * @param $class
	 * @param null $tag
	 */
	function addDisplayAction ($selection, $class, $tag = NULL)
	{
		$item['type'] = 'display';
		$item['selection'] = $selection;
		$item['class'] = $class;
		$item['tag'] = $tag;

		$this->actions[] = $item;
	}

	/**
	 * Specify what code should be inserted when the user presses the button.
	 *
	 * @param null $repeat
	 * @param null $empty_before
	 * @param null $empty_after
	 * @param null $start
	 * @param null $end
	 * @param null $before
	 * @param null $after
	 * @param null $tag
	 */
	function addWrapSelectionAction ($repeat = NULL, $empty_before = NULL, $empty_after = NULL, $start = NULL, $end = NULL, $before = NULL, $after = NULL, $tag = NULL)
	{
		$item['type'] = 'wrap-selection';
		$item['repeat'] = $repeat;

		if ($repeat)
		{
			$item['empty_before'] = $empty_before;
			$item['empty_after'] = $empty_after;
			$item['start'] = $start;
			$item['end'] = $end;
			$item['before'] = $before;
			$item['after'] = $after;
		}

		$item['start'] = $start;
		$item['end'] = $end;
		$item['name'] = $this->class;
		$item['class'] = $this->class;
		$item['tag'] = $tag;

		$this->actions[] = $item;
	}

	/**
	 * Open the specified URL when the button is pressed.
	 *
	 * @param $url
	 */
	function addUrlAction ($url)
	{
		$item['type'] = 'url';
		$item['url'] = $url;
		$this->actions[] = $item;
	}
}

/**
 * Class KunenaBbcodeEditorSeparator
 */
class KunenaBbcodeEditorSeparator extends KunenaBbcodeEditorElement
{
	/**
	 * Generate JS part for element
	 *
	 * @return string
	 */
	public function generateHMVCJs()
	{
		$js = "{separator:'|' }";

		return $js;
	}

	public function generateJs ($identifier)
	{
		$js = "\nkbbcode.addFunction('#', function() {";
		$js .= "\n}, {";
		$js .= "\n	'class': 'kbbcode-separator'";
		$js .= "});\n";

		return $js;
	}

	public static function parseXML (SimpleXMLElement $xml)
	{
		return new KunenaBbcodeEditorSeparator((string)$xml['name']);
	}

	/**
	 * Parse XML for separator editor part
	 *
	 * @param SimpleXMLElement $xml
	 * @return KunenaBbcodeEditorSeparator
	 */
	public static function parseHMVCXML (SimpleXMLElement $xml)
	{
		return new KunenaBbcodeEditorSeparator((string) $xml['name']);
	}
}
