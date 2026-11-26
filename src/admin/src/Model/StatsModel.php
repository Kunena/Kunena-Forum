<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Model;

\defined('_JEXEC') or die();

use Joomla\CMS\MVC\Model\AdminModel;

/**
 * Stats Model for Kunena
 *
 * @since   Kunena 2.0
 */
class StatsModel extends AdminModel
{
    /**
     * @inheritDoc
     *
     * @param   array    $data      data
     * @param   boolean  $loadData  load data
     *
     * @return false  This model has no form.
     *
     * @since  Kunena 6.0
     */
    public function getForm($data = [], $loadData = true)
    {
        // No form: this model does not use one (the method is kept for the callers of getForm()).
        return false;
    }
}
