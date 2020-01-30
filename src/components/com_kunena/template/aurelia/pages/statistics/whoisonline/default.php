<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Pages.Statistics
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
**/

namespace Kunena\Forum\Site;

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use function defined;

$content = $this->execute('Statistics/WhoIsOnline');

$this->addBreadcrumb(
	Text::_('COM_KUNENA_MENU_STATISTICS_WHOSONLINE'),
	'index.php?option=com_kunena&view=statistics&layout=whoisonline'
);

echo $content;
