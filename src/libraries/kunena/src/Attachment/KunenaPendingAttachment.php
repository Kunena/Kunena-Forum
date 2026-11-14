<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Attachment
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Attachment;

\defined('_JEXEC') or die();

/**
 * Which pending attachments the scheduled clean-up, and Cancel in the posting form, may remove.
 *
 * A file that is uploaded in the editor is pending (status 0, no message) until the message is posted. The task removed every pending
 * attachment at every run, also the one of a member who was still writing the message. The table has no date, so the age is that
 * of the file on the disk: only an upload that is older than the grace period is removed.
 *
 * Cancel in the posting form removes the uploads of that form at once: only the pending uploads of the logged-in user himself.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaPendingAttachment
{
    /**
     * Seconds that a pending upload is left alone (a long message can take hours to write).
     */
    public const GRACE = 86400;

    /**
     * @param   integer|null  $fileTime  The time the file was last changed, or null when there is no file.
     * @param   integer       $now       The current time.
     * @param   integer       $grace     Seconds to leave the upload alone.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function mayRemove(?int $fileTime, int $now, int $grace = self::GRACE): bool
    {
        // A record without a file is only a leftover.
        if ($fileTime === null || $fileTime <= 0) {
            return true;
        }

        // A time in the future (a clock that was changed) is not old.
        return $fileTime <= $now - max(0, $grace);
    }

    /**
     * Whether Cancel in the posting form may remove an attachment: a pending upload (no message yet) of the logged-in user himself.
     *
     * @param   integer  $mesid       The message of the attachment, 0 while it is pending.
     * @param   integer  $uploaderId  The user who uploaded the attachment, 0 for a guest.
     * @param   integer  $userId      The current user, 0 for a guest.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function mayRemoveOnCancel(int $mesid, int $uploaderId, int $userId): bool
    {
        // A guest cannot prove that an upload is his: every guest has the user id 0.
        return $userId > 0 && $mesid === 0 && $uploaderId === $userId;
    }
}
