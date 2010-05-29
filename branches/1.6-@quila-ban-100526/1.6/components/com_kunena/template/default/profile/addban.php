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

<h2><?php echo JText::_('COM_KUNENA_BAN_ADDBANFOR'); ?> <?php echo $this->profile->name; ?></h2>
<form id="kform-ban" name="kformban" action="index.php" method="post">
<table class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : ''; ?> kblock">
	<tbody>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="kcol-addban-left"><b><?php echo JText::_('COM_KUNENA_BAN_USERNAME'); ?></b></td>
		<td class="kcol-addban-right" width="600"><?php echo $this->profile->username; ?> </td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="kcol-addban-left"><b><?php echo JText::_('COM_KUNENA_BAN_USERID'); ?></b></td>
		<td class="kcol-addban-right"> <?php echo $this->profile->userid; ?> </td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="kcol-addban-left"><b><?php echo JText::_('COM_KUNENA_BAN_LATESTIP'); ?></b><br />
			<span class="ks"><?php echo JText::_('COM_KUNENA_BAN_LATESTIP_DESC'); ?></span>
		</td>
		<td class="kcol-addban-right">
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
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="kcol-addban-left" ><b><?php echo JText::_('COM_KUNENA_BAN_BANLEVEL'); ?></b><br />
			<span class="blueban ks"><?php echo JText::_('COM_KUNENA_BAN_BANLEVEL_BLUE_DESC'); ?></span><br />
			<span class="grayban ks"><?php echo JText::_('COM_KUNENA_BAN_BANLEVEL_GRAY_DESC'); ?></span><br />
			<span class="redban ks"><?php echo JText::_('COM_KUNENA_BAN_BANLEVEL_RED_DESC'); ?></span>
		</td>
		<td class="kcol-addban-right"><?php
					// make the select list for the view type
					$bantype[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_BAN_BANLEVEL_RED'));
					$bantype[] = JHTML::_('select.option', 2, JText::_('COM_KUNENA_BAN_BANLEVEL_GRAY'));
					$bantype[] = JHTML::_('select.option', 1, JText::_('COM_KUNENA_BAN_BANLEVEL_BLUE'));
					// build the html select list
					echo JHTML::_('select.genericlist', $bantype, 'bantype', 'class="inputbox" size="1"', 'value', 'text');
					?></td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="kcol-addban-left"><b><?php echo JText::_('COM_KUNENA_BAN_EXPIRETIME'); ?></b><br />
			<span class="ks"><?php echo JText::_('COM_KUNENA_BAN_STARTEXPIRETIME_DESC'); ?></span>
		</td>
		<td class="kcol-addban-right">
			<input class="inputbox" type="text" maxlength="15" name="Expire_Time" id="Expire_Time" />
				<img src="templates/system/images/calendar.png" alt="Calendar" onclick="showCalendar('Expire_Time','%Y-%m-%d');$('Expire_Time').removeProperty('style');" />
		</td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="kcol-addban-left"><b><?php echo JText::_('COM_KUNENA_BAN_PUBLICREASON'); ?></b><br />
			<span class="ks"><?php echo JText::_('COM_KUNENA_BAN_PUBLICREASON_DESC'); ?></span>
		</td>
		<td class="kcol-addban-right">
			<textarea class=" required" name="public_reason" id="public_reason" ></textarea>
		</td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="kcol-addban-left"><b><?php echo JText::_('COM_KUNENA_BAN_PRIVATEREASON'); ?></b><br />
			<span class="ks"><?php echo JText::_('COM_KUNENA_BAN_PRIVATEREASON_DESC'); ?></span>
		</td>
		<td class="kcol-addban-right">
			<textarea class="required" name="private_reason" id="private_reason"></textarea>
		</td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="kcol-addban-left"><b><?php echo JText::_('COM_KUNENA_BAN_FOLLOWIPS'); ?></b><br />
			<span class="ks"><?php echo JText::_('COM_KUNENA_BAN_FOLLOWIPS_DESC'); ?></span>
		</td>
		<td class="kcol-addban-right">
			<ul>
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
				<li><span>
					<a href="http://ws.arin.net/whois/?queryinput=<?php echo $ip->ip; ?>" target="_blank"><?php echo $ip->ip; ?></a>
					</span> (<?php echo JText::_('COM_KUNENA_MODERATE_OTHER_USERS_WITH_IP'); ?>: <?php echo $username; ?>)
				</li>
				<?php
					} else {
					?>
				<li><span>
					<a href="http://ws.arin.net/whois/?queryinput=<?php echo $ip->ip; ?>" target="_blank"><?php echo $ip->ip; ?></a>
					</span> (<?php echo JText::_('COM_KUNENA_MODERATE_OTHER_USERS_WITH_IP'); ?>: <?php echo JText::_('COM_KUNENA_MODERATION_USER_NONE_IPS'); ?>)
				</li>
				<?php
					}
				} ?>
			</ul>
		</td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="kcol-addban-left"><b><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_BAD_SIGNATURE'); ?></b></td>
		<td class="kcol-addban-right"><input type="checkbox" id="ban-delsignature" name="delsignature" value="delsignature" class="" /></td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="kcol-addban-left"><b><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_BAD_AVATAR'); ?></b></td>
		<td class="kcol-addban-right"><input type="checkbox" id="ban-delavatar" name="delavatar" value="delavatar" /></td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="kcol-addban-left"><b><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_BAD_PROFILEINFO'); ?></b></td>
		<td class="kcol-addban-right"><input type="checkbox" id="ban-delprofileinfo" name="delprofileinfo" value="delprofileinfo" /></td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="kcol-addban-left"><b><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_ALL_POSTS'); ?></b></td>
		<td class="kcol-addban-right"><input type="checkbox" id="ban-delposts" name="bandelposts" value="bandelposts" /></td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="kcol-addban-center" style="text-align:center;" width="100%" colspan="2">
			<input class="kbutton kbutton ks" type="submit" value="<?php echo JText::_('COM_KUNENA_BAN_ADDBAN'); ?>" name="Submit" />
			<input type="hidden" name="option" value="com_kunena" />
			<input type="hidden" name="func" value="banactions" />
			<input type="hidden" name="thisuserid" value="<?php echo $this->profile->userid; ?>" />
		</td>
	</tr>
</tbody>
</table>
</form>
