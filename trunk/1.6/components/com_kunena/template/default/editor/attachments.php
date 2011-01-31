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
		<ul class="kfile-attach-editing">
		<?php foreach($this->attachments as $attachment) : ?>
			<li class="kattachment-old">
				<span>
					<input type="checkbox" name="attach-id[]" checked="checked" value="<?php echo intval($attachment->id); ?>" />
					<a href="#" class="kattachment-insert" style="display: none;"><?php echo  JText::_('COM_KUNENA_EDITOR_INSERT'); ?></a>
				</span>
				<?php echo $attachment->thumblink; ?>
				<span>
					<span class="kfilename"><?php echo $this->escape($attachment->filename); ?></span>
					<span><?php echo '('.number_format(intval($attachment->size)/1024,0,'',',').'KB)'; ?></span>
				</span>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>