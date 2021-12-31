<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Form
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

jimport('joomla.form.formfield');

/**
 * Class \Joomla\CMS\Form\FormField|KunenaCategoryList
 * @since Kunena
 */
class JFormFieldKunenaCategoryList extends \Joomla\CMS\Form\FormField
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $type = 'KunenaCategoryList';

	/**
	 * @return string
	 * @throws Exception
	 * @since Kunena
	 */
	protected function getInput()
	{
		if (!class_exists('KunenaForum') || !KunenaForum::installed())
		{
			echo '<a href="index.php?option=com_kunena">PLEASE COMPLETE KUNENA INSTALLATION</a>';

			return '';
		}

		KunenaFactory::loadLanguage('com_kunena');

		$size  = $this->element['size'];
		$class = $this->element['class'];

		$attribs = ' ';

		if ($size)
		{
			$attribs .= 'size="' . $size . '"';
		}

		if ($class)
		{
			$attribs .= 'class="' . $class . '"';
		}
		else
		{
			$attribs .= 'class="inputbox"';
		}

		if (!empty($this->element['multiple']))
		{
			$attribs .= ' multiple="multiple"';
		}

		// Get the field options.
		$options = $this->getOptions();

		return HTMLHelper::_('kunenaforum.categorylist', $this->name, 0, $options, $this->element, $attribs, 'value', 'text', $this->value);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 * @since   11.1
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		foreach ($this->element->children() as $option)
		{
			// Only add <option /> elements.
			if ($option->getName() != 'option')
			{
				continue;
			}

			// Create a new option object based on the <option /> element.
			$tmp = HTMLHelper::_('select.option', (string) $option['value'], Text::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text', ((string) $option['disabled'] == 'true'));

			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
