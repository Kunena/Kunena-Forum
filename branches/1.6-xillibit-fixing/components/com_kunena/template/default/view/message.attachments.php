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
	<div class="msgattach">
	<?php echo JText::_('COM_KUNENA_ATTACHMENTS');?>
		<ul class="kfile-attach">
		<?php
		foreach($this->attachments as $attachment){
		?>
			<li>
			<?php
			// shortname for output
			$shortname = CKunenaTools::shortenFileName($attachment->filename);
			// First lets check the attachment file type
			switch (strtolower($attachment->shorttype)){
				case 'image' :

					// Check for thumbnail and if available, use for display
					if (file_exists(JPATH_ROOT.$attachment->folder.'/thumb/'.$attachment->filename)){
						$thumb = $attachment->folder.'/thumb/'.$attachment->filename;
						$imgsize = '';
					} else {
						$thumb = $attachment->folder.'/'.$attachment->filename;
						$imgsize = 'width="'.$this->config->thumbwidth.'px" height="'.$this->config->thumbheight.'px"';
					}

					echo '<a href="'.$attachment->folder.'/'.$attachment->filename.'" rel="nofollow">'.
						'<img '.$imgsize.' src="'.JURI::ROOT().$thumb.'" alt="'.$attachment->filename.'" />'.
						'</a>'.
						'<span>'.CKunenaLink::GetAttachmentLink($attachment->folder,$attachment->filename,$shortname,$shortname, 'nofollow').
						'&nbsp;('.number_format(($attachment->size)/1024,0,'',',').'KB)</span>';
					break;
				default :
					// Filetype without thumbnail or icon support - use default file icon

					echo '<span><img src="'.KUNENA_URLICONSPATH.'attach_generic.png" alt="'.JText::_('COM_KUNENA_ATTACH').'" />'.CKunenaLink::GetAttachmentLink($attachment->folder,$attachment->filename,$attachment->filename,$attachment->filename, 'nofollow').'&nbsp;('.number_format(($attachment->size)/1024,0,'',',').'KB)</span>';
			}
			?>
			</li>
		<?php
		}
		?>
		</ul>
	</div>
</div>
