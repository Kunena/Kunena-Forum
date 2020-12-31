<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3b3
 * @subpackage      Pages.Statistics
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

$content = $this->execute('Statistics/WhoIsOnline');

$this->addBreadcrumb(
	Text::_('COM_KUNENA_MENU_STATISTICS_WHOSONLINE'),
	'index.php?option=com_kunena&view=statistics&layout=whoisonline'
);

echo $content;
