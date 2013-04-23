<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

foreach ($this->fieldsets as $name => $fieldset) :
	echo '<div class="tab-pane" id="options-'.$name.'">';
	$label = !empty($fieldset->label) ? $fieldset->label : 'COM_PLUGINS_'.$name.'_FIELDSET_LABEL';
	if (isset($fieldset->description) && trim($fieldset->description)) :
		?>
		<p class="tip"><?php echo $this->escape(JText::_($fieldset->description)) ?></p>;
	<?php
	endif;
	?>
	<?php $hidden_fields = ''; ?>
	<table class="table table-striped">
		<thead>
		<tr>
			<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
			<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
		</tr>
		</thead>
		<tbody>
			<?php foreach ($this->form->getFieldset($name) as $field) : ?>
				<?php if (!$field->hidden) : ?>
					<tr>
						<td>
							<?php echo $field->label; ?>
						</td>
						<td>
							<?php echo $field->input; ?>
						</td>
					</tr>
				<?php else : $hidden_fields .= $field->input; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $hidden_fields; ?>
	<?php echo '</div>'; // .tab-pane div ?>
<?php endforeach; ?>