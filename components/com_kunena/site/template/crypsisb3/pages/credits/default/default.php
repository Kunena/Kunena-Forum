<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Pages.Credits
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$content = $this->execute('Credits');

$this->addBreadcrumb(
	JText::_('COM_KUNENA_VIEW_CREDITS_DEFAULT'),
	'index.php?option=com_kunena&view=credits'
);

echo $content;
