<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport('joomla.html.html');

/**
 * Joomla 2.5 tabs class.
 */
abstract class JHtmlKunenaTabs
{
	public static function start()
	{
		self::_loadBehavior();
	}

	protected static function _loadBehavior()
	{
		// Include MooTools framework
		//JHtml::_('behavior.framework', true);

		$js = "window.addEvent('domready', function() {
					document.getElements('a[data-toggle=\"tab\"]').each(function(tabs) {
						new KunenaTabs(tabs);
					})
				})";

		$document = JFactory::getDocument();
		$document->addScriptDeclaration($js);
		JHtml::_('script', KPATH_MEDIA.'/kunena/js/tabs.js' , false, true);
	}
}
