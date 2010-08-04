<?php
/**
 * @version $Id$
 * Kunena Discuss Plugin
 * @package Kunena Discuss
 *
 * @Copyright (C) 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
defined( '_JEXEC' ) or die ( '' );
?>
<div class="kdiscuss-item kdiscuss-item<?php echo $this->mmm & 1 ? 1 : 2 ?>">
<a name="<?php echo intval($this->id) ?>" id="id<?php echo intval($this->id) ?>" > </a>
<table>
	<tr>
		<?php if ($this->config->allowavatar) : ?>
		<td valign="top" class="kdiscuss-avatar-cover">
			<?php echo CKunenaLink::GetProfileLink ( $this->profile->userid, $this->avatar ) ?>
		</td>
		<?php endif; ?>
		<td valign="top" class="kdiscuss-content-cover">
			<span class="kdiscuss-subject"><?php echo $this->escape($this->subject); ?></span>
			<span class="kdiscuss-date" title="<?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat_hover'); ?>">
				<?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat'); ?>
			</span>
			<span class="kdiscuss-username">
				<?php echo CKunenaLink::GetProfileLink ( $this->profile->userid, $this->escape($this->username) ) ?>
			</span>
			<div class="kdiscuss-text"><?php echo $this->messageHtml; ?></div>
		</td>
		<td valign="top" class="kdiscuss-itemid-cover">#<?php echo intval($this->id) ?></td>
	</tr>
</table>
</div>
