<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.formvalidation');
$this->document->addScriptDeclaration('// <![CDATA[
	function kunenaValidate(f) { return document.formvalidator.isValid(f); }
// ]]>');
?>
<div class="kmodule announcement-edit">
	<div class="kbox-wrapper kbox-full">
		<div class="announcement-edit-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
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
				<div class="announcement-details detailsbox kbox-border kbox-border_radius kbox-shadow" >
					<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement') ?>" class="form-validate" method="post" name="editform" onsubmit="return kunenaValidate(this);">
						<input type="hidden" name="task" value="save" />
						<?php echo $this->displayInput('id') ?>
						<?php echo JHtml::_( 'form.token' ) ?>

							<ul class="announcements-list list-unstyled kform">
								<li class="announcement-row kbox-hover kbox-hover_list-row">
									<div class="form-label">
										<div class="innerspacer-left kbox-full">
											<label>
												<?php echo JText::_('COM_KUNENA_ANN_TITLE') ?>:
											</label>
										</div>
									</div>
									<div class="form-field">
										<div class="innerspacer kbox-full">
											<?php echo $this->displayInput('title', 'class="kbox-width inputbox required"') ?>
										</div>
									</div>
								</li>
								<li class="announcement-row kbox-hover kbox-hover_list-row">
									<div class="form-label">
										<div class="innerspacer-left kbox-full">
											<label>
												<?php echo JText::_('COM_KUNENA_ANN_SORTTEXT') ?>:
											</label>
										</div>
									</div>
									<div class="form-field">
										<div class="innerspacer kbox-full">
											<?php echo $this->displayInput('sdescription', 'class="kbox-width txtarea hasTip required" rows="80" cols="4"') ?>
										</div>
									</div>
								</li>
								<li class="announcement-row kbox-hover kbox-hover_list-row">
									<div class="form-label">
										<div class="innerspacer-left kbox-full">
											<label>
												<?php echo JText::_('COM_KUNENA_ANN_LONGTEXT') ?>:
											</label>
										</div>
									</div>
									<div class="form-field">
										<div class="innerspacer kbox-full">
											<?php echo $this->displayInput('description', 'class="kbox-width txtarea hasTip" rows="80" cols="16"') ?>
										</div>
									</div>
								</li>
								<li class="announcement-row kbox-hover kbox-hover_list-row">
									<div class="form-label">
										<div class="innerspacer-left kbox-full">
											<label>
												<?php echo JText::_('COM_KUNENA_ANN_DATE') ?>:
											</label>
										</div>
									</div>
									<div class="form-field">
										<div class="innerspacer kbox-full">
											<?php echo $this->displayInput('created', 'addcreated', 'kanncreated') ?>
										</div>
									</div>
								</li>
								<li class="announcement-row kbox-hover kbox-hover_list-row">
									<div class="form-label">
										<div class="innerspacer-left kbox-full">
											<label>
												<?php echo JText::_('COM_KUNENA_ANN_SHOWDATE') ?>:
											</label>
										</div>
									</div>
									<div class="form-field">
										<div class="innerspacer kbox-full">
											<?php echo $this->displayInput('showdate') ?>
										</div>
									</div>
								</li>
								<li class="announcement-row kbox-hover kbox-hover_list-row">
									<div class="form-label">
										<div class="innerspacer-left kbox-full">
											<label>
												<?php echo JText::_('COM_KUNENA_ANN_PUBLISH') ?>:
											</label>
										</div>
									</div>
									<div class="form-field">
										<div class="innerspacer kbox-full">
											<?php echo $this->displayInput('published') ?>
										</div>
									</div>
								</li>
							</ul>
						<input name="submit" class="kbutton" type="submit" value="<?php echo JText::_('COM_KUNENA_SAVE') ?>"/>
						<input onclick="javascript:window.history.back();" name="cancel" class="kbutton" type="button" value="<?php echo JText::_('COM_KUNENA_CANCEL') ?>"/>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

