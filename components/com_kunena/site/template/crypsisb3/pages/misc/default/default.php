<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Pages.Misc
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

// No breadcrumb - this page can only be accessed by creating a menu item for it.

echo $this->subLayout('Widget/Custom')
	->set('header', $this->header)
	->set('body', $this->body);
