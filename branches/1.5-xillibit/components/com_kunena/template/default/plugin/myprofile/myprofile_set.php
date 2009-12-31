<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/
defined( '_JEXEC' ) or die('Restricted access');
?>
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr1">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr2">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr3">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr4">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr5">
<form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=myprofile&amp;do=updateset'); ?>" method = "post" name = "postform">
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
				<td>
					<strong><?php echo _USER_ORDER; ?>*</strong>:
				</td>

				<td>
					<?php
					// make the select list for the view type
					$yesno1[] = JHTML::_('select.option', 0, _USER_ORDER_ASC);
					$yesno1[] = JHTML::_('select.option', 1, _USER_ORDER_DESC);
					// build the html select list
					$tosend   = JHTML::_('select.genericlist', $yesno1, 'neworder', 'class="inputbox" size="2"', 'value', 'text', $ordering);
					echo $tosend;
					?>
				</td>
			</tr>
            <tr >
				<td>
					<strong><?php echo _KUNENA_USER_HIDEEMAIL; ?>*</strong>:
				</td>

				<td colspan = "2">
					<?php
					// make the select list for the view type
					$yesno3[] = JHTML::_('select.option', 0, _COM_A_NO);
					$yesno3[] = JHTML::_('select.option', 1, _COM_A_YES);
					// build the html select list
					$tosend   = JHTML::_('select.genericlist', $yesno3, 'newhideEmail', 'class="inputbox" size="2"', 'value', 'text', $hideEmail);
					echo $tosend;

					?>
				</td>
			</tr>

            <tr >
				<td>
					<strong><?php echo _USER_SHOWONLINE; ?>*</strong>:
				</td>

				<td>
					<?php
					// make the select list for the view type
					$yesno4[] = JHTML::_('select.option', 0, _COM_A_NO);
					$yesno4[] = JHTML::_('select.option', 1, _COM_A_YES);
					// build the html select list
					$tosend   = JHTML::_('select.genericlist', $yesno4, 'newshowOnline', 'class="inputbox" size="2"', 'value', 'text', $showOnline);
					echo $tosend;

					?>
				</td>
			</tr>


			<tr>
				<td colspan = "2" align="center">
					<input type = "submit" class = "button" value = "<?php echo _GEN_SUBMIT;?>">
				</td>
			</tr>
            <tr >
				<td colspan = "2">
					<?php echo '<br /><font size="1"><em>*' . _USER_CHANGE_VIEW . '</em></font>'; ?>
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
