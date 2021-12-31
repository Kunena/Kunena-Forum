<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Template;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Kunena\Forum\Libraries\Template\KunenaTemplate;

/**
 * Template view for Kunena backend
 *
 * @since  K6.0
 */
class HtmlView extends BaseHtmlView
{
	public $templatename;

	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function display($tpl = null)
	{
		$app = Factory::getApplication();

		if ($this->getLayout() == "chooseCss")
		{
			$this->setToolBarChooseCss();
			$this->templatename = $app->getUserState('kunena.templatename');

			$file = KPATH_SITE . '/template/' . $this->templatename . '/assets/css/custom.css';

			if (!file_exists($file) && Folder::exists(KPATH_SITE . '/template/' . $this->templatename . '/assets/css/'))
			{
				if (!Folder::exists(KPATH_SITE . '/template/' . $this->templatename . '/assets/css/'))
				{
					Folder::create(KPATH_SITE . '/template/' . $this->templatename . '/assets/css/');
				}

				$fp = fopen($file, "w");
				fwrite($fp, "");
				fclose($fp);
			}

			$this->dir   = KPATH_SITE . '/template/' . $this->templatename . '/assets/css';
			$this->files = Folder::files($this->dir, '\.css$', false, false);

			return parent::display($tpl);
		}
		elseif ($this->getLayout() == "ChooseScss")
		{
			$this->setToolBarChooseScss();
			$this->templatename = $app->getUserState('kunena.templatename');

			$file = KPATH_SITE . '/template/' . $this->templatename . '/assets/scss/custom.scss';

			if (!file_exists($file) && Folder::exists(KPATH_SITE . '/template/' . $this->templatename . '/assets/scss/'))
			{
				$fp = fopen($file, "w");
				fwrite($fp, "");
				fclose($fp);
			}

			$this->dir   = KPATH_SITE . '/template/' . $this->templatename . '/assets/scss';
			$this->files = Folder::files($this->dir, '\.scss$', false, false);

			return parent::display($tpl);
		}
		else
		{
			$this->form         = $this->get('Form');
			$this->params       = $this->get('editparams');
			$this->details      = $this->get('templatedetails');
			$this->templatename = Factory::getApplication()->getUserState('kunena.edit.templatename');
			$template           = KunenaTemplate::getInstance($this->templatename);
			$template->initializeBackend();

			$this->templateFile = KPATH_SITE . '/template/' . $this->templatename . '/config/params.ini';

			if (!file_exists($this->templateFile) && Folder::exists(KPATH_SITE . '/template/' . $this->templatename . '/config/'))
			{
				$ourFileHandle = fopen($this->templateFile, 'w');

				if ($ourFileHandle)
				{
					fclose($ourFileHandle);
				}
			}

			$this->addToolbar();

			return parent::display($tpl);
		}
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarChooseCss(): void
	{
		ToolbarHelper::spacer();
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		ToolbarHelper::custom('editCss', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS');
		ToolbarHelper::spacer();
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarChooseScss(): void
	{
		ToolbarHelper::spacer();
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		ToolbarHelper::custom('editscss', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITSCSS');
		ToolbarHelper::spacer();
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function addToolbar(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TEMPLATE_MANAGER'), 'color-palette');
		ToolbarHelper::spacer();
		ToolbarHelper::apply('template.applychanges');
		ToolbarHelper::spacer();
		ToolbarHelper::save('template.save');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('template.restore', 'checkin.png', 'checkin_f2.png', 'COM_KUNENA_TRASH_RESTORE_TEMPLATE_SETTINGS', false);
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/templates/edit-template-settings';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}
}
