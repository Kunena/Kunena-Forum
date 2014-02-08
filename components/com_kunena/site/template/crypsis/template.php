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
		'topicicons' => 'media/topicicons',
		'images' => 'media/images',
		'js' => 'media/js',
		'css' => 'media/css'
	);

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

		// Load script and CSS for autocomplete, emojiis...
		$this->addScript('js/atwho.js');
		$this->addStyleSheet('css/atwho.css');

		// Load JavaScript.
		$this->addScript('plugins.js');

		// Compile CSS from LESS files.
		$this->compileLess('main.less', 'kunena.css');
		$this->addStyleSheet('kunena.css');

		$config = KunenaFactory::getConfig();

		// If polls are enabled, load also poll JavaScript.
		if ($config->pollenabled == 1)
		{
			JText::script('COM_KUNENA_POLL_OPTION_NAME');
			JText::script('COM_KUNENA_EDITOR_HELPLINE_OPTION');
			$this->addScript('poll.js');
		}

		// Load FancyBox library if enabled in configuration
		if ($config->lightbox == 1)
		{
			$this->addScript('js/fancybox.js');
			$this->addStyleSheet('css/fancybox.css');
			JFactory::getDocument()->addScriptDeclaration('
				jQuery(document).ready(function() {
					jQuery(".fancybox-button").fancybox({
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

		parent::initialize();
	}

	public function addStyleSheet($filename, $group='forum')
	{
		$filename = $this->getFile($filename, false, '', "media/kunena/cache/{$this->name}/css");
		return JFactory::getDocument()->addStyleSheet(JUri::root(true)."/{$filename}");
	}

	public function getButton($link, $name, $scope, $type, $id = null)
	{
		$types = array('communication'=>'comm', 'user'=>'user', 'moderation'=>'mod', 'permanent'=>'mod');
		$names = array('unfavorite'=>'favorite', 'unsticky'=>'sticky', 'unlock'=>'lock', 'create'=>'newtopic',
				'quickreply'=>'reply', 'quote'=>'quote', 'edit'=>'edit', 'permdelete'=>'delete',
				'flat'=>'layout-flat', 'threaded'=>'layout-threaded', 'indented'=>'layout-indented',
				'list'=>'reply');

		// Need special style for buttons in drop-down list
		$buttonsDropdown = array('reply', 'quote', 'edit', 'delete', 'subscribe', 'unsubscribe', 'unfavorite', 'favorite', 'unsticky', 'sticky', 'unlock', 'lock', 'moderate', 'undelete', 'permdelete', 'flat', 'threaded', 'indented');

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
				<a $id style="" href="{$link}" rel="nofollow" title="{$title}">
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

}
