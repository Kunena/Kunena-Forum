<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008-2011 www.kunena.org All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/
// Dont allow direct linking
defined( '_JEXEC' ) or die();

// FIXME: AJAX Javascript does not work, it needs different logic
//$this->call_javascript_vote();
$dataspollresult = $this->get_poll_data($this->id);
if (empty($dataspollresult)) return;
//To show the number total of votes for the poll
$nbvoters = $this->get_number_total_voters($this->id);
//To show the usernames of the users which have voted for this poll
$pollusersvoted = $this->get_users_voted($this->id);
//To know if an user has already voted for this poll
$dataspollusers = $this->get_data_poll_users($this->my->id,$this->id);
$i = 0;
if (!isset($dataspollusers[0]->userid) && !isset($dataspollusers[0]->pollid)) {
	$dataspollusers[0]->userid = null;
	$dataspollusers[0]->pollid = null;

}
?>
<div class="kblock kpollbox">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kpolls_tbody"></a></span>
		<h2><span><?php echo JText::_('COM_KUNENA_POLL_NAME'); ?> <?php echo KunenaParser::parseText ($dataspollresult[0]->title); ?></span></h2>
	</div>
	<div class="kcontainer" id="kpolls_tbody">
		<div class="kbody">
			<table class = "kblocktable" id = "kpoll">
				<tr>
					<td>
						<div class = "kpolldesc">
							<?php if ( $dataspollusers[0]->userid == $this->my->id || $this->my->id == "0") { //if the user has already voted for this poll ?>
							<table>
								<?php foreach ($dataspollresult as $row) :
								?>
								<tr class="krow<?php echo ($i^=1)+1;?>">
									<td class="kpoll-option"><?php echo KunenaParser::parseText ( $row->text ); ?></td>
									<td class="kpoll-bar"><img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_JLIVEURL."components/com_kunena/template/default/images/bar.png"; ?>" height = "10" width = "<?php if(isset($row->votes)) { echo (intval($row->votes*25)/5); } else { echo "0"; }?>" alt=""/></td>
									<td class="kpoll-number"><?php if(isset($row->votes) && ($row->votes > 0)) { echo intval($row->votes); } else { echo JText::_('COM_KUNENA_POLL_NO_VOTE'); } ?></td>
									<td class="kpoll-percent"><?php if($row->votes != '0' && $nbvoters != '0' ) { echo round(($row->votes*100)/$nbvoters,1)."%"; } else { echo "0%"; } ?></td>
								</tr>
								<?php endforeach ?>
								<tr class="krow<?php echo ($i^=1)+1;?>">
									<td colspan="4">
									<?php
									if( empty($nbvoters) ) $nbvoters = "0";
									echo JText::_('COM_KUNENA_POLL_VOTERS_TOTAL')."<b>".$nbvoters."</b> ";
									if ( $this->config->pollresultsuserslist && !empty($pollusersvoted) ) :
										echo " ( ";
										foreach ( $pollusersvoted as $row ) echo CKunenaLink::GetProfileLink(intval($row->userid))." ";
										echo " ) ";
									endif;
									?>
									</td>
								</tr>

								<?php if ($this->my->id == '0') : ?>
								<tr class="krow2">
									<td colspan="4" class="kpoll-info"><?php echo JText::_('COM_KUNENA_POLL_NOT_LOGGED'); ?></td>
								</tr>
								<?php elseif ( !$this->config->pollallowvoteone ) : ?>
								<tr class="krow2">
									<td colspan="4">
										<a href = "<?php echo CKunenaLink::GetPollURL('vote', intval($this->id), intval($this->catid));?>">
										<?php echo JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?></a>
									</td>
								</tr>
								<?php else : ?>
								<tr class="krow2">
									<td colspan="4">
										<a href = "<?php echo CKunenaLink::GetPollURL('changevote', intval($this->id), intval($this->catid)); ?>">
										<?php echo JText::_('COM_KUNENA_POLL_BUTTON_CHANGEVOTE'); ?></a>
									</td>
								</tr>
								<?php endif; ?>
						</table>
						<?php
						} elseif (JFactory::getDate()->toMySQL() <= $dataspollresult[0]->polltimetolive
							|| $dataspollresult[0]->polltimetolive == "0000-00-00 00:00:00") {
						?>
						<div id="kpoll-text-help"></div>
						<form id="kpoll-form-vote" method="post" action="<?php echo CKunenaLink::GetPollsURL('vote', intval($this->catid)); ?>">
							<fieldset>
								<legend><?php echo JText::_('COM_KUNENA_POLL_OPTIONS'); ?></legend>
								<ul>
									<?php foreach ($dataspollresult as $i=>$option) : ?>
									<li>
										<input class="kpoll-boxvote" type="radio" name="kpollradio" id="radio_name<?php echo intval($i) ?>" value="<?php echo intval($option->id) ?>" />
										<?php echo KunenaParser::parseText ($option->text ) ?>
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
							<?php echo JHTML::_( 'form.token' ); ?>
						</form>
						<?php
						} else {
						?>
						<table>
							<?php foreach ( $dataspollresult as $row ) : ?>
							<tr class="krow<?php echo ($i^=1)+1;?>">
								<td class="kcol-option"><?php echo KunenaParser::parseText ($row->text); ?></td>
								<td class="kcol-bar"><img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_JLIVEURL."components/com_kunena/template/default/images/bar.png"; ?>" height = "10" width = "<?php echo isset($row->votes) ? ($row->votes*25)/5 : "0"; ?>" /></td>
								<td class="kcol-number"><?php if(isset($row->votes) && ($row->votes > 0)) { echo $row->votes; } else { echo JText::_('COM_KUNENA_POLL_NO_VOTE'); } ?></td>
								<td class="kcol-percent"><?php if($row->votes != "0" && $nbvoters != '0') { echo round(($row->votes*100)/$nbvoters,1)."%"; } else { echo "0%"; } ?></td>
							</tr>
							<?php endforeach; ?>
							<tr class="krow<?php echo ($i^=1)+1;?>">
								<td colspan="4">
									<?php
									if(empty($nbvoters)) $nbvoters = "0";
									echo JText::_('COM_KUNENA_POLL_VOTERS_TOTAL')." <strong>".$nbvoters."</strong> ";
									if($this->config->pollresultsuserslist && !empty($pollusersvoted)) :
										echo " ( ";
										foreach($pollusersvoted as $row) echo CKunenaLink::GetProfileLink(intval($row->userid))." ";
										echo " ) ";
									endif; ?>
								</td>
							</tr>
							<?php if ($this->my->id == "0") : ?>
							<tr class="krow2">
								<td colspan="4" class="kpoll-info"><?php echo JText::_('COM_KUNENA_POLL_NOT_LOGGED'); ?></td>
							</tr>
							<?php elseif (!$this->config->pollallowvoteone) : ?>
							<tr class="krow2">
								<td colspan="4">

									<a href = "<?php echo CKunenaLink::GetPollURL('vote', $this->id, $this->catid);?>">
										<?php echo JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>
									</a>

								</td>
							</tr>
							<?php else : ?>
							<tr class="krow2">
								<td colspan="4">

										<a href = <?php echo CKunenaLink::GetPollURL('changevote', $this->id, $this->catid); ?>>
											<?php echo JText::_('COM_KUNENA_POLL_BUTTON_CHANGEVOTE'); ?>
										</a>

								</td>
							</tr>
							<?php endif; ?>
						</table>
					<?php
					}
					?>
					</div>
				</td>
			</tr>
	</table>
</div>
</div>
</div>
