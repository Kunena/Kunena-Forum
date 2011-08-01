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
$row = 0;
?>
<div class="forumlist">
	<div class="inner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon pollvote">
						<dt><?php echo JText::_('COM_KUNENA_POLL_NAME'); ?> <?php echo KunenaHtmlParser::parseText ($this->poll->title); ?></dt>
						<dd class="tk-toggler"><a class="ktoggler close" rel="kpolls-body"></a></dd>
					</dl>
				</li>
			</ul>

	<div class="kcontainer" id="kpolls-body">
		<div class="kbody">
			<table class="kblocktable" id="kpoll">
				<tr>
					<td>
						<div class="kpolldesc">
							<table class="kblocktable">
							<?php foreach ( $this->poll->getOptions() as $option ) : ?>
							<tr class="krow<?php echo (++$row)%2+1;?>">
								<td class="kcol-option"><?php echo KunenaHtmlParser::parseText ($option->text); ?></td>
								<td class="kcol-bar"><img class="jr-forum-stat-bar" src="<?php echo JURI::root(true)."/components/com_kunena/template/default/images/bar.png"; ?>" height="10" width="<?php echo intval(($option->votes*300)/max($this->poll->getTotal(),1))+3; ?>" /></td>
								<td class="kcol-number"><?php if(isset($option->votes) && ($option->votes > 0)) { echo $option->votes; } else { echo JText::_('COM_KUNENA_POLL_NO_VOTE'); } ?></td>
								<td class="kcol-percent"><?php echo round(($option->votes*100)/max($this->poll->getTotal(),1),1)."%"; ?></td>
							</tr>
							<?php endforeach; ?>
							<tr class="krow<?php echo (++$row)%2+1;?>">
								<td colspan="4">
									<?php
									echo JText::_('COM_KUNENA_POLL_VOTERS_TOTAL')." <strong>".$this->usercount."</strong> ";
									echo " ( ".implode(', ', $this->users_voted_list)." ) "; ?>
									<?php if ( $this->usercount > '5' ) : ?><a href="#" id="kpoll-moreusers"><?php echo JText::_('COM_KUNENA_POLLUSERS_MORE')?></a>
									<div style="display: none;" id="kpoll-moreusers-div"><?php echo implode(', ', $this->users_voted_morelist); ?></div>
									<?php endif; ?>
								</td>
							</tr>
							<tr class="krow<?php echo $row%2+1;?>">
								<td colspan="4" class="kpoll-info">
									<?php if (!$this->me->exists()) : ?>
										<?php echo JText::_('COM_KUNENA_POLL_NOT_LOGGED'); ?>
									<?php elseif ($this->voted && !$this->config->pollallowvoteone) : ?>
									<a class="tk-submit-button" href="<?php echo CKunenaLink::GetPollURL('vote', $this->topic->id, $this->category->id);?>">
										<?php echo JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>
									</a>
									<?php else : ?>
									<a class="tk-cancel-button" href="<?php echo CKunenaLink::GetPollURL('changevote', $this->topic->id, $this->category->id); ?>">
										<?php echo JText::_('COM_KUNENA_POLL_BUTTON_CHANGEVOTE'); ?>
									</a>
									<?php endif; ?>
									<?php if( $this->me->isModerator() ) : ?>
									<a class="tk-cancel-button" href="<?php echo KunenaRoute::_("index.php?option=com_kunena&view=topic&id={$this->topic->id}&catid={$this->category->id}&pollid={$this->poll->id}&task=resetvotes&".JUtility::getToken() .'=1') ?>">Reset votes</a>
									<?php endif; ?>
								</td>
							</tr>
							</table>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>

		<span class="corners-bottom"><span></span></span>
	</div>
</div>