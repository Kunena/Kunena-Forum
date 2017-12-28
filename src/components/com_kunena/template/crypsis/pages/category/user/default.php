<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Pages.Category
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$content = $this->execute('Category/Subscriptions');

$this->addBreadcrumb(
	JText::_('COM_KUNENA_VIEW_CATEGORIES_USER'),
	'index.php?option=com_kunena&view=category&layout=user'
);

echo $content;
