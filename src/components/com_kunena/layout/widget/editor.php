<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.widget
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\Folder;

/**
 * KunenaLayoutTopicEditEditor
 *
 * @since   Kunena 4.0
 */
class KunenaLayoutWidgetEditor extends KunenaLayout
{
	/**
	 * Get geshi code types.
	 *
	 * @return  array|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getCodeTypes()
	{
		$templatesettings = KunenaFactory::getTemplate()->params;

		if (!$templatesettings->get('highlightcode'))
		{
			return;
		}

		$paths = [
			JPATH_ROOT . '/plugins/content/geshiall/geshi/geshi',
			JPATH_ROOT . '/plugins/content/geshi/geshi/geshi',
		];

		foreach ($paths as $path)
		{
			if (!is_dir($path))
			{
				continue;
			}

			$files     = Folder::files($path, ".php");
			$options   = [];
			$options[] = HTMLHelper::_('select.option', '', Text::_('COM_KUNENA_EDITOR_CODE_TYPE'));

			foreach ($files as $file)
			{
				$options[] = HTMLHelper::_('select.option', substr($file, 0, -4), substr($file, 0, -4));
			}

			$list = HTMLHelper::_('select.genericlist', $options, 'kcodetype', 'class="kbutton form-control"', 'value', 'text', '-1');

			return $list;
		}

		return;
	}

	/**
	 * Define javascript variables to show or disable some bbcode buttons
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getBBcodesEnabled()
	{
		$this->ktemplate  = KunenaFactory::getTemplate();
		$templatesettings = $this->ktemplate->params;
		$config           = KunenaConfig::getInstance();

		$bbcodes = [
			"spoiler",
			"maps",
			"twitter",
			"link",
			"picture",
			"hide",
			"table",
			"code",
			"quote",
			"divider",
			"instagram",
			"soundcloud",
			"confidential",
			"hr",
			"listitem",
			"supscript",
			"subscript",
			"numericlist",
			"bulletedlist",
			"alignright",
			"alignleft",
			"center",
			"underline",
			"italic",
			"bold",
			"strikethrough",
			"colors",
			"size",
			"video",
			"emoticons",
			"ebay"
		];

		foreach ($bbcodes as $item)
		{
			$option = 0;

			if ($item == 'video' || $item == 'ebay')
			{
				$tag = "show" . $item . "tag";

				if ($config->$tag && $templatesettings->get($item))
				{
					$option = 1;
				}
			}
			elseif ($item == 'emoticons')
			{
				if (!$config->disemoticons && $templatesettings->get($item))
				{
					$option = 1;
				}
			}
			elseif ($templatesettings->get($item))
			{
				$option = 1;
			}

			$this->addScriptOptions("kunena_show" . $item . "tag", $option);
		}
	}
}
