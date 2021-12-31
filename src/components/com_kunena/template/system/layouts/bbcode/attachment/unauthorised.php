<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

$attachment = $this->attachment;

$user = isset($this->user) ? $this->user : null;

// Get authorisation message.
$exception = $attachment->tryAuthorise('read', $user, false);

if (!$exception)
{
	$exception = new KunenaExceptionAuthorise('Bad Request.', 400);
}
?>
<div class="kmsgattach">
	<?php echo $exception->getMessage(); ?>
</div>
