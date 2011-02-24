<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/

defined( '_JEXEC' ) or die();


class html_Kunena {
	// Begin: HEADER FUNC
	function showFbHeader() {
		$document = JFactory::getDocument();
		$document->addStyleSheet ( JURI::base().'components/com_kunena/media/css/admin.css' );
		?>
<!--[if IE]>
<style type="text/css">

table.kadmin-stat caption {
	display:block;
	font-size:12px !important;
	padding-top: 10px !important;
}

</style>
<![endif]-->

<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
			<?php
			} // Finish: HEADER FUNC

			// Begin: FOOTER FUNC
			function showFbFooter() {
?>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>

	<?php
	} // Finish: FOOTER FUNC

	function controlPanel() {
		$kunena_config = KunenaFactory::getConfig ();
		?>

	<div class="kadmin-functitle icon-cpanel"><?php echo JText::_('COM_KUNENA_CP'); ?></div>
	<?php
		$path = JPATH_COMPONENT_ADMINISTRATOR . '/kunena.cpanel.php';

		if (file_exists ( $path )) {
			require $path;
		} else {
			echo '<br />mcap==: ' . JPATH_COMPONENT_ADMINISTRATOR . ' .... help!!';
		}
	}

			function showCss($file, $option) {
				$f = fopen ( $file, "r" );
				$content = fread ( $f, filesize ( $file ) );
		?>
		<div class="kadmin-functitle icon-editcss"><?php echo JText::_('COM_KUNENA_CSSEDITOR'); ?></div>
		<form action="index.php?" method="post" name="adminForm" class="adminForm" id="adminForm">
			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
				<tr>
					<th colspan="4"><?php echo JText::_('COM_KUNENA_PATH'). kescape($file); ?></th>
				</tr>
				<tr>
					<td><textarea cols="100" rows="20" name="csscontent"><?php echo kescape($content); ?></textarea></td>
				</tr>
				<tr>
					<td class="error"><?php echo JText::_('COM_KUNENA_CSSERROR'); ?></td>
				</tr>
			</table>
			<input type="hidden" name="file" value="<?php echo kescape($file); ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value=""> <input type="hidden" name="boxchecked" value="0" /></form>
		<?php
			} //end function showCss

function showProfiles($option, &$users, $pageNav, $order, $lists) {
?>
<div class="kadmin-functitle icon-profiles"><?php echo JText::_('COM_KUNENA_FUM'); ?></div>
	<form action="index.php" method="post" name="adminForm">
		<table class="kadmin-sort">
			<tr>
				<td class="left" width="90%">
					<?php echo JText::_( 'COM_KUNENA_FILTER' ); ?>:
					<input type="text" name="search" id="search" value="<?php echo kescape($lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( 'COM_KUNENA_GO' ); ?></button>
					<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_KUNENA_RESET' ); ?></button>
				</td>
			</tr>
		</table>
		<table class="adminlist" border="0" cellspacing="0" cellpadding="3" width="100%">
			<thead>
				<tr>
					<th align="center" width="5">#</th>
					<th align="center" width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $users ); ?>);" /></th>
					<th align="center"><?php echo JText::_('COM_KUNENA_USRL_AVATAR'); ?></th>
					<th class="title" align="center"><?php echo JHTML::_('grid.sort', 'COM_KUNENA_ANN_ID', 'id', $lists['order_Dir'], $lists['order'] ); ?></th>
					<th align="left"><?php echo JHTML::_('grid.sort', 'COM_KUNENA_USRL_NAME', 'username', $lists['order_Dir'], $lists['order'] ); ?></th>
					<th align="left"><?php echo JHTML::_('grid.sort', 'COM_KUNENA_USRL_REALNAME', 'name', $lists['order_Dir'], $lists['order'] ); ?></th>
					<th align="center"><?php echo JText::_('COM_KUNENA_USRL_LOGGEDIN'); ?></th>
					<th align="center"><?php echo JText::_('COM_KUNENA_USRL_ENABLED'); ?></th>
					<th align="center"><?php echo JText::_('COM_KUNENA_USRL_BANNED'); ?></th>
<?php /*
					<th align="left"><?php echo JText::_('COM_KUNENA_GEN_EMAIL'); ?></th>
					<th align="left"><?php echo JText::_('COM_KUNENA_GEN_USERGROUP'); ?></th>
*/ ?>
					<th align="left"><?php echo JHTML::_('grid.sort', 'COM_KUNENA_VIEW_MODERATOR', 'moderator', $lists['order_Dir'], $lists['order'] ); ?></th>
					<th align="left"><?php echo JText::_('COM_KUNENA_GEN_SIGNATURE'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="14">
						<div class="pagination">
							<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'). $pageNav->getLimitBox (); ?></div>
							<?php echo $pageNav->getPagesLinks (); ?>
							<div class="limit"><?php echo $pageNav->getResultsCounter (); ?></div>
						</div>
					</td>
				</tr>
			</tfoot>
			<?php
			if (!empty($users)) {
					$k = 1;
					//foreach ($profileList as $pl)
					$i = 0;
					foreach($users as $user) {
						$kunena_user = KunenaFactory::getUser($user->id);
						$k = 1 - $k;
						$userLogged = $kunena_user->isOnline() ? '<img src="components/com_kunena/images/tick.png" width="16" height="16" border="0" alt="" />': '';
						$userEnabled = $kunena_user->isBlocked() ? 'publish_x.png' : 'tick.png';
						$altUserEnabled = $kunena_user->isBlocked() ? JText::_( 'Blocked' ) : JText::_( 'Enabled' );
						$userBlockTask =  $kunena_user->isBlocked() ? 'userunblock' : 'userblock';
						$userbanned = $kunena_user->isBanned() ? 'tick.png' : 'publish_x.png';
						$userBannedTask = $kunena_user->isBanned() ? 'userunban' : 'userban';
						$altUserBanned = $kunena_user->isBanned() ? JText::_( 'Banned' ) : JText::_( 'Not banned' );
					?>
			<tr class="row<?php echo $k; ?>">
			<td class="right"><?php echo $i + $pageNav->limitstart + 1; ?></td>
				<td align="center">
					<?php echo JHTML::_('grid.id', $i, $user->id) ?>
				</td>
				<td align="center" width="1%"><?php echo $kunena_user->getAvatarLink('kavatar', 36, 36); ?></td>
				<td align="center" width="1%"><?php echo kescape($kunena_user->userid); ?></td>
				<td>
					<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','userprofile')"><?php echo kescape($kunena_user->username); ?></a>
				</td>
				<td>
					<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','userprofile')"><?php echo kescape($kunena_user->name); ?></a></td>
				<td align="center"><?php echo $userLogged; ?></td>
				<td align="center">
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $userBlockTask; ?>')">
						<img src="images/<?php echo $userEnabled;?>" width="16" height="16" border="0" alt="<?php echo $altUserEnabled; ?>" />
					</a></td>
				<td align="center">
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $userBannedTask; ?>')">
						<img src="images/<?php echo $userbanned;?>" width="16" height="16" border="0" alt="<?php echo $altUserBanned; ?>" />
					</a>
				</td>
<?php /*
				<td width="100"><?php echo kescape($kunena_user->email);
						?>&nbsp;</td>
				<td width="100"><?php echo kescape($kunena_user->usertype);
						?>&nbsp;</td>
*/ ?>
				<td align="center">
					<?php
					if ($kunena_user->moderator) {
						echo JText::_('COM_KUNENA_ANN_YES');
					} else {
						echo JText::_('COM_KUNENA_ANN_NO');
					}
					?>
				</td>
				<td width="*"><?php echo kescape ( $kunena_user->signature ); ?></td>
			</tr>
		<?php $i++; }
		} else { ?>
			<tr><td colspan="11"><?php echo JText::_('COM_KUNENA_NOUSERSFOUND') ?></td></tr>
		<?php } ?>
		</table>
		<input type="hidden" name="filter_order" value="<?php echo kescape($lists['order']); ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo kescape($lists['order_Dir']); ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="showprofiles" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="limitstart" value="<?php echo $pageNav->limitstart ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
	<?php
} //end function showProfiles

function newModerator($option, $id, $moderators, &$modIDs, $forumName, &$userList, $countUL, $pageNav) {
	?>

	<form action="index.php" method="post" name="adminForm">
		<table cellpadding="4" class="adminheading" cellspacing="0" border="0" width="100%">
			<tr>
				<th width="100%" class="user"><?php echo JText::_('COM_KUNENA_ADDMOD'). kescape($forumName); ?>
				</th>
				<td><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?></td>
				<td><?php echo $pageNav->getLimitBox (); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<table class="adminlist" border="0" cellspacing="0" cellpadding="3" width="100%">
			<tr>
				<th width="20">#</th>
				<th width="20"><input type="checkbox" name="toggle" value=""
					onclick="checkAll(<?php echo count ( $userList ); ?>);" /></th>
				<th><?php echo JText::_('COM_KUNENA_ANN_ID'); ?></th>
				<th align="left"><?php echo JText::_('COM_KUNENA_USRL_NAME'); ?></th>
				<th align="left"><?php echo JText::_('COM_KUNENA_GEN_EMAIL'); ?></th>
				<th><?php echo JText::_('COM_KUNENA_PUBLISHED'); ?></th>
				<th>&nbsp;</th>
			</tr>
			<?php if ($countUL > 0) {
					$k = 0;
					$i = 0;

					for($i = 0, $n = count ( $userList ); $i < $n; $i ++) {
						$pl = &$userList [$i];
						$k = 1 - $k;
			?>
			<tr class="row<?php echo $k;
						?>">
				<td width="20" align="right"><?php echo $i + $pageNav->limitstart + 1;
						?></td>
				<td width="20"><input type="checkbox" id="cb<?php echo $i;
						?>"
					name="cid[]" value="<?php echo kescape($pl->id);
						?>"
					onclick="isChecked(this.checked);" /></td>
				<td width="20"><a
					href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=userprofile&amp;do=show&amp;user_id=<?php echo kescape($pl->id);
						?>"><?php echo kescape($pl->id);
						?></a>&nbsp;</td>
				<td><?php echo kescape($pl->name);
						?>&nbsp;</td>
				<td><?php echo kescape($pl->email);
						?>&nbsp;</td>
				<td align="center"><?php 		if ($moderators) {
							if (in_array ( $pl->id, $modIDs )) {
								echo "<img src=\"images/tick.png\">";
							} else {
								echo "<img src=\"images/publish_x.png\">";
							}
						} else {
							echo "<img src=\"images/publish_x.png\">";
						}
						?></td>
				<td>&nbsp;</td>
			</tr>

			<?php 	}
				} else {
					echo "<tr><td align='left' colspan='7'>" . JText::_('COM_KUNENA_NOMODSAV') . "</td></tr>";
				}
				?>

			<tr>
				<td class="kadmin-paging" colspan="6">
					<div class="pagination">
						<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'). $pageNav->getLimitBox (); ?></div>
							<?php echo $pageNav->getPagesLinks (); ?>
						<div class="limit"><?php echo $pageNav->getResultsCounter (); ?></div>
					</div>
				</td>
			</tr>

			<tr>
				<td colspan="7"><?php echo JText::_('COM_KUNENA_NOTEUS'); ?></td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo kescape($id); ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="task" value="newmoderator" />
		<input type="hidden" name="limitstart" value="0" /></form>

		<?php
}

function editUserProfile($option, $user, $subslist, $subscatslist, $selectRank, $selectPref, $selectMod, $selectOrder, $uid, $modCats, $useriplist) {
				$kunena_config = KunenaFactory::getConfig ();
				$kunena_db = &JFactory::getDBO ();
				//fill the variables needed later
				$signature = $user->signature;
				$username = $user->username;
				$avatarint = KunenaFactory::getAvatarIntegration();
				$editavatar = is_a($avatarint, 'KunenaAvatarKunena') ? true : false;
				$avatar = $avatarint->getLink($user->id, '', 'profile');
				$ordering = $user->ordering;
				//that's what we got now; later the 'future_use' columns can be used..

				$csubslist = count ( $subslist );
				//        include_once ('components/com_kunena/bb_adm.js'); ?>
		<div class="kadmin-functitle icon-profiles"> <?php echo JText::_('COM_KUNENA_PROFFOR'); ?>: <?php echo kescape($user->name) .' ('. kescape($user->username) .')'; ?></div>
		<form action="index.php?option=<?php echo $option; ?>" method="post" name="adminForm">
		<?php jimport('joomla.html.pane');
			$myTabs = &JPane::getInstance('tabs', array('startOffset'=>0));
			?>
		<dl class="tabs" id="pane">

		<dt title="<?php echo JText::_('COM_KUNENA_A_BASIC_SETTINGS') ?>"><?php echo JText::_('COM_KUNENA_A_BASIC_SETTINGS') ?></dt>
		<dd>
		<fieldset>
		<legend><?php echo JText::_('COM_KUNENA_A_BASIC_SETTINGS') ?></legend>
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
			<tr>
				<th colspan="3" class="title"><?php echo JText::_('COM_KUNENA_GENPROF'); ?></th>
			</tr>
			<tr>
				<td width="150" class="contentpane"><?php echo JText::_('COM_KUNENA_PREFOR'); ?></td>
				<td align="left" valign="top" class="contentpane"><?php echo $selectOrder; ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="150" class="contentpane"><?php echo JText::_('COM_KUNENA_RANKS'); ?></td>
				<td align="left" valign="top" class="contentpane"><?php echo $selectRank; ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="150" valign="top" class="contentpane"><?php echo JText::_('COM_KUNENA_GEN_SIGNATURE'); ?>:
				</td>
				<td align="left" valign="top" class="contentpane">

<script type="text/javascript">
var current_count = <?php echo JString::strlen($signature) ?>;
var max_count = <?php echo $kunena_config->maxsig; ?>;

function textCounter(field, target) {
	if (field.value.length > max_count) {
		field.value = field.value.substring(0, max_count);
	} else {
		current_count = max_count - field.value.length;
		target.value = current_count;
	}
}
</script>

	<textarea class="inputbox" name="message" cols="50" rows="6"
	onkeyup="textCounter(this, this.form.current_count);"><?php echo kescape( $signature ); ?></textarea>
	<br /><br />
	<div><?php echo JText::sprintf('COM_KUNENA_SIGNATURE_LENGTH_COUNTER', intval($kunena_config->maxsig),
			'<input readonly="readonly" type="text" name="current_count" value="'.(intval($kunena_config->maxsig)-JString::strlen($signature)).'" size="3" />');?>
	</div>
	<br />
	<div> <input type="checkbox" value="1" name="deleteSig" /> <em><?php echo JText::_('COM_KUNENA_DELSIG'); ?></em></div>

	</td>
	</tr>
	</table>
</fieldset>
</dd>
<dt title="<?php echo JText::_('COM_KUNENA_A_AVATARS') ?>"><?php echo JText::_('COM_KUNENA_A_AVATARS') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_AVATARS') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
				<tr>
				<th colspan="2" class="title"><?php echo JText::_('COM_KUNENA_UAVATAR'); ?></th>
			</tr>
			<tr>
				<td class="contentpane">
				<?php echo $avatar;
				if ($editavatar) { ?>
					<p><input type="checkbox" value="1"
					name="deleteAvatar" /> <em><?php echo JText::_('COM_KUNENA_DELAV'); ?></em></p></td>
				<?php } else {
					echo "<td>&nbsp;</td>";
					echo '<input type="hidden" value="" name="avatar" />';
				}
				?>

				<td><?php if ($editavatar) {
					 } else {
					echo "<td>&nbsp;</td>";
				}
				?></td>
			</tr>
		</table>

	</fieldset>
</dd>

	<dt title="<?php echo JText::_('COM_KUNENA_MOD_NEW') ?>"><?php echo JText::_('COM_KUNENA_MOD_NEW') ?></dt>
		<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_MOD_NEW') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr>
						<th colspan="2" class="title"><?php echo JText::_('COM_KUNENA_MODCHANGE'); ?></th>
					</tr>
					<tr>
						<td width="150" class="contentpane"><?php echo JText::_('COM_KUNENA_ISMOD'); ?></td>
						<td><?php echo JText::_('COM_KUNENA_MODCATS'); ?></td>
					</tr>
					<tr>
						<td width="150" class="contentpane"><?php
						echo $selectMod;
						?>
						</td>
						<td><?php echo $modCats; ?></td>
					</tr>
				</table>
				<input type="hidden" name="uid" value="<?php echo kescape($uid); ?>" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="option" value="com_kunena" />
				<input type="hidden" name="boxchecked" value="1" />
			</fieldset>
		</dd>
		<dt title="<?php echo JText::_('COM_KUNENA_CATEGORIES_SUBSCRIPTIONS') ?>"><?php echo JText::_('COM_KUNENA_CATEGORIES_SUBSCRIPTIONS') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_CATEGORIES_SUBSCRIPTIONS') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr>
						<th colspan="2" class="title"><?php
						echo JText::_('COM_KUNENA_SUBFOR');
						echo kescape($username);
						?>
						</th>
					</tr>
					<?php
					$enum = 1; //reset value
					$k = 0; //value for alternating rows

					if (!empty($subscatslist)) {
						foreach($subscatslist as $subscats) { //get all category details for each subscription
							$kunena_db->setQuery ( "select cat.name AS catname, cat.id, msg.subject, msg.id, msg.catid, msg.name AS username from #__kunena_categories AS cat INNER JOIN #__kunena_messages AS msg ON cat.id=msg.catid where cat.id='$subscats->category_id' GROUP BY cat.id" );
							$catdetail = $kunena_db->loadObjectList ();
							if (KunenaError::checkDatabaseError()) break;

							foreach ( $catdetail as $cat ) {
								$k = 1 - $k;
								echo "<tr class=\"row$k\">";
								echo "  <td width=\"30\">$enum</td>";
								echo " <td><strong>" . kescape ( $cat->catname ) ."</strong>" ." &nbsp;". JText::_('COM_KUNENA_LAST_MESSAGE'). "<em>".kescape ( $cat->subject )."</em>" ." &nbsp;". JText::_('COM_KUNENA_BY') ." &nbsp;". "<em>".kescape ( $cat->username )."</em></td>";
								echo "</tr>";
								$enum ++;
							}
						}
					} else {
						echo "<tr><td class=\"message\">" . JText::_('COM_KUNENA_NOCATSUBS') . "</td></tr>";
					}
					?>
				</table>
			</fieldset>
			</dd>
			<dt title="<?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS') ?>"><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr>
						<th colspan="2" class="title"><?php
						echo JText::_('COM_KUNENA_SUBFOR');
						echo kescape($username);
						?>
						</th>
					</tr>
					<?php
						$enum = 1; //reset value
						$k = 0; //value for alternating rows


					if ($csubslist > 0) {
						foreach ( $subslist as $subs ) { //get all message details for each subscription
							$kunena_db->setQuery ( "select * from #__kunena_messages where id=$subs->thread" );
							$subdet = $kunena_db->loadObjectList ();
							if (KunenaError::checkDatabaseError()) break;

							foreach ( $subdet as $sub ) {
								$k = 1 - $k;
								echo "<tr class=\"row$k\">";
								echo "  <td width=\"30\">$enum</td>";
								echo " <td><strong>" . kescape ( $sub->subject ) ."</strong>" ." &nbsp;". JText::_('COM_KUNENA_BY' ) ." &nbsp;". "<em>".kescape ( $sub->name )."</em></td>";
								echo "</tr>";
								$enum ++;
							}
						}
					} else {
						echo "<tr><td class=\"message\">" . JText::_('COM_KUNENA_NOSUBS') . "</td></tr>";
					}
					?>
				</table>
			</fieldset>
		</dd>
		<dt title="<?php echo JText::_('COM_KUNENA_TRASH_IP') ?>"><?php echo JText::_('COM_KUNENA_TRASH_IP') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_TRASH_IP') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr>
						<th colspan="3" class="title"><?php
						echo JText::sprintf('COM_KUNENA_IPFOR', kescape($username));
						?>
						</th>
					</tr>
					<?php
					$i = 0;
					$k = 0; //value for alternating rows

					$userids='';
					foreach ($useriplist as $ip => $list) {
						$k = 1 - $k;
						$i++;
						$userlist = array();
						$mescnt = 0;
						foreach ($list as $curuser) {
							if ($curuser->userid == $user->id) {
								$mescnt += intval($curuser->mescnt);
								continue;
							}
							$userlist[] = kescape($curuser->username).' ('.kescape($curuser->mescnt).')';
						}
						$userlist = implode(', ', $userlist);
						echo "<tr class=\"row$k\">";
						echo "  <td width=\"30\">".$i."</td>";
						echo "  <td width=\"60\"><strong>".kescape($ip)."</strong></td>";
						echo "  <td>(".JText::sprintf('COM_KUNENA_IP_OCCURENCES', $mescnt).(!empty($userlist)?" ".JText::sprintf('COM_KUNENA_USERIDUSED', kescape($userlist)):'').")</td>";
						//echo "  <td>&nbsp;</td>";
						echo "</tr>";
					}
					?>
				</table>
			</fieldset>
		</dd>
	</dl>
<?php echo JHTML::_( 'form.token' ); ?>
</form>
	<?php
		}
		function moveUserMessages ( $option, $return, $uid, $lists, $userids ) {
	?>
			<div class="kadmin-functitle icon-profiles"><?php echo JText::_('COM_KUNENA_A_MOVE_USERMESSAGES'); ?></div>
			<table class="adminform">
				<tbody>
					<tr>
						<td>
						<strong><?php echo JText::_('COM_KUNENA_CATEGORY_TARGET'); ?></strong>
						<form action="index.php" method="post" name="adminForm">
						<?php
							echo $lists;
						?>
						<input type="hidden" name="boxchecked" value="1" />
						<input type="hidden" name="return" value="<?php echo kescape($return);?>" />
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="option" value="<?php echo $option; ?>" />
						<input type="hidden" name="uid[]" value="<?php echo implode(',',$uid); ?>" />
						<?php echo JHTML::_( 'form.token' ); ?>
						</form>
						</td>
						<td><strong><?php echo JText::_('COM_KUNENA_MOVEUSERMESSAGES_USERS_CURRENT'); ?></strong>
						<ol>
						<?php
						foreach($userids as $id){
							echo '<li>'.kescape($id->username).' ('.JText::_('COM_KUNENA_TRASH_AUTHOR_USERID').' '.kescape($id->id).')</li> ';
						}

						?>
						</ol>
						</td>
					</tr>
				</tbody>
			</table>
	<?php
		}

} //end class