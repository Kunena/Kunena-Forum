<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * KunenaLayoutTopicEditEditor
 *
 * @since  3.1
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

			$list = JHTML::_('select.genericlist', $options, 'kcodetype"', 'class="kbutton" ' , 'value', 'text', '-1' );

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
		if ($this->config->showvideotag)
		{
			$this->addScriptDeclaration("kunena_showvideotag=1;");
		}
		else
		{
			$this->addScriptDeclaration("kunena_showvideotag=0;");
		}

		if (!$this->config->disemoticons)
		{
			$this->addScriptDeclaration("kunena_disemoticons=1;");
		}
		else
		{
			$this->addScriptDeclaration("kunena_disemoticons=0;");
		}

		if ($this->config->showebaytag)
		{
			$this->addScriptDeclaration("kunena_showebaytag=1;");
		}
		else
		{
			$this->addScriptDeclaration("kunena_showebaytag=0;");
		}

		if ($this->config->showspoilertag)
		{
			$this->addScriptDeclaration("kunena_showspoilertag=1;");
		}
		else
		{
			$this->addScriptDeclaration("kunena_showspoilertag=0;");
		}
	}
}
