<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage BBCode
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined('_JEXEC') or die();

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
	 * @param   array $config
	 *
	 */
	function __construct($config = array())
	{
		$this->config = $config;
		$this->template = KunenaFactory::getTemplate();
	}

	/**
	 * @param   array $config
	 * @return KunenaBbcodeEditor
	 */
	public static function getInstance($config = array())
	{
		static $instance = false;

		if (! $instance)
		{
			$instance = new KunenaBbcodeEditor($config);
		}

		return $instance;
	}

	/**
	 * Inserts a button or another element at the specified location. See insertElements for details.
	 *
	 * @param $element
	 * @param   null $pos
	 * @param $where
	 * @return boolean
	 */
	public function insertElement($element, $pos = null, $where = null)
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
	 * @param   null $pos
	 * @param $where
	 * @return boolean
	 */
	public function insertElements($elements, $pos = null, $where = null)
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
	public static function parseXML(SimpleXMLElement $xml, $parseMethod)
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
	 * Initialize editor by calling HMVC version
	 *
	 *
	 * @return void
	 */
	public function initialize()
	{
		$template = KunenaFactory::getTemplate();

		$this->isHMVC = $template->isHmvc();
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
		$dispatcher = JEventDispatcher::getInstance();
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
		file_put_contents(KPATH_SITE . '/template/' . $this->template->name . '/assets/js/markitup.editor.js', $js);
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
	 * Internal function that is used to parse an XML representation of an element.
	 *
	 * @static
	 * @abstract
	 * @param $xml
	 */
	public static function parseHMVCXML(SimpleXMLElement $xml)
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

	/**
	 *
	 * @param   SimpleXMLElement $xml
	 * @return KunenaBbcodeEditorButton
	 */
	public static function parseHMVCXML(SimpleXMLElement $xml)
	{
		$obj = new KunenaBbcodeEditorButton((string) $xml['name'], (string) $xml['class'], (string) $xml['tag'], (string) $xml['title'], (string) $xml['alt']);

		foreach ($xml as $xml_item)
		{
			$item = array();
			$item['type'] = $xml_item->getName();
			$item['tag'] = (string) $xml_item['tag'];

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
					$item['empty_before'] = (string) $xml_item['empty_before'];
					$item['empty_after'] = (string) $xml_item['empty_after'];
					$item['repeat'] = (string) $xml_item['repeat'];
					$item['start'] = (string) $xml_item['start'];
					$item['end'] = (string) $xml_item['end'];
					$item['before'] = (string) $xml_item['before'];
					$item['after'] = (string) $xml_item['after'];
					$item['class'] = (string) $xml_item['class'];
					$item['key'] = (string) $xml_item['key'];
					$item['name'] = (string) $xml_item['name'];

					break;
				case 'dropdown':
					$item['start'] = (string) $xml_item['start'];
					$item['end'] = (string) $xml_item['end'];
					$item['selection'] = (string) $xml_item['selection'];
					$item['class'] = (string) $xml_item['class'];
					$item['key'] = (string) $xml_item['key'];
					$item['name'] = (string) $xml_item['name'];
					break;
				case 'modal':
					$item['key'] = (string) $xml_item['key'];
					$item['start'] = (string) $xml_item['start'];
					$item['end'] = (string) $xml_item['end'];
					$item['class'] = (string) $xml_item['class'];
					$item['name'] = (string) $xml_item['name'];
					break;
				case 'link':
					$item['url'] = (string) $xml_item['url'];
					break;
			}

			$obj->actions[] = $item;
		}

		return $obj;
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

					if (!empty($action['class']))
					{
						$selection[] = "className: '" . $action['class'] . "'";
					}

					$name = '';

					if (!empty($action['name']))
					{
						$selection[] = "name: '" . $action['name'] . "'";
					}

					if ($action['name'] == 'Bold')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_BOLD') . "'";
					}
					elseif ($action['name'] == 'Italic')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_ITALIC') . "'";
					}
					elseif ($action['name'] == 'Underline')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_UNDERL') . "'";
					}
					elseif ($action['name'] == 'Stroke')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_STRIKE') . "'";
					}
					elseif ($action['name'] == 'Subscript')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_SUB') . "'";
					}
					elseif ($action['name'] == 'Supscript')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_SUP') . "'";
					}
					elseif ($action['name'] == 'Unordered List')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_ULIST') . "'";
					}
					elseif ($action['name'] == 'Ordered List')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_OLIST') . "'";
					}
					elseif ($action['name'] == 'Li')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_LIST') . "'";
					}
					elseif ($action['name'] == 'HR')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_HR') . "'";
					}
					elseif ($action['name'] == 'Left')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_LEFT') . "'";
					}
					elseif ($action['name'] == 'Center')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_CENTER') . "'";
					}
					elseif ($action['name'] == 'Right')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_RIGHT') . "'";
					}
					elseif ($action['name'] == 'Quote')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_QUOTE') . "'";
					}
					elseif ($action['name'] == 'Code')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_CODE') . "'";
					}
					elseif ($action['name'] == 'Table')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_TABLE') . "'";
					}
					elseif ($action['name'] == 'Spoiler')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_SPOILER') . "'";
					}
					elseif ($action['name'] == 'Hide')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_EDITOR_HIDE') . "'";
					}
					elseif ($action['name'] == 'confidential')
					{
						$selection[] = "name: '" .  JText::_('COM_KUNENA_BBCODE_CONFIDENTIAL_TEXT') . "'";
					}

					$key = '';

					if (!empty($action['key']))
					{
						$selection[] = "key: '" . $action['key'] . "'";
					}

					$start = '';

					if (!empty($action['start']))
					{
						$selection[] = "openWith: '" . $action['start'] . "'";
					}

					$end = '';

					if (!empty($action['end']))
					{
						$selection[] = "closeWith: '" . $action['end'] . "'";
					}

					$selection = implode(',', $selection);

					$js = "{" . $selection . "}";
					break;
				case 'dropdown':
					if ($action['name'] == "Size")
					{
						$js = "{className: '" . $action['class'] . "', name:'" . JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE') . "', key:'" . $action['key'] . "', openWith:'" . $action['start'] . "', closeWith:'" . $action['end'] . "',	dropMenu :[
						{name: '" . JText::_('COM_KUNENA_EDITOR_SIZE_VERY_VERY_SMALL') . "', openWith:'[size=1]', closeWith:'[/size]' },
						{name: '" . JText::_('COM_KUNENA_EDITOR_SIZE_VERY_SMALL') . "', openWith:'[size=2]', closeWith:'[/size]' },
						{name: '" . JText::_('COM_KUNENA_EDITOR_SIZE_SMALL') . "', openWith:'[size=3]', closeWith:'[/size]' },
						{name: '" . JText::_('COM_KUNENA_EDITOR_SIZE_NORMAL') . "', openWith:'[size=4]', closeWith:'[/size]' },
						{name: '" . JText::_('COM_KUNENA_EDITOR_SIZE_BIG') . "', openWith:'[size=5]', closeWith:'[/size]' },
						{name: '" . JText::_('COM_KUNENA_EDITOR_SIZE_SUPER_BIGGER') . "', openWith:'[size=6]', closeWith:'[/size]' }
						]}";
					}
					elseif ($action['name'] == "videodropdownbutton")
					{
						$js = "{name: '" . JText::_('COM_KUNENA_EDITOR_VIDEO') . "', className: 'videodropdownbutton', dropMenu: [{name:  '" . JText::_('COM_KUNENA_EDITOR_VIDEO_PROVIDER') . "', className: '" . $action['class'] . "', beforeInsert:function() {
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
						{name: '" . JText::_('COM_KUNENA_EDITOR_VIDEO') . "', className: 'videoURLbutton', beforeInsert:function() {
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
						$js = "{className: '" . $action['class'] . "', name:'" . JText::_('COM_KUNENA_EDITOR_COLOR') . "', key:'" . $action['key'] . "', openWith:'" . $action['start'] . "', closeWith:'" . $action['end'] . "',dropMenu: [
						{name: '" . JText::_('COM_KUNENA_EDITOR_COLOR_BLACK') . "',	openWith:'[color=black]', 	closeWith:'[/color]', className:'col1-1' },
						{name: '" . JText::_('COM_KUNENA_EDITOR_COLOR_ORANGE') . "',	openWith:'[color=orange]', 	closeWith:'[/color]', className:'col1-2' },
						{name: '" . JText::_('COM_KUNENA_EDITOR_COLOR_RED') . "', 	openWith:'[color=red]', 	closeWith:'[/color]', className:'col1-3' },

						{name: '" . JText::_('COM_KUNENA_EDITOR_COLOR_BLUE') . "', 	openWith:'[color=blue]', 	closeWith:'[/color]', className:'col2-1' },
						{name: '" . JText::_('COM_KUNENA_EDITOR_COLOR_PURPLE') . "', openWith:'[color=purple]', 	closeWith:'[/color]', className:'col2-2' },
						{name: '" . JText::_('COM_KUNENA_EDITOR_COLOR_GREEN') . "', 	openWith:'[color=green]', 	closeWith:'[/color]', className:'col2-3' },

						{name: '" . JText::_('COM_KUNENA_EDITOR_COLOR_WHITE') . "', 	openWith:'[color=white]', 	closeWith:'[/color]', className:'col3-1' },
						{name: '" . JText::_('COM_KUNENA_EDITOR_COLOR_GRAY') . "', 	openWith:'[color=gray]', 	closeWith:'[/color]', className:'col3-2' }
						]}";
					}
					break;
				case 'modal':
					if ($action['name'] == "picture")
					{
						$js = "{name:'" . JText::_('COM_KUNENA_EDITOR_IMAGELINK') . "', className: '" . $action['class'] . "', beforeInsert:function() {
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
					}
					elseif ($action['name'] == "link")
					{
						$js = "{name:'" . JText::_('COM_KUNENA_EDITOR_LINK') . "', className: '" . $action['class'] . "', beforeInsert:function() {
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
					}
					elseif ($action['name'] == "map")
					{
						$js = "{name:'" . JText::_('COM_KUNENA_EDITOR_MAP') . "', className: '" . $action['class'] . "', beforeInsert:function() {
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
					}
					elseif ($action['name'] == "Tweet")
					{
						$js = "{name:'" . JText::_('COM_KUNENA_LIB_BBCODE_TWEET_STATUS_LINK') . "', className: '" . $action['class'] . "', beforeInsert:function() {
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
					}
					elseif ($action['name'] == "emoticons")
					{
						$js = "{name:'" . JText::_('COM_KUNENA_EDITOR_EMOTICONS') . "', className: '" . $action['class'] . "', beforeInsert:function() {
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
					}
					else
					{
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
					}
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

	/**
	 * Add a new display action. This can be used to show a button specific action area.
	 *
	 * @param $selection
	 * @param $class
	 * @param   null $tag
	 */
	function addDisplayAction($selection, $class, $tag = null)
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
	 * @param   null $repeat
	 * @param   null $empty_before
	 * @param   null $empty_after
	 * @param   null $start
	 * @param   null $end
	 * @param   null $before
	 * @param   null $after
	 * @param   null $tag
	 */
	function addWrapSelectionAction($repeat = null, $empty_before = null, $empty_after = null, $start = null, $end = null, $before = null, $after = null, $tag = null)
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
	function addUrlAction($url)
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

	/**
	 * Parse XML for separator editor part
	 *
	 * @param   SimpleXMLElement $xml
	 * @return KunenaBbcodeEditorSeparator
	 */
	public static function parseHMVCXML(SimpleXMLElement $xml)
	{
		return new KunenaBbcodeEditorSeparator((string) $xml['name']);
	}
}
