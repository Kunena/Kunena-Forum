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
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/**
 * Kunena Attachments Controller
 *
 * @since   Kunena 2.0
 */
class AttachmentsController extends FormController
{
    /**
     * @var     null|string
     * @since   Kunena 2.0.0-BETA2
     */
    protected $baseurl = null;

    /**
     * Constructor
     *
     * @param   array  $config  Construct
     *
     * @throws  Exception
     * @since   Kunena 2.0
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->baseurl = 'administrator/index.php?option=com_kunena&view=attachments';
    }

    /**
     * Delete
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 2.0
     */
    public function delete(): void
    {
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $cid = $this->input->get('cid', [], 'array');
        $cid = ArrayHelper::toInteger($cid, []);

        if (!$cid) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ATTACHMENTS_SELECTED'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        foreach ($cid as $id) {
            $attachment = KunenaAttachmentHelper::get($id);

            // An attachment which is not there is nothing to delete.
            if (!$attachment->exists()) {
                continue;
            }

            $message = $attachment->getMessage();

            // An attachment which was uploaded and belongs to no message: only the attachment goes.
            if (!$message->exists()) {
                $attachment->delete();

                continue;
            }

            // The chosen attachment: the message deletes it and counts it off the topic on save. (The list was [0, 1], the keys
            // of an array: the attachment 1 of the message went too.)
            $message->removeAttachments([$attachment->id]);

            $messageText = $attachment->removeBBCodeInMessage($message->message);

            if (!empty($messageText) && $attachment->inline) {
                $message->message = $messageText;
            }

            $message->save();
        }

        $this->app->enqueueMessage(Text::_('COM_KUNENA_ATTACHMENTS_DELETED_SUCCESSFULLY'), 'success');
        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }
}
