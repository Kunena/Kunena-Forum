<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Report
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
?>
<div class="container-fluid">
<div class="row-fluid">
 <div class="span2">
	<div><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	</div>
		<!-- Right side -->
			<div class="span10">
            <div class="well well-small" style="min-height:120px;">
                       <div class="nav-header"><?php echo JText::_('COM_KUNENA_REPORT_SYSTEM'); ?></div>
                         <div class="row-striped">
                         <br />	
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="1" />

		<fieldset><?php echo JText::_('COM_KUNENA_REPORT_SYSTEM_DESC'); ?><br /></fieldset>
		<fieldset>
			<div><a href="#" id="link_sel_all" ><?php echo JText::_('COM_KUNENA_REPORT_SELECT_ALL'); ?></a></div>
			<textarea id="report_final" name="report_final" cols="80" rows="15"><?php echo $this->escape($this->systemreport); ?></textarea>
		</fieldset>
		</form>
		</div>
        </div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
</div>
</div>