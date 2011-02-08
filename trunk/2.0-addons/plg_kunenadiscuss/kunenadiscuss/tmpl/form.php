<?php
/**
 * @version $Id: form.php 4062 2010-12-23 07:15:10Z severdia $
 * Kunena Discuss Plug-in
 * @package Kunena Discuss
 *
 * @Copyright (C) 2010-2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined( '_JEXEC' ) or die ( '' );
?>
<div id="kdiscuss-quick-post<?php echo $row->id ?>" class="kdiscuss-form">
	<div class="kdiscuss-title"><?php echo JText::_('PLG_KUNENADISCUSS_DISCUSS') ?></div>
	<?php if (isset($this->msg)) : ?>
		<?php echo $this->msg; ?>
	<?php else: ?>
	<form method="post" name="postform">
		<table>
			<tr>
				<td valign="top">
					<table>
					<tr>
						<td><span class="kdiscuss-quick-post-label"><?php echo JText::_('PLG_KUNENADISCUSS_NAME') ?></span></td>
						<td><input type="text" name="name" value="<?php echo $this->name ?>" <?php if ($this->user->exists()) echo 'disabled="disabled" '; ?>/></td>
					</tr>
					<?php if(!$this->user->exists()) : ?>
					<tr>
						<td><span class="kdiscuss-quick-post-label"><?php echo JText::_('PLG_KUNENADISCUSS_EMAIL') ?></span></td>
						<td><input type="text" name="email" value="" /></td>
					</tr>
					<?php endif; ?>
					<tr>
						<td><span class="kdiscuss-quick-post-label">
						<?php echo JText::_('PLG_KUNENADISCUSS_MESSAGE') ?></span>
					</tr>
					<tr>
						<td colspan="2"><textarea name="message" rows="5" cols="60"></textarea></td>
					</tr>
					<?php if ($this->hasCaptcha()) : ?>
					<tr>
						<td><span class="kdiscuss-quick-post-label"><?php echo JText::_('PLG_KUNENADISCUSS_CAPTCHA') ?></span></td>
						<td><?php $this->displayCaptcha(); ?></td>
					</tr>
					<?php endif; ?>
					<tr>
						<td><input type="submit" class="kbutton" value="<?php echo JText::_('PLG_KUNENADISCUSS_SUBMIT') ?>" /></td>
					</tr>
					</table>
				</td>
			</tr>
		</table>
		<input type="hidden" name="kdiscussContentId" value="<?php echo $row->id ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
	<?php endif; ?>
</div>

