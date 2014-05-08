<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

?>
<div class="kblock kreport">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_REPORT_TO_MODERATOR') ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
			<div id="kreport-container">
				<form method="post" action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" class="kform-report">
					<input type="hidden" name="view" value="topic" />
					<input type="hidden" name="task" value="report" />
					<input type="hidden" name="catid" value="<?php echo intval($this->catid); ?>"/>
					<input type="hidden" name="id" value="<?php echo intval($this->id); ?>"/>
					<input type="hidden" name="mesid" value="<?php echo intval($this->mesid); ?>"/>
					<?php echo JHtml::_( 'form.token' ); ?>

					<label for="kreport-reason"><?php echo JText::_('COM_KUNENA_REPORT_REASON') ?>:</label>
					<input type="text" name="reason" class="inputbox" size="30" id="kreport-reason"/>
					<label for="kreport-msg"><?php echo JText::_('COM_KUNENA_REPORT_MESSAGE') ?>:</label>
					<textarea id="kreport-msg" name="text" cols="40" rows="10" class="inputbox"></textarea>
					<input class="kbutton ks" type="submit" name="Submit" value="<?php echo JText::_('COM_KUNENA_REPORT_SEND') ?>"/>
					<input class="kbutton ks" onclick="history.back()" type="button" name="button" value="<?php echo JText::_('COM_KUNENA_BACK') ?>"/>
				</form>
			</div>
		</div>
	</div>
</div>
