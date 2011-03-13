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

kimport('kunena.forum.message.thankyou.helper');

$document = JFactory::getDocument();
$document->addStyleSheet ( JURI::base(true).'/components/com_kunena/media/css/admin.css' );
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-stats"><?php echo JText::_('COM_KUNENA_STATS_GEN_STATS');?></div>
	<div class="kadmin-statscover">

<!-- BEGIN: STATS -->
<div class="kadmin-statscover">

<table cellspacing="1"  border="0" width="100%" class="kadmin-stat">
	<caption><?php echo JText::_('COM_KUNENA_STATS_GEN_STATS'); ?></caption>
	<col class="col1" />
	<col class="col2" />
	<col class="col1" />
	<col class="col2" />
	<thead>
	<tr>
		<th><?php echo JText::_('COM_KUNENA_STATISTIC');?></th>
		<th><?php echo JText::_('COM_KUNENA_VALUE');?></th>
		<th><?php echo JText::_('COM_KUNENA_STATISTIC');?></th>
		<th><?php echo JText::_('COM_KUNENA_VALUE');?></th>
	</tr>
	</thead>

	<tbody>
	<tr>
		<td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_MEMBERS'); ?> </td>
		<td><strong><?php echo $this->totalmembers; ?></strong></td>
		<td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_SECTIONS'); ?> </td>
		<td><strong><?php echo $this->totalsections;?></strong></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_REPLIES'); ?></td>
		<td><strong><?php echo $this->totalmessages;?></strong></td>
		<td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_CATEGORIES'); ?> </td>
		<td><strong><?php echo $this->totalcats;?></strong></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_TOPICS'); ?></td>
		<td><strong><?php echo $this->totaltitles;?></strong></td>
		<td><?php echo JText::_('COM_KUNENA_STATS_LATEST_MEMBER'); ?> </td>
		<td><strong><?php echo $this->lastestmembername; ?></strong></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_THANKYOU'); ?></td>
		<td><strong><?php echo $this->topthanks;?></strong></td>
		<td><?php echo JText::_('COM_KUNENA_STATS_TODAY_THANKYOU'); ?> </td>
		<td><strong><?php echo KunenaForumMessageThankyouHelper::getTotal($this->datem->toMySQL(),$this->datee->toMySQL()) ;?></strong></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_KUNENA_STATS_TODAY_TOPICS'); ?></td>
		<td><strong><?php echo $this->todayopen; ?></strong></td>
		<td><?php echo JText::_('COM_KUNENA_STATS_YESTERDAY_TOPICS'); ?> </td>
		<td><strong><?php echo $this->yesterdayopen;?></strong></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_KUNENA_STATS_TODAY_REPLIES'); ?></td>
		<td><strong><?php echo $this->todayanswer;?></strong></td>
		<td><?php echo JText::_('COM_KUNENA_STATS_YESTERDAY_REPLIES'); ?></td>
		<td><strong><?php echo $this->yesterdayanswer; ?></strong></td>
	</tr>
	</tbody>
</table>
<!-- B: UserStat -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td width="49%" valign="top">
	<?php if (is_array($this->topposters)) : ?>
		<table cellspacing="1"  border="0" width="100%" class="kadmin-stat">
		<caption><?php echo JText::_('COM_KUNENA_STATS_TOP_POSTERS'); ?></caption>
		<col class="col1" />
		<col class="col2" />
		<col class="col2" />
		<thead>
			<tr>
			<th><?php echo JText::_('COM_KUNENA_USRL_USERNAME');?></th>
			<th></th>
			<th><?php echo JText::_('COM_KUNENA_USRL_HITS');?></th>
			</tr>
		</thead>
		<tbody>
			<?php

				foreach ($this->topposters as $poster) {
					if ($poster->posts == $this->topposters[0]->posts) {
						$barwidth = 100;
					}
					else {
						$barwidth = round(($poster->posts * 100) / $this->topposters[0]->posts);
					}
			?>
			<tr>
			<td><?php echo $poster->username;?> </td>
			<td ><img style="margin-bottom:1px" src="<?php echo JURI::Root().'components/com_kunena/template/default/images/bar.png'; ?>" alt="" height="15" width="<?php echo $barwidth;?>" /> </td>
			<td ><?php echo $poster->posts;?></td>
			</tr>
			<?php
				}
				if (empty($this->topposters)) {
					echo '<tr><td colspan="3">---</td></tr>';
				}
				?>
		</tbody>
		</table>
		<?php endif; ?>
	</td>
	<td width="1%">&nbsp;</td>
	<td width="49%" valign="top">
	   <?php if(is_array($this->topprofiles)): ?>
		<table cellspacing="1"  border="0" width="100%" class="kadmin-stat">
		<caption><?php echo  JText::_('COM_KUNENA_STATS_POPULAR_PROFILE'); ?></caption>
		<col class="col1" />
		<col class="col2" />
		<col class="col2" />
		<thead>
			<tr>
			<th><?php echo JText::_('COM_KUNENA_USRL_USERNAME');?></th>
			<th></th>
			<th><?php echo JText::_('COM_KUNENA_USRL_HITS');?></th>
			</tr>
		</thead>
		<tbody>
			<?php
		  foreach ($this->topprofiles as $profile) {
			if ($profile->hits == $this->topprofiles[0]->hits)
				$barwidth = 100;
			else
				$barwidth = round(($profile->hits * 100) / $this->topprofiles[0]->hits);
			?>
			<tr>
			<td><?php echo KunenaUser::getInstance($profile->id)->getName(); ?></td>
			<td ><img style="margin-bottom:1px" src="<?php echo JURI::Root().'components/com_kunena/template/default/images/bar.png'; ?>" alt="" height="15" width="<?php echo $barwidth;?>" /> </td>
			<td ><?php echo $profile->hits;?></td>
			</tr>
			<?php
				}
				if (empty($this->topprofiles)) {
					echo '<tr><td colspan="3">---</td></tr>';
				}
				?>
		</tbody>
		</table>
		<?php endif; ?>
	</td>
	</tr>
</table>
<!-- F: UserStat -->
<!-- Thank you stat -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td width="49%" valign="top">
	<?php if(is_array($this->topuserthanks)): ?>
		<table cellspacing="1"  border="0" width="100%" class="kadmin-stat">
		<caption><?php echo JText::_('COM_KUNENA_STATS_TOP_GOT_THANKYOU'); ?></caption>
		<col class="col1" />
		<col class="col2" />
		<col class="col2" />
		<thead>
			<tr>
			<th width="32%"><?php echo JText::_('COM_KUNENA_USRL_USERNAME');?></th>
			<th></th>
			<th width="18%"><?php echo JText::_('COM_KUNENA_USRL_THANKYOUS');?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach ($this->topuserthanks as $thankyou) {
					if ($thankyou->receivedthanks == $this->topuserthanks[0]->receivedthanks) {
						$barwidth = 100;
					}
					else {
						$barwidth = round(($thankyou->receivedthanks * 100) / $this->topuserthanks[0]->receivedthanks);
					}
			?>
			<tr>
			<td><?php echo $thankyou->username;?> </td>
			<td ><img style="margin-bottom:1px" src="<?php echo JURI::Root().'components/com_kunena/template/default/images/bar.png'; ?>" alt="" height="15" width="<?php echo $barwidth;?>" /> </td>
			<td ><?php echo $thankyou->receivedthanks;?></td>
			</tr>
			<?php
				}
				if (empty($this->topuserthanks)) {
					echo '<tr><td colspan="3">---</td></tr>';
				}
				?>
		</tbody>
		</table>
		<?php endif; ?>
	</td>
	<td width="1%">&nbsp;</td>
	<td width="49%" valign="top">
		<table cellspacing="1"  border="0" width="100%" class="kadmin-stat">
		<caption><?php echo  JText::_('COM_KUNENA_STATS_TOP_SAID_THANKYOU'); ?></caption>
		<col class="col1" />
		<col class="col2" />
		<col class="col2" />
		<thead>
			<tr>
			<th width="32%"><?php echo JText::_('COM_KUNENA_USRL_USERNAME');?></th>
			<th></th>
			<th width="18%"><?php echo JText::_('COM_KUNENA_USRL_THANKYOUS');?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach ($this->topsaidthanks as $said_thankyou) {
					if ($said_thankyou->countid == $this->maxsaidthanks->countid) {
						$barwidth = 100;
					}
					else {
						$barwidth = round(($said_thankyou->countid * 100) / $this->maxsaidthanks->countid);
					}
			?>
			<tr>
			<td><?php echo $said_thankyou->username;?> </td>
			<td ><img style="margin-bottom:1px" src="<?php echo JURI::Root().'components/com_kunena/template/default/images/bar.png'; ?>" alt="" height="15" width="<?php echo $barwidth;?>" /> </td>
			<td ><?php echo $said_thankyou->countid;?></td>
			</tr>
			<?php
				}
				if (empty($this->topsaidthanks)) {
					echo '<tr><td colspan="3">---</td></tr>';
				}
				?>
		</tbody>
		</table>
	</td>
	</tr>
</table>
<!-- Thank you stat -->
<?php if(is_array($this->toptitles)): ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="49%" valign="top">
		<!-- Begin : Top popular topics -->
		<table cellspacing="1"  border="0" width="100%" class="kadmin-stat">
			<caption><?php echo JText::_('COM_KUNENA_STATS_POPULAR_TOPICS'); ?></caption>
			<col class="col1" />
			<col class="col2" />
			<col class="col2" />
			<thead>
			<tr>
				<th><?php echo JText::_('COM_KUNENA_USERPROFILE_TOPICS');?></th>
				<th></th>
				<th><?php echo JText::_('COM_KUNENA_USRL_HITS');?></th>
			</tr>
			</thead>
			<tbody>
			<?php
				foreach ($this->toptitles as $post) {
					if ($post->hits == $this->toptitles[0]->hits) {
						$barwidth = 100;
					}
					else {
						$barwidth = round(($post->hits * 100) / $this->toptitles[0]->hits);
					}
					$link = '../'.KunenaRoute::_("index.php?option=com_kunena&view=topic&catid={$post->catid}&id={$post->id}");
				?>
			<tr>
				<td ><a href="<?php echo $link;?>"><?php echo $post->subject;?></a> </td>
				<td ><img src="<?php echo JURI::Root().'components/com_kunena/template/default/images/bar.png'; ?>" alt="" style="margin-bottom:1px" height="15" width="<?php echo $barwidth;?>" /> </td>
				<td ><?php echo $post->hits;?></td>
			</tr>
			<?php }
				if (empty($this->toptitles)) {
					echo '<tr><td colspan="3">---</td></tr>';
				} ?>
			</tbody>
		</table>
		<!-- Finish : Top popular topics -->
		</td>
		<td width="1%">&nbsp;</td>
		<td width="49%" valign="top">
			<!-- Begin: Top Thank you topics -->
			<table cellspacing="1"  border="0" width="100%" class="kadmin-stat">
			<caption><?php echo JText::_('COM_KUNENA_STATS_THANKYOU_TOPICS'); ?></caption>
			<col class="col1" />
			<col class="col2" />
			<col class="col2" />
			<thead>
			<tr>
				<th width="32%"><?php echo JText::_('COM_KUNENA_USERPROFILE_TOPICS');?></th>
				<th></th>
				<th width="18%"><?php echo JText::_('COM_KUNENA_USRL_THANKYOUS');?></th>
			</tr>
			</thead>
			<tbody>
			<?php
				$top_posts=KunenaForumMessageThankyouHelper::getTopMessages();
				foreach ($top_posts as $post) {
					if ($post->countid == $top_posts[0]->countid) {
						$barwidth = 100;
					}
					else {
						$barwidth = round(($post->countid * 100) / $top_posts[0]->countid);
					}
					$link = '../'.KunenaRoute::_("index.php?option=com_kunena&view=topic&catid={$post->catid}&id={$post->postid}");
				?>
			<tr>
				<td ><a href="<?php echo $link;?>"><?php echo $post->subject;?></a> </td>
				<td ><img src="<?php echo JURI::Root().'components/com_kunena/template/default/images/bar.png'; ?>" alt="" style="margin-bottom:1px" height="15" width="<?php echo $barwidth;?>" /> </td>
				<td ><?php echo $post->countid;?></td>
			</tr>
			<?php }
				if (empty($top_posts)) {
					echo '<tr><td colspan="3">---</td></tr>';
				}
			?>
			</tbody>
			</table>
		<!-- Fnish : Top Thank you topics  -->
		</td>
	</tr>
</table>
<?php endif; ?>

</div>

<!-- FINISH: STATS -->
</div>
</div>
</div>
<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>