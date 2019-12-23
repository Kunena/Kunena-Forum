<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Template
 * @subpackage      Prune
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>

<div id="kunena" class="container-fluid">
	<div class="row">
		<div id="j-main-container" class="col-md-12" role="main">
			<div class="card card-block bg-faded p-2">
				<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>"
					  method="post" id="adminForm"
					  name="adminForm">
					<input type="hidden" name="task" value="cleanupip"/>
					<?php echo HTMLHelper::_('form.token'); ?>

					<fieldset>
						<legend><?php echo Text::_('COM_KUNENA_LEGEND_CLEANUP_IP'); ?></legend>
						<table class="table table-bordered table-striped">
							<tr>
								<td width="20%"><?php echo Text::_('COM_KUNENA_CLEANUP_IP_LEGEND_FROMDAYS') ?></td>
								<td>
									<div class="input-append">
										<input class="col-md-3" type="text" name="cleanup_ip_days" value="30"/>
										<span class="add-on"><?php echo Text::_('COM_KUNENA_CLEANUP_IP_LEGEND_DAYS') ?></span>
									</div>
								</td>
							</tr>
							<tr>
								<td width="20%"><?php echo Text::_('COM_KUNENA_DELETE_USERS_IP') ?></td>
								<td width="10%"><input type="checkbox" name="deleteipusers" value="1"/></td>
								<td width="79%"><?php echo Text::_('COM_KUNENA_DELETE_USERS_IP_DESC'); ?></td>
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
