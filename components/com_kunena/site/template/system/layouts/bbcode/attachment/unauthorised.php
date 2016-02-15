<?php
/**
 * Kunena Component
* @package Kunena.Template.Crypsis
* @subpackage BBCode
*
* @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link https://www.kunena.org
**/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAttachment $attachment */
$attachment = $this->attachment;
/** @var KunenaUser $user */
$user = isset($this->user) ? $this->user : null;

// Get authorisation message.
$exception = $attachment->tryAuthorise('read', $user, false);
if (!$exception) $exception = new KunenaExceptionAuthorise('Bad Request.', 400);
?>
<div class="kmsgattach">
	<?php echo $exception->getMessage(); ?>
</div>
