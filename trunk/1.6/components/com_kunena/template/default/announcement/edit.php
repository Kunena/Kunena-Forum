<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/
// Dont allow direct linking
defined( '_JEXEC' ) or die();

$document = JFactory::getDocument();
$document->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . stripslashes($this->config->board_title));
$calendar = JHTML::_('calendar', $this->created, 'created', 'addcreated');
?>
<script type="text/javascript">
	<!--
	function validate_form()
	{
		valid=true;

		if (document.editform.title.value == "")
		{
			alert("Please fill in the 'Title' box.");
			valid=false;
		}

		if (document.editform.sdescription.value == "")
		{
			alert("Please fill in the 'Short Desc' box.");
			valid=false;
		}

		return valid;
	}
			//-->
</script>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<h1><?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?>: <?php echo $this->id ? JText::_('COM_KUNENA_ANN_EDIT') : JText::_('COM_KUNENA_ANN_ADD'); ?> | <?php echo CKunenaLink::GetAnnouncementLink('show',NULL, JText::_('COM_KUNENA_ANN_CPANEL'), JText::_('COM_KUNENA_ANN_CPANEL')); ?></h1>
<table class="kblocktable" id="kannouncement">
	<tbody id="announcement_tbody">
		<tr>
			<td class="kanndesc" valign="top">
				<form action="<?php echo CKunenaLink::GetAnnouncementURL('doedit'); ?>" method="post" name="editform" onSubmit="return validate_form ( );">
					<strong><?php echo JText::_('COM_KUNENA_ANN_TITLE'); ?>:</strong>
					<br/>
					<input type="text" name="title" size="40" maxlength="150" value="<?php echo $this->escape($this->announcement->title) ;?>"/>
					<br/>
					<strong><?php echo JText::_('COM_KUNENA_ANN_SORTTEXT'); ?>:</strong>
					<br/>
					<textarea id="textarea1" name="sdescription" rows="8" cols="60" style="width:95%; height:300px;"><?php echo $this->escape($this->announcement->sdescription); ?></textarea>
					<br/>
					<strong><?php echo JText::_('COM_KUNENA_ANN_LONGTEXT'); ?>:</strong>
					<br/>
					<textarea id="textarea2" name="description" rows="30" cols="60" style="width:95%; height:550px;"><?php echo $this->escape($this->announcement->description); ?></textarea>
					<br/>
					<strong><?php echo JText::_('COM_KUNENA_ANN_DATE'); ?>:</strong>
					<?php echo $calendar;?>
					<br/>
					<strong><?php echo JText::_('COM_KUNENA_ANN_SHOWDATE'); ?>: &nbsp;&nbsp;&nbsp;</strong>
					<select name="showdate">
						<option value="1"<?php echo ($this->showdate == 1 ? 'selected="selected"' : ''); ?>><?php echo JText::_('COM_KUNENA_ANN_YES'); ?></option>
						<option value="0"<?php echo ($this->showdate == 0 ? 'selected="selected"' : ''); ?>><?php echo JText::_('COM_KUNENA_ANN_NO'); ?></option>
					</select>
					<br/>
					<strong><?php echo JText::_('COM_KUNENA_ANN_PUBLISH'); ?>: &nbsp;&nbsp;&nbsp;</strong>
					<select name="published">
						<option value="1"<?php echo ($this->published == 1 ? 'selected="selected"' : ''); ?>><?php echo JText::_('COM_KUNENA_ANN_YES'); ?></option>
						<option value="0"<?php echo ($this->published == 0 ? 'selected="selected"' : ''); ?>><?php echo JText::_('COM_KUNENA_ANN_NO'); ?></option>
					</select>
					<br/>
					<input type='hidden' name="do" value="doedit"/>
					<input type='hidden' name="id" value="<?php echo $this->id ;?>"/>
					<input name="submit" type="submit" value="<?php echo JText::_('COM_KUNENA_ANN_SAVE'); ?>"/>
				</form>
			</td>
		</tr>
	</tbody>
</table>
</div>
</div>
</div>
</div>
</div>