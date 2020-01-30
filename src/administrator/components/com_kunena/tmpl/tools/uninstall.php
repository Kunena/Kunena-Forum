<?php
/**
 * Kunena Component
 *
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
use Kunena\Forum\Administrator\Install\KunenaVersion;
use Kunena\Forum\Libraries\Route\KunenaRoute;

?>

<div class="alert alert-error">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4><?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_ALERTBOX_WARNING') ?></h4>
	<?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_ALERTBOX_DESC') ?>
</div>

<div id="kunena" class="container-fluid">
	<div class="row">
		<div id="j-main-container" class="col-md-12" role="main">
			<div class="card card-block bg-faded p-2">
				<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>"
				      method="post" id="adminForm"
				      name="adminForm">
					<input type="hidden" name="task" value="uninstall"/>
					<?php echo HTMLHelper::_('form.token'); ?>

					<fieldset>
						<legend><?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_TITLE'); ?></legend>
						<table class="table table-bordered table-striped">
							<tr>
								<td colspan="2"><?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_DESC') ?></td>
							</tr>
							<tr>
								<td width="20%"><?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_LOGIN') ?></td>
								<td>
									<div>
										<input class="col-md-3" type="text" name="username" value=""/>
									</div>
								</td>
							</tr>
							<tr>
								<td width="20%"><?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_PASSWORD') ?></td>
								<td>
									<div>
										<input class="col-md-3" type="password" name="password" value=""/>
									</div>
								</td>
							</tr>
							<?php if ($this->isTFAEnabled)
								:
								?>
								<tr>
									<td width="20%"><?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_SECRETKEY') ?></td>
									<td>
										<div>
											<input class="col-md-3" type="text" name="secretkey" value=""/>
										</div>
									</td>
								</tr>
							<?php endif; ?>
							<tr>
								<td></td>
								<td>
									<button type="button" class="btn btn-outline-danger" data-toggle="modal"
									        data-target="#modalconfirmuninstall"><?php echo Text::_('COM_KUNENA_TOOLS_BUTTON_UNINSTALL_PROCESS') ?></button>

									<div class="modal fade" id="modalconfirmuninstall" tabindex="-1" role="dialog"
									     aria-labelledby="modalconfirmuninstalLabel" aria-hidden="true">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title"
													    id="modalconfirmuninstalLabel"><?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_TITLE'); ?></h5>
													<button type="button" class="close" data-dismiss="modal"
													        aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<div class="modal-body">
													<p><?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_DESC') ?></p>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-secondary"
													        data-dismiss="modal"><?php echo Text::_('COM_KUNENA_TOOLS_BUTTON_UNINSTALL_CLOSE') ?></button>
													<button type="submit"
													        class="btn btn-outline-danger"><?php echo Text::_('COM_KUNENA_TOOLS_BUTTON_UNINSTALL_PROCESS') ?></button>
												</div>
											</div>
										</div>
									</div>

								</td>
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
