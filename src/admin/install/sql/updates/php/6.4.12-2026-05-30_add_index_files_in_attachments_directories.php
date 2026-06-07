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

use Joomla\CMS\Language\Text;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\File;

/**
 * @param $parent
 *
 * @return array
 * @throws Exception
 * @since Kunena 7.0.6
 */
function kunena_6412_2026_05_30_add_index_files_in_attachments_directories($parent) {
    $path = JPATH_ROOT . '/media/kunena/attachments/';
    
    $attachmensFolders = Folder::folders($path, '.', false, true);
    
    foreach($attachmensFolders as $folder) {
        // Create index.hml into the user folder
        $content = '<html><body></body></html>';
        File::write($folder . '/index.html', $content);
    }
    
    return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_706_ADD_INDEX_FILES_ATTACHMENTS_DIRECTORIES'), 'success' => true);
}