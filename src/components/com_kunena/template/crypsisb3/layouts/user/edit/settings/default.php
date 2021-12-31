<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

Text::script('COM_KUNENA_CLEARED');
?>
<h3>
	<?php echo $this->headerText; ?>
</h3>

<table class="table table-bordered table-striped table-hover">
	<tbody>
	<?php foreach ($this->settings as $field)
		:
		?>
		<tr>
			<td class="col-md-3">
				<?php echo $field->label; ?>
			</td>
			<td>
				<?php echo $field->field; ?>
			</td>
		</tr>
	<?php endforeach ?>
	<tr>
		<td class="col-md-3"><?php echo Text::_('COM_KUNENA_USER_SETTINGS_CLEAR'); ?></td>
		<td>
			<button type="button" class="btn btn-default btn-small" onClick="window.localStorage.clear()"
			        data-loading-text="Loading..."><?php echo Text::_('COM_KUNENA_USER_SETTINGS_CLEAR'); ?></button>
		</td>
	</tr>
	</tbody>
</table>
