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
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Tools\KunenaTopicEditor;
use Kunena\Forum\Libraries\Tools\KunenaTopicManager;

/**
 * One topic edited (view "Topic", layout "edit"): its settings and the text of its messages.
 *
 * @since   Kunena 8.0
 */
class TopicController extends BaseController
{
    /**
     * Saves the topic and shows it again.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function apply(): void
    {
        $this->store(false);
    }

    /**
     * Saves the topic and goes back to the list.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function save(): void
    {
        $this->store(true);
    }

    /**
     * Back to the list, without saving.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public function cancel(): void
    {
        $this->setRedirect(KunenaRoute::_('administrator/index.php?option=com_kunena&view=topics' . $this->returnQuery(), false));
    }

    /**
     * Saves the topic of the form.
     *
     * @param   bool  $close  Go back to the list (else the same page of the topic again).
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    private function store(bool $close): void
    {
        $id   = $this->input->post->getInt('id', 0);
        $page = max(1, $this->input->post->getInt('page', 1));
        $edit = 'administrator/index.php?option=com_kunena&view=topic&layout=edit&id=' . $id . ($page > 1 ? '&page=' . $page : '') . $this->returnQuery();

        $this->setRedirect(KunenaRoute::_($edit, false));

        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');

            return;
        }

        if (!$this->app->getIdentity()->authorise('core.admin', 'com_kunena')) {
            $this->app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');

            return;
        }

        $fields = (array) $this->input->post->get('topic', [], 'array');
        // The text of a message is BBCode: kept as it is typed (it is escaped when it is shown, as on the site).
        $posted = (array) $this->input->post->get('messages', [], 'raw');
        $result = KunenaTopicEditor::saveOnSite($id, $fields, $posted, $this->input->post->getString('reason', ''));

        if (!$result['topic'] && !$result['saved']) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_EDIT_ERROR'), 'error');

            return;
        }

        $this->app->enqueueMessage(
            Text::_('COM_KUNENA_TOPIC_EDIT_SAVED') . ($result['saved'] ? ' ' . Text::plural('COM_KUNENA_TOPIC_EDIT_N_SAVED', $result['saved']) : ''),
            'success'
        );

        if ($result['empty']) {
            $this->app->enqueueMessage(Text::plural('COM_KUNENA_TOPIC_EDIT_N_EMPTY', $result['empty']), 'warning');
        }

        if ($result['failed']) {
            $this->app->enqueueMessage(Text::plural('COM_KUNENA_TOPIC_EDIT_N_FAILED', $result['failed']), 'warning');
        }

        if ($result['moved'] === false) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_EDIT_NOT_MOVED'), 'warning');
        }

        if ($close) {
            $this->cancel();
        }
    }

    /**
     * The filters of the list to go back to, from the form.
     *
     * @return  string  "&filter[...]=..." or empty.
     *
     * @since   Kunena 8.0
     */
    private function returnQuery(): string
    {
        $filter = (array) $this->input->get('filter', [], 'array');
        $query  = http_build_query(['filter' => KunenaTopicManager::active(KunenaTopicManager::filters($filter))]);

        return $query !== '' ? '&' . $query : '';
    }
}
