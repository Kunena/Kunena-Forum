<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage CleanIPAdresses
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

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
						<input type="hidden" name="task" value="cleanIPAdresses" />
						<?php echo JHtml::_( 'form.token' ); ?>

						<fieldset>
							<legend><?php echo JText::_('COM_KUNENA_LEGEND_CLEAN_ADRESSES_IP'); ?></legend>
							<table class="table table-bordered table-striped">
								<tr>
									<td colspan="2"><?php echo JText::_('COM_KUNENA_TOOLS_DESC_CLEAN_IP_ADRESSES') ?></td>
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
