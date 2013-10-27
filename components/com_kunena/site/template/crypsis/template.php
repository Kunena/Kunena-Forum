<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Template
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Crypsis template.
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

	protected $userClasses = array(
		'kwho-',
		'admin'=>'kwho-admin',
		'globalmod'=>'kwho-globalmoderator',
		'moderator'=>'kwho-moderator',
		'user'=>'kwho-user',
		'guest'=>'kwho-guest',
		'banned'=>'kwho-banned',
		'blocked'=>'kwho-blocked'
	);

	/**
	 * Default category icons.
	 *
	 * @var array
	 */
	public $categoryIcons = array(
		'kreadforum',
		'kunreadforum'
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
		KunenaFactory::loadLanguage('com_kunena.templates', 'site');

		foreach (array_reverse($this->default) as $template)
		{
			$file = "kunena_tmpl_{$template}";
			$lang->load($file, JPATH_SITE)
				|| $lang->load($file, KPATH_SITE)
				|| $lang->load($file, KPATH_SITE . "/template/{$template}");
		}
	}

	/**
	 * Template initialization.
	 *
	 * @return void
	 */
	public function initialize()
	{
		// Template requires Mootools 1.4+ framework.
		$this->loadMootools();
		JHtml::_('behavior.tooltip');
		JHtml::_('bootstrap.modal');

		// Template also requires jQuery framework.
		JHtml::_('jquery.framework');
		JHtml::_('formbehavior.chosen');

		// Load JavaScript.
		$this->addScript ( 'js/plugins.js' );

		// Compile CSS from LESS files.
		$this->compileLess('main.less', 'kunena.css');
		$this->addStyleSheet ( 'css/kunena.css' );

		$config = KunenaFactory::getConfig();

		// If polls are enabled, load also poll JavaScript.
		if ($config->pollenabled == 1)
		{
			JText::script('COM_KUNENA_POLL_OPTION_NAME');
			JText::script('COM_KUNENA_EDITOR_HELPLINE_OPTION');
			$this->addScript( 'js/kunena.poll.js' );
		}

		// If enabled, load also MediaBox advanced.
		if ($config->lightbox == 1)
		{
			// TODO: replace with bootstrap compatible version
			$this->addScript( 'js/mediaboxAdv.js' );
			//$this->addStyleSheet ( 'css/mediaboxAdv.css');
		}

		parent::initialize();
	}

	public function addStyleSheet($filename, $group='forum')
	{
		$filename = $filename = $this->getFile($filename, false, '', "media/kunena/cache/{$this->name}");
		return JFactory::getDocument ()->addStyleSheet ( JUri::root(true)."/{$filename}" );
	}

	public function getButton($link, $name, $scope, $type, $id = null)
	{
		$types = array('communication'=>'comm', 'user'=>'user', 'moderation'=>'mod', 'permanent'=>'mod');
		$names = array('unfavorite'=>'favorite', 'unsticky'=>'sticky', 'unlock'=>'lock', 'create'=>'newtopic',
				'quickreply'=>'reply', 'quote'=>'quote', 'edit'=>'edit', 'permdelete'=>'delete',
				'flat'=>'layout-flat', 'threaded'=>'layout-threaded', 'indented'=>'layout-indented',
				'list'=>'reply');

		// need special style for buttons in drop-down list
		$buttonsDropdown = array('reply', 'quote', 'edit', 'delete', 'unsubscribe', 'unfavorite', 'favorite', 'unsticky', 'sticky', 'unlock', 'lock', 'moderate', 'undelete', 'permdelete' );

		$text = JText::_("COM_KUNENA_BUTTON_{$scope}_{$name}");
		$title = JText::_("COM_KUNENA_BUTTON_{$scope}_{$name}_LONG");

		if ($title == "COM_KUNENA_BUTTON_{$scope}_{$name}_LONG") $title = '';

		if ($id) $id = 'id="'.$id.'"';

		if ( in_array($name,$buttonsDropdown) )
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
				<a $id class="btn" style="" href="{$link}" rel="nofollow" title="{$title}">
				<span class="{$name}"></span>
				{$text}
				</a>
HTML;
		}
	}

	public function getIcon($name, $title='')
	{
		return '<span class="kicon '.$name.'" title="'.$title.'"></span>';
	}

	public function getImage($image, $alt='')
	{
		return '<img src="'.$this->getImagePath($image).'" alt="'.$alt.'" />';
	}

	public function getPaginationListRender($list)
	{
		$html = '<div class="pagination pagination-small" ><ul class="pagination-small">';
		$last = 0;

		foreach($list['pages'] as $i=>$page)
		{
			if ($last+1 != $i) $html .= '<li><a class="disabled">...</a></li>';
			$html .= '<li>'.$page['data'].'</li>';
			$last = $i;
		}

		$html .= '</ul></div><div class="clearfix"></div>';

		return $html;
	}
}
