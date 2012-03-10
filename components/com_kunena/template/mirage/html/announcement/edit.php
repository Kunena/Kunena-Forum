<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
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
<div class="kmodule">
	<div class="box-wrapper">
		<div class="announcement-kbox kbox box-color box-border box-border_radius box-border_radius-child box-shadow">
			<div class="headerbox-wrapper box-full">
				<div class="header fl">
					<h2 class="header">
						<a class="link" title="<?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') ?>" rel="kannouncement-detailsbox">
							<?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') ?>: <?php echo $this->announcement->exists() ? JText::_('COM_KUNENA_ANN_EDIT') : JText::_('COM_KUNENA_ANN_ADD') ?>
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
						<input type="hidden" name="task" value="save" />
						<?php echo $this->displayInput('id') ?>
						<?php echo JHtml::_( 'form.token' ) ?>

							<ul class="list-unstyled kform announcement-list clearfix">
								<li class="announcement-row box-hover box-hover_list-row">
									<div class="form-label">
										<label>
											<?php echo JText::_('COM_KUNENA_ANN_TITLE') ?>:
										</label>
									</div>
									<div class="form-field">
										<?php echo $this->displayInput('title', 'class="box-width inputbox required"') ?>
									</div>
								</li>
								<li class="announcement-row box-hover box-hover_list-row">
									<div class="form-label">
										<label>
											<?php echo JText::_('COM_KUNENA_ANN_SORTTEXT') ?>:
										</label>
									</div>
									<div class="form-field">
										<?php echo $this->displayInput('sdescription', 'class="box-width textbox required" rows="80" cols="4"') ?>
									</div>
								</li>
								<li class="announcement-row box-hover box-hover_list-row">
									<div class="form-label">
										<label>
											<?php echo JText::_('COM_KUNENA_ANN_LONGTEXT') ?>:
										</label>
									</div>
									<div class="form-field">
										<?php echo $this->displayInput('description', 'class="box-width textbox" rows="80" cols="16"') ?>
									</div>
								</li>
								<li class="announcement-row box-hover box-hover_list-row">
									<div class="form-label">
										<label>
											<?php echo JText::_('COM_KUNENA_ANN_DATE') ?>:
										</label>
									</div>
									<div class="form-field">
										<?php echo $this->displayInput('created', 'addcreated') ?>
									</div>
								</li>
								<li class="announcement-row box-hover box-hover_list-row">
									<div class="form-label">
										<label>
											<?php echo JText::_('COM_KUNENA_ANN_SHOWDATE') ?>:
										</label>
									</div>
									<div class="form-field">
										<?php echo $this->displayInput('showdate') ?>
									</div>
								</li>
								<li class="announcement-row box-hover box-hover_list-row">
									<div class="form-label">
										<label>
											<?php echo JText::_('COM_KUNENA_ANN_PUBLISH') ?>:
										</label>
									</div>
									<div class="form-field">
										<?php echo $this->displayInput('published') ?>
									</div>
								</li>
							</ul>
						<input name="submit" class="kbutton" type="submit" value="<?php echo JText::_('COM_KUNENA_ANN_SAVE') ?>"/>
						<input onclick="javascript:window.history.back();" name="cancel" class="kbutton" type="button" value="<?php echo JText::_('COM_KUNENA_ANN_CANCEL') ?>"/>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>
