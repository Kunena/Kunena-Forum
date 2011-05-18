<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
?>
<div>
	<div class="kmsgattach">
	<?php echo JText::_('COM_KUNENA_ATTACHMENTS');?>
		<ul class="kfile-attach">
		<?php foreach($this->attachments as $attachment) : ?>
			<li>
				<?php echo $attachment->thumblink; ?>
				<span>
					<?php echo $attachment->textLink; ?>
				</span>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>
