<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsis
 * @subpackage      Template
 *
 * @copyright       Copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
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
		JHtml::_('bootstrap.framework');
		JHtml::_('bootstrap.tooltip', '[data-toggle="tooltip"]');

		// Template also requires jQuery framework.
		JHtml::_('jquery.framework');

		if (version_compare(JVERSION, '4.0', '>'))
		{
			JHtml::_('bootstrap.renderModal');
		}
		else
		{
			JHtml::_('bootstrap.modal');
		}

		// Load JavaScript.
		$this->addScript('assets/js/main.js');

		$this->ktemplate = KunenaFactory::getTemplate();
		$storage         = $this->ktemplate->params->get('storage');

		if ($storage)
		{
			$this->addScript('assets/js/localstorage.js');
		}

		// Compile CSS from LESS files.
		$this->compileLess('assets/less/crypsis.less', 'kunena.css');
		$this->addStyleSheet('kunena.css');

		$filenameless = JPATH_SITE . '/components/com_kunena/template/crypsis/assets/less/custom.less';

		if (file_exists($filenameless) && 0 != filesize($filenameless))
		{
			$this->compileLess('assets/less/custom.less', 'kunena-custom.css');
			$this->addStyleSheet('kunena-custom.css');
		}

		$filename = JPATH_SITE . '/components/com_kunena/template/crypsis/assets/css/custom.css';

		if (file_exists($filename))
		{
			$this->addStyleSheet('assets/css/custom.css');
		}

		$bootstrap = $this->ktemplate->params->get('bootstrap');
		$doc       = \Joomla\CMS\Factory::getDocument();

		if ($bootstrap)
		{
			/** @noinspection PhpDeprecationInspection */
			$doc->addStyleSheet(\Joomla\CMS\Uri\Uri::base(true) . '/media/jui/css/bootstrap.min.css');
			/** @noinspection PhpDeprecationInspection */
			$doc->addStyleSheet(\Joomla\CMS\Uri\Uri::base(true) . '/media/jui/css/bootstrap-extended.css');
			/** @noinspection PhpDeprecationInspection */
			$doc->addStyleSheet(\Joomla\CMS\Uri\Uri::base(true) . '/media/jui/css/bootstrap-responsive.min.css');

			if ($this->ktemplate->params->get('icomoon'))
			{
				/** @noinspection PhpDeprecationInspection */
				$doc->addStyleSheet(\Joomla\CMS\Uri\Uri::base(true) . '/media/jui/css/icomoon.css');
			}
		}

		$fontawesome = $this->ktemplate->params->get('fontawesome');

		if ($fontawesome)
		{
			/** @noinspection PhpDeprecationInspection */
			$doc->addScript('https://use.fontawesome.com/releases/v5.0.6/js/all.js', array(), array('defer' => true));
		}

		// Load template colors settings
		$styles    = <<<EOF
		/* Kunena Custom CSS */
EOF;
		$iconcolor = $this->ktemplate->params->get('IconColor');

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

		$iconcolornew = $this->ktemplate->params->get('IconColorNew');

		if ($iconcolornew)
		{
			$styles .= <<<EOF
		.layout#kunena [class*="category"] .knewchar { color: {$iconcolornew} !important; }
		.layout#kunena sup.knewchar { color: {$iconcolornew} !important; }
		.layout#kunena .topic-item-unread { border-left-color: {$iconcolornew} !important;}
		.layout#kunena .topic-item-unread .icon { color: {$iconcolornew} !important;}
		.layout#kunena .topic-item-unread i.fa { color: {$iconcolornew} !important;}
EOF;
		}

		$document = \Joomla\CMS\Factory::getDocument();
		$document->addStyleDeclaration($styles);

		parent::initialize();
	}

	/**
	 * @param          $filename
	 * @param   string $group group
	 *
	 * @return \Joomla\CMS\Document\Document
	 * @since Kunena
	 */
	public function addStyleSheet($filename, $group = 'forum')
	{
		$filename = $this->getFile($filename, false, '', "media/kunena/cache/{$this->name}/css");

		/** @noinspection PhpDeprecationInspection */
		return \Joomla\CMS\Factory::getDocument()->addStyleSheet(\Joomla\CMS\Uri\Uri::root(true) . "/{$filename}");
	}
}
