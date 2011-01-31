<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

$this->id = JRequest::getInt('id', 0);
$this->catid = JRequest::getInt('catid', 0);
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
					<input type = "hidden" name = "do" value = "sendreport"/>
					<?php echo JHTML::_( 'form.token' ); ?>
					<input class="kbutton ks" type = "submit" name = "Submit" value = "<?php echo JText::_('COM_KUNENA_REPORT_SEND') ?>"/>
				</form>
			</div>
		</div>
	</div>
</div>