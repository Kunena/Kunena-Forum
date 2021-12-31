<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Pages.Category
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

$content = $this->execute('Category/Index');

$this->addBreadcrumb(
	Text::_('COM_KUNENA_VIEW_CATEGORIES_DEFAULT'),
	'index.php?option=com_kunena&view=category&layout=list'
);

echo $content;
echo $this->subRequest('Widget/WhoIsOnline');
echo $this->subRequest('Widget/Statistics');
