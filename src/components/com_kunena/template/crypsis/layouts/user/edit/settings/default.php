<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;
?>
<h3>
	<?php echo $this->headerText; ?>
</h3>

<table class="table table-bordered table-striped table-hover">
	<tbody>
		<?php foreach ($this->settings as $field) : ?>
			<tr>
				<td class="span3">
					<?php echo $field->label; ?>
				</td>
				<td>
					<?php echo $field->field; ?>
				</td>
			</tr>
		<?php endforeach ?>
		<tr>
			<td class="span3"><?php echo JText::_('COM_KUNENA_USER_SETTINGS_CLEAR');?></td>
				<td>
					<button type="button" class="btn btn-small" onClick="window.localStorage.clear()" data-loading-text="Loading..."><?php echo JText::_('COM_KUNENA_USER_SETTINGS_CLEAR');?></button>
				</td>
		</tr>
	</tbody>
</table>
