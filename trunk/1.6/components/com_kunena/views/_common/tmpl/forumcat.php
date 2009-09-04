<?php
/**
 * @version		$Id: $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
?>
<div class="choose_forumcat">
	<form name="choose_forum" method="post" target="_self" action="/forum">
		<input type="hidden" name="func" value="showcat"/>
		<select name="catid" class="input_forum" onchange="if(this.options[this.selectedIndex].value> 0){ this.form.submit() }">
			<option value="0" selected="selected"><?php echo JText::_('K_BOARD_CATEGORIES'); ?></option>
			<option value="94">Kunena - To Speak!</option>
			<option value="77">...&nbsp;General Talk about Kunena</option>
			<option value="119">...&nbsp;Feature Requests</option>
			<option value="136">...&nbsp;Templates and Design</option>
		</select>
		<input type="submit" name="<?php echo JText::_('K_GO'); ?>" class="go_btn" value="<?php echo JText::_('K_GO'); ?>"/>
	</form>
</div>
