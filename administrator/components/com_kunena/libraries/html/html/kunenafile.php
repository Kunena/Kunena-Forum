<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage HTML
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Taken from Joomla Platform 11.1
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Utility class for creating HTML Upload
 */
abstract class JHtmlKunenaFile
{
	/**
	 * Display a boolean setting widget.
	 *
	 * @param   integer  The row index.
	 * @param   integer  The value of the boolean field.
	 * @param   string   Task to turn the boolean setting on.
	 * @param   string   Task to turn the boolean setting off.
	 *
	 * @return  string   The boolean setting widget.
	 */
	public static function uploader($name = 'file') {
		// Load the behavior.
		self::behavior();

		$uploadUri = KunenaRoute::_('index.php?option=com_kunena&view=topic&task=upload&'.JUtility::getToken().'=1');
		$baseUri = JURI::root(true).'/media/kunena/js/plupload';
		$textRemove = JText::_('COM_KUNENA_GEN_REMOVE_FILE');
		$textInsert = JText::_('COM_KUNENA_EDITOR_INSERT');

		$js = <<<JS
window.addEvent('domready', function() {
	var uploader = new Kunena.Uploader('{$name}', {
		url: '{$uploadUri}',
		max_file_size : '10mb',
		chunk_size : '1mb',
		unique_names : true,
		resize : {width : 320, height : 240, quality : 90},
	});
});
JS;

		// Add the uploader initialization to the document head.
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($js);
	}

	public static function behavior() {
		static $loaded = false;

		if (!$loaded)
		{
			$document = JFactory::getDocument();
			$document->addScript ( 'media/kunena/js/plupload/plupload.js' );
			$document->addScript ( 'components/com_kunena/template/default20/js/uploader.js' );

			$loaded = true;
		}
	}
}