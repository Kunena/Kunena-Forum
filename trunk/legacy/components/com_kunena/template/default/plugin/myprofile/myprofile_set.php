<?php
/**
* @version $Id: myprofile_set.php 479 2007-12-12 18:52:34Z sisko1990 $
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
<form action = "<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=myprofile&amp;do=updateset'); ?>" method = "post" name = "postform">
	<input type = "hidden" name = "do" value = "updateset">
	<table class = "fb_blocktable" id = "fb_forumprofile_sub" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
		<thead>
			<tr>
				<th colspan = "2">
					<div class = "fb_title_cover">
						<span class = "fb_title"><?php echo _USER_GENERAL; ?></span>
					</div>
				</th>
			</tr>
		</thead>

		<tbody class = "fb_myprofile_general">
			<tr >
				<td >
					<strong><?php echo _USER_PREFERED; ?>*</strong>:
				</td>

				<td >
					<?php
					// make the select list for the view type
					$yesno[]  = mosHTML::makeOption('flat', _GEN_FLAT);
					$yesno[]  = mosHTML::makeOption('threaded', _GEN_THREADED);
					// build the html select list
					$tosend   = mosHTML::selectList($yesno, 'newview', 'class="inputbox" size="2"', 'value', 'text', $prefview);
					echo $tosend;
					?>
				</td>
			</tr>

			<tr >
				<td >
					<strong><?php echo _USER_ORDER; ?>*</strong>:
				</td>

				<td  colspan = "2">
					<?php
					// make the select list for the view type
					$yesno1[] = mosHTML::makeOption(0, _USER_ORDER_ASC);
					$yesno1[] = mosHTML::makeOption(1, _USER_ORDER_DESC);
					// build the html select list
					$tosend   = mosHTML::selectList($yesno1, 'neworder', 'class="inputbox" size="2"', 'value', 'text', $ordering);
					echo $tosend;
					echo '<br /><font size="1"><em>*' . _USER_CHANGE_VIEW . '</em></font>';
					?>
				</td>
			</tr>
            <tr >
				<td >
					<strong><?php echo _FB_USER_HIDEEMAIL; ?>*</strong>:
				</td>

				<td colspan = "2">
					<?php
					// make the select list for the view type
					$yesno3[] = mosHTML::makeOption(0, _COM_A_NO);
					$yesno3[] = mosHTML::makeOption(1, _COM_A_YES);
					// build the html select list
					$tosend   = mosHTML::selectList($yesno3, 'newhideEmail', 'class="inputbox" size="2"', 'value', 'text', $hideEmail);
					echo $tosend;
					
					?> 
				</td>
			</tr>
            
            <tr >
				<td >
					<strong><?php echo _USER_SHOWONLINE; ?>*</strong>:
				</td>

				<td  colspan = "2">
					<?php
					// make the select list for the view type
					$yesno4[] = mosHTML::makeOption(0, _COM_A_NO);
					$yesno4[] = mosHTML::makeOption(1, _COM_A_YES);
					// build the html select list
					$tosend   = mosHTML::selectList($yesno4, 'newshowOnline', 'class="inputbox" size="2"', 'value', 'text', $showOnline);
					echo $tosend;
					
					?>
				</td>
			</tr>
          

			<tr><td colspan = "2" align="center"><input type = "submit" class = "button" value = "<?php echo _GEN_SUBMIT;?>"></td>
			</tr>
		</tbody>
	</table>
</form>
</div>
</div>
</div>
</div>
</div>
