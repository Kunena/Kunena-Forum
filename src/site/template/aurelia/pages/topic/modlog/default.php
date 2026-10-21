<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Pages.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

$content = $this->execute('Topic/Modlog');

$this->addBreadcrumb(
    Text::_('COM_KUNENA_MODLOG'),
    'index.php?option=com_kunena&view=topic&layout=modlog'
);

echo $content;
