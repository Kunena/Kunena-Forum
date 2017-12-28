<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Pages.Statistics
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$content = $this->execute('Statistics/WhoIsOnline');

$this->addBreadcrumb(
	JText::_('COM_KUNENA_MENU_STATISTICS_WHOSONLINE'),
	'index.php?option=com_kunena&view=statistics&layout=whoisonline'
);

echo $content;
