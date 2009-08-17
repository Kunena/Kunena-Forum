<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
?>
<fieldset>
	<legend><?php echo JText::_('Setup'); ?></legend>

	<table width="100%" class="admintable" cellspacing="1">
		<tr>
			<td width="40%" class="key">
				<label for="config_board_title" class="hasTip" title="Board Title::The name of your board.">Board Title</label>
			</td>
			<td>
				<input type="text" name="config[board_title]" id="config_board_title" value="<?php echo $this->options->get('board_title'); ?>" size="30" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_board_email" class="hasTip" title="Board Email::This is the board's e-mail address. Make this a valid e-mail address.">Board Email</label>
			</td>
			<td>
				<input type="text" name="config[board_email]" id="config_board_email" value="<?php echo $this->options->get('board_email'); ?>" size="30" />
			</td>
		</tr>
	</table>
</fieldset>
