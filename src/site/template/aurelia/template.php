<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Template
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Template\KunenaTemplate;

/**
 * Aurelia template.
 *
 * @since   Kunena 4.0
 */
class KunenaTemplateAurelia extends KunenaTemplate
{
	/**
	 * List of parent template names.
	 *
	 * This template will automatically search for missing files from listed parent templates.
	 * The feature allows you to create one base template and only override changed files.
	 *
	 * @var array
	 * @since   Kunena 6.0
	 */
	protected $default = ['Aurelia'];

	/**
	 * Relative paths to various file types in this template.
	 *
	 * These will override default files in JROOT/media/kunena
	 *
	 * @var array
	 * @since   Kunena 6.0
	 */
	protected $pathTypes = [
		'emoticons'     => 'media/kunena/emoticons',
		'ranks'         => 'media/kunena/ranks',
		'icons'         => 'media/kunena/icons',
		'categoryIcons' => 'media/kunena/category_icons',
		'images'        => 'media/kunena/core/images',
		'js'            => 'media/kunena/core/js',
		'css'           => 'media/kunena/core/css',
	];

	/**
	 * User group initialization.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected $userClasses = [
		'kwho-',
		'admin'     => 'kwho-admin',
		'globalmod' => 'kwho-globalmoderator',
		'moderator' => 'kwho-moderator',
		'user'      => 'kwho-user',
		'guest'     => 'kwho-guest',
		'banned'    => 'kwho-banned',
		'blocked'   => 'kwho-blocked',
	];

	/**
	 * Logic to load language strings for the template.
	 *
	 * By default language files are also loaded from the parent templates.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function loadLanguage(): void
	{
		$lang = Factory::getApplication()->getLanguage();
		KunenaFactory::loadLanguage('kunena_tmpl_aurelia');

		foreach (array_reverse($this->default) as $template)
		{
			$file = "kunena_tmpl_aurelia";
			$lang->load($file, JPATH_SITE) || $lang->load($file, KPATH_SITE) || $lang->load($file, KPATH_SITE . "/template/{$template}");
		}
	}

	/**
	 * Template initialization.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 * @throws \ScssPhp\ScssPhp\Exception\SassException
	 */
	public function initialize(): void
	{
		$bootstrap = $this->params->get('bootstrap');

		if ($bootstrap)
		{
            HTMLHelper::_('bootstrap.loadCss');
            HTMLHelper::_('bootstrap.dropdown');
			HTMLHelper::_('bootstrap.tooltip');
			HTMLHelper::_('bootstrap.renderModal');
			HTMLHelper::_('bootstrap.collapse');
			HTMLHelper::_('bootstrap.offcanvas');
			HTMLHelper::_('bootstrap.alert');
		}

		$doc = Factory::getApplication()->getDocument();

		/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
		$wa = $doc->getWebAssetManager();
		$wa->useScript('jquery');

		$this->addScript('assets/js/main.js');
		$this->addScript('assets/js/tooltips.js');

		if ($bootstrap)
		{
            $this->addScript('assets/js/offcanvas.js');
        }

		// Compile CSS from SCSS files.
		$this->compileScss('assets/scss/aurelia.scss', 'kunena.css');
		$this->addStyleSheet('kunena.css');

		$storage = $this->params->get('localstorage');

		if ($storage)
		{
			$this->addScript('localstorage.js');
		}

		$filenamescss = JPATH_SITE . '/components/com_kunena/template/aurelia/assets/scss/custom.scss';

		if (file_exists($filenamescss) && 0 != fileSize($filenamescss))
		{
			$this->compileScss('assets/scss/custom.scss', 'kunena-custom.css');
			$this->addStyleSheet('kunena-custom.css');
		}

		$filename = KPATH_MEDIA . '/core/css/custom.css';

		if (file_exists($filename))
		{
			$this->addStyleSheet('custom.css');
		}

		$this->loadFontawesome();

		// Load template colors settings
		$styles    = <<<EOF
		/* Kunena Custom CSS */
EOF;
		$iconcolor = $this->params->get('IconColor');

		if ($iconcolor)
		{
			$styles .= <<<EOF
		.layout#kunena [class*="category"] i,
		.layout#kunena .glyphicon-topic,
		.layout#kunena #kwho i.icon-users,
		.layout#kunena#kstats i.icon-bars { color: {$iconcolor}; }
EOF;
		}

		$iconcolornew = $this->params->get('IconColorNew');

		if ($iconcolornew)
		{
			$styles .= <<<EOF
		.layout#kunena [class*="category"] .knewchar { color: {$iconcolornew} !important; }
		.layout#kunena sup.knewchar { color: {$iconcolornew} !important; }
		.layout#kunena .topic-item-unread { border-left-color: {$iconcolornew} !important;}
		.layout#kunena .topic-item-unread .glyphicon { color: {$iconcolornew} !important;}
		.layout#kunena .topic-item-unread i.fa { color: {$iconcolornew} !important;}
		.layout#kunena .topic-item-unread svg { color: {$iconcolornew} !important;}
EOF;
		}

		$doc->addStyleDeclaration($styles);

		$this->addScriptOptions('com_kunena.tooltips', $this->params->get('tooltips'));

		parent::initialize();
	}

	/**
	 * @param         $link
	 * @param         $name
	 * @param         $scope
	 * @param         $type
	 * @param   null  $id  id
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getButton($link, $name, $scope, $type, $id = null): string
	{
		// Need special style for buttons in drop-down list
		$buttonsDropdown = ['reply', 'quote', 'edit', 'delete', 'subscribe', 'unsubscribe', 'unfavorite', 'favorite', 'unsticky', 'sticky', 'unlock', 'lock', 'moderate', 'undelete', 'permdelete', 'flat', 'threaded', 'indented'];

		$text  = Text::_("COM_KUNENA_BUTTON_{$scope}_{$name}");
		$title = Text::_("COM_KUNENA_BUTTON_{$scope}_{$name}_LONG");

		if ($title == "COM_KUNENA_BUTTON_{$scope}_{$name}_LONG")
		{
			$title = '';
		}

		if ($id)
		{
			$id = 'id="' . $id . '"';
		}

		if (in_array($name, $buttonsDropdown))
		{
			return <<<HTML
				<a {$id} style="" href="{$link}" rel="nofollow" data-bs-toggle="tooltip" title="{$title}">
				{$text}
				</a>
HTML;
		}
		else
		{
			return <<<HTML
				<a {$id} style="" href="{$link}" rel="nofollow" data-bs-toggle="tooltip" title="{$title}">
				<span class="{$name}"></span>
				{$text}
				</a>
HTML;
		}
	}

	/**
	 * @param           $name
	 * @param   string  $title  title
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getIcon($name, $title = ''): string
	{
		return '<span class="kicon ' . $name . '" data-bs-toggle="tooltip" title="' . $title . '"></span>';
	}

	/**
	 * @param           $image
	 * @param   string  $alt  alt
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getImage($image, $alt = ''): string
	{
		return '<img loading=lazy src="' . $this->getImagePath($image) . '" alt="' . $alt . '" />';
	}
}
