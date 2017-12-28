<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.BBCode
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

// [email]john.doe@domain.com[/email]
// [email=john.doe@domain.com]John Doe[/email]

// Display email address (cloak it).
echo JHtml::_(
	'email.cloak',
	$this->escape($this->email), $this->mailto,
	$this->escape($this->text), $this->textCloak
);
