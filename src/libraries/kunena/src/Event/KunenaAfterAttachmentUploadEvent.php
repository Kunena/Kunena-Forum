<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Event
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Event;

\defined('_JEXEC') or die;

use BadMethodCallException;
use Joomla\CMS\Event\AbstractImmutableEvent;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;

/**
 * Class for onKunenaAfterAttachmentUpload event, dispatched once a file uploaded through the editor is stored and saved.
 * Example:
 *  new KunenaAfterAttachmentUploadEvent('onKunenaAfterAttachmentUpload', ['subject' => KunenaAttachment]);
 *
 * @since  Kunena 8.0
 */
class KunenaAfterAttachmentUploadEvent extends AbstractImmutableEvent
{
    /**
     * Constructor.
     *
     * @param   string  $name       The event name.
     * @param   array   $arguments  The event arguments.
     *
     * @since   Kunena 8.0
     * @throws  BadMethodCallException
     */
    public function __construct(string $name, array $arguments = [])
    {
        if (!\array_key_exists('subject', $arguments) || !$arguments['subject'] instanceof KunenaAttachment) {
            throw new BadMethodCallException("Argument 'subject' of event {$name} is required and must be a KunenaAttachment");
        }

        parent::__construct($name, $arguments);
    }

    /**
     * Getter for the subject argument: the saved attachment.
     *
     * @return  KunenaAttachment
     * @since   Kunena 8.0
     */
    public function getAttachment(): KunenaAttachment
    {
        return $this->arguments['subject'];
    }
}
