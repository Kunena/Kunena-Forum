<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
		<form method="post" action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" class="kform kform-report">
			<input type="hidden" name="view" value="topic" />
			<input type="hidden" name="task" value="report" />
			<input type="hidden" name="catid" value="<?php echo intval($this->catid); ?>"/>
			<input type="hidden" name="id" value="<?php echo intval($this->id); ?>"/>
			<input type="hidden" name="mesid" value="<?php echo intval($this->mesid); ?>"/>
			<?php echo JHTML::_( 'form.token' ); ?>
			<div class="ksection kreport">
				<h2 class="kheader"><?php echo JText::_('COM_KUNENA_COM_A_REPORT') ?></h2>
				<div class=kdetailsbox>
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
							<div class="kform-field"><input class="kbutton ks" type="submit" name="Submit" value="<?php echo JText::_('COM_KUNENA_REPORT_SEND') ?>"/></div>
						</li>
					</ul>
				</div>
			</div>
		</form>
