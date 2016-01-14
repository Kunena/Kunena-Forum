<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Pages.Search
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$content = $this->execute('Search/Form');

$this->addBreadcrumb(
	JText::_('COM_KUNENA_MENU_SEARCH'),
	'index.php?option=com_kunena&view=search'
);

echo $content;
echo $this->subRequest('Search/Results');
