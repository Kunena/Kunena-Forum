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

use Joomla\CMS\Form\Field\RadioField;
use Joomla\CMS\Uri\Uri;

/**
 * ProtectAttachmentsField
 *
 * @since 7.0.0
 */
class ProtectAttachmentsField extends RadioField
{
    /**
     * The form field type.
     *
     * @var    string
     */
    public $type = 'ProtectAttachments';

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
            Uri::root(\false) . 'media/kunena/attachments/image.png'
        );

        return $data;
    }
}
