<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Tables
 *
 * @copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined('_JEXEC') or die();
require_once(__DIR__ . '/kunena.php');
/**
 * Kunena Private Message map to forum posts.
 * Provides access to the #__kunena_private_post_map table
 */
class TableKunenaPrivatePostMap extends KunenaTable
{
    protected $_autoincrement = false;
    public $private_id = null;
    public $message_id = null;
    public function __construct($db)
    {
        parent::__construct('#__kunena_private_post_map', array('private_id', 'message_id'), $db);
    }
}
