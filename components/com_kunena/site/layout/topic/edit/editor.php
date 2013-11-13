<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
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
			if (!file_exists($path))
			{
				continue;
			}

			$files = JFolder::files($path, ".php");
			$options = array();
			$options[] = JHTML::_('select.option', '', JText::_('COM_KUNENA_EDITOR_CODE_TYPE'));

			foreach ($files as $file)
			{
				$options[] = JHTML::_('select.option', substr($file,0,-4), substr($file,0,-4));
			}

			$javascript = "document.id('helpbox').set('value', '".JText::_('COM_KUNENA_EDITOR_HELPLINE_CODETYPE', true)."')";
			$list = JHTML::_('select.genericlist', $options, 'kcodetype"', 'class="kbutton" onmouseover="'.$javascript.'"' , 'value', 'text', '-1' );

			return $list;
		}

		return null;
	}
}
