<?php
/**
* @version $Id: pollbox.php 3959 2010-11-30 19:43:16Z mahagr $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2011 www.kunena.org All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/
// Dont allow direct linking
defined( '_JEXEC' ) or die();
?>
<div class="kblock kpollbox">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kpolls_tbody"></a></span>
		<h2><span><?php echo JText::_('COM_KUNENA_POLL_NAME'); ?> <?php echo KunenaHtmlParser::parseText ($this->polldata[0]->title); ?></span></h2>
	</div>
	<div class="kcontainer" id="kpolls_tbody">
		<div class="kbody">
			<table class = "kblocktable" id = "kpoll">
				<tr>
					<td>
						<div class = "kpolldesc">
						<div id="kpoll-text-help"></div>
						<form id="kpoll-form-vote" method="post" action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>">
							<fieldset>
								<legend><?php echo JText::_('COM_KUNENA_POLL_OPTIONS'); ?></legend>
								<ul>
									<?php foreach ($this->polldata as $i=>$option) : ?>
									<li>
										<input class="kpoll-boxvote" type="radio" name="kpollradio" id="radio_name<?php echo intval($i) ?>" value="<?php echo intval($option->id) ?>" />
										<?php echo KunenaHtmlParser::parseText ($option->text ) ?>
									</li>
									<?php endforeach; ?>
								</ul>
								<input type="hidden" name="kpoll-id" value="<?php echo intval($this->id); ?>" />
							</fieldset>
							<div id="kpoll-btns">
								<input id="kpoll-button-vote" class="kbutton ks" type="submit" value="<?php echo JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>" />
								<?php if($dataspollusers[0]->userid == $this->my->id) : ?>
								<a href = "<?php echo CKunenaLink::GetPollURL('changevote', $this->id, $this->catid); ?>"><?php echo JText::_('COM_KUNENA_POLL_BUTTON_CHANGEVOTE'); ?></a>
								<?php endif; ?>
							</div>
							<input type="hidden" id="kpollvotejsonurl" value="<?php echo CKunenaLink::GetJsonURL('pollvote', '', true); ?>" />
							<input type="hidden" name="option" value="com_kunena" />
							<input type="hidden" name="view" value="topic" />
							<input type="hidden" name="task" value="pollvote" />
							<?php echo JHTML::_( 'form.token' ); ?>
						</form>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>