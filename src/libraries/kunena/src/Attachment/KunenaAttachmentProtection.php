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
 * Tells which checks the protection level of an attachment asks for.
 *
 * The level is a bitmask (KunenaAttachment::PROTECTION_*). The checks used to be written as
 * "$this->protected && KunenaAttachment::PROTECTION_PRIVATE", which tests the constant instead of the bit, so every check ran for
 * every protected attachment, whatever its level: the check of the message (ACL) was able to open an attachment of a private
 * message, and the author was let in whatever the level was.
 *
 * The values are the ones of KunenaAttachment (a test keeps them equal).
 *
 * @since   Kunena 7.1
 */
abstract class KunenaAttachmentProtection
{
    public const PUBLIC      = 1;
    public const ACL         = 2;
    public const PRIVATE     = 32;
    public const AUTHOR      = 64;
    public const UNPUBLISHED = 128;

    /**
     * The access of the message decides: the attachment is shown to those who may see the message.
     *
     * An attachment of a private message is never opened by the access of a message.
     *
     * @param   integer  $protected  The protection level.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function usesMessageAccess(int $protected): bool
    {
        return !self::isPrivate($protected) && ($protected & (self::PUBLIC | self::ACL)) !== 0;
    }

    /**
     * The attachment belongs to a private message: only its participants (and moderators) may see it.
     *
     * @param   integer  $protected  The protection level.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function isPrivate(int $protected): bool
    {
        return ($protected & self::PRIVATE) !== 0;
    }

    /**
     * The author may see the attachment, as long as it is not unpublished (level < AUTHOR * 2).
     *
     * @param   integer  $protected  The protection level.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function authorMaySee(int $protected): bool
    {
        return $protected > 0 && $protected < self::AUTHOR * 2;
    }
}
