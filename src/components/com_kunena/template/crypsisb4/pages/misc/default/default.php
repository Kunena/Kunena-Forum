<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Pages.Misc
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

// No breadcrumb - this page can only be accessed by creating a menu item for it.

echo $this->subLayout('Widget/Custom')
	->set('header', $this->header)
	->set('body', $this->body);
