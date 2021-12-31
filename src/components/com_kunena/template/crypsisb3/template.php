<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Template
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

/**
 * Crypsis template.
 *
 * @since  K4.0
 */
class KunenaTemplateCrypsisb3 extends KunenaTemplate
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
	 * User group initialization.
	 *
	 * @return void
	 * @since Kunena
	 */
	protected $userClasses = array(
		'kwho-',
		'admin'     => 'kwho-admin',
		'globalmod' => 'kwho-globalmoderator',
		'moderator' => 'kwho-moderator',
		'user'      => 'kwho-user',
		'guest'     => 'kwho-guest',
		'banned'    => 'kwho-banned',
		'blocked'   => 'kwho-blocked',
	);

	/**
	 * Logic to load language strings for the template.
	 *
	 * By default language files are also loaded from the parent templates.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function loadLanguage()
	{
		$lang = Factory::getLanguage();
		KunenaFactory::loadLanguage('kunena_tmpl_crypsis');

		foreach (array_reverse($this->default) as $template)
		{
			$file = "kunena_tmpl_crypsis";
			$lang->load($file, JPATH_SITE) || $lang->load($file, KPATH_SITE) || $lang->load($file, KPATH_SITE . "/template/{$template}");
		}
	}

	/**
	 * Template initialization.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function initialize()
	{
		HTMLHelper::_('bootstrap.framework');
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

		$this->addScript('assets/js/main.js');

		// Compile CSS from LESS files.
		$this->compileLess('assets/less/crypsisb3.less', 'kunena.css');
		$this->addLessSheet('kunena.css');

		$this->ktemplate = KunenaFactory::getTemplate();
		$storage         = $this->ktemplate->params->get('storage');

		if ($storage)
		{
			$this->addScript('localstorage.js');
		}

		$filename = JPATH_SITE . '/components/com_kunena/template/crypsisb3/assets/css/custom.css';

		if (file_exists($filename) && filesize($filename) != 0)
		{
			$this->addLessSheet('assets/css/custom.css');
		}

		$this->loadFontawesome();

		$icons = $this->ktemplate->params->get('icons');

		if ($icons)
		{
			$this->addStyleSheet("https://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css");
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
		.layout#kunena .topic-item-unread .glyphicon { color: {$iconcolornew} !important;}
		.layout#kunena .topic-item-unread i.fa { color: {$iconcolornew} !important;}
		.layout#kunena .topic-item-unread svg { color: {$iconcolornew} !important;}
EOF;
		}

		$this->addStyleDeclaration($styles);

		parent::initialize();
	}

	/**
	 * @param        $link
	 * @param        $name
	 * @param        $scope
	 * @param        $type
	 * @param   null $id id
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getButton($link, $name, $scope, $type, $id = null)
	{
		$types = array('communication' => 'comm', 'user' => 'user', 'moderation' => 'mod', 'permanent' => 'mod');
		$names = array('unfavorite' => 'favorite', 'unsticky' => 'sticky', 'unlock' => 'lock', 'create' => 'newtopic', 'quickreply' => 'reply', 'quote' => 'quote', 'edit' => 'edit', 'permdelete' => 'delete', 'flat' => 'layout-flat', 'threaded' => 'layout-threaded', 'indented' => 'layout-indented', 'list' => 'reply');

		// Need special style for buttons in drop-down list
		$buttonsDropdown = array('reply', 'quote', 'edit', 'delete', 'subscribe', 'unsubscribe', 'unfavorite', 'favorite', 'unsticky', 'sticky', 'unlock', 'lock', 'moderate', 'undelete', 'permdelete', 'flat', 'threaded', 'indented');

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
				<a {$id} style="" href="{$link}" rel="nofollow" title="{$title}">
				{$text}
				</a>
HTML;
		}
		else
		{
			return <<<HTML
				<a {$id} style="" href="{$link}" rel="nofollow" title="{$title}">
				<span class="{$name}"></span>
				{$text}
				</a>
HTML;
		}
	}

	/**
	 * @param          $name
	 * @param   string $title title
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getIcon($name, $title = '')
	{
		return '<span class="kicon ' . $name . '" title="' . $title . '"></span>';
	}

	/**
	 * @param          $image
	 * @param   string $alt alt
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getImage($image, $alt = '')
	{
		return '<img src="' . $this->getImagePath($image) . '" alt="' . $alt . '" />';
	}
}
