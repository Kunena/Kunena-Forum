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
$document->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . $this->config->board_title);
$calendar = JHTML::_('calendar', $this->escape($this->created), 'created', 'addcreated');
JHTML::_('behavior.formvalidation');

$document->addScriptDeclaration('
	function myValidate(f) {
	if (document.formvalidator.isValid(f)) {
		return true;
	}
	return false;
}
');
?>
<div class="kblock kannouncement">
	<div class="ktitle">
		<h1><?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?>: <?php echo $this->id ? JText::_('COM_KUNENA_ANN_EDIT') : JText::_('COM_KUNENA_ANN_ADD'); ?></h1>
	</div>
	<div class="kcontainer" id="kannouncement">
		<div class="kactions"><?php echo CKunenaLink::GetAnnouncementLink('show',NULL, JText::_('COM_KUNENA_ANN_CPANEL'), JText::_('COM_KUNENA_ANN_CPANEL')); ?></div>
		<div class="kbody">
			<div class="kanndesc">
				<form class="form-validate" action="<?php echo CKunenaLink::GetAnnouncementURL('doedit'); ?>" method="post" name="editform" onsubmit="return myValidate(this);">
					<?php echo JHTML::_( 'form.token' ); ?>
					<label>
						<?php echo JText::_('COM_KUNENA_ANN_TITLE'); ?>:
						<input class="klarge required" type="text" name="title" value="<?php echo $this->escape($this->announcement->title) ;?>"/>
					</label>
					<label>
						<?php echo JText::_('COM_KUNENA_ANN_SORTTEXT'); ?>:
						<textarea class="ksmall required" rows="80" cols="4" name="sdescription"><?php echo $this->escape($this->announcement->sdescription); ?></textarea>
					</label>
					<label>
						<?php echo JText::_('COM_KUNENA_ANN_LONGTEXT'); ?>:
						<textarea class="klarge" rows="80" cols="16" name="description"><?php echo $this->escape($this->announcement->description); ?></textarea>
					</label>
					<label>
						<?php echo JText::_('COM_KUNENA_ANN_DATE'); ?>:
						<?php echo $calendar;?>
					</label>
					<label>
						<?php echo JText::_('COM_KUNENA_ANN_SHOWDATE'); ?>:
						<select name="showdate">
							<option value="1" <?php echo ($this->showdate == 1 ? 'selected="selected"' : ''); ?>><?php echo JText::_('COM_KUNENA_ANN_YES'); ?></option>
							<option value="0" <?php echo ($this->showdate == 0 ? 'selected="selected"' : ''); ?>><?php echo JText::_('COM_KUNENA_ANN_NO'); ?></option>
						</select>
					</label>
					<label>
						<?php echo JText::_('COM_KUNENA_ANN_PUBLISH'); ?>:
						<select name="published">
							<option value="1" <?php echo ($this->published == 1 ? 'selected="selected"' : ''); ?>><?php echo JText::_('COM_KUNENA_ANN_YES'); ?></option>
							<option value="0" <?php echo ($this->published == 0 ? 'selected="selected"' : ''); ?>><?php echo JText::_('COM_KUNENA_ANN_NO'); ?></option>
						</select>
					</label>
					<input type='hidden' name="do" value="doedit"/>
					<input type='hidden' name="id" value="<?php echo $this->id ;?>"/>
					<input name="submit" type="submit" value="<?php echo JText::_('COM_KUNENA_ANN_SAVE'); ?>"/>
				</form>
			</div>
		</div>
	</div>
</div>