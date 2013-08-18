<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Topics
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

echo $this->subLayout('Message/Row')
	->set('message', $this->message)
	->set('spacing', !empty($this->spacing))
	->set('position', 'kunena_message_row' . $this->position)
	->set('checkbox', !empty($this->postActions))
	->setLayout('table');
