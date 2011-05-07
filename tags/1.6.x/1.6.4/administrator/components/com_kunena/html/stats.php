<?php
// Dont allow direct linking
defined( '_JEXEC' ) or die();
?>

<div class="kadmin-functitle icon-stats"><?php echo JText::_('COM_KUNENA_STATS_GEN_STATS');?></div>
<div class="kadmin-statscover">

<!-- BEGIN: STATS -->
<div class="kadmin-statscover">
<?php include_once (JPATH_COMPONENT_ADMINISTRATOR .'/lib/kunena.stats.class.php');
jimport ( 'joomla.utilities.date' );
$datem = new JDate(date("Y-m-d 00:00:01"));
$datee = new JDate(date("Y-m-d 23:59:59"));?>
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
	<?php $yesterday = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")); ?>
	<tbody>
	<tr>
		<td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_MEMBERS'); ?> </td>
		<td><strong><?php echo jbStats::get_total_members(); ?></strong></td>
		<td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_SECTIONS'); ?> </td>
		<td><strong><?php echo jbStats::get_total_sections();?></strong></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_REPLIES'); ?></td>
		<td><strong><?php echo jbStats::get_total_messages() ;?></strong></td>
		<td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_CATEGORIES'); ?> </td>
		<td><strong><?php echo jbStats::get_total_categories() ;?></strong></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_TOPICS'); ?></td>
		<td><strong><?php echo jbStats::get_total_topics() ;?></strong></td>
		<td><?php echo JText::_('COM_KUNENA_STATS_LATEST_MEMBER'); ?> </td>
		<td><strong><?php echo jbStats::get_latest_member() ;?></strong></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_THANKYOU'); ?></td>
		<td><strong><?php echo KunenaThankYou::getTotalThankYou();?></strong></td>
		<td><?php echo JText::_('COM_KUNENA_STATS_TODAY_THANKYOU'); ?> </td>
		<td><strong><?php echo KunenaThankYou::getTotalThankYou($datem->toMySQL(),$datee->toMySQL()) ;?></strong></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_KUNENA_STATS_TODAY_TOPICS'); ?></td>
		<td><strong><?php echo jbStats::get_total_topics(date("Y-m-d 00:00:01"),date("Y-m-d 23:59:59")) ;?></strong></td>
		<td><?php echo JText::_('COM_KUNENA_STATS_YESTERDAY_TOPICS'); ?> </td>
		<td><strong><?php echo jbStats::get_total_topics(date("Y-m-d 00:00:01",$yesterday),date("Y-m-d 23:59:59",$yesterday)) ;?></strong></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_KUNENA_STATS_TODAY_REPLIES'); ?></td>
		<td><strong><?php echo jbStats::get_total_messages(date("Y-m-d 00:00:01"),date("Y-m-d 23:59:59")) ;?></strong></td>
		<td><?php echo JText::_('COM_KUNENA_STATS_YESTERDAY_REPLIES'); ?></td>
		<td><strong><?php echo jbStats::get_total_messages(date("Y-m-d 00:00:01",$yesterday),date("Y-m-d 23:59:59",$yesterday)) ; ?></strong></td>
	</tr>
	</tbody>
</table>
<!-- B: UserStat -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td width="49%" valign="top">
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
				$KUNENA_top_posters=jbStats::get_top_posters();
				foreach ($KUNENA_top_posters as $KUNENA_poster) {
					if ($KUNENA_poster->posts == $KUNENA_top_posters[0]->posts) {
						$barwidth = 100;
					}
					else {
						$barwidth = round(($KUNENA_poster->posts * 100) / $KUNENA_top_posters[0]->posts);
					}
			?>
			<tr>
			<td><?php echo $KUNENA_poster->username;?> </td>
			<td ><img style="margin-bottom:1px" src="<?php echo KUNENA_DIRECTURL.'template/default/images/bar.png'; ?>" alt="" height="15" width="<?php echo $barwidth;?>" /> </td>
			<td ><?php echo $KUNENA_poster->posts;?></td>
			</tr>
			<?php
				}
				if (empty($KUNENA_top_posters)) {
					echo '<tr><td colspan="3">---</td></tr>';
				}
				?>
		</tbody>
		</table>
	</td>
	<td width="1%">&nbsp;</td>
	<td width="49%" valign="top">
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
		$fb_top_profiles=jbStats::get_top_profiles();
		foreach ($fb_top_profiles as $fb_profile) {
			if ($fb_profile->uhits == $fb_top_profiles[0]->uhits)
				$barwidth = 100;
			else
				$barwidth = round(($fb_profile->uhits * 100) / $fb_top_profiles[0]->uhits);
			?>
			<tr>
			<td><?php echo $fb_profile->username; ?></td>
			<td ><img style="margin-bottom:1px" src="<?php echo KUNENA_DIRECTURL.'template/default/images/bar.png'; ?>" alt="" height="15" width="<?php echo $barwidth;?>" /> </td>
			<td ><?php echo $fb_profile->uhits;?></td>
			</tr>
			<?php
				}
				if (empty($fb_top_profiles)) {
					echo '<tr><td colspan="3">---</td></tr>';
				}
				?>
		</tbody>
		</table>
	</td>
	</tr>
</table>
<!-- F: UserStat -->
<!-- Thank you stat -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td width="49%" valign="top">
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
				$KUNENA_top_thankyous=KunenaThankYou::getMostThankYou();
				foreach ($KUNENA_top_thankyous as $KUNENA_thankyou) {
					if ($KUNENA_thankyou->countid == $KUNENA_top_thankyous[0]->countid) {
						$barwidth = 100;
					}
					else {
						$barwidth = round(($KUNENA_thankyou->countid * 100) / $KUNENA_top_thankyous[0]->countid);
					}
			?>
			<tr>
			<td><?php echo $KUNENA_thankyou->username;?> </td>
			<td ><img style="margin-bottom:1px" src="<?php echo KUNENA_DIRECTURL.'template/default/images/bar.png'; ?>" alt="" height="15" width="<?php echo $barwidth;?>" /> </td>
			<td ><?php echo $KUNENA_thankyou->countid;?></td>
			</tr>
			<?php
				}
				if (empty($KUNENA_top_thankyous)) {
					echo '<tr><td colspan="3">---</td></tr>';
				}
				?>
		</tbody>
		</table>
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
		$KUNENA_top_said_thankyous=KunenaThankYou::getMostThankYou('said');
				foreach ($KUNENA_top_said_thankyous as $KUNENA_said_thankyou) {
					if ($KUNENA_said_thankyou->countid == $KUNENA_top_said_thankyous[0]->countid) {
						$barwidth = 100;
					}
					else {
						$barwidth = round(($KUNENA_said_thankyou->countid * 100) / $KUNENA_top_said_thankyous[0]->countid);
					}
			?>
			<tr>
			<td><?php echo $KUNENA_said_thankyou->username;?> </td>
			<td ><img style="margin-bottom:1px" src="<?php echo KUNENA_DIRECTURL.'template/default/images/bar.png'; ?>" alt="" height="15" width="<?php echo $barwidth;?>" /> </td>
			<td ><?php echo $KUNENA_said_thankyou->countid;?></td>
			</tr>
			<?php
				}
				if (empty($KUNENA_top_said_thankyous)) {
					echo '<tr><td colspan="3">---</td></tr>';
				}
				?>
		</tbody>
		</table>
	</td>
	</tr>
</table>
<!-- Thank you stat -->
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
				$KUNENA_top_posts=jbStats::get_top_topics();
				foreach ($KUNENA_top_posts as $KUNENA_post) {
					if ($KUNENA_post->hits == $KUNENA_top_posts[0]->hits) {
						$barwidth = 100;
					}
					else {
						$barwidth = round(($KUNENA_post->hits * 100) / $KUNENA_top_posts[0]->hits);
					}
					$link = KUNENA_LIVEURL.'&amp;func=view&amp;id='.$KUNENA_post->id.'&amp;catid='.$KUNENA_post->catid;
				?>
			<tr>
				<td ><a href="<?php echo $link;?>"><?php echo $KUNENA_post->subject;?></a> </td>
				<td ><img src="<?php echo KUNENA_DIRECTURL.'template/default/images/bar.png'; ?>" alt="" style="margin-bottom:1px" height="15" width="<?php echo $barwidth;?>" /> </td>
				<td ><?php echo $KUNENA_post->hits;?></td>
			</tr>
			<?php }
				if (empty($KUNENA_top_posts)) {
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
				$KUNENA_top_posts=KunenaThankYou::getTopThankYouTopics();
				foreach ($KUNENA_top_posts as $KUNENA_post) {
					if ($KUNENA_post->countid == $KUNENA_top_posts[0]->countid) {
						$barwidth = 100;
					}
					else {
						$barwidth = round(($KUNENA_post->countid * 100) / $KUNENA_top_posts[0]->countid);
					}
					$link = KUNENA_LIVEURL.'&amp;func=view&amp;id='.$KUNENA_post->postid.'&amp;catid='.$KUNENA_post->catid;
				?>
			<tr>
				<td ><a href="<?php echo $link;?>"><?php echo $KUNENA_post->subject;?></a> </td>
				<td ><img src="<?php echo KUNENA_DIRECTURL.'template/default/images/bar.png'; ?>" alt="" style="margin-bottom:1px" height="15" width="<?php echo $barwidth;?>" /> </td>
				<td ><?php echo $KUNENA_post->countid;?></td>
			</tr>
			<?php }
				if (empty($KUNENA_top_posts)) {
					echo '<tr><td colspan="3">---</td></tr>';
				}
			?>
			</tbody>
		</table>
		<!-- Fnish : Top Thank you topics  -->
		</td>
	</tr>
</table>
</div>
<!-- FINISH: STATS -->
</div>