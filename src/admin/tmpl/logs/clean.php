<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Logs
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Version\KunenaVersion;
use Kunena\Forum\Libraries\Route\KunenaRoute;

?>

<div id="kunena" class="container-fluid">
	<div class="row">
		<div id="j-main-container" class="col-md-12" role="main">
			<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=logs') ?>"
				  method="post"
				  id="adminForm"
				  name="adminForm">
				<input type="hidden" name="task" value="clean"/>
				<?php echo HTMLHelper::_('form.token'); ?>

				<fieldset>
					<legend><?php echo Text::_('COM_KUNENA_LOG_MANAGER'); ?></legend>
					<table class="table table-bordered table-striped">
						<tr>
							<td colspan="2"><?php echo Text::_('COM_KUNENA_LOG_CLEAN_DESC') ?></td>
						</tr>
						<tr>
							<td width="20%"><?php echo Text::_('COM_KUNENA_LOG_CLEAN_FROM') ?></td>
							<td>
								<div class="input-append">
									<label>
										<input class="col-md-3" type="text" name="clean_days" value="30"/>
									</label>
									<span class="add-on"><?php echo Text::_('COM_KUNENA_LOG_CLEAN_FROM_DAYS') ?></span>
								</div>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
