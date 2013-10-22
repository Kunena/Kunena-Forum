<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<h3>
	<?php echo JText::_('COM_KUNENA_PROFILE_EDIT_SETTINGS_TITLE'); ?>
</h3>

<table class="table table-bordered table-striped table-hover">
	<tbody>
		<?php foreach ($this->settings as $setting) : ?>
			<tr>
				<td class="span3">
					<?php echo $setting->label ?>
				</td>
				<td>
					<?php echo $setting->field ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
