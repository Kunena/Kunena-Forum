<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2010 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/
// Dont allow direct linking
defined( '_JEXEC' ) or die();

$this->call_javascript_vote();
$dataspollresult = $this->get_poll_data($this->id);
//To show the number total of votes for the poll
$nbvoters = $this->get_number_total_voters($this->id);
//To show the usernames of the users which have voted for this poll
$pollusersvoted = $this->get_users_voted($this->id);
//To know if an user has already voted for this poll
$dataspollusers = $this->get_data_poll_users($this->my->id,$this->id);
if (!isset($dataspollusers[0]->userid) && !isset($dataspollusers[0]->pollid)) {
	$dataspollusers[0]->userid = null;
	$dataspollusers[0]->pollid = null;
}
?>
<div class="k-bt-cvr1">
<div class="k-bt-cvr2">
<div class="k-bt-cvr3">
<div class="k-bt-cvr4">
<div class="k_bt_cvr5">
	<table class = "kblocktable" id = "kpoll" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
		<thead>
			<tr>
				<th align="left">
					<div class = "ktitle-cover km">
						<span class = "ktitle kl"><?php echo JText::_('COM_KUNENA_POLL_NAME'); ?> <?php echo KunenaParser::parseText ($dataspollresult[0]->title); ?></span>
					</div>

					<span class="fltrt ktoggler"><a class="ktoggler close" rel="kpolls_tbody"></a></span>
				</th>
			</tr>
		</thead>
		<tbody id = "kpolls_tbody">
				<tr class = "ksectiontableentry2">
					<td class = "td-1 km" align="left">
						<div class = "kpolldesc">
							<?php if ( $dataspollusers[0]->userid == $this->my->id || $this->my->id == "0") { //if the user has already voted for this poll ?>
							<table border = "0" cellspacing = "0" cellpadding = "0" width="100%">
								<?php foreach ($dataspollresult as $row) : ?>
								<tr>
									<td><?php echo KunenaParser::parseText ( $row->text ); ?></td>
									<td><img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_JLIVEURL."components/com_kunena/template/default/images/backgrounds/bar.png"; ?>" height = "10" width = "<?php if(isset($row->votes)) { echo ($this->escape($row->votes*25)/5); } else { echo "0"; }?>"/></td>
									<td><?php if(isset($row->votes) && ($row->votes > 0)) { echo $this->escape($row->votes); } else { echo JText::_('COM_KUNENA_POLL_NO_VOTE'); } ?></td>
									<td><?php if($row->votes != '0' && $nbvoters != '0' ) { echo round(($row->votes*100)/$nbvoters,1)."%"; } else { echo "0%"; } ?></td>
								</tr>
								<?php endforeach ?>
								<tr>
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
								<tr>
									<td colspan="4"><?php echo JText::_('COM_KUNENA_POLL_NOT_LOGGED'); ?> </td>
								</tr>
								<?php elseif ( !$this->config->pollallowvoteone ) : ?>
								<tr>
									<td colspan="4">
										<a href = "<?php echo CKunenaLink::GetPollURL('vote', intval($this->id), intval($this->catid));?>">
										<?php echo JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?></a>
									</td>
								</tr>
								<?php else : ?>
								<tr>
									<td colspan="4">
										<a href = "<?php echo CKunenaLink::GetPollURL('changevote', intval($this->id), intval($this->catid)); ?>">
										<?php echo JText::_('COM_KUNENA_POLL_BUTTON_CHANGEVOTE'); ?></a>
									</td>
								</tr>
								<?php endif; ?>
						</table>
						<?php
						} elseif ((strftime("%Y-%m-%d %H:%M:%S",time()) <= $dataspollresult[0]->polltimetolive)
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
							<input type="hidden" id="kpollvotejsonurl" value="<?php echo CKunenaLink::GetJsonURL('pollvote'); ?>" />
						</form>
						<?php
						} else {
						?>
						<table border = "0" cellspacing = "0" cellpadding = "0" width="100%">
							<?php foreach ( $dataspollresult as $row ) : ?>
							<tr>
								<td><?php echo KunenaParser::parseText ($row->text); ?></td>
								<td><img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_JLIVEURL."components/com_kunena/template/default/images/backgrounds/bar.png"; ?>" height = "10" width = "<?php echo isset($row->votes) ? ($row->votes*25)/5 : "0"; ?>" /></td>
								<td><?php if(isset($row->votes) && ($row->votes > 0)) { echo $row->votes; } else { echo JText::_('COM_KUNENA_POLL_NO_VOTE'); } ?></td>
								<td><?php if($row->votes != "0" && $nbvoters != '0') { echo round(($row->votes*100)/$nbvoters,1)."%"; } else { echo "0%"; } ?></td>
							</tr>
							<?php endforeach; ?>
							<tr>
								<td colspan="4">
									<?php
									if(empty($nbvoters)) $nbvoters = "0";
									echo JText::_('COM_KUNENA_POLL_VOTERS_TOTAL')."<strong>".$nbvoters."</strong> ";
									if($this->config->pollresultsuserslist && !empty($pollusersvoted)) :
										echo " ( ";
										foreach($pollusersvoted as $row) echo CKunenaLink::GetProfileLink($row->userid)." ";
										echo " ) ";
									endif; ?>
								</td>
							</tr>
							<?php if ($this->my->id == "0") : ?>
							<tr>
								<td colspan="4"><?php echo JText::_('COM_KUNENA_POLL_NOT_LOGGED'); ?> </td>
							</tr>
							<?php elseif (!$this->config->pollallowvoteone) : ?>
							<tr>
								<td colspan="4">
									<a href = "<?php echo CKunenaLink::GetPollURL('vote', $this->id, $this->catid);?>">
										<?php echo JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>
									</a>
								</td>
							</tr>
							<?php else : ?>
							<tr>
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
		</tbody>
	</table>
</div>
</div>
</div>
</div>
</div>
