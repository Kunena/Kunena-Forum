<?php
/**
 * @version $Id: listcat.php 3901 2010-11-15 14:14:02Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

?>
<div class="kblock kreport">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_COM_A_REPORT') ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
			<div id="kreport-container">
				<form method = "post" action = "<?php echo CKunenaLink::GetReportURL(); ?>" class="kform-report">
					<label for="kreport-reason"><?php echo JText::_('COM_KUNENA_REPORT_REASON') ?>:</label>
					<input type = "text" name = "reason" class = "inputbox" size = "30" id="kreport-reason"/>
					<label for="kreport-msg"><?php echo JText::_('COM_KUNENA_REPORT_MESSAGE') ?>:</label>
					<textarea id = "kreport-msg" name = "text" cols = "40" rows = "10" class = "inputbox"></textarea>
					<input type = "hidden" name = "id" value = "<?php echo intval($this->id); ?>"/>
					<input type = "hidden" name = "catid" value = "<?php echo intval($this->catid); ?>"/>
					<input type="hidden" name="option" value="com_kunena" />
					<input type="hidden" name="view" value="report" />
					<input type="hidden" name="task" value="submit" />
					<?php echo JHTML::_( 'form.token' ); ?>
					<input class="kbutton ks" type = "submit" name = "Submit" value = "<?php echo JText::_('COM_KUNENA_REPORT_SEND') ?>"/>
				</form>
			</div>
		</div>
	</div>
</div>