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

<div>
	<h3><span><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_SETTINGS_TITLE'); ?></span></h3>
</div>
<div>
	<div>
		<table id="kflattable">
			<tbody>
				<?php $i=1; foreach ($this->settings as $setting) : ?>
					<tr class="krow<?php echo (++$i & 1)+1 ?>">
						<td><?php echo $setting->label ?></td>
						<td> <?php echo $setting->field ?> </td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>
