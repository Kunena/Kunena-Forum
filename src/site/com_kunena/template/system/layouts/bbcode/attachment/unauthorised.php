<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

defined('_JEXEC') or die();

use function defined;

$attachment = $this->attachment;

$user = isset($this->user) ? $this->user : null;

// Get authorisation message.
$exception = $attachment->tryAuthorise('read', $user, false);

if (!$exception)
{
	$exception = new \Kunena\Forum\Libraries\Exception\Authorise('Bad Request.', 400);
}
?>
<div class="kmsgattach">
	<?php echo $exception->getMessage(); ?>
</div>
