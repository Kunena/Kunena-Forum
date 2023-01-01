<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;

$attachment = $this->attachment;

$user = isset($this->user) ? $this->user : null;

// Get authorisation message.
$exception = $attachment->tryAuthorise('read', $user, false);

if (!$exception) {
    $exception = new KunenaExceptionAuthorise('Bad Request.', 400);
}
?>
<div class="kmsgattach">
    <?php echo $exception->getMessage(); ?>
</div>
