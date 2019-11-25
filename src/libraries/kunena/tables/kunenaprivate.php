<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Tables
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined('_JEXEC') or die();
use Joomla\CMS\Language\Text;
require_once(__DIR__ . '/kunena.php');
/**
 * Kunena Private Messages
 * Provides access to the #__kunena_private table
 */
class TableKunenaPrivate extends KunenaTable
{
    public $id = null;
    public $parent_id = null;
    public $author_id = null;
    public $created_at = null;
    public $attachments = null;
    public $subject = null;
    public $body = null;
    public $params = null;
    
    /**
     * @param   JDatabaseDriver $db Database driver
     *
     * @since Kunena
     */
    public function __construct($db)
    {
        parent::__construct('#__kunena_private', 'id', $db);
    }
    /**
     * @return boolean
     * @throws Exception
     * @since Kunena
     */
    public function check()
    {
        if (!$this->created_at) {
            $this->created_at = Joomla\CMS\Date\Date::getInstance()->toSql();
        }
        $this->subject = trim($this->subject);
        if (!$this->subject)
        {
            // TODO: translate
            $this->setError(Text::sprintf('COM_KUNENA_LIB_TABLE_PRIVATE_ERROR_NO_SUBJECT'));
        }
        if (!$this->body && !$this->attachments)
        {
            // TODO: translate
            $this->setError(Text::sprintf('COM_KUNENA_LIB_TABLE_PRIVATE_ERROR_NO_BODY'));
        }
        if ($this->params instanceof Joomla\Registry\Registry) {
            $this->params = $this->params->toString();
        }
        return ($this->getError() == '');
    }
}
