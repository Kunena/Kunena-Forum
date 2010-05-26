<?php
/**
 * @version $Id: editavatar.php 2406 2010-05-04 06:16:56Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
defined( '_JEXEC' ) or die();
$i=0;
JHTML::_('behavior.calendar');
	$path = KUNENA_PATH_LIB.'/kunena.moderation.class.php';
	require_once ($path);
	$kunena_mod = CKunenaModeration::getInstance();
	$iplist = $kunena_mod->getUserIPs ($this->user->id);
	$useriplist = $kunena_mod->getUsernameMatchingIPs($this->user->id);
?>

<h2><?php echo JText::_('Add new ban'); ?></h2>
<form id="kform-ban" name="kformban" action="index.php" method="post">
<table border="0" cellpadding="5" class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : ''; ?>">
	<tbody>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;padding: 1 10px;" width="40%"><?php //echo $this->id; ?><b>Username</b></td>
		<td style="text-align:left;padding: 1 10px;" width="64%"><?php echo $this->profile->username; ?> </td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="40%"><b><?php echo JText::_('User ID'); ?></b></td>
		<td style="text-align:left;padding: 1 10px;" width="64%"> <?php echo $this->profile->userid; ?> </td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="40%"><b><?php echo JText::_('Latest user IP'); ?></b><br /><span class="ks">Please select IP if you want to block</span></td>
		<td style="text-align:left;padding: 1 10px;" width="64%">
				<?php
				$ipselect = array();
				foreach ($iplist as $ip) {
					$ipselect [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_MODERATE_SELECT_IP') );
					$ipselect [] = JHTML::_ ( 'select.option', $ip->ip, $ip->ip );
					$ipselect [] = JHTML::_ ( 'select.option', 'allips', JText::_('COM_KUNENA_MODERATE_ALLIPS') );
				}
				echo $lists = JHTML::_ ( 'select.genericlist', $ipselect, 'prof_ip_select', 'class="inputbox" size="1"', 'value', 'text' );
				?>
		</td>
	</tr>
	<!--<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="40%"><b><?php echo JText::_('Latest user IP'); ?></b><br /><span class="ks">Please write IP if you want to block</span></td>
		<td style="text-align:left;padding: 1 10px;" width="64%"><input type="text" name="aim" value="<?php echo $this->profile->AIM; ?>" size="30" /> </td>
	</tr>-->
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="40%"><b><?php echo JText::_('Ban Level'); ?></b><br /><span class="blueban ks"><font color ="blue">Blue ban = Ban in Kunena (read-only mode).</font></span><br /><span class="redban ks"><font color ="red">Red ban = Block in Joomla (even block login).</font></span></td>
		<td style="text-align:left;padding: 1 10px;" width="64%"><?php
					// make the select list for the view type
					$gender[] = JHTML::_('select.option', 0, JText::_('Blue ban '));
					$gender[] = JHTML::_('select.option', 1, JText::_('Red ban '));
					// build the html select list
					echo JHTML::_('select.genericlist', $gender, 'gender', 'class="inputbox" size="1"', 'value', 'text', $this->profile->gender);
					?></td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="40%"><b><?php echo JText::_('Start Time'); ?></b><br /><span class="ks">YYYY-MM-DD - HH:MM:SS</span></td>
		<td style="text-align:left;padding: 1 10px;" width="64%"><input class="inputbox" type="text" maxlength="15"
				name="Start_Time" id="Start_Time"
				value="" /> <img
				src="templates/system/images/calendar.png" alt="Calendar"
				onclick="showCalendar('Start_Time','%Y-%m-%d');$('Start_Time').removeProperty('style');"
				onmouseover="javascript:$('helpbox').set('value', '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_POLLLIFESPAN'); ?>')" /></td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="40%"><b><?php echo JText::_('Expire Time'); ?></b><br /><span class="ks">YYYY-MM-DD - HH:MM:SS</span></td>
		<td style="text-align:left;padding: 1 10px;" width="64%"><input class="inputbox" type="text" maxlength="15"
				name="Expire_Time" id="Expire_Time"
				value="" /> <img
				src="templates/system/images/calendar.png" alt="Calendar"
				onclick="showCalendar('Expire_Time','%Y-%m-%d');$('Expire_Time').removeProperty('style');"
				onmouseover="javascript:$('helpbox').set('value', '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_POLLLIFESPAN'); ?>')" /></td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="40%"><b><?php echo JText::_('Public Reason'); ?></b><br /><span class="ks">Viewable Public reason of ban</span></td>
		<td style="text-align:left;padding: 1 10px;" width="64%"><textarea class=" required" name="signature"
		id="" style="width:60%;height:5em;" ><?php
			echo kunena_htmlspecialchars ( $this->profile->signature );
			?></textarea></td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="40%"><b><?php echo JText::_('Private Reason'); ?></b><br /><span class="ks">Admin viewable Private reason of ban</span></td>
		<td style="text-align:left;padding: 1 10px;" width="64%"><textarea class="required" name="signature"
		id="" style="width:60%;height:5em;"><?php
			echo kunena_htmlspecialchars ( $this->profile->signature );
			?></textarea></td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="40%"><b><?php echo JText::_('On Profile ?'); ?></b><br /><span class="ks">Show banlevel info or image on user profile page?</span></td>
		<td style="text-align:left;padding: 1 10px;" width="64%"><input id="konprofile_keep" type="checkbox" name="onprofile" value="keep" /></td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="40%"><b><?php echo JText::_('On Message ?'); ?></b><br /><span class="ks">Show banlevel info or image on user message?</span></td>
		<td style="text-align:left;padding: 1 10px;" width="64%"><input id="konmessage_keep" type="checkbox" name="onmessage" value="keep" /></td>
	</tr>
	<!--<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="40%"><b><?php echo JText::_('IP History'); ?></b></td>
		<td style="text-align:left;padding: 1 10px;" width="64%"> dragan </td>
	</tr>-->
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="40%"><b><?php echo JText::_('Follow IPs'); ?></b><br /><span class="ks">Find other users that might use the same IP</span></td>
		<td style="text-align:left;padding: 1 10px;" width="64%"><ul>
				<?php
				$usernames = array();
				foreach ($iplist as $ip) {
					$usernames = array_merge($usernames,$useriplist[$ip->ip]);
					$username = array();
					foreach ($usernames as $user) {
						$username[] = CKunenalink::GetProfileLink($user->userid, $user->name);
					}
					$username=implode(', ',$username);

					if (!empty($useriplist[$ip->ip])) {
				?>
				<li><span><a href="http://ws.arin.net/whois/?queryinput=<?php echo $ip->ip; ?>" target="_blank"><?php echo $ip->ip; ?></a></span> (<?php echo JText::_('COM_KUNENA_MODERATE_OTHER_USERS_WITH_IP'); ?>: <?php echo $username; ?>)</li>
				<?php
					} else {
					?>
				<li><span><a href="http://ws.arin.net/whois/?queryinput=<?php echo $ip->ip; ?>" target="_blank"><?php echo $ip->ip; ?></a></span> (<?php echo JText::_('COM_KUNENA_MODERATE_OTHER_USERS_WITH_IP'); ?>: <?php echo JText::_('COM_KUNENA_MODERATION_USER_NONE_IPS'); ?>)</li>
				<?php
					}
				} ?></ul>
		</td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="40%"><b><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_BAD_SIGNATURE'); ?></b></td>
		<td style="text-align:left;padding: 1 10px;" width="64%"><input type="checkbox" id="ban-delsignature" name="delsignature" value="delsignature" class="" /></td>
	</tr>
	<!--<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="40%"><b><?php echo JText::_('COM_KUNENA_MODERATE_BAN_USER'); ?></b></td>
		<td style="text-align:left;padding: 1 10px;" width="64%"><input type="checkbox" id="ban-user" name="banuser" value="banuser" class="" /></td>
	</tr>-->
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="40%"><b><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_BAD_AVATAR'); ?></b></td>
		<td style="text-align:left;padding: 1 10px;" width="64%"><input type="checkbox" id="ban-delavatar" name="delavatar" value="delavatar" class="" /></td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="40%"><b><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_BAD_PROFILEINFO'); ?></b></td>
		<td style="text-align:left;padding: 1 10px;" width="64%"><input type="checkbox" id="ban-delprofileinfo" name="delprofileinfo" value="delprofileinfo" class="" /></td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="40%"><b><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_ALL_POSTS'); ?></b></td>
		<td style="text-align:left;padding: 1 10px;" width="64%"><input type="checkbox" id="ban-delposts" name="bandelposts" value="bandelposts" class="" /></td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:center;" width="100%" colspan="2">
			<input class="kbutton kbutton ks" type="submit" value="<?php echo JText::_('Add ban'); ?>" name="Submit" />
			<input type="hidden" name="option" value="com_kunena" />
			<input type="hidden" name="func" value="banactions" />
			<input type="hidden" name="thisuserid" value="<?php echo $this->user->id; ?>" />
		</td>
	</tr>
</tbody>
</table>
</form>
