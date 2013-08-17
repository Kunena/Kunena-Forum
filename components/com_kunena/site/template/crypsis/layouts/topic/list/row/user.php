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

echo $this->subLayout('Topic/Row')
	->set('topic', $this->topic)
	->set('spacing', !empty($this->spacing))
	->set('position', 'kunena_topic_' . $this->position)
	->set('checkbox', !empty($this->topicActions))
	->setLayout('user_table');
