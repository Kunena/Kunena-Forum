<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Tools\KunenaTopicManager;

/**
 * The list of the topics (view "Topics"): the moderation actions on the chosen topics, from the toolbar or a row.
 *
 * @since   Kunena 8.0
 */
class TopicsController extends BaseController
{
    /**
     * Approves the chosen topics which wait for approval.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function approve(): void
    {
        $this->run('approve');
    }

    /**
     * Locks the chosen topics.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function lock(): void
    {
        $this->run('lock');
    }

    /**
     * Unlocks the chosen topics.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function unlock(): void
    {
        $this->run('unlock');
    }

    /**
     * Makes the chosen topics sticky.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function stick(): void
    {
        $this->run('stick');
    }

    /**
     * Makes the chosen topics not sticky.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function unstick(): void
    {
        $this->run('unstick');
    }

    /**
     * Moves the chosen topics to the category of the form.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function move(): void
    {
        $this->run('move');
    }

    /**
     * Moves the chosen topics to the trash.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function trash(): void
    {
        $this->run('trash');
    }

    /**
     * Publishes again the chosen topics which are in the trash.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function restore(): void
    {
        $this->run('restore');
    }

    /**
     * Deletes for good the chosen topics which are in the trash.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function delete(): void
    {
        $this->run('delete');
    }

    /**
     * Applies an action to the chosen topics, then shows the same list again.
     *
     * @param   string  $action  One of KunenaTopicManager::ACTIONS.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    private function run(string $action): void
    {
        $filters = KunenaTopicManager::filters((array) $this->input->post->get('filter', [], 'array'));
        $query   = http_build_query(['filter' => KunenaTopicManager::active($filters)]);

        $this->setRedirect(KunenaRoute::_('administrator/index.php?option=com_kunena&view=topics' . ($query !== '' ? '&' . $query : ''), false));

        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');

            return;
        }

        if (!$this->app->getIdentity()->authorise('core.admin', 'com_kunena')) {
            $this->app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');

            return;
        }

        $ids = ArrayHelper::toInteger((array) $this->input->post->get('cid', [], 'array'));

        if (!$ids) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_TOPICS_NONE_CHOSEN'), 'warning');

            return;
        }

        $result = KunenaTopicManager::apply($ids, $action, $this->input->post->getInt('target', 0));

        $this->app->enqueueMessage(Text::plural('COM_KUNENA_TOOLS_TOPICS_N_' . strtoupper($action), $result['done']), $result['done'] ? 'success' : 'info');

        if ($result['failed']) {
            $this->app->enqueueMessage(Text::plural('COM_KUNENA_TOOLS_TOPICS_N_FAILED', $result['failed']), 'warning');
        }
    }
}
