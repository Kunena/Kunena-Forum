<?php
/**
 * @version $Id$
 * Kunena Discuss Plugin
 * @package Kunena Discuss
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
defined( '_JEXEC' ) or die ( '' );
?>
<div class="kdiscuss-item kdiscuss-item<?php echo $this->rowid ?>">
<a name="<?php echo $this->msg_html->id ?>" id="id<?php echo $this->msg_html->id ?>" > </a>
<table>
	<tr>
		<?php if ($this->config->allowavatar) : ?>
		<td valign="top" class="kdiscuss-avatar-cover">
			<?php echo CKunenaLink::GetProfileLink ( $this->config, $this->kunena_message->userid, $this->msg_html->avatar ) ?>
		</td>
		<?php endif; ?>
		<td valign="top" class="kdiscuss-content-cover">
			<span class="kdiscuss-subject"><?php echo $this->msg_html->subject; ?></span>
			<span class="kdiscuss-date" title="<?php echo CKunenaTimeformat::showDate($this->kunena_message->time, 'config_post_dateformat_hover'); ?>">
				<?php echo CKunenaTimeformat::showDate($this->kunena_message->time, 'config_post_dateformat'); ?>
			</span>
			<span class="kdiscuss-username">
				<?php echo CKunenaLink::GetProfileLink ( $this->config, $this->kunena_message->userid, $this->msg_html->username ) ?>
			</span>
			<div class="kdiscuss-text"><?php echo $this->msg_html->text; ?></div>
		</td>
		<td valign="top" class="kdiscuss-itemid-cover">#<?php echo $this->msg_html->id ?></td>
	</tr>
</table>
</div>
