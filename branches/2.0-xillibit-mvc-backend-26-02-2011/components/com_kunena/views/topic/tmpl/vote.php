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
<div class="kblock kpollbox">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kpolls_tbody"></a></span>
		<h2><span><?php echo JText::_('COM_KUNENA_POLL_NAME') .' '. KunenaHtmlParser::parseText ($this->polldata[0]->title); ?></span></h2>
	</div>
	<div class="kcontainer" id="kpolls_tbody">
		<div class="kbody">
			<table class="kblocktable" id="kpoll">
				<tr>
					<td>
						<div class="kpolldesc">
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
								<input type="hidden" name="kpoll-id" value="<?php echo intval($this->topic->poll_id); ?>" />
							</fieldset>
							<div id="kpoll-btns">
								<input id="kpoll-button-vote" class="kbutton ks" type="submit" value="<?php echo JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>" />
								<?php if($this->voted) : ?>
								<a href="<?php echo CKunenaLink::GetPollURL('changevote', $this->topic->id, $this->category->id); ?>"><?php echo JText::_('COM_KUNENA_POLL_BUTTON_CHANGEVOTE'); ?></a>
								<?php endif; ?>
							</div>
							<input type="hidden" id="kpollvotejsonurl" value="<?php echo CKunenaLink::GetJsonURL('pollvote', '', true); ?>" />
							<input type="hidden" name="option" value="com_kunena" />
							<input type="hidden" name="view" value="topic" />
							<input type="hidden" name="task" value="vote" />
							<input type="hidden" name="id" value="<?php echo $this->topic->id ?>" />
							<?php echo JHTML::_( 'form.token' ); ?>
						</form>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>