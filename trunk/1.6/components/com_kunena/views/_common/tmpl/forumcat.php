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
	<form name="choose_forum" method="post" target="_self" action="<?php echo JRoute::_('index.php?option=com_kunena&view=categories');?>">
		<select name="category" class="input_forum" onchange="if(this.options[this.selectedIndex].value> 0){ this.form.submit() }">
			<option value="" selected="selected"><?php echo JText::_('K_BOARD_CATEGORIES');?></option>
			<?php echo JHtml::_('select.options', JHtml::_('kcategorylist.options'), 'value', 'text', 0);?>
		</select>
		<input type="submit" name="<?php echo JText::_('K_GO'); ?>" class="go_btn" value="<?php echo JText::_('K_GO'); ?>"/>
	</form>
</div>
