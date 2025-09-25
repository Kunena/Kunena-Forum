<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      File
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https: //www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https: //www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Form\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\NumberField;

/**
 * FileSizeField
 *
 * @since 7.0.0
 */
class FileSizeField extends NumberField
{
    /**
     * The form field type.
     *
     * @var    string
     */
    public $type = 'FileSize';

    /**
     * Method to get the data to be passed to the layout for rendering.
     *
     * @return  array
     *
     * @since 7.0.0
     */
    protected function getLayoutData()
    {
        $data = parent::getLayoutData();
        $data['description'] = \sprintf(
            $data['description'],
            \ini_get('post_max_size'),
            \ini_get('upload_max_filesize'),
            \function_exists('php_ini_loaded_file') ? \php_ini_loaded_file() : ''
        );

        return $data;
    }
}
