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
	<legend><?php echo JText::_('Communication'); ?></legend>

	<table width="100%" class="admintable" cellspacing="1">
		<tr>
			<td width="40%" class="key">
				<label for="config_regonly" class="hasTip" title="Registered Users Only::Set to 'Yes' to allow only registered users to use the Forum (view &amp; post). Set to 'No' to allow any visitor to use the Forum.">Registered Users Only</label>
			</td>
			<td>
				<select name="config[regonly]" id="config_regonly">
					<option value="0"<?php echo (($this->options->get('regonly', 1) == 0) ? ' selected="selected"' : ''); ?>>No</option>
					<option value="1"<?php echo (($this->options->get('regonly', 1) == 1) ? ' selected="selected"' : ''); ?>>Yes</option>
				</select>
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_flood_protection" class="hasTip" title="Flood Protection::The amount of seconds a user has to wait between two consecutive posts. Set to 0 (zero) to turn Flood Protection off. NOTE: Flood Protection can cause degradation of performance.">Flood Protection</label>
			</td>
			<td>
				<input type="text" name="config[flood_protection]" id="config_flood_protection" value="<?php echo $this->options->get('flood_protection', 10); ?>" size="5" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_flood_protection" class="hasTip" title="Flood Protection::The amount of seconds a user has to wait between two consecutive posts. Set to 0 (zero) to turn Flood Protection off. NOTE: Flood Protection can cause degradation of performance.">Flood Protection</label>
			</td>
			<td>
				<input type="text" name="config[flood_protection]" id="config_flood_protection" value="<?php echo $this->options->get('flood_protection', 10); ?>" size="5" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_flood_protection" class="hasTip" title="Flood Protection::The amount of seconds a user has to wait between two consecutive posts. Set to 0 (zero) to turn Flood Protection off. NOTE: Flood Protection can cause degradation of performance.">Flood Protection</label>
			</td>
			<td>
				<input type="text" name="config[flood_protection]" id="config_flood_protection" value="<?php echo $this->options->get('flood_protection', 10); ?>" size="5" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_flood_protection" class="hasTip" title="Flood Protection::The amount of seconds a user has to wait between two consecutive posts. Set to 0 (zero) to turn Flood Protection off. NOTE: Flood Protection can cause degradation of performance.">Flood Protection</label>
			</td>
			<td>
				<input type="text" name="config[flood_protection]" id="config_flood_protection" value="<?php echo $this->options->get('flood_protection', 10); ?>" size="5" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_flood_protection" class="hasTip" title="Flood Protection::The amount of seconds a user has to wait between two consecutive posts. Set to 0 (zero) to turn Flood Protection off. NOTE: Flood Protection can cause degradation of performance.">Flood Protection</label>
			</td>
			<td>
				<input type="text" name="config[flood_protection]" id="config_flood_protection" value="<?php echo $this->options->get('flood_protection', 10); ?>" size="5" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="key">
				<label for="config_flood_protection" class="hasTip" title="Flood Protection::The amount of seconds a user has to wait between two consecutive posts. Set to 0 (zero) to turn Flood Protection off. NOTE: Flood Protection can cause degradation of performance.">Flood Protection</label>
			</td>
			<td>
				<input type="text" name="config[flood_protection]" id="config_flood_protection" value="<?php echo $this->options->get('flood_protection', 10); ?>" size="5" />
			</td>
		</tr>
	</table>
</fieldset>
