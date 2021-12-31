<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.BBCode
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

// [email]john.doe@domain.com[/email]
// [email=john.doe@domain.com]John Doe[/email]

// Display email address (cloak it).
echo HTMLHelper::_(
	'email.cloak',
	$this->escape($this->email), $this->mailto,
	$this->escape($this->email), $this->mailto
);
