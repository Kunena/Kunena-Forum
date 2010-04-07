<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
?>
<div class="kmessage_buttons_cover">
	<div class="kmessage_buttons_row">
			<?php if (empty( $this->message_closed )) : ?>
				<?php echo $this->message_quickreply; ?>
				<?php echo $this->message_reply; ?>
				<?php echo $this->message_quote; ?>
				<?php echo $this->message_edit; ?>
				<?php echo $this->message_merge; ?>
				<?php echo $this->message_split; ?>
				<?php echo $this->message_delete; ?>
				<?php echo $this->message_move; ?>
				<?php echo $this->message_publish; ?>
			<?php else : ?>
				<?php echo $this->message_closed; ?>
			<?php endif ?>
	</div>
</div>