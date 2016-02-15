<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

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
	 */
	public function getCodeTypes()
	{
		if (!$this->config->highlightcode)
		{
			return null;
		}

		$paths = array(
			JPATH_ROOT.'/plugins/content/geshiall/geshi/geshi',
			JPATH_ROOT.'/plugins/content/geshi/geshi/geshi'
		);

		foreach ($paths as $path)
		{
			if (!is_dir($path))
			{
				continue;
			}

			$files = KunenaFolder::files($path, ".php");
			$options = array();
			$options[] = JHTML::_('select.option', '', JText::_('COM_KUNENA_EDITOR_CODE_TYPE'));

			foreach ($files as $file)
			{
				$options[] = JHTML::_('select.option', substr($file,0,-4), substr($file,0,-4));
			}

			$list = JHTML::_('select.genericlist', $options, 'kcodetype', 'class="kbutton form-control"' , 'value', 'text', '-1' );

			return $list;
		}

		return null;
	}

	/**
	 * Define javascript variables to show or disable some bbcode buttons
	 *
	 * @return void
	 */
	public function getBBcodesEnabled()
	{
		$templatesettings = KunenaFactory::getTemplate()->params;
		if ($this->config->showvideotag && $templatesettings->get('video'))
		{
			$this->addScriptDeclaration("kunena_showvideotag=1;");
		}
		else
		{
			$this->addScriptDeclaration("kunena_showvideotag=0;");
		}

		if (!$this->config->disemoticons && $templatesettings->get('emoticons'))
		{
			$this->addScriptDeclaration("kunena_disemoticons=1;");
		}
		else
		{
			$this->addScriptDeclaration("kunena_disemoticons=0;");
		}

		if ($this->config->showebaytag && $templatesettings->get('ebay'))
		{
			$this->addScriptDeclaration("kunena_showebaytag=1;");
		}
		else
		{
			$this->addScriptDeclaration("kunena_showebaytag=0;");
		}

		if ($templatesettings->get('spoiler'))
		{
			$this->addScriptDeclaration("kunena_showspoilertag=1;");
		}
		else
		{
			$this->addScriptDeclaration("kunena_showspoilertag=0;");
		}

		if ($templatesettings->get('maps'))
		{
			$this->addScriptDeclaration("kunena_showmapstag=1;");
		}
		else
		{
			$this->addScriptDeclaration("kunena_showmapstag=0;");
		}

		if ($templatesettings->get('twitter'))
		{
			$this->addScriptDeclaration("kunena_showtwittertag=1;");
		}
		else
		{
			$this->addScriptDeclaration("kunena_showtwittertag=0;");
		}

		if ($templatesettings->get('link'))
		{
			$this->addScriptDeclaration("kunena_showlinktag=1;");
		}
		else
		{
			$this->addScriptDeclaration("kunena_showlinktag=0;");
		}

		if ($this->config->image_upload != "" && $templatesettings->get('picture'))
		{
			$this->addScriptDeclaration("kunena_showpicturetag=1;");
		}
		else
		{
			$this->addScriptDeclaration("kunena_showpicturetag=0;");
		}

		if ($templatesettings->get('hide'))
		{
			$this->addScriptDeclaration("kunena_showhidetag=1;");
		}
		else
		{
			$this->addScriptDeclaration("kunena_showhidetag=0;");
		}

		if ($templatesettings->get('table'))
		{
			$this->addScriptDeclaration("kunena_showtabletag=1;");
		}
		else
		{
			$this->addScriptDeclaration("kunena_showtabletag=0;");
		}

		if ($templatesettings->get('code'))
		{
			$this->addScriptDeclaration("kunena_showcodetag=1;");
		}
		else
		{
			$this->addScriptDeclaration("kunena_showcodetag=0;");
		}

		if ($templatesettings->get('quote'))
		{
			$this->addScriptDeclaration("kunena_showquotetag=1;");
		}
		else
		{
			$this->addScriptDeclaration("kunena_showquotetag=0;");
		}

		if ($templatesettings->get('divider'))
		{
			$this->addScriptDeclaration("kunena_showdividertag=1;");
		}
		else
		{
			$this->addScriptDeclaration("kunena_showdividertag=0;");
		}
	}
}
