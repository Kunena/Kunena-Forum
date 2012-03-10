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
<div class="kmodule">
	<div class="box-wrapper">
		<div class="topic_poll-kbox kbox box-color box-border box-border_radius box-border_radius-child box-shadow">
			<div class="headerbox-wrapper box-full">
				<div class="header">
					<h2 class="kheader"><?php echo JText::_('COM_KUNENA_POLL_NAME') .' '. KunenaHtmlParser::parseText ($this->poll->title); ?></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer">
				<div class="kdetailsbox box-full box-hover box-border box-border_radius box-shadow">
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
								<input id="kpoll-button-vote" class="kbutton ks" type="submit" value="<?php echo $this->voted ? JText::_('COM_KUNENA_POLL_BUTTON_CHANGEVOTE') : JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>" />
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
