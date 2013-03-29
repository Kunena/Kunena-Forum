<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule topic-report">
	<div class="kbox-wrapper kbox-full">
		<div class="topic-report-kbox kbox kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><?php echo JText::_('COM_KUNENA_REPORT_TO_MODERATOR') ?></h2>
				</div>
			</div>
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" class="kform kform-report">
				<input type="hidden" name="view" value="topic" />
				<input type="hidden" name="task" value="report" />
				<input type="hidden" name="catid" value="<?php echo intval($this->catid); ?>"/>
				<input type="hidden" name="id" value="<?php echo intval($this->id); ?>"/>
				<input type="hidden" name="mesid" value="<?php echo intval($this->mesid); ?>"/>
				<?php echo JHtml::_( 'form.token' ); ?>
				<div class="detailsbox-wrapper innerspacer">
					<div class="detailsbox kbox-full kbox-border kbox-border_radius kbox-shadow">
						<ul class="kform clearfix">
							<li class="krow-odd">
								<div class="kform-label"><label for="kreport-reason"><?php echo JText::_('COM_KUNENA_REPORT_REASON') ?>:</label></div>
								<div class="kform-field"><input type="text" name="reason" class="inputbox" size="30" id="kreport-reason"/></div>
							</li>
							<li class="krow-even">
								<div class="kform-label"><label for="kreport-msg"><?php echo JText::_('COM_KUNENA_REPORT_MESSAGE') ?>:</label></div>
								<div class="kform-field"><textarea id="kreport-msg" name="text" cols="40" rows="10" class="inputbox"></textarea></div>
							</li>
							<li class="krow-odd">
								<div class="kform-field"><input class="kbutton ks" type="submit" name="Submit" value="<?php echo JText::_('COM_KUNENA_REPORT_SEND') ?>"/>
								<input onclick="javascript:window.history.back();" title="<?php echo JText::_ ( 'COM_KUNENA_CANCEL_DESC') ?>" class="kbutton ks" type="button" name="back" value="<?php echo JText::_('COM_KUNENA_CANCEL') ?>"/></div>
							</li>
						</ul>
				</div>
				</div>
			</form>
		</div>
	</div>
</div>

