<?php
/**
* @version $Id$
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
			<table class="kblocktable" id="kpoll">
				<tr>
					<td>
						<div class="kpolldesc">
							<table>
							<?php foreach ( $this->polldata as $poll ) : ?>
							<tr class="krow<?php echo ($i^=1)+1;?>">
								<td class="kcol-option"><?php echo KunenaHtmlParser::parseText ($poll->text); ?></td>
								<td class="kcol-bar"><img class="jr-forum-stat-bar" src="<?php echo JURI::root()."components/com_kunena/template/default/images/bar.png"; ?>" height="10" width="<?php echo isset($row->votes) ? ($row->votes*25)/5 : "0"; ?>" /></td>
								<td class="kcol-number"><?php if(isset($poll->votes) && ($poll->votes > 0)) { echo $poll->votes; } else { echo JText::_('COM_KUNENA_POLL_NO_VOTE'); } ?></td>
								<td class="kcol-percent"><?php if($poll->votes != "0" && $this->nbvoters != '0') { echo round(($poll->votes*100)/$this->nbvoters,1)."%"; } else { echo "0%"; } ?></td>
							</tr>
							<?php endforeach; ?>
							<tr class="krow<?php echo ($i^=1)+1;?>">
								<td colspan="4">
									<?php
									if(empty($this->nbvoters)) $this->nbvoters = "0";
									echo JText::_('COM_KUNENA_POLL_VOTERS_TOTAL')." <strong>".$this->nbvoters."</strong> ";
									if($this->config->pollresultsuserslist && !empty($this->usersvoted)) :
										echo " ( ";
										foreach($this->usersvoted as $row) echo CKunenaLink::GetProfileLink(intval($row->userid))." ";
										echo " ) ";
									endif; ?>
								</td>
							</tr>
							<?php if (!$this->me->exists()) : ?>
							<tr class="krow2">
								<td colspan="4" class="kpoll-info"><?php echo JText::_('COM_KUNENA_POLL_NOT_LOGGED'); ?></td>
							</tr>
							<?php elseif (!$this->config->pollallowvoteone) : ?>
							<tr class="krow2">
								<td colspan="4">
									<a href="<?php echo CKunenaLink::GetPollURL('vote', $this->topic->id, $this->category->id);?>">
										<?php echo JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>
									</a>
								</td>
							</tr>
							<?php else : ?>
							<tr class="krow2">
								<td colspan="4">
										<a href=<?php echo CKunenaLink::GetPollURL('changevote', $this->topic->id, $this->category->id); ?>>
											<?php echo JText::_('COM_KUNENA_POLL_BUTTON_CHANGEVOTE'); ?>
										</a>
								</td>
							</tr>
							<?php endif; ?>
							</table>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>

</div>
