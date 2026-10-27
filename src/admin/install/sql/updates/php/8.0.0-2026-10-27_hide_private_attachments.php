<?php

/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;

/**
 * The files of the private attachments had the name of their upload, in a folder which the site serves (without the protection of
 * the attachments): anybody who guessed the name opened the file. They get a name which nobody can guess.
 *
 * @param   mixed  $parent  The installer.
 *
 * @return  array
 *
 * @throws  Exception
 * @since   Kunena 8.0
 */
function kunena_800_2026_10_27_hide_private_attachments($parent)
{
    KunenaAttachmentHelper::hidePrivateFiles();

    return ['action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_800_HIDE_PRIVATE_ATTACHMENTS'), 'success' => true];
}
