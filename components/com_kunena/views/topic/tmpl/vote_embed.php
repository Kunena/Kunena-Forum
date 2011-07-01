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
<div id="Kunena">
<?php
$this->displayMenu ();
$this->displayLoginBox ();
?>
<div class="kblock kpollbox">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kpolls_tbody"></a></span>
		<h2><span><?php echo JText::_('COM_KUNENA_POLL_NAME') .' '. KunenaHtmlParser::parseText ($this->poll->title); ?></span></h2>
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
									<?php foreach ($this->poll->getOptions() as $key=>$option) : ?>
									<li>
										<input class="kpoll-boxvote" type="radio" name="kpollradio" id="radio_name<?php echo intval($key) ?>" value="<?php echo intval($option->id) ?>" <?php if ($this->voted && $this->voted->lastvote == $option->id) echo 'checked="checked"' ?> />
										<?php echo KunenaHtmlParser::parseText ($option->text ) ?>
									</li>
									<?php endforeach; ?>
								</ul>
							</fieldset>
							<div id="kpoll-btns">
								<input id="kpoll-button-vote" class="kbutton ks" type="submit" value="<?php echo $this->voted ? JText::_('COM_KUNENA_POLL_BUTTON_CHANGEVOTE') : JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>" />
							</div>
							<input type="hidden" name="option" value="com_kunena" />
							<input type="hidden" name="view" value="topic" />
							<input type="hidden" name="task" value="vote" />
							<input type="hidden" name="catid" value="<?php echo $this->topic->category_id ?>" />
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
<?php $this->displayFooter (); ?>
</div>