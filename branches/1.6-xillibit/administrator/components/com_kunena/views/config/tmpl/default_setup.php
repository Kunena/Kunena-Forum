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

	<legend>Forum Settings</legend>
	<table class="admintable" cellspacing="1">
	<tbody>
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
		<tr>
			<td width="40%" class="key">
				<label for="config_avatar_src" class="hasTip" title="Forum Offline::...">Forum Offline</label>
			</td>
			<td>
				<select name="config[board_offline]" id="config_board_offline">
					<option value="no"<?php echo (($this->options->get('board_offline', 'kunena') == 'no') ? ' selected="selected"' : ''); ?>>No</option>
					<option value="yes"<?php echo (($this->options->get('board_offline', 'kunena') == 'yes') ? ' selected="selected"' : ''); ?>>Yes</option>
				</select>
			</td>
		</tr>

	</tbody>
	</table>
	<legend>Layout Settings</legend>
	<table class="admintable" cellspacing="1">
	<tbody>

		<tr>
			<td width="40%" class="key">
				<label for="config_threads_per_page" class="hasTip" title="Threads per Page::Number of threads to be displayed per page.">Threads per Page</label>
			</td>
			<td>
				<input type="text" name="config[threads_per_page]" id="config_threads_per_page" value="<?php echo $this->options->get('threads_per_page'); ?>" size="5" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_messages_per_page" class="hasTip" title="Messages per Page::Number of messages to be displayed per page.">Messages per Page</label>
			</td>
			<td>
				<input type="text" name="config[messages_per_page]" id="config_messages_per_page" value="<?php echo $this->options->get('messages_per_page'); ?>" size="5" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_new_indicator" class="hasTip" title="New Indicator::If set, characters will highlight new messages.">New Indicator</label>
			</td>
			<td>
				<input type="text" name="config[new_indicator]" id="config_new_indicator" value="<?php echo $this->options->get('new_indicator'); ?>" size="5" />
			</td>
		</tr>





	</tbody>
	</table>
</fieldset>
