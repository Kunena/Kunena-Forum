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
				<label for="config_avatar_src" class="hasTip" title="Avatars::...">Avatars</label>
			</td>
			<td>
				<select name="config[avatar_src]" id="config_avatar_src">
					<option value="kunena"<?php echo (($this->options->get('avatar_src', 'kunena') == 'kunena') ? ' selected="selected"' : ''); ?>>Kunena</option>
					<option value="cb"<?php echo (($this->options->get('avatar_src', 'kunena') == 'cb') ? ' selected="selected"' : ''); ?>>Community Builder</option>
					<option value="jomsocial"<?php echo (($this->options->get('avatar_src', 'kunena') == 'jomsocial') ? ' selected="selected"' : ''); ?>>JomSocial</option>
					<option value="gravatar"<?php echo (($this->options->get('avatar_src', 'kunena') == 'gravatar') ? ' selected="selected"' : ''); ?>>Gravatar</option>
				</select>
			</td>
		</tr>
	</table>
</fieldset>
