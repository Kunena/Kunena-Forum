<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
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
					<?php echo CKunenaLink::GetAttachmentLink($this->escape($attachment->folder),$this->escape($attachment->filename),$this->escape($attachment->shortname),$this->escape($attachment->filename), 'nofollow').' ('.number_format(intval($attachment->size)/1024,0,'',',').'KB)'; ?>
				</span>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>
