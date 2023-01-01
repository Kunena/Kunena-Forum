<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Pages.Announcement
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

$content = $this->execute('Announcement/Item');

$this->addBreadcrumb(
    Text::_('COM_KUNENA_ANN_ANNOUNCEMENTS'),
    'index.php?option=com_kunena&view=announcement&layout=listing'
);

echo $content;
