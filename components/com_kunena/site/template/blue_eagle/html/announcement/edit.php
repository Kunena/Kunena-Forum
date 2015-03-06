<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.formvalidation');
$this->document->addScriptDeclaration('// <![CDATA[
	function kunenaValidate(f) { return document.formvalidator.isValid(f); }
// ]]>');
?>
<div class="kblock kannouncement">
	<div class="kheader">
		<h2><?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') ?>: <?php echo $this->announcement->exists() ? JText::_('COM_KUNENA_ANN_EDIT') : JText::_('COM_KUNENA_ANN_ADD') ?></h2>
	</div>
	<div class="kcontainer" id="kannouncement">
		<div class="kactions"><?php echo JHtml::_('kunenaforum.link', $this->returnUrl, JText::_('COM_KUNENA_ANN_CPANEL'), JText::_('COM_KUNENA_ANN_CPANEL')) ?></div>
		<div class="kbody">
			<div class="kanndesc">
				<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement') ?>" class="form-validate" method="post" name="editform" onsubmit="return kunenaValidate(this);">
					<input type="hidden" name="task" value="save" />
					<?php echo $this->displayInput('id') ?>
					<?php echo JHtml::_( 'form.token' ) ?>

				<div>
					<label>
						<?php echo JText::_('COM_KUNENA_ANN_TITLE') ?>:
						<?php echo $this->displayInput('title', 'class="klarge required"') ?>
					</label>
					<label>
						<?php echo JText::_('COM_KUNENA_ANN_SORTTEXT') ?>:
						<?php echo $this->displayInput('sdescription', 'class="ksmall required" rows="80" cols="4"') ?>
					</label>
				</div>
				<div>
					<label>
						<?php echo JText::_('COM_KUNENA_ANN_LONGTEXT') ?>:
						<?php echo $this->displayInput('description', 'class="klarge" rows="80" cols="16"') ?>
					</label>
				</div>
				<div>
					<label>
						<?php echo JText::_('COM_KUNENA_ANN_DATE') ?>:
						<?php echo $this->displayInput('created', 'addcreated', 'kanncreated') ?>
					</label>
					<label>
						<?php echo JText::_('COM_KUNENA_ANN_SHOWDATE') ?>:
						<?php echo $this->displayInput('showdate') ?>
					</label>
					<label>
						<?php echo JText::_('COM_KUNENA_ANN_PUBLISH') ?>:
						<?php echo $this->displayInput('published') ?>
					</label>
				</div>
					<input name="submit" class="kbutton" type="submit" value="<?php echo JText::_('COM_KUNENA_SAVE') ?>"/>
					<input onclick="window.history.back();" name="cancel" class="kbutton" type="button" value="<?php echo JText::_('COM_KUNENA_CANCEL') ?>"/>
				</form>
			</div>
		</div>
	</div>
</div>
