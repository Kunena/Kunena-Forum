<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage CleanIPAddresses
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewTools $this */
?>
<div id="kunena" class="admin override">
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<div id="j-sidebar-container" class="span2">
					<div id="sidebar">
						<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?></div>
					</div>
				</div>
				<div id="j-main-container" class="span10">
					<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" id="adminForm" name="adminForm">
						<input type="hidden" name="task" value="cleanupIP" />
						<?php echo JHtml::_( 'form.token' ); ?>

						<fieldset>
							<legend><?php echo JText::_('COM_KUNENA_LEGEND_CLEANUP_IP'); ?></legend>
							<table class="table table-bordered table-striped">
								<tr>
									<td width="20%"><?php echo JText::_('COM_KUNENA_CLEANUP_IP_LEGEND_FROMDAYS') ?></td>
									<td>
										<div class="input-append">
											<input class="span3" type="text" name="cleanup_ip_days" value="30" />
											<span class="add-on"><?php echo JText::_('COM_KUNENA_CLEANUP_IP_LEGEND_DAYS') ?></span>
										</div>
									</td>
								</tr>
							</table>
						</fieldset>
					</form>
				</div>

				<div class="pull-right small">
					<?php echo KunenaVersion::getLongVersionHTML(); ?>
				</div>
			</div>
		</div>
	</div>
</div><?php
