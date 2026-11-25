<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Topics;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Administrator\Helper\ConfigHelper;
use Kunena\Forum\Libraries\Tools\KunenaCategoryTransfer;
use Kunena\Forum\Libraries\Tools\KunenaTopicManager;

/**
 * All the topics of the forum in one list: filtered, changed in bulk, each one opened for an edit.
 *
 * @since   Kunena 8.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The actions of the toolbar and their icons (the move has its own row under the list: it needs a category).
     *
     * @since   Kunena 8.0
     */
    public const ACTION_ICONS = [
        'approve' => 'check',
        'lock'    => 'lock',
        'unlock'  => 'unlock',
        'stick'   => 'thumbtack',
        'unstick' => 'circle',
        'trash'   => 'trash',
        'restore' => 'undo-2',
    ];

    /**
     * The filters of the list (KunenaTopicManager::filters()).
     *
     * @since   Kunena 8.0
     */
    public array $filters = [];

    /**
     * The categories, in the order of the tree.
     *
     * @since   Kunena 8.0
     */
    public array $categories = [];

    /**
     * The topics found: total and items of the page.
     *
     * @since   Kunena 8.0
     */
    public array $found = ['total' => 0, 'items' => []];

    /**
     * May the user change the topics (core.admin, as the tools)?
     *
     * @since   Kunena 8.0
     */
    public bool $canAdmin = false;

    /**
     * @param   string|null  $tpl  The template.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function display($tpl = null): void
    {
        $this->filters    = KunenaTopicManager::filters((array) Factory::getApplication()->getInput()->get('filter', [], 'array'));
        $this->categories = KunenaCategoryTransfer::sortTree(KunenaCategoryTransfer::rowsOnSite());
        $this->found      = KunenaTopicManager::findOnSite($this->filters, time());

        // The page which is shown (a page after the last one shows the last one): the actions and the edit come back to it.
        $this->filters['page'] = $this->found['page'];
        $this->canAdmin        = $this->getCurrentUser()->authorise('core.admin', 'com_kunena');

        $this->getDocument()->setTitle(Text::_('COM_KUNENA_TOPIC_MANAGER'));
        $this->addToolbar();
        self::useScript($this);

        // The check boxes and the actions of a row, as the lists of Joomla (Joomla.listItemTask).
        Text::script('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
        $this->getDocument()->getWebAssetManager()->useScript('list-view')->useScript('multiselect');

        parent::display($tpl);
    }

    /**
     * The script of the tools: the confirmations and the "select all" boxes. A full URL: the asset manager would look for a
     * relative one in the media folder.
     *
     * @param   BaseHtmlView  $view  The view.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function useScript(BaseHtmlView $view): void
    {
        Text::script('COM_KUNENA_TOOLS_CONFIRM');
        $view->getDocument()->getWebAssetManager()->registerAndUseScript(
            'kunena_admin_tools',
            Uri::base() . 'components/com_kunena/media/js/kunena-tools.js',
            ['version' => 'auto'],
            ['defer' => true],
            ['core']
        );
    }

    /**
     * @return  void
     *
     * @since   Kunena 8.0
     */
    protected function addToolbar(): void
    {
        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TOPIC_MANAGER'), 'list');

        // The moderation of the chosen topics, as the lists of Joomla: one menu "Actions".
        if ($this->canAdmin && $this->found['items']) {
            $toolbar  = $this->getDocument()->getToolbar();
            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('icon-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);
            $child = $dropdown->getChildToolbar();

            foreach (self::ACTION_ICONS as $action => $icon) {
                $child->standardButton($action, 'COM_KUNENA_TOOLS_TOPICS_ACTION_' . strtoupper($action), 'topics.' . $action)
                    ->icon('icon-' . $icon)
                    ->listCheck(true);
            }

            // For good: only in the list of the trash, where it is expected.
            if ($this->filters['state'] === 'trashed') {
                $toolbar->delete('topics.delete', 'COM_KUNENA_TOOLS_TOPICS_ACTION_DELETE')
                    ->message('COM_KUNENA_TOPICS_DELETE_CONFIRM')
                    ->listCheck(true);
            }
        }

        $canDo = ContentHelper::getActions('com_kunena');

        if ($canDo->get('core.admin') || $canDo->get('core.options')) {
            ToolbarHelper::link(ConfigHelper::link(), 'JTOOLBAR_OPTIONS', 'options');
        }

        ToolbarHelper::help('COM_KUNENA', false, 'https://docs.kunena.org/en/');
    }
}
