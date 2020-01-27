<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Category;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\ToolbarHelper;
use KunenaForumCategory;

/**
 * About view for Kunena backend
 *
 * @since   Kunena 6.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @var     array|KunenaForumCategory[]
	 * @since   Kunena 6.0
	 */
	public $category = [];

	/**
	 * The model state
	 *
	 * @var    CMSObject
	 * @since  6.0
	 */
	protected $state;

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function displayCreate()
	{
		$this->displayEdit();
	}

	/**
	 * @param   null  $tpl  tmpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->category = $this->get('AdminCategory');
		$this->state    = $this->get('State');

		// FIXME: better access control and gracefully handle no rights
		// Prevent fatal error if no rights:
		if (!$this->category)
		{
			return;
		}

		$this->options    = $this->get('AdminOptions');
		$this->moderators = $this->get('AdminModerators');

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function addToolbar()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_CATEGORY_MANAGER'), 'list-view');
		ToolbarHelper::spacer();
		ToolbarHelper::apply('category.apply');
		ToolbarHelper::save('category.save');
		ToolbarHelper::save2new('category.save2new');

		// If an existing item, can save to a copy.
		if ($this->category->exists())
		{
			ToolbarHelper::save2copy('category.save2copy');
		}

		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/categories/new-section-category';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}
}
