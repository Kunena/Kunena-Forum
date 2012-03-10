<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.formvalidation');
$this->document->addScriptDeclaration('// <![CDATA[
	function kunenaValidate(f) { return document.formvalidator.isValid(f); }
// ]]>');
?>
<div class="box-module">
	<div class="block-wrapper box-color box-border box-border_radius box-shadow">
		<div id="announce" class="block">
			<div class="headerbox-wrapper box-full">
				<div class="header fl">
					<h2 class="header">
						<a class="link" title="<?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') ?>" rel="kannouncement-detailsbox">
							<?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?>: <?php echo $this->announcement->id ? JText::_('COM_KUNENA_ANN_EDIT') : JText::_('COM_KUNENA_ANN_ADD'); ?>
						</a>
					</h2>
				</div>
				<div class="header fr">
					<?php echo JHtml::_('kunenaforum.link', $this->returnUrl, JText::_('COM_KUNENA_ANN_CPANEL'), JText::_('COM_KUNENA_ANN_CPANEL')) ?>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer">
				<div class="announcement-details detailsbox box-border box-border_radius box-shadow" >
					<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement') ?>" class="form-validate" method="post" name="editform" onsubmit="return kunenaValidate(this);">
						<input type="hidden" name="view" value="announcement" />
						<input type="hidden" name="task" value="save" />
						<input type="hidden" name="id" value="<?php echo intval($this->announcement->id) ;?>" />
						<?php echo JHtml::_( 'form.token' ); ?>
							<ul class="list-unstyled kform announcement-list clearfix">
								<li class="announcement-row box-hover box-hover_list-row">
									<div class="form-label">
										<label>
											<?php echo JText::_('COM_KUNENA_ANN_TITLE'); ?>:
										</label>
									</div>
									<div class="form-field">
										<input class="box-width inputbox required" type="text" name="title" value="<?php echo $this->escape($this->announcement->title) ;?>"/>
									</div>
								</li>
								<li class="announcement-row box-hover box-hover_list-row">
									<div class="form-label">
										<label>
											<?php echo JText::_('COM_KUNENA_ANN_SORTTEXT'); ?>:
										</label>
									</div>
									<div class="form-field">
										<textarea class="box-width inputbox required" rows="80" cols="16" name="sdescription"><?php echo $this->escape($this->announcement->sdescription); ?></textarea>
									</div>
								</li>
								<li class="announcement-row box-hover box-hover_list-row">
									<div class="form-label">
										<label>
											<?php echo JText::_('COM_KUNENA_ANN_LONGTEXT'); ?>:
										</label>
									</div>
									<div class="form-field">
										<textarea class="box-width textbox" rows="80" cols="16" name="description"><?php echo $this->escape($this->announcement->description); ?></textarea>
									</div>
								</li>
								<li class="announcement-row box-hover box-hover_list-row">
									<div class="form-label">
										<label>
											<?php echo JText::_('COM_KUNENA_ANN_DATE'); ?>:
										</label>
									</div>
									<div class="form-field">
										<?php echo JHTML::_('calendar', $this->escape($this->announcement->created), 'created', 'addcreated');?>
									</div>
								</li>
								<li class="announcement-row box-hover box-hover_list-row">
									<div class="form-label">
										<label>
											<?php echo JText::_('COM_KUNENA_ANN_SHOWDATE'); ?>:
										</label>
									</div>
									<div class="form-field">
										<select name="showdate">
											<option value="1" <?php echo ($this->announcement->showdate == 1 ? 'selected="selected"' : ''); ?>><?php echo JText::_('COM_KUNENA_ANN_YES'); ?></option>
											<option value="0" <?php echo ($this->announcement->showdate == 0 ? 'selected="selected"' : ''); ?>><?php echo JText::_('COM_KUNENA_ANN_NO'); ?></option>
										</select>
									</div>
								</li>
								<li class="announcement-row box-hover box-hover_list-row">
									<div class="form-label">
										<label>
											<?php echo JText::_('COM_KUNENA_ANN_PUBLISH'); ?>:
										</label>
									</div>
									<div class="form-field">
										<select name="published">
											<option value="1" <?php echo ($this->announcement->published == 1 ? 'selected="selected"' : ''); ?>><?php echo JText::_('COM_KUNENA_ANN_YES'); ?></option>
											<option value="0" <?php echo ($this->announcement->published == 0 ? 'selected="selected"' : ''); ?>><?php echo JText::_('COM_KUNENA_ANN_NO'); ?></option>
										</select>
									</div>
								</li>
							</ul>
						<input name="submit" class="kbutton" type="submit" value="<?php echo JText::_('COM_KUNENA_ANN_SAVE'); ?>"/>
						<input onclick="javascript:window.history.back();" name="cancel" class="kbutton" type="button" value="<?php echo JText::_('COM_KUNENA_ANN_CANCEL'); ?>"/>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>
