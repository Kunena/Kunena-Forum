<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Model;

use Joomla\CMS\MVC\Model\ListModel;

\defined('_JEXEC') or die();

/**
 * Misc Model for Kunena
 *
 * @since   Kunena 2.0
 */
class MiscModel extends ListModel
{
    /**
     * The component of the model. Joomla reads it from the namespace of the class (what follows "\Component\"), which the namespace
     * of Kunena has not: it made "com_m" of it.
     *
     * @var    string
     * @since  Kunena 8.0
     */
    protected $option = 'com_kunena';

}
