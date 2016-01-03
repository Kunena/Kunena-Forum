<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Template.Crypsis
 * @subpackage  Template
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
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
	 */
	protected $default = array('crypsis');

	/**
	 * Relative paths to various file types in this template.
	 *
	 * These will override default files in JROOT/media/kunena
	 *
	 * @var array
	 */
	protected $pathTypes = array(
		'emoticons' => 'media/emoticons',
		'ranks' => 'media/ranks',
		'icons' => 'media/icons',
		'categoryicons' => 'media/category_icons',
		'images' => 'media/images',
		'js' => 'media/js',
		'css' => 'media/css'
	);

	/**
	 * User group initialization.
	 *
	 * @return void
	 */
	protected $userClasses = array(
		'kwho-',
		'admin' => 'kwho-admin',
		'globalmod' => 'kwho-globalmoderator',
		'moderator' => 'kwho-moderator',
		'user' => 'kwho-user',
		'guest' => 'kwho-guest',
		'banned' => 'kwho-banned',
		'blocked' => 'kwho-blocked'
	);

	/**
	 * Logic to load language strings for the template.
	 *
	 * By default language files are also loaded from the parent templates.
	 *
	 * @return void
	 */
	public function loadLanguage()
	{
		$lang = JFactory::getLanguage();
		KunenaFactory::loadLanguage('kunena_tmpl_crypsis');

		foreach (array_reverse($this->default) as $template)
		{
			$file = "kunena_tmpl_{$template}";
			$lang->load($file, JPATH_SITE) || $lang->load($file, KPATH_SITE) || $lang->load($file, KPATH_SITE . "/template/{$template}");
		}
	}

	/**
	 * Template initialization.
	 *
	 * @return void
	 */
	public function initialize()
	{
		// Template requires Bootstrap javascript
		JHtml::_('bootstrap.framework');
		JHtml::_('bootstrap.tooltip', '[data-toggle="tooltip"]');

		// Template also requires jQuery framework.
		JHtml::_('jquery.framework');
		JHtml::_('bootstrap.modal');

		// Load JavaScript.
		$this->addScript('main.js');

		// Compile CSS from LESS files.
		$this->compileLess('crypsis.less', 'kunena.css');
		$this->addStyleSheet('kunena.css');

		$filename = JPATH_SITE . '/components/com_kunena/template/crypsis/css/custom.css';
		if (file_exists($filename))
		{
			$this->addStyleSheet ( 'custom.css' );
		}

		$this->ktemplate = KunenaFactory::getTemplate();
		$fontawesome = $this->ktemplate->params->get('fontawesome');
		if ($fontawesome) : ?>
			<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		<?php endif;

		// Load template colors settings
		$styles = <<<EOF
		/* Kunena Custom CSS */
EOF;
		$iconcolor = $this->ktemplate->params->get('IconColor');
		if ($iconcolor) {
			$styles .= <<<EOF
		.layout#kunena [class*="category"] i,
		.layout#kunena .glyphicon-topic,
		.layout#kunena h3 i,
		.layout#kunena #kwho i.icon-users,
		.layout#kunena#kstats i.icon-bars { color: {$iconcolor}; }
EOF;
		}

		$iconcolornew = $this->ktemplate->params->get('IconColorNew');
		if ($iconcolornew) {
			$styles .= <<<EOF
		.layout#kunena [class*="category"] .icon-knewchar { color: {$iconcolornew} !important; }
		.layout#kunena sup.knewchar { color: {$iconcolornew} !important; }
		.layout#kunena .topic-item-unread { border-left-color: {$iconcolornew} !important;}
		.layout#kunena .topic-item-unread .icon { color: {$iconcolornew} !important;}
		.layout#kunena .topic-item-unread i.fa { color: {$iconcolornew} !important;}
EOF;
		}

		$document = JFactory::getDocument();
		$document->addStyleDeclaration($styles);

		parent::initialize();
	}

	/**
	 * @param        $filename
	 * @param string $group
	 *
	 * @return JDocument
	 */
	public function addStyleSheet($filename, $group = 'forum')
	{
		$filename = $this->getFile($filename, false, '', "media/kunena/cache/{$this->name}/css");

		return JFactory::getDocument()->addStyleSheet(JUri::root(true) . "/{$filename}");
	}

	/**
	 * @param      $link
	 * @param      $name
	 * @param      $scope
	 * @param      $type
	 * @param null $id
	 *
	 * @return string
	 */
	public function getButton($link, $name, $scope, $type, $id = null)
	{
		$types = array('communication' => 'comm', 'user' => 'user', 'moderation' => 'mod', 'permanent' => 'mod');
		$names = array('unfavorite' => 'favorite', 'unsticky' => 'sticky', 'unlock' => 'lock', 'create' => 'newtopic', 'quickreply' => 'reply', 'quote' => 'quote', 'edit' => 'edit', 'permdelete' => 'delete', 'flat' => 'layout-flat', 'threaded' => 'layout-threaded', 'indented' => 'layout-indented', 'list' => 'reply');

		// Need special style for buttons in drop-down list
		$buttonsDropdown = array('reply', 'quote', 'edit', 'delete', 'subscribe', 'unsubscribe', 'unfavorite', 'favorite', 'unsticky', 'sticky', 'unlock', 'lock', 'moderate', 'undelete', 'permdelete', 'flat', 'threaded', 'indented');

		$text  = JText::_("COM_KUNENA_BUTTON_{$scope}_{$name}");
		$title = JText::_("COM_KUNENA_BUTTON_{$scope}_{$name}_LONG");

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
				<a $id style="" href="{$link}" rel="nofollow" title="{$title}">
				{$text}
				</a>
HTML;
		}
		else
		{
			return <<<HTML
				<a $id style="" href="{$link}" rel="nofollow" title="{$title}">
				<span class="{$name}"></span>
				{$text}
				</a>
HTML;
		}
	}

	/**
	 * @param        $name
	 * @param string $title
	 *
	 * @return string
	 */
	public function getIcon($name, $title = '')
	{
		return '<span class="kicon ' . $name . '" title="' . $title . '"></span>';
	}

	/**
	 * @param        $image
	 * @param string $alt
	 *
	 * @return string
	 */
	public function getImage($image, $alt = '')
	{
		return '<img src="' . $this->getImagePath($image) . '" alt="' . $alt . '" />';
	}
}
