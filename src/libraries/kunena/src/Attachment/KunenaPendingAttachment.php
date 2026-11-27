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

use Joomla\CMS\Factory;
use Throwable;

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
     * The key of the session which has the uploads of a guest.
     */
    public const GUEST_UPLOADS = 'kunena.guest.uploads';

    /**
     * The uploads which the guest of this session made and which wait for their message.
     *
     * @return  int[]
     *
     * @since   Kunena 8.0
     */
    public static function guestUploads(): array
    {
        try {
            $ids = Factory::getApplication()->getSession()->get(self::GUEST_UPLOADS, []);
        } catch (Throwable $e) {
            // No session (the command line): no upload of a guest.
            return [];
        }

        return array_values(array_unique(array_filter(array_map('intval', (array) $ids))));
    }

    /**
     * Remember an upload of the guest of this session. Every guest has the user id 0, so the id of the user does not tell whose
     * upload a pending attachment is: the session of the guest who uploaded it does.
     *
     * @param   int  $id  The attachment.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function rememberGuestUpload(int $id): void
    {
        if ($id < 1) {
            return;
        }

        // The last uploads are enough: a message has a limited number of attachments.
        $ids = \array_slice(array_merge(self::guestUploads(), [$id]), -100);

        Factory::getApplication()->getSession()->set(self::GUEST_UPLOADS, array_values(array_unique($ids)));
    }

    /**
     * May the current user put a pending upload (of no message yet) into his message: a member his own uploads, a guest the
     * uploads of his session.
     *
     * @param   int    $attachmentId  The attachment.
     * @param   int    $uploaderId    The user who uploaded it, 0 for a guest.
     * @param   int    $userId        The current user, 0 for a guest.
     * @param   int[]  $guestUploads  The uploads of the session of the guest.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function mayAttach(int $attachmentId, int $uploaderId, int $userId, array $guestUploads): bool
    {
        if ($uploaderId !== $userId) {
            return false;
        }

        return $userId > 0 || \in_array($attachmentId, $guestUploads, true);
    }

    /**
     * How many times the limit of one guest all the guests together may have waiting: it keeps the disk from filling when a guest
     * drops his session for every upload.
     */
    public const GUESTS_FACTOR = 10;

    /**
     * Has the user too many uploads which wait for a message? A member: his own. A guest: those of his session, and all the
     * pending uploads of guests together have a limit too.
     *
     * @param   int   $own      The pending uploads of the member, or of the session of the guest.
     * @param   int   $all      All the pending uploads of the user id (for a guest: of all the guests).
     * @param   int   $limit    The limit of one user.
     * @param   bool  $guest    The user is a guest.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function overLimit(int $own, int $all, int $limit, bool $guest): bool
    {
        if ($own >= $limit) {
            return true;
        }

        return $guest && $all >= $limit * self::GUESTS_FACTOR;
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
