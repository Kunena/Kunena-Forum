<?php
/**
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="forumlist">
	<div class="inner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt><?php echo JText::_('COM_KUNENA_POLL_NAME') .' '. KunenaHtmlParser::parseText ($this->poll->title); ?></dt>
						<dd>&nbsp;</dd>
					</dl>
				</li>
			</ul>
			<div class="kdetailsbox">
				<div class="kpolldesc">
				<div id="kpoll-text-help"></div>
				<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" id="kpoll-form-vote" method="post">
					<input type="hidden" name="view" value="topic" />
					<input type="hidden" name="task" value="vote" />
					<input type="hidden" name="catid" value="<?php echo $this->topic->category_id ?>" />
					<input type="hidden" name="id" value="<?php echo $this->topic->id ?>" />
					<?php echo JHTML::_( 'form.token' ); ?>

					<fieldset>
						<legend><?php echo JText::_('COM_KUNENA_POLL_OPTIONS'); ?></legend>
						<ul>
							<?php foreach ($this->poll->getOptions() as $key=>$option) : ?>
							<li>
								<input class="kpoll-boxvote" type="radio" name="kpollradio" id="kpollradio_<?php echo intval($key) ?>" value="<?php echo intval($option->id) ?>" <?php if ($this->voted && $this->voted->lastvote == $option->id) echo 'checked="checked"' ?> />
								<?php echo KunenaHtmlParser::parseText ($option->text ) ?>
							</li>
							<?php endforeach; ?>
						</ul>
					</fieldset>
					<div id="kpoll-btns">
						<input id="kpoll-button-vote" class="tk-submit-button" type="submit" value="<?php echo $this->voted ? JText::_('COM_KUNENA_POLL_BUTTON_CHANGEVOTE') : JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>" />
					</div>
				</form>
				</div>
			</div>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>