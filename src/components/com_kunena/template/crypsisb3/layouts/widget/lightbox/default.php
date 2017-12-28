<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$config = KunenaFactory::getConfig();

// Load FancyBox library if enabled in configuration
if ($config->lightbox == 1)
{
	$template = KunenaTemplate::getInstance();

	if ($template->params->get('lightboxColor') == 'white')
	{
		$this->addStyleSheet('assets/css/fancybox.white.css');
	}
	else  {
		$this->addStyleSheet('assets/css/fancybox.black.css');
	}

	$this->addScript('assets/js/fancybox.js');
	JFactory::getDocument()->addScriptDeclaration('
				jQuery(document).ready(function() {
					jQuery(".fancybox-button").fancybox({
						type: \'image\',
						prevEffect		: \'none\',
						nextEffect		: \'none\',
						closeBtn		:  true,
						helpers		: {
							title	: { type : \'inside\' },
							buttons	: {}
						}
					});
				});
			');
}

?>
