<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Layout.Topic
 *
 * @copyright   (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/**
 * KunenaLayoutTopicEditEditor
 *
 * @since  K4.0
 *
 */
class KunenaLayoutTopicEditEditor extends KunenaLayout
{
	/**
	 * Get geshi code types.
	 *
	 * @return array|null
	 * @since       Kunena
	 */
	public function getCodeTypes()
	{
		if (!$this->config->highlightcode)
		{
			return null;
		}

		$paths = array(
			JPATH_ROOT . '/plugins/content/geshiall/geshi/geshi',
			JPATH_ROOT . '/plugins/content/geshi/geshi/geshi'
		);

		foreach ($paths as $path)
		{
			if (!is_dir($path))
			{
				continue;
			}

			$files     = KunenaFolder::files($path, ".php");
			$options   = array();
			$options[] = JHtml::_('select.option', '', Text::_('COM_KUNENA_EDITOR_CODE_TYPE'));

			foreach ($files as $file)
			{
				$options[] = JHtml::_('select.option', substr($file, 0, -4), substr($file, 0, -4));
			}

			$list = JHtml::_('select.genericlist', $options, 'kcodetype', 'class="kbutton form-control"', 'value', 'text', '-1');

			return $list;
		}

		return null;
	}

	/**
	 * Define javascript variables to show or disable some bbcode buttons
	 *
	 * @return void
	 * @throws Exception
	 * @since       Kunena
	 *
	 */
	public function getBBcodesEnabled()
	{
		$this->ktemplate  = KunenaFactory::getTemplate();
		$templatesettings = $this->ktemplate->params;
		$config           = KunenaConfig::getInstance();

		if ($config->showvideotag && $templatesettings->get('video'))
		{
			$this->addScriptOptions("kunena_showvideotag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showvideotag", 0);
		}

		if (!$config->disemoticons && $templatesettings->get('emoticons'))
		{
			$this->addScriptOptions("kunena_disemoticons", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_disemoticons", 0);
		}

		if ($config->showebaytag && $templatesettings->get('ebay'))
		{
			$this->addScriptOptions("kunena_showebaytag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showebaytag", 0);
		}

		if ($templatesettings->get('spoiler'))
		{
			$this->addScriptOptions("kunena_showspoilertag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showspoilertag", 0);
		}

		if ($templatesettings->get('maps'))
		{
			$this->addScriptOptions("kunena_showmapstag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showmapstag", 0);
		}

		if ($templatesettings->get('twitter'))
		{
			$this->addScriptOptions("kunena_showtwittertag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showtwittertag", 0);
		}

		if ($templatesettings->get('link'))
		{
			$this->addScriptOptions("kunena_showlinktag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showlinktag", 0);
		}

		if ($templatesettings->get('picture'))
		{
			$this->addScriptOptions("kunena_showpicturetag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showpicturetag", 0);
		}

		if ($templatesettings->get('hide'))
		{
			$this->addScriptOptions("kunena_showhidetag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showhidetag", 0);
		}

		if ($templatesettings->get('table'))
		{
			$this->addScriptOptions("kunena_showtabletag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showtabletag", 0);
		}

		if ($templatesettings->get('code'))
		{
			$this->addScriptOptions("kunena_showcodetag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showcodetag", 0);
		}

		if ($templatesettings->get('quote'))
		{
			$this->addScriptOptions("kunena_showquotetag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showquotetag", 0);
		}

		if ($templatesettings->get('divider'))
		{
			$this->addScriptOptions("kunena_showdividertag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showdividertag", 0);
		}

		if ($templatesettings->get('instagram'))
		{
			$this->addScriptOptions("kunena_showinstagramtag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showinstagramtag", 0);
		}

		if ($templatesettings->get('soundcloud'))
		{
			$this->addScriptOptions("kunena_showsoundcloudtag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showsoundcloudtag", 0);
		}

		if ($templatesettings->get('confidential'))
		{
			$this->addScriptOptions("kunena_showconfidentialtag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showconfidentialtag", 0);
		}

		if ($templatesettings->get('hr'))
		{
			$this->addScriptOptions("kunena_showhrtag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showhrtag", 0);
		}

		if ($templatesettings->get('listitem'))
		{
			$this->addScriptOptions("kunena_showlistitemtag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showlistitemtag", 0);
		}

		if ($templatesettings->get('supscript'))
		{
			$this->addScriptOptions("kunena_showsupscripttag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showsupscripttag", 0);
		}

		if ($templatesettings->get('subscript'))
		{
			$this->addScriptOptions("kunena_showsubscripttag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showsubscripttag", 0);
		}

		if ($templatesettings->get('numericlist'))
		{
			$this->addScriptOptions("kunena_shownumericlisttag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_shownumericlisttag", 0);
		}

		if ($templatesettings->get('bulletedlist'))
		{
			$this->addScriptOptions("kunena_showbulletedlisttag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showbulletedlisttag", 0);
		}

		if ($templatesettings->get('alignright'))
		{
			$this->addScriptOptions("kunena_showalignrighttag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showalignrighttag", 0);
		}

		if ($templatesettings->get('alignleft'))
		{
			$this->addScriptOptions("kunena_showalignlefttag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showalignlefttag", 0);
		}

		if ($templatesettings->get('center'))
		{
			$this->addScriptOptions("kunena_showcentertag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showcentertag", 0);
		}

		if ($templatesettings->get('underline'))
		{
			$this->addScriptOptions("kunena_showunderlinetag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showunderlinetag", 0);
		}

		if ($templatesettings->get('italic'))
		{
			$this->addScriptOptions("kunena_showitalictag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showitalictag", 0);
		}

		if ($templatesettings->get('bold'))
		{
			$this->addScriptOptions("kunena_showboldtag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showboldtag", 0);
		}

		if ($templatesettings->get('strikethrough'))
		{
			$this->addScriptOptions("kunena_showstrikethroughtag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showstrikethroughtag", 0);
		}

		if ($templatesettings->get('colors'))
		{
			$this->addScriptOptions("kunena_showcolorstag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showcolorstag", 0);
		}

		if ($templatesettings->get('size'))
		{
			$this->addScriptOptions("kunena_showsizetag", 1);
		}
		else
		{
			$this->addScriptOptions("kunena_showsizetag", 0);
		}
	}
}
