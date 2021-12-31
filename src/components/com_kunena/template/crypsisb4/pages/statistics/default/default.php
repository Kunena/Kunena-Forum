<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Pages.Statistics
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$content = $this->execute('Statistics/General');

$this->addBreadcrumb(
	Text::_('COM_KUNENA_MENU_STATISTICS'),
	'index.php?option=com_kunena&view=statistics&layout=default'
);

echo $content;
