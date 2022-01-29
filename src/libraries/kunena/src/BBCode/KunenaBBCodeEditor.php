<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    BBCode
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\BBCode;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use SimpleXMLElement;

// TODO: add possibility to hide contents from these tags:
// [hide], [confidential], [spoiler], [attachment], [code]

/**
 * Kunena BBCode Editor Class
 *
 * @since   Kunena 2.0
 */
class KunenaBBCodeEditor
{
	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $editor_elements = [];

	/**
	 * @var KunenaTemplate
	 * @since version
	 */
	private $template;

	/**
	 * @var array
	 * @since version
	 */
	private $config;

	/**
	 * @param   array  $config  config
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function __construct($config = [])
	{
		$this->config   = $config;
		$this->template = KunenaFactory::getTemplate();
	}

	/**
	 * @param   array  $config  config
	 *
	 * @return  KunenaBBCodeEditor
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getInstance($config = [])
	{
		static $instance = false;

		if (!$instance)
		{
			$instance = new KunenaBBCodeEditor($config);
		}

		return $instance;
	}

	/**
	 * Inserts a button or another element at the specified location. See insertElements for details.
	 *
	 * @param   mixed  $element  element
	 * @param   null   $pos      pos
	 * @param   mixed  $where    where
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function insertElement($element, $pos = null, $where = null): void
	{
		if (is_subclass_of($element, 'KunenaBBCodeEditorElement'))
		{
			$this->insertElements([$element], $pos, $where);
		}
	}

	/**
	 * Inserts a list of buttons or other elements at the specified location. The position $pos can be
	 * specified as NULL, 'after' or 'before. In the case of 'after' and 'before' the element will be
	 * inserted after/before the element named $where. If NULL is specified then it is inserted after
	 * all other elements.
	 *
	 * @param   mixed  $elements  elements
	 * @param   null   $pos       pos
	 * @param   mixed  $where     where
	 *
	 * @return bool
	 *
	 * @since   Kunena 6.0
	 */
	public function insertElements($elements, $pos = null, $where = null): bool
	{
		$new_elements_keys = [];

		if (!\is_array($elements))
		{
			return false;
		}

		foreach ($elements as $v)
		{
			$new_elements_keys[] = $v->name;
		}

		$new_elements_values = array_values($elements);

		$editor_keys   = array_keys($this->editor_elements);
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
				$pos = \count($editor_keys);
		}

		array_splice($editor_keys, $pos, 0, $new_elements_keys);
		array_splice($editor_values, $pos, 0, $new_elements_values);

		$this->editor_elements = array_combine($editor_keys, $editor_values);

		return true;
	}

	/**
	 * Initialize editor by calling HMVC version
	 *
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function initialize(): void
	{
	}

	/**
	 * Initialize HMVC editor
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function initializeHMVC(): void
	{
		$xml_file = simplexml_load_file(\dirname(__FILE__) . '/aurelia_editor.xml');

		$this->editor_elements = self::parseXML($xml_file, 'parseHMVCXML');

		// Hook to manipulate the Editor XML like adding buttons

		PluginHelper::importPlugin('kunena');
		Factory::getApplication()->triggerEvent('onKunenaBBCodeEditorInit', [$this]);

		$js = "bbcodeSettings = {
		previewParserPath:	'',
		markupSet: [";

		$itemjs = [];

		foreach ($this->editor_elements as $item)
		{
			$itemjs[] = $item->generateHMVCJs();
		}

		$itemjs = implode(',', $itemjs);

		$js .= $itemjs;

		$js .= ']};';

		// Write the js elements into editor.markitup.js file
		file_put_contents(KPATH_SITE . '/template/' . $this->template->name . '/assets/js/markitup.editor.js', $js);
	}

	/**
	 * Parses an XML description of the buttons into the internal object representation.
	 *
	 * @param   SimpleXMLElement  $xml          The XML object to parse
	 * @param   string            $parseMethod  The parse method name to call
	 *
	 * @return  array
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function parseXML(SimpleXMLElement $xml, $parseMethod): array
	{
		$elements = [];

		foreach ($xml as $xml_item)
		{
			if ($xml_item['config'])
			{
				$cfgVariable = (string) $xml_item['config'];
				$cfgValue    = \intval($cfgVariable[0] != '!');

				if (!$cfgValue)
				{
					$cfgVariable = substr($cfgVariable, 1);
				}

				if (KunenaFactory::getConfig()->$cfgVariable != $cfgValue)
				{
					continue;
				}
			}

			$class = "Kunena\Forum\Libraries\BBCode\KunenaBBCodeEditor" . ucfirst($xml_item->getName());

			$item = \call_user_func([$class, $parseMethod], $xml_item);

			$elements[$item->name] = $item;
		}

		return $elements;
	}
}

/**
 * Class KunenaBbcodeEditorElement
 *
 * @since   Kunena 6.0
 */
abstract class KunenaBBCodeEditorElement
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $name;

	/**
	 * Constructor for the base class for editor elements.
	 *
	 * @param   string  $name  name
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(string $name)
	{
		$this->name = $name;
	}

	/**
	 * Internal function that is used to parse an XML representation of an element.
	 *
	 * @static
	 * @abstract
	 *
	 * @param   SimpleXMLElement  $xml  xml
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public static function parseHMVCXML(SimpleXMLElement $xml)
	{
	}
}

/**
 * Class KunenaBbcodeEditorButton
 *
 * @since   Kunena 6.0
 */
class KunenaBBCodeEditorButton extends KunenaBBCodeEditorElement
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $tag;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $config;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $title;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $alt;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $class;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $actions = [];

	/**
	 * Create a button that can be added to the BBCode Editor.
	 *
	 * @param   string  $name   name
	 * @param   string  $class  class
	 * @param   string  $tag    tag
	 * @param   string  $title  title
	 * @param   string  $alt    alt
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(string $name, string $class, string $tag, string $title, string $alt)
	{
		parent::__construct($name);

		$this->tag   = $tag;
		$this->title = $title;
		$this->alt   = $alt;
		$this->class = $class;
	}

	/**
	 * @param   SimpleXMLElement  $xml  xml
	 *
	 * @return  KunenaBBCodeEditorButton
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function parseHMVCXML(SimpleXMLElement $xml): KunenaBBCodeEditorButton
	{
		$obj = new KunenaBBCodeEditorButton((string) $xml['name'], (string) $xml['class'], (string) $xml['tag'], (string) $xml['title'], (string) $xml['alt']);

		foreach ($xml as $xml_item)
		{
			$item         = [];
			$item['type'] = $xml_item->getName();
			$item['tag']  = (string) $xml_item['tag'];

			if ($xml_item['disabled'] == 'disabled')
			{
				continue;
			}

			if ($xml_item['config'])
			{
				$cfgVariable = (string) $xml_item['config'];
				$cfgValue    = \intval($cfgVariable[0] != '!');

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
					$item['empty_after']  = (string) $xml_item['empty_after'];
					$item['repeat']       = (string) $xml_item['repeat'];
					$item['start']        = (string) $xml_item['start'];
					$item['end']          = (string) $xml_item['end'];
					$item['before']       = (string) $xml_item['before'];
					$item['after']        = (string) $xml_item['after'];
					$item['class']        = (string) $xml_item['class'];
					$item['key']          = (string) $xml_item['key'];
					$item['name']         = (string) $xml_item['name'];

					break;
				case 'dropdown':
					$item['start']     = (string) $xml_item['start'];
					$item['end']       = (string) $xml_item['end'];
					$item['selection'] = (string) $xml_item['selection'];
					$item['class']     = (string) $xml_item['class'];
					$item['key']       = (string) $xml_item['key'];
					$item['name']      = (string) $xml_item['name'];
					break;
				case 'modal':
					$item['key']   = (string) $xml_item['key'];
					$item['start'] = (string) $xml_item['start'];
					$item['end']   = (string) $xml_item['end'];
					$item['class'] = (string) $xml_item['class'];
					$item['name']  = (string) $xml_item['name'];
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
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function generateHMVCJs(): string
	{
		return $this->editorActionHMVCJs();
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected function editorActionHMVCJs(): string
	{
		$js = '';

		foreach ($this->actions as $action)
		{
			switch ($action['type'])
			{
				case 'wrap-selection':
					$selection = [];

					if (!empty($action['class']))
					{
						$selection[] = "className: '" . $action['class'] . "'";
					}

					if ($action['name'] == Text::_('COM_KUNENA_EDITOR_BOLD'))
					{
						$selection[] = "name: Joomla.Text._('COM_KUNENA_EDITOR_BOLD')";
					}
					elseif ($action['name'] == Text::_('COM_KUNENA_EDITOR_ITALIC'))
					{
						$selection[] = "name: Joomla.Text._('COM_KUNENA_EDITOR_ITALIC')";
					}
					elseif ($action['name'] == Text::_('COM_KUNENA_EDITOR_UNDERL'))
					{
						$selection[] = "name: Joomla.Text._('COM_KUNENA_EDITOR_UNDERL')";
					}
					elseif ($action['name'] == Text::_('COM_KUNENA_EDITOR_SUB'))
					{
						$selection[]   = "name: Joomla.Text._('COM_KUNENA_EDITOR_SUB')";
						$action['key'] = '';
					}
					elseif ($action['name'] == Text::_('COM_KUNENA_EDITOR_ULIST'))
					{
						$selection[] = "name: Joomla.Text._('COM_KUNENA_EDITOR_ULIST')";
					}
					elseif ($action['name'] == Text::_('COM_KUNENA_EDITOR_OLIST'))
					{
						$selection[] = "name: Joomla.Text._('COM_KUNENA_EDITOR_OLIST')";
					}
					elseif ($action['name'] == Text::_('COM_KUNENA_EDITOR_HR'))
					{
						$selection[] = "name: Joomla.Text._('COM_KUNENA_EDITOR_HR')";
					}
					elseif ($action['name'] == Text::_('COM_KUNENA_EDITOR_CENTER'))
					{
						$selection[] = "name: Joomla.Text._('COM_KUNENA_EDITOR_CENTER')";
					}
					elseif ($action['name'] == Text::_('COM_KUNENA_EDITOR_QUOTE'))
					{
						$selection[] = "name: Joomla.Text._('COM_KUNENA_EDITOR_QUOTE')";
					}
					elseif ($action['name'] == Text::_('COM_KUNENA_EDITOR_CODE'))
					{
						$selection[] = "name: Joomla.Text._('COM_KUNENA_EDITOR_CODE')";
					}
					elseif ($action['name'] == Text::_('COM_KUNENA_EDITOR_TABLE'))
					{
						$selection[] = "name: Joomla.Text._('COM_KUNENA_EDITOR_TABLE')";
					}
					elseif ($action['name'] == Text::_('COM_KUNENA_EDITOR_SPOILER'))
					{
						$selection[] = "name: Joomla.Text._('COM_KUNENA_EDITOR_SPOILER')";
					}
					elseif ($action['name'] == Text::_('COM_KUNENA_BBCODE_CONFIDENTIAL_TEXT'))
					{
						$selection[] = "name: Joomla.Text._('COM_KUNENA_BBCODE_CONFIDENTIAL_TEXT')";
					}
					else
					{
						$action['name'] = str_replace('button', '', $action['class']);

						if ($action['name'] == 'stroke')
						{
							$action['name'] = 'strike';
							$action['key']  = '';
						}
						elseif ($action['name'] == 'supscript')
						{
							$action['name'] = 'sup';
							$action['key']  = '';
						}
						elseif ($action['name'] == 'listitem')
						{
							$action['name'] = 'list';
						}
						elseif ($action['name'] == 'alignleft')
						{
							$action['name'] = 'left';
						}
						elseif ($action['name'] == 'alignright')
						{
							$action['name'] = 'right';
						}
						elseif ($action['name'] == 'hiddentext')
						{
							$action['name'] = 'hide';
						}

						$selection[] = "name: Joomla.Text._('COM_KUNENA_EDITOR_" . strtoupper($action['name']) . "')";
					}

					if (!empty($action['key']))
					{
						$selection[] = "key: '" . $action['key'] . "'";
					}

					if (!empty($action['start']))
					{
						$selection[] = "openWith: '" . $action['start'] . "'";
					}

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
						$js = "{ className: '" . $action['class'] . "', name: Joomla.Text._('COM_KUNENA_EDITOR_FONTSIZE_SELECTION'),
						 key:'" . $action['key'] . "', openWith:'" . $action['start'] . "', closeWith:'" . $action['end'] . "',	dropMenu :[
						{ name: Joomla.Text._('COM_KUNENA_EDITOR_SIZE_VERY_VERY_SMALL') , openWith:'[size=1]', closeWith:'[/size]' },
						{ name: Joomla.Text._('COM_KUNENA_EDITOR_SIZE_VERY_SMALL') , openWith:'[size=2]', closeWith:'[/size]' },
						{ name: Joomla.Text._('COM_KUNENA_EDITOR_SIZE_SMALL') , openWith:'[size=3]', closeWith:'[/size]' },
						{ name: Joomla.Text._('COM_KUNENA_EDITOR_SIZE_NORMAL') , openWith:'[size=4]', closeWith:'[/size]' },
						{ name: Joomla.Text._('COM_KUNENA_EDITOR_SIZE_BIG') , openWith:'[size=5]', closeWith:'[/size]' },
						{ name: Joomla.Text._('COM_KUNENA_EDITOR_SIZE_SUPER_BIGGER'), openWith:'[size=6]', closeWith:'[/size]' }
						]}";
					}
					elseif ($action['name'] == "videodropdownbutton")
					{
						$js = "{ name: Joomla.Text._('COM_KUNENA_EDITOR_VIDEO'), className: 'videodropdownbutton', dropMenu: [
						{ name: Joomla.Text._('COM_KUNENA_EDITOR_VIDEO_PROVIDER'), className: '" . $action['class'] . "', beforeInsert:function() {
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
						{ name: Joomla.Text._('COM_KUNENA_EDITOR_VIDEO'), className: 'videoURLbutton', beforeInsert:function() {
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
						$js = "{className: '" . $action['class'] . "', name: Joomla.Text._('COM_KUNENA_EDITOR_COLORS'), key:'" . $action['key'] . "', openWith:'" . $action['start'] . "', closeWith:'" . $action['end'] . "',dropMenu: [
						{ name: Joomla.Text._('COM_KUNENA_EDITOR_COLOR_BLACK'),	openWith:'[color=black]', 	closeWith:'[/color]', className:'col1-1' },
						{ name: Joomla.Text._('COM_KUNENA_EDITOR_COLOR_ORANGE'),	openWith:'[color=orange]', 	closeWith:'[/color]', className:'col1-2' },
						{ name: Joomla.Text._('COM_KUNENA_EDITOR_COLOR_RED'), 	openWith:'[color=red]', 	closeWith:'[/color]', className:'col1-3' },

						{ name: Joomla.Text._('COM_KUNENA_EDITOR_COLOR_BLUE'), 	openWith:'[color=blue]', 	closeWith:'[/color]', className:'col2-1' },
						{ name: Joomla.Text._('COM_KUNENA_EDITOR_COLOR_PURPLE'), openWith:'[color=purple]', 	closeWith:'[/color]', className:'col2-2' },
						{ name: Joomla.Text._('COM_KUNENA_EDITOR_COLOR_GREEN'), 	openWith:'[color=green]', 	closeWith:'[/color]', className:'col2-3' },

						{ name: Joomla.Text._('COM_KUNENA_EDITOR_COLOR_WHITE'), 	openWith:'[color=white]', 	closeWith:'[/color]', className:'col3-1' },
						{ name: Joomla.Text._('COM_KUNENA_EDITOR_COLOR_GRAY'), 	openWith:'[color=gray]', 	closeWith:'[/color]', className:'col3-2' }
						]}";
					}
					break;
				case 'modal':
					if ($action['name'] == "picture")
					{
						$js = "{ name: Joomla.Text._('COM_KUNENA_EDITOR_IMAGELINK'), className: '" . $action['class'] . "', beforeInsert:function() {
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
						$js = "{ name: Joomla.Text._('COM_KUNENA_EDITOR_LINK'), className: '" . $action['class'] . "', beforeInsert:function() {
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
						$js = "{ name: Joomla.Text._('COM_KUNENA_EDITOR_MAP'), className: '" . $action['class'] . "', beforeInsert:function() {
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
						$js = "{ name: Joomla.Text._('COM_KUNENA_LIB_BBCODE_TWEET_STATUS_LINK'), className: '" . $action['class'] . "', beforeInsert:function() {
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
						$js = "{ name: Joomla.Text._('COM_KUNENA_EDITOR_EMOTICONS'), className: '" . $action['class'] . "', beforeInsert:function() {
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
						$js = "{ name:'" . $action['name'] . "', className: '" . $action['class'] . "', beforeInsert:function() {
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
	 * Add a new display action. This can be used to show a button specific action area.
	 *
	 * @param   mixed   $selection  selection
	 * @param   string  $class      class
	 * @param   null    $tag        tag
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function addDisplayAction($selection, string $class, $tag = null): void
	{
		$item['type']      = 'display';
		$item['selection'] = $selection;
		$item['class']     = $class;
		$item['tag']       = $tag;

		$this->actions[] = $item;
	}

	/**
	 * Specify what code should be inserted when the user presses the button.
	 *
	 * @param   null  $repeat        repeat
	 * @param   null  $empty_before  empty
	 * @param   null  $empty_after   empty
	 * @param   null  $start         start
	 * @param   null  $end           end
	 * @param   null  $before        before
	 * @param   null  $after         after
	 * @param   null  $tag           tag
	 *
	 *
	 * @return  void
	 * @since   Kunena 6.0
	 */
	public function addWrapSelectionAction($repeat = null, $empty_before = null, $empty_after = null, $start = null, $end = null, $before = null, $after = null, $tag = null)
	{
		$item['type']   = 'wrap-selection';
		$item['repeat'] = $repeat;

		if ($repeat)
		{
			$item['empty_before'] = $empty_before;
			$item['empty_after']  = $empty_after;
			$item['start']        = $start;
			$item['end']          = $end;
			$item['before']       = $before;
			$item['after']        = $after;
		}

		$item['start'] = $start;
		$item['end']   = $end;
		$item['name']  = $this->class;
		$item['class'] = $this->class;
		$item['tag']   = $tag;

		$this->actions[] = $item;
	}

	/**
	 * Open the specified URL when the button is pressed.
	 *
	 * @param   string  $url  url
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function addUrlAction(string $url): void
	{
		$item['type']    = 'url';
		$item['url']     = $url;
		$this->actions[] = $item;
	}
}

/**
 * Class KunenaBbcodeEditorSeparator
 *
 * @since   Kunena 6.0
 */
class KunenaBBCodeEditorSeparator extends KunenaBBCodeEditorElement
{
	/**
	 * Parse XML for separator editor part
	 *
	 * @param   SimpleXMLElement  $xml  xml
	 *
	 * @return  KunenaBBCodeEditorSeparator
	 *
	 * @since   Kunena 6.0
	 */
	public static function parseHMVCXML(SimpleXMLElement $xml): KunenaBBCodeEditorSeparator
	{
		return new self((string) $xml['name']);
	}

	/**
	 * Generate JS part for element
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function generateHMVCJs(): string
	{
		return "{separator:'|' }";
	}
}
