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

$content = $this->execute('Topic/Unsubscribe');

$this->addBreadcrumb(
    Text::_('COM_KUNENA_EMAIL_UNSUBSCRIBE'),
    'index.php?option=com_kunena&view=topic&layout=unsubscribe'
);

echo $content;
