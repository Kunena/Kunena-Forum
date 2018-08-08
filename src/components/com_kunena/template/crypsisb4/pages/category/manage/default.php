<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Pages.Category
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

$content = $this->execute('Category/Manage');

$this->addBreadcrumb(
	Text::_('COM_KUNENA_MENU_CATEGORY_MANAGE'),
	'index.php?option=com_kunena&view=category&layout=manage'
);

echo $content;
