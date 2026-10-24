<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Tables
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Tables;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Kunena\Forum\Libraries\File\KunenaFile;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;
use UnexpectedValueException;

/**
 * Kunena Attachments Table
 * Provides access to the #__kunena_attachments table
 *
 * @since   Kunena 6.0
 */
class TableKunenaAttachments extends KunenaTable
{
    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public $id = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public $userid = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $mesid = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $protected = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $hash = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $size = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $folder = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $filetype = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $filename = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $filename_real = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $comment = null;

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    public mixed $inline = null;

    /**
     * @var     mixed
     * @since   Kunena 7.2
     */
    public mixed $caption = null;

    /**
     * @var     mixed
     * @since   Kunena 7.2
     */
    public mixed $status = null;

    /**
     * @param   DatabaseDriver  $db  Database driver
     *
     * @since   Kunena 6.0
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__kunena_attachments', 'id', $db);
    }

    /**
     * @return  boolean
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function check(): bool
    {
        $user    = KunenaUserHelper::get($this->userid);
        $message = KunenaMessageHelper::get($this->mesid);

        if ($user->userid != 0 && !$user->exists()) {
            throw new RuntimeException(Text::sprintf('COM_KUNENA_LIB_TABLE_ATTACHMENTS_ERROR_USER_INVALID', (int) $user->userid));
        }

        if ($message->id && !$message->exists()) {
            throw new RuntimeException(Text::sprintf('COM_KUNENA_LIB_TABLE_ATTACHMENTS_ERROR_MESSAGE_INVALID', (int) $message->id));
        }

        $this->folder = trim((string) $this->folder, '/');

        if (!$this->folder) {
            throw new UnexpectedValueException(Text::_('COM_KUNENA_LIB_TABLE_ATTACHMENTS_ERROR_NO_FOLDER'));
        }

        if (!$this->filename) {
            throw new UnexpectedValueException(Text::_('COM_KUNENA_LIB_TABLE_ATTACHMENTS_ERROR_NO_FILENAME'));
        }

        if (!$this->filename_real) {
            $this->filename_real = $this->filename;
        }

        $file = JPATH_ROOT . "/{$this->folder}/{$this->filename}";

        if (!is_file($file)) {
            throw new UnexpectedValueException(Text::sprintf('COM_KUNENA_LIB_TABLE_ATTACHMENTS_ERROR_FILE_MISSING', "{$this->folder}/{$this->filename}"));
        }

        if (!$this->hash) {
            $this->hash = md5_file($file);
        }

        if (!$this->size) {
            $this->size = fileSize($file);
        }

        if (!$this->filetype) {
            $this->filetype = KunenaFile::getMime($file);
        }

        return true;
    }
}
