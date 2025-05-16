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
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\KunenaPrivate\KunenaPrivateMessage;

/**
 * Populate the private tables for atachments because since 6.4.3 it's populate when post new attachments
 *
 * @param $parent
 *
 * @return array
 * @throws Exception
 * @since Kunena 6.4.0
 */
function kunena_640_2025_05_14_fill_kunena_private_table($parent) {
    $db     = Factory::getContainer()->get('DatabaseDriver');
    $query  = $db->createQuery();
    $query->select('id, mesid')
    ->from($db->quoteName('#__kunena_attachments'))
    ->where($db->quoteName('protected') . ' = 32')
    ->where($db->quoteName('mesid') . ' != 0');
    $db->setQuery($query);
    
    $attachmentsPrivate = (array) $db->loadObjectList('id');
    
    $listMesid = [];
    foreach ($attachmentsPrivate as $attach) {
        $listMesid[] = $attach->mesid;
    }
    
    $messages = KunenaMessageHelper::getMessages($listMesid);
    
    $listAttachByMessage = [];
    foreach($attachmentsPrivate as $attachment) {
        $listAttachByMessage[$attachment->mesid][] = $attachment->id;
    }
    
    foreach($messages as $message) {
        $attachIds = $listAttachByMessage[$message->id];
        
        $parent             = $message->getParent();
        $author             = $message->getAuthor();
        $moderator          = $author->isModerator($message->getCategory());
        $pAuthor            = $parent->getAuthor();
        $private            = new KunenaPrivateMessage();
        $private->author_id = $author->userid;
        $private->subject   = $message->subject;
        $private->body      = '';
        
        // Attach message.
        $private->posts()->add($message->id);
        
        // Attach author of the message.
        if ($author->exists()) {
            $private->users()->add($author->userid);
        }
        
        if ($pAuthor->exists() && ($moderator || $pAuthor->isModerator($message->getCategory()))) {
            // Attach receiver (but only if moderator either posted or replied parent post).
            if ($pAuthor->exists()) {
                $private->users()->add($pAuthor->userid);
            }
        }
        
        $private->attachments()->setMapped($attachIds);
        
        try {
            $private->save();
        } catch (Exception $e) {
            KunenaError::displayDatabaseError($e);
        }
    }
    
    return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_640_FILL_KUNENA_PRIVATE_TABLE'), 'success' => true);
}
