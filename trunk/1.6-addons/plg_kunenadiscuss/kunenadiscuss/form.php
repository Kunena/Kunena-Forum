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
<div id="kdiscuss-quick-post<?php echo $row->id ?>">
<div class="kdiscuss-title"><?php echo JText::_('PLG_KUNENADISCUSS_DISCUSS') ?></div>
<form method="post" name="postform">
	<table>
		<tr>
		<td valign="top">
		<table>
		<tr>
			<td><span class="kdiscuss-quick-post-label"><?php echo JText::_('PLG_KUNENADISCUSS_NAME') ?></span></td>
			<td><input type="text" name="name" value="<?php echo $this->name ?>" <?php if ($this->_my->id) echo 'disabled="disabled" '; ?>/></td>
		</tr>
		<?php if(!$this->_my->id) : ?>
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
		<?php if ($this->config->captcha && !$this->_my->id) : ?>
		<tr>
			<td><span class="kdiscuss-quick-post-label"><?php echo JText::_('PLG_KUNENADISCUSS_CAPTCHA') ?></span></td>
			<td>CAPTCHA IMAGE</td>
		</tr>
		<?php endif; ?>
		<tr>
			<td><input type="submit" class="button" value="<?php echo JText::_('PLG_KUNENADISCUSS_SUBMIT') ?>" /></td>
		</tr>
		</table>
		</td>
	</tr>
</table>
<input type="hidden" name="kdiscussContentId" value="<?php echo $row->id ?>" />
</form>
</div>

