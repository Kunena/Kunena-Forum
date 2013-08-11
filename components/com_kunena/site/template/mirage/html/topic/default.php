<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$this->displayPoll();
$this->displayModulePosition( 'kunena_poll' );
//$this->displayTopicActions();
$this->displayTemplateFile('topic', 'default', 'list');

?>
