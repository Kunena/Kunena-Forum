<?php
/**
 * @version $Id: message.contents.php 1952 2010-02-20 03:10:27Z fxstein $
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
$kunena_config = & CKunenaConfig::getInstance ();

if ( isset ( $this->msg_html->attachments ) ) { ?>
<div>
	<div class="msgattach">
	<?php echo JText::_('COM_KUNENA_ATTACHMENTS');?>
		<ul class="kfile-attach">
		<?php
		foreach($this->msg_html->attachments as $attachment){
		?>
			<li>
				<?php echo $attachment;?>
			</li>
		<?php
		}
		?>
		</ul>
	</div>
</div>
<?php } ?>
