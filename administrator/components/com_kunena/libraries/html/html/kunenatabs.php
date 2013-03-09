<?php

defined ( '_JEXEC' ) or die ();

jimport('joomla.html.html');

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
