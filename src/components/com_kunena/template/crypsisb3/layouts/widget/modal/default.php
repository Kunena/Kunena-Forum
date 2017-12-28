<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$id = ' id="' . $this->id . '"';
$class = 'class="modal fade"';
$name = ' name="' . $this->name . '"';
$label  = $this->label;
$description  = $this->description;
$data = $this->data;
$form = $this->form;
?>
<div <?php echo $class . $id?> tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
				<h3><?php echo $label ?></h3>
			</div>
			<div class="modal-body">
				<p><?php echo $description ?></p>
				<div class="control-group">
					<div class="controls">
						<textarea <?php echo $name ?> style="resize: none; text-align: left;" class="input-block-level" rows="3" maxlength="255"><?php echo $data; ?></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('<?php echo $form ?> ');">
					<?php echo JText::_('JSUBMIT'); ?>
				</button>
				<button class="btn btn-default" type="button" data-dismiss="modal">
					<?php echo JText::_('JCANCEL'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
