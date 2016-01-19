<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Pages.User
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$content = $this->execute('User/Edit');

$this->addBreadcrumb(
	JText::_('COM_KUNENA_EDIT'),
	'index.php?option=com_kunena&view=user&layout=edit'
);

echo $content;
