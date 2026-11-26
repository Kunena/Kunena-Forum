<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Category;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\ToolbarFactoryInterface;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;

/**
 * About view for Kunena backend
 *
 * @since   Kunena 6.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * @var     array|KunenaCategory[]
     * @since   Kunena 6.0
     */
    public mixed $category = [];

    public mixed $options = null;

    /**
     * The users subscribed to the category.
     *
     * @var     array|boolean
     * @since   Kunena 8.0
     */
    public mixed $subscribers = [];

    /**
     * The model state
     *
     * @since  6.0
     */
    protected mixed $state = null;

    /**
     * @return  void
     *
     * @since   Kunena 6.0
     * @throws Exception
     */
    public function displayCreate(): void
    {
        $this->displayEdit();
    }

    /**
     * @return  void
     * @since Kunena 6.0
     *
     *
     * @throws Exception
     */
    public function displayEdit()
    {
        $model          = $this->getModel();
        $this->category = $model->getAdminCategory();

        if (!$this->category) {
            $this->leaveWithoutRights();

            return;
        }

        $this->options     = $model->getAdminOptions();
        $this->moderators  = $model->getAdminModerators();
        $this->subscribers = $model->getAdminSubscribers();
        $this->setToolBarEdit();
        $this->display();
    }

    /**
     * Back to the categories with a message: the model gives no category when the user may not administer it (the page was
     * empty).
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    protected function leaveWithoutRights(): void
    {
        $app = Factory::getApplication();
        $app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
        $app->redirect(Route::_('index.php?option=com_kunena&view=categories', false));
    }

    /**
     * @return  void
     * @since Kunena 6.0
     */
    protected function setToolBarEdit()
    {
        $model          = $this->getModel();
        $this->category = $model->getAdminCategory();

        // Get the toolbar object instance
        $this->bar = Factory::getContainer()->get(ToolbarFactoryInterface::class)->createToolbar('toolbar');

        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_CATEGORY_MANAGER'), 'list-view');
        ToolbarHelper::spacer();
        ToolbarHelper::apply();
        ToolbarHelper::save();
        ToolbarHelper::save2new();

        // If an existing item, can save to a copy.
        if ($this->category->exists()) {
            ToolbarHelper::save2copy();
        }

        ToolbarHelper::cancel();
        ToolbarHelper::spacer();
        $helpUrl = 'https://docs.kunena.org/en/manual/backend/categories/new-section-category';
        ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
    }

    /**
     * @param   string|null  $tpl  tmpl
     *
     * @return  mixed
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function display($tpl = null)
    {
        $model          = $this->getModel();
        $this->category = $model->getAdminCategory();
        $this->state    = $model->getState();

        if (!$this->category) {
            $this->leaveWithoutRights();

            return;
        }

        $this->options     = $model->getAdminOptions();
        $this->moderators  = $model->getAdminModerators();
        $this->subscribers = $model->getAdminSubscribers();

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
    protected function addToolbar(): void
    {
        $model          = $this->getModel();
        $this->category = $model->getAdminCategory();

        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_CATEGORY_MANAGER'), 'list-view');
        ToolbarHelper::spacer();
        ToolbarHelper::apply('category.apply');
        ToolbarHelper::save('category.save');
        ToolbarHelper::save2new('category.save2newcategory');

        // If an existing item, can save to a copy.
        if ($this->category->exists()) {
            ToolbarHelper::save2copy('category.save2copycategory');
        }

        ToolbarHelper::cancel('category.cancel');
        ToolbarHelper::spacer();
        $helpUrl = 'https://docs.kunena.org/en/manual/backend/categories/new-section-category';
        ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
    }
}
