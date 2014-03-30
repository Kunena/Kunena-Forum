<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Report
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewReport $this */

$document = JFactory::getDocument();
$document->addScriptDeclaration("
window.addEvent('domready', function(){
	$('link_sel_all').addEvent('click', function(e){
		$('report_final').select();
	});
});"
);

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
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
					<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="boxchecked" value="1" />

						<fieldset>
							<legend><?php echo JText::_('COM_KUNENA_REPORT_SYSTEM'); ?></legend>
							<table class="table table-bordered table-striped">
								<tr>
									<td>
										<p><?php echo JText::_('COM_KUNENA_REPORT_SYSTEM_DESC'); ?></p>
										<p><a href="#" id="link_sel_all" ><?php echo JText::_('COM_KUNENA_REPORT_SELECT_ALL'); ?></a></p>
										<textarea id="report_final" class="input-block-level" name="report_final" cols="80" rows="15"><?php echo $this->escape($this->systemreport); ?></textarea>
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
