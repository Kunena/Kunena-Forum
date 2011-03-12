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
<div class="kblock kpollbox">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kpolls_tbody"></a></span>
		<h2><span><?php echo JText::_('COM_KUNENA_POLL_NAME'); ?> <?php echo KunenaHtmlParser::parseText ($this->poll->title); ?></span></h2>
	</div>
	<div class="kcontainer" id="kpolls_tbody">
		<div class="kbody">
			<table class="kblocktable" id="kpoll">
				<tr>
					<td>
						<div class="kpolldesc">
							<table>
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
									if($this->config->pollresultsuserslist && !empty($this->usersvoted)) :
										echo " ( ";
										foreach($this->usersvoted as $userid=>$vote) echo CKunenaLink::GetProfileLink($userid)." ";
										echo " ) ";
									endif; ?>
								</td>
							</tr>
							<tr class="krow<?php echo $row%2+1;?>">
								<td colspan="4" class="kpoll-info">
									<?php if (!$this->me->exists()) : ?>
										<?php echo JText::_('COM_KUNENA_POLL_NOT_LOGGED'); ?>
									<?php elseif ($this->voted && !$this->config->pollallowvoteone) : ?>
									<a href="<?php echo CKunenaLink::GetPollURL('vote', $this->topic->id, $this->category->id);?>">
										<?php echo JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>
									</a>
									<?php else : ?>
									<a href="<?php echo CKunenaLink::GetPollURL('changevote', $this->topic->id, $this->category->id); ?>">
										<?php echo JText::_('COM_KUNENA_POLL_BUTTON_CHANGEVOTE'); ?>
									</a>
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
</div>