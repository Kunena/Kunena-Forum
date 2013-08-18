<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

echo $this->subLayout('Message/Item')
	->set('profile', $this->profile)
	->set('topic', $this->topic)
	->set('message', $this->message)
	->set('numLink', $this->numLink)
	->set('view', $this)
	->set('signatureHtml', $this->signatureHtml)
	->set('reportMessageLink', $this->reportMessageLink)
	->set('ipLink', isset($this->ipLink) ? $this->ipLink : null)
	->set('attachments', $this->attachments);
