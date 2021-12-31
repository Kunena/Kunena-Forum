<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$id          = ' id="' . $this->id . '"';
$class       = 'class="modal fade"';
$name        = ' name="' . $this->name . '"';
$label       = $this->label;
$description = $this->description;
$data        = $this->data;
$form        = $this->form;
?>
<div <?php echo $class . $id ?> tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
								style="display: none;">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title"><?php echo $label ?></h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p><?php echo $description ?></p>
				<div class="control-group">
					<div class="controls">
						<textarea <?php echo $name ?> style="resize: none; text-align: left;" class="input-block-level"
													  rows="3"
													  maxlength="255"><?php echo $data; ?></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-outline-primary border" type="submit" id="btn_statustext">
					<?php echo Text::_('JSUBMIT'); ?>
				</button>
				<button class="btn btn-outline-primary border" type="button" data-dismiss="modal">
					<?php echo Text::_('JCANCEL'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
