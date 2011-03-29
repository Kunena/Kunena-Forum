<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
							<div class="kbuttonbar-post">
								<ul class="kmessage-buttons">
									<?php if (empty($this->message_closed)) : ?>
									<!-- User buttons  -->
									<?php if (!empty($this->message_quickreply)) : ?><li><?php echo $this->message_quickreply ?></li><?php endif ?>
									<?php if (!empty($this->message_reply)) : ?><li><?php echo $this->message_reply ?></li><?php endif ?>
									<?php if (!empty($this->message_quote)) : ?><li><?php echo $this->message_quote ?></li><?php endif ?>
									<?php if (!empty($this->message_thankyou)) : ?><li><?php echo $this->message_thankyou ?></li><?php endif ?>
									<?php if (!empty($this->message_report)) : ?><li><?php echo $this->message_report ?></li><?php endif ?>
									<?php if (!empty($this->message_edit)) : ?><li><?php echo $this->message_edit ?></li><?php endif ?>
									<!-- Moderator buttons  -->
									<?php if (!empty($this->message_moderate)) : ?><li><?php echo $this->message_moderate ?></li><?php endif ?>
									<?php if (!empty($this->message_delete)) : ?><li><?php echo $this->message_delete ?></li><?php endif ?>
									<?php if (!empty($this->message_permdelete)) : ?><li><?php echo $this->message_permdelete ?></li><?php endif ?>
									<?php if (!empty($this->message_undelete)) : ?><li><?php echo $this->message_undelete ?></li><?php endif ?>
									<?php if (!empty($this->message_publish)) : ?><li><?php echo $this->message_publish ?></li><?php endif ?>
									<?php else : ?>
									<li><?php echo $this->message_closed; ?></li>
									<?php endif ?>
								</ul>
							</div>