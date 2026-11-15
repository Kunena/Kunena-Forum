<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Api
 * @subpackage      View
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Api\View\Posts;

\defined('_JEXEC') or die();

use Joomla\CMS\MVC\View\JsonApiView as BaseApiView;
use Kunena\Forum\Libraries\Api\KunenaApi;

/**
 * The posts of the forum. The JSON:API document of a list and of an item.
 *
 * @since   Kunena 8.0
 */
class JsonapiView extends BaseApiView
{
    /**
     * @param   array  $config  The configuration.
     *
     * @since   Kunena 8.0
     */
    public function __construct($config = [])
    {
        $this->fieldsToRenderItem = KunenaApi::POST_FIELDS;
        $this->fieldsToRenderList = KunenaApi::POST_FIELDS;

        parent::__construct($config);
    }
}
