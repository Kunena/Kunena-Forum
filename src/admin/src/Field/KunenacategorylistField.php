<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Field
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;

/**
 * Kunenacategorylist field.
 *
 * @since  Kunena 6.0
 */
class KunenacategorylistField extends ListField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  Kunena 6.0
	 */
	protected $type = 'KunenacategoryList';

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function getInput(): string
	{
		if (!class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') || !KunenaForum::installed())
		{
			echo '<a href="' . Route::_('index.php?option=com_kunena') . '">PLEASE COMPLETE KUNENA INSTALLATION</a>';

			return '';
		}

		Factory::getApplication()->bootComponent('com_kunena');
		KunenaFactory::loadLanguage('com_kunena');

		$size  = $this->element['size'];
		$class = $this->element['class'];

		$attribs = '';

		if ($size)
		{
			$attribs .= ' size="' . $size . '"';
		}
		else
		{
			$attribs .= ' size="5"';
		}

		if ($class)
		{
			$attribs .= ' class="' . $class . '"';
		}
		else
		{
			$attribs .= ' class="inputbox form-control"';
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
	 *
	 * @since   Kunena 6.0
	 */
	protected function getOptions(): array
	{
		// Initialize variables.
		$options = [];

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
