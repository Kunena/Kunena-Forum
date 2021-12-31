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
use Joomla\CMS\Language\Text;

$attachment = $this->attachment;
?>
<div class="kmsgattach">
	<h4>
		<?php echo Text::sprintf('COM_KUNENA_ATTACHMENT_DELETED', $attachment->getFilename()); ?>
	</h4>
</div>
