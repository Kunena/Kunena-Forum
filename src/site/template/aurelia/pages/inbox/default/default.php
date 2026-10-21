<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Pages.Inbox
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

$this->addBreadcrumb(
    Text::_('COM_KUNENA_VIEW_INBOX_DEFAULT'),
    'index.php?option=com_kunena&view=inbox'
);
?>
<div class="card">
    <h3 class="card-header"><?php echo $this->header; ?></h3>
    <div class="card-body">
        <?php echo $this->content; ?>
    </div>
</div>
