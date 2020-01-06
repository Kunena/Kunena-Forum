<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers.Misc
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * KunenaLayoutWidgetModule
 *
 * @since   Kunena 4.0
 */
class KunenaLayoutWidgetModule extends KunenaLayout
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $position = null;

	/**
	 * Renders module position.
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function renderPosition()
	{
		$document = Factory::getApplication()->getDocument();

		if ($this->position && $document->countModules($this->position))
		{
			$renderer = $document->loadRenderer('modules');
			$options  = ['style' => 'xhtml'];

			return (string) $renderer->render($this->position, $options, null);
		}

		return '';
	}
}
