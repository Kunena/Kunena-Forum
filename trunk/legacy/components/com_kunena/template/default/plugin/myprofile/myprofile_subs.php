<?php
/**
* @version $Id: myprofile_subs.php 947 2008-08-11 01:56:01Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');
?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<form action = "<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=myprofile&amp;do=unsubscribe'); ?>" method = "post" name = "postform">
	<input type = "hidden" name = "do" value = "unsubscribe"/>
	<table class = "fb_blocktable" id = "fb_forumprofile_sub" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
		<thead>
			<tr>
				<th colspan = "5">
					<div class = "fb_title_cover">
						<span class = "fb_title"><?php echo _USER_SUBSCRIPTIONS; ?></span>
					</div></th>
			</tr>
		</thead>

		<tbody id = "<?php echo $boardclass ;?>fbuserprofile_tbody">
			<tr class = "fb_sth">
				<th class = "th-1 <?php echo $boardclass ;?>sectiontableheader"><?php echo _GEN_TOPICS; ?>
				</th>

				<th class = "th-2 <?php echo $boardclass ;?>sectiontableheader" style = "text-align:center; width:25%"><?php echo _GEN_AUTHOR; ?>
				</th>

				<th class = "th-3 <?php echo $boardclass ;?>sectiontableheader" style = "text-align:center; width:25%"><?php echo _GEN_DATE; ?>
				</th>

				<th class = "th-3 <?php echo $boardclass ;?>sectiontableheader" style = "text-align:center; width:5%"><?php echo _GEN_HITS; ?>
				</th>

				<th class = "th-4 <?php echo $boardclass ;?>sectiontableheader"><?php echo _GEN_DELETE; ?>
				</th>
			</tr>

			<?php
			$enum = 1; //reset value
			$tabclass = array
			(
				"sectiontableentry1",
				"sectiontableentry2"
			);         //alternating row CSS classes

			$k    = 0; //value for alternating rows

			require("$mosConfig_absolute_path/includes/pageNavigation.php");
			$pageNav = new mosPageNav($total, $limitstart, $limit);

			if ($csubslist > 0)
			{
				foreach ($subslist as $subs)
				{ //get all message details for each subscription
					$database->setQuery("select * from #__fb_messages where id=$subs->thread ");
					$subdet = $database->loadObjectList();
						check_dberror("Unable to load messages.");

					foreach ($subdet as $sub)
					{
						$k = 1 - $k;

						echo '<tr class="' . $boardclass . '' . $tabclass[$k] . '" >';
						echo '<td class="td-1" width="54%" align="left">' . $enum . ': <a href="' . sefRelToAbs(JB_LIVEURLREL . '&amp;func=view&amp;catid=' . $sub->catid . '&amp;id=' . $sub->id) . '">' . $sub->subject;
			?>

						</a>

						</td>

						<td class = "td-2" style = "text-align:center; width:15%"> <?php echo $sub->name; ?></td>

						<td class = "td-3" style = "text-align:center; width:25%"> <?php echo '' . date(_DATETIME, $sub->time) . ''; ?></td>

						<td class = "td-4" style = "text-align:center; width:5%"> <?php echo $sub->hits; ?></td>

						<td class = "td-5" width = "1%">
							<input id = "cid<?php echo $enum;?>" name = "cid[]" value = "<?php echo $subs->thread; ?>"  type = "checkbox"/>
						</td>

						</tr>

			<?php
						$enum++;
					}
				}
			?>

				<tr>
					<td colspan = "5" class = "fb_profile-bottomnav" style = "text-align:right">
<?php echo _FB_USRL_DISPLAY_NR; ?>

<?php
echo $pageNav->writeLimitBox("index.php?option=com_fireboard&amp;func=myprofile&amp;do=showsub" . FB_FB_ITEMID_SUFFIX . "");
?>

			<input type = "submit" class = "button" value = "<?php echo _GEN_DELETE;?>"/>
					</td>
				</tr>

			<?php
			}
			else
			{
				echo '<tr class="' . $boardclass . '' . $tabclass[$k] . '"><td class="td-1" colspan = "5" >' . _USER_NOSUBSCRIPTIONS . '</td></tr>';
			}
			?>

			<tr><td colspan = "5" class = "fb_profile-bottomnav">
					<?php
					// TODO: fxstein - Need to perform SEO cleanup
					echo $pageNav->writePagesLinks("index.php?option=com_fireboard&amp;func=myprofile&amp;do=showsub" . FB_FB_ITEMID_SUFFIX);
					?>

					<br/>
<?php echo $pageNav->writePagesCounter(); ?>
				</td>
			</tr>
		</tbody>
	</table>
</form>
</div>
</div>
</div>
</div>
</div>