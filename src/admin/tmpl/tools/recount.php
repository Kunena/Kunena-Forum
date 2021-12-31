<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      SyncUsers
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Version\KunenaVersion;
use Kunena\Forum\Libraries\Route\KunenaRoute;

Text::script('COM_KUNENA_MODAL_CLOSE');
Text::script('COM_KUNENA_AJAXMODAL_START_HEADER');
Text::script('COM_KUNENA_AJAXMODAL_START_BODY');
Text::script('COM_KUNENA_AJAXMODAL_ERROR_UNKNOWN_HEADER');
Text::script('COM_KUNENA_AJAXMODAL_ERROR_RESPONSE_BODY');
Text::script('COM_KUNENA_AJAXMODAL_ERROR_TIMEOUT_HEADER');
Text::script('COM_KUNENA_AJAXMODAL_ERROR_TIMEOUT_BODY');
Text::script('COM_KUNENA_AJAXMODAL_ERROR_CANCEL_HEADER');
Text::script('COM_KUNENA_AJAXMODAL_ERROR_ABORT_BODY');
Text::script('COM_KUNENA_AJAXMODAL_ERROR_ABORT_HEADER');
Text::script('COM_KUNENA_AJAXMODAL_ERROR_UNKNOWN_HEADER');
Text::script('COM_KUNENA_AJAXMODAL_ERROR_UNKNOWN_BODY');

$this->document->addScript(Uri::root() . 'administrator\components\com_kunena\template\tools\recount.js');
?>

<div id="kunena" class="container-fluid">
	<div class="row">
		<div id="j-main-container" class="col-md-12" role="main">
			<div class="card card-block bg-faded p-2">
				<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>"
					  method="post" id="adminForm"
					  name="adminForm">
					<input type="hidden" name="task" value="recount"/>
					<?php echo HTMLHelper::_('form.token'); ?>

					<fieldset>
						<legend><?php echo Text::_('COM_KUNENA_A_RECOUNT'); ?></legend>
						<table class="table table-bordered table-striped">
							<tr>
								<td><?php echo Text::_('COM_KUNENA_A_RECOUNT_TOPICS'); ?></td>
								<td><input type="checkbox" checked="checked" name="topics" value="1"/></td>
								<td><?php echo Text::_('COM_KUNENA_A_RECOUNT_TOPICS_DESC'); ?></td>
							</tr>
							<tr>
								<td><?php echo Text::_('COM_KUNENA_A_RECOUNT_USERTOPICS'); ?></td>
								<td><input type="checkbox" checked="checked" name="usertopics" value="1"/></td>
								<td><?php echo Text::_('COM_KUNENA_A_RECOUNT_USERTOPICS_DESC'); ?></td>
							</tr>
							<tr>
								<td><?php echo Text::_('COM_KUNENA_A_RECOUNT_CATEGORIES'); ?></td>
								<td><input type="checkbox" checked="checked" name="categories" value="1"/></td>
								<td><?php echo Text::_('COM_KUNENA_A_RECOUNT_CATEGORIES_DESC'); ?></td>
							</tr>
							<tr>
								<td><?php echo Text::_('COM_KUNENA_A_RECOUNT_USERS'); ?></td>
								<td><input type="checkbox" checked="checked" name="users" value="1"/></td>
								<td><?php echo Text::_('COM_KUNENA_A_RECOUNT_USERS_DESC'); ?></td>
							</tr>
						</table>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>

<!-- Modal -->
<div id="recountModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="recountModalLabel"
	 aria-hidden="true"
	 data-backdrop="static" data-keyboard="false">
	<div class="modal-header">
		<button type="button" class="close recount-close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
		<h3></h3>
	</div>
	<div class="modal-body">
		<p></p>
		<div class="progress progress-striped">
			<div class="bar"></div>
		</div>
		<div class="modal-error"></div>
	</div>
	<div class="modal-footer">
		<button class="btn recount-close" data-bs-dismiss="modal"
				aria-hidden="true"><?php echo Text::_('COM_KUNENA_MODAL_CLOSE'); ?></button>
	</div>
</div>
