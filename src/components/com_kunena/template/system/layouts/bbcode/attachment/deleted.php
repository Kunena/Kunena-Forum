<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  BBCode
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

// @var KunenaAttachment $attachment

$attachment = $this->attachment;
?>
<div class="kmsgattach">
	<h4>
		<?php echo JText::sprintf('COM_KUNENA_ATTACHMENT_DELETED', $attachment->getFilename()); ?>
	</h4>
</div>
