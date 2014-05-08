<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage PurgeRe
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
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
						<input type="hidden" name="task" value="purgeReStatements" />
						<?php echo JHtml::_( 'form.token' ); ?>

						<fieldset>
							<legend><?php echo JText::_('COM_KUNENA_A_PURGE_RE_STATEMENTS'); ?></legend>
							<table class="table table-bordered table-striped">
								<tr>
									<td>
										<p><?php echo JText::_('COM_KUNENA_A_PURGE_ENTER_RE_STATEMENTS'); ?></p>
										<input type="text" name="re_string" value="" />
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
</div>
