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
		<ul>
		<?php
		// FIXME: use attachments class
		foreach($this->attachments as $attachment){
			// shortname for output
			$shortname = $this->escape(CKunenaTools::shortenFileName($attachment->filename));
			// First lets check the attachment file type
			switch (strtolower($attachment->shorttype)){
				case 'image' :
					// Check for thumbnail and if available, use for display
					if (file_exists(JPATH_ROOT.'/'.$this->escape($attachment->folder).'/thumb/'.$this->escape($attachment->filename))){
						$thumb = $this->escape($attachment->folder).'/thumb/'.$this->escape($attachment->filename);
						$imgsize = '';
					} else {
						$thumb = $this->escape($attachment->folder).'/'.$this->escape($attachment->filename);
						$imgsize = 'width="'.$this->escape($this->config->thumbwidth).'px" height="'.$this->escape($this->config->thumbheight).'px"';
					}

					$img = '<img title="'.$this->escape($attachment->filename).'" '.$imgsize.' src="'.JURI::ROOT().$thumb.'" alt="'.$this->escape($attachment->filename).'" />';
					$html = CKunenaLink::GetAttachmentLink($this->escape($attachment->folder),$this->escape($attachment->filename),$img,$this->escape($attachment->filename), 'lightbox-attachments'.intval($this->id));
					break;
				default :
					// Filetype without thumbnail or icon support - use default file icon
					$img = '<img src="'.KUNENA_URLICONSPATH.'attach_generic.png" alt="'.JText::_('COM_KUNENA_ATTACH').'" />';
					$html = CKunenaLink::GetAttachmentLink($this->escape($attachment->folder),$this->escape($attachment->filename),$img,$this->escape($attachment->filename), 'nofollow');
			}
			?>
			<li>
			<?php
			$html .='<span>'.CKunenaLink::GetAttachmentLink($this->escape($attachment->folder),$this->escape($attachment->filename),$shortname,$this->escape($attachment->filename), 'nofollow').' ('.number_format($attachment->size/1024,0,'',',').'KB)</span>';
			echo $html;
			?>
			</li>
		<?php
		}
		?>
		</ul>
	</div>
</div>
