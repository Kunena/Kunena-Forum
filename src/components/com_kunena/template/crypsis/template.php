<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsis
 * @subpackage      Template
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

/**
 * Crypsis template.
 *
 * @since  K4.0
 */
class KunenaTemplateCrypsis extends KunenaTemplate
{
	/**
	 * List of parent template names.
	 *
	 * This template will automatically search for missing files from listed parent templates.
	 * The feature allows you to create one base template and only override changed files.
	 *
	 * @var array
	 * @since Kunena
	 */
	protected $default = array('crypsis');

	/**
	 * Template initialization.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function initialize()
	{
		// Template requires Bootstrap javascript
		HTMLHelper::_('bootstrap.framework');

		// Template also requires jQuery framework.
		HTMLHelper::_('jquery.framework');
		HTMLHelper::_('bootstrap.tooltip');

		if (version_compare(JVERSION, '4.0', '>'))
		{
			HTMLHelper::_('bootstrap.renderModal');
		}
		else
		{
			HTMLHelper::_('bootstrap.modal');
		}

		// Load JavaScript.
		$this->addScript('assets/js/main.js');

		$ktemplate = KunenaFactory::getTemplate();
		$storage   = $ktemplate->params->get('storage');

		if ($storage)
		{
			$this->addScript('localstorage.js');
		}

		// Compile CSS from LESS files.
		$this->compileLess('assets/less/crypsis.less', 'kunena.css');
		$this->addLessSheet('kunena.css');

		$filename = JPATH_SITE . '/components/com_kunena/template/crypsis/assets/css/custom.css';

		if (file_exists($filename) && filesize($filename) != 0)
		{
			$this->addLessSheet('assets/css/custom.css');
		}

		$bootstrap = $ktemplate->params->get('bootstrap');

		if ($bootstrap)
		{
			$this->addStyleSheet(Uri::base() . 'media/jui/css/bootstrap.min.css');
			$this->addStyleSheet(Uri::base() . 'media/jui/css/bootstrap-extended.css');
			$this->addStyleSheet(Uri::base() . 'media/jui/css/bootstrap-responsive.min.css');

			if ($ktemplate->params->get('icomoon'))
			{
				$this->addStyleSheet(Uri::base() . 'media/jui/css/icomoon.css');
			}
		}

		$this->loadFontawesome();

		// Load template colors settings
		$styles    = <<<EOF
		/* Kunena Custom CSS */
EOF;
		$iconcolor = $ktemplate->params->get('IconColor');

		if ($iconcolor)
		{
			$styles .= <<<EOF
		.layout#kunena [class*="category"] i,
		.layout#kunena .glyphicon-topic,
		.layout#kunena h3 i,
		.layout#kunena #kwho i.icon-users,
		.layout#kunena#kstats i.icon-bars { color: {$iconcolor}; }
EOF;
		}

		$iconcolornew = $ktemplate->params->get('IconColorNew');

		if ($iconcolornew)
		{
			$styles .= <<<EOF
		.layout#kunena [class*="category"] .knewchar { color: {$iconcolornew} !important; }
		.layout#kunena sup.knewchar { color: {$iconcolornew} !important; }
		.layout#kunena .topic-item-unread { border-left-color: {$iconcolornew} !important;}
		.layout#kunena .topic-item-unread .icon { color: {$iconcolornew} !important;}
		.layout#kunena .topic-item-unread i.fa { color: {$iconcolornew} !important;}
		.layout#kunena .topic-item-unread svg { color: {$iconcolornew} !important;}
EOF;
		}

		$this->addStyleDeclaration($styles);
		parent::initialize();
	}
}
