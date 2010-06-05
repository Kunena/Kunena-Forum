<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

$this->id 		= JRequest::getInt('id', 0);
$this->catid 		= JRequest::getInt('catid', 0);

?>

 <h1><?php echo JText::_('COM_KUNENA_COM_A_REPORT') ?></h1>
<div class = "k-bt-cvr1">
    <div class = "k_bt_cvr2">
        <div class = "k_bt_cvr3">
            <div class = "k_bt_cvr4">
                <div class = "k_bt_cvr5">
	                <div id="kreport-container">
		                 <form method = "post" action = "<?php echo CKunenaLink::GetReportURL(); ?>" class="kform-report">

							<label for="kreport-reason"><?php echo JText::_('COM_KUNENA_REPORT_REASON') ?>:</label>
							<input type = "text" name = "reason" class = "inputbox" size = "30" id="kreport-reason"/>

							<label for="kreport-msg"><?php echo JText::_('COM_KUNENA_REPORT_MESSAGE') ?>:</label>

							<textarea id = "kreport-msg" name = "text" cols = "40" rows = "10" class = "inputbox"></textarea>
							<input type = "hidden" name = "id" value = "<?php echo $this->id; ?>"/>
							<input type = "hidden" name = "catid" value = "<?php echo $this->catid; ?>"/>
							<input type = "hidden" name = "do" value = "sendreport"/>

							<input class="kbutton ks" type = "submit" name = "Submit" value = "<?php echo JText::_('COM_KUNENA_REPORT_SEND') ?>"/>
						</form>
	                </div>
                </div>
            </div>
        </div>
    </div>
</div>

