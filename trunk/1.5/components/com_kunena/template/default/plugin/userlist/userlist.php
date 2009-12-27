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
* Code borrowed from Userlist
* Created : 23-May-2004, Emir Sakic, http://www.sakic.net
* The "GNU General Public License" (GPL) is available at
* http://www.gnu.org/copyleft/gpl.html.
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

$kunena_config =& CKunenaConfig::getInstance();
$document=& JFactory::getDocument();

$document->setTitle(_KUNENA_USRL_USERLIST . ' - ' . stripslashes($kunena_config->board_title));

list_users();

function list_users()
{
    global $lang;

    $kunena_config =& CKunenaConfig::getInstance();

    $kunena_db = &JFactory::getDBO();

    jimport('joomla.html.pagination');

    $orderby = JRequest::getVar('orderby', 'registerDate');
    $direction = JRequest::getVar('direction', 'ASC');
    $search = JRequest::getVar('search', '');
    $limitstart = JRequest::getInt('limitstart', 0);
    $limit = JRequest::getInt('limit', $kunena_config->userlist_rows);

    // Total
    $kunena_db->setQuery("SELECT COUNT(*) FROM #__users WHERE block =0");
    $total_results = $kunena_db->loadResult();

    // Search total
    $query = "SELECT COUNT(*) FROM #__users AS u INNER JOIN #__fb_users AS fu ON u.id=fu.userid";

    if ($search != "") {
        $query .= " WHERE (u.name LIKE '%$search%' OR u.username LIKE '%$search%')";
    }

    $kunena_db->setQuery($query);
    $total = $kunena_db->loadResult();

    if ($limit > $total) {
        $limitstart = 0;
    }

    $query_ext = "";
    // Select query
    $query
        = "SELECT u.id, u.name, u.username, u.usertype, u.email, u.registerDate, u.lastvisitDate, fu.userid, fu.showOnline, fu.group_id, fu.posts, fu.karma, fu.uhits, g.id AS gid, g.title "
        ." FROM #__users AS u INNER JOIN #__fb_users AS fu ON fu.userid = u.id INNER JOIN #__fb_groups AS g ON g.id = fu.group_id WHERE block =0";

    if ($search != "")
    {
        $query .= " WHERE (name LIKE '%$search%' OR username LIKE '%$search%')";
        $query_ext .= "&amp;search=" . $search;
    }

    $query .= " ORDER BY $orderby $direction, id $direction";

    if ($orderby != "id") {
        $query_ext .= "&amp;orderby=" . $orderby . "&amp;direction=" . $direction;
    }

    $query .= " LIMIT $limitstart, $limit";

    $kunena_db->setQuery($query);
    $ulrows = $kunena_db->loadObjectList();

    // echo "<pre>"; print_r($ulrows); die;
    $pageNav = new JPagination($total, $limitstart, $limit);
    HTML_userlist_content::showlist($ulrows, $total_results, $pageNav, $limitstart, $query_ext, $search);
}

function convertDate($date)
{
	// used for non-FB dates only!
    $format = _KUNENA_USRL_DATE_FORMAT;

    if ($date != "0000-00-00 00:00:00" && preg_match('`(\d{4})-(\d{2})-(\d{2})[[:space:]](\d{2}):(\d{2}):(\d{2})`', $date, $regs))
    {
        $date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
        $date = $date > -1 ? strftime($format, CKunenaTools::fbGetShowTime($date, 'UTC')) : '-';
    }
    else {
        $date = _KUNENA_USRL_NEVER;
    }

    return $date;
}
?>

<?php
class HTML_userlist_content
{
    function showlist($ulrows, $total_results, $pageNav, $limitstart, $query_ext, $search = "")
    {
    	global $kunena_is_moderator;
		$kunena_app =& JFactory::getApplication();
        $kunena_config =& CKunenaConfig::getInstance();
        $kunena_db = &JFactory::getDBO();

        if ($search == "") {
            $search = _KUNENA_USRL_SEARCH;
        }
?>

        <script type = "text/javascript">
            <!--
            function validate()
            {
                if ((document.usrlform.search == "") || (document.usrlform.search.value == ""))
                {
                    alert('<?php echo _KUNENA_USRL_SEARCH_ALERT; ?>');
                    return false;
                }
                else
                {
                    return true;
                }
            }
                    //-->
        </script>
		<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr1">
		<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr2">
		<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr3">
		<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr4">
		<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr5">
        <table class = "fb_blocktable" id ="fb_userlist" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
                <tr>
                    <th>
                        <table width = "100%" border = "0" cellspacing = "0" cellpadding = "0">
                            <tr>
                                <td align = "left">
                                    <div class = "fb_title_cover  fbm">
                                        <span class="fb_title fbl"> <?php echo _KUNENA_USRL_USERLIST; ?></span>

                                        <?php
                                        printf(_KUNENA_USRL_REGISTERED_USERS, $kunena_app->getCfg('sitename'), $total_results);
                                        ?>
                                    </div>
                                </td>

                                <td align = "right">
                                    <form name = "usrlform" method = "post" action = "<?php echo CKunenaLink::GetUserlistURL(); ?>" onsubmit = "return validate()">
                                        <input type = "text"
                                            name = "search"
                                            class = "inputbox"
                                            style = "width:150px"
                                            maxlength = "100" value = "<?php echo $search; ?>" onblur = "if(this.value=='') this.value='<?php echo $search; ?>';" onfocus = "if(this.value=='<?php echo $search; ?>') this.value='';" />

                                        <input type = "image" src = "<?php echo KUNENA_TMPLTMAINIMGURL .'/images/usl_search_icon.gif' ;?>" alt = "<?php echo _KUNENA_USRL_SEARCH; ?>" align = "top" style = "border: 0px;"/>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td class = "<?php echo KUNENA_BOARD_CLASS; ?>fb-userlistinfo">
                        <!-- Begin: Listing -->
                        <table width = "100%" border = "0" cellspacing = "0" cellpadding = "0">
                            <tr class = "fb_sth  fbs">
                                <th class = "th-1 frst <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="center">
                                </th>

                                <?php
                                if ($kunena_config->userlist_online)
                                {
                                ?>

                                    <th class = "th-2 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="center">
<?php echo _KUNENA_USRL_ONLINE; ?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_avatar)
                                {
                                ?>

                                    <th class = "th-3 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="center">
<?php echo _KUNENA_USRL_AVATAR; ?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_name)
                                {
                                ?>

                                    <th class = "th-4 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="center">
<?php echo _KUNENA_USRL_NAME; ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=name&amp;direction=ASC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/down.gif" border="0" alt="' . _KUNENA_USRL_ASC .'" />'); ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=name&amp;direction=DESC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/up.gif" border="0" alt="' . _KUNENA_USRL_DESC .'" />'); ?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_username)
                                {
                                ?>

                                    <th class = "th-5 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="center">
<?php echo _KUNENA_USRL_USERNAME; ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=username&amp;direction=ASC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/down.gif" border="0" alt="' . _KUNENA_USRL_ASC .'" />'); ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=username&amp;direction=DESC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/up.gif" border="0" alt="' . _KUNENA_USRL_DESC .'" />'); ?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_posts)
                                {
                                ?>

                                    <th class = "th-7 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="center">
<?php echo _KUNENA_USRL_POSTS; ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=posts&amp;direction=ASC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/down.gif" border="0" alt="' . _KUNENA_USRL_ASC .'" />'); ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=posts&amp;direction=DESC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/up.gif" border="0" alt="' . _KUNENA_USRL_DESC .'" />'); ?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_karma)
                                {
                                ?>

                                    <th class = "th-7 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="center">
<?php echo _KUNENA_USRL_KARMA; ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=karma&amp;direction=ASC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/down.gif" border="0" alt="' . _KUNENA_USRL_ASC .'" />'); ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=karma&amp;direction=DESC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/up.gif" border="0" alt="' . _KUNENA_USRL_DESC .'" />'); ?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_email)
                                {
                                ?>

                                    <th class = "th-8 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="center">
<?php echo _KUNENA_USRL_EMAIL; ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=email&amp;direction=ASC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/down.gif" border="0" alt="' . _KUNENA_USRL_ASC .'" />'); ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=email&amp;direction=DESC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/up.gif" border="0" alt="' . _KUNENA_USRL_DESC .'" />'); ?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_usertype)
                                {
                                ?>

                                    <th class = "th-9 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="center">
<?php echo _KUNENA_USRL_USERTYPE; ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=usertype&amp;direction=ASC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/down.gif" border="0" alt="' . _KUNENA_USRL_ASC .'" />'); ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=usertype&amp;direction=DESC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/up.gif" border="0" alt="' . _KUNENA_USRL_DESC .'" />'); ?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_joindate)
                                {
                                ?>

                                    <th class = "th-10 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="center">
<?php echo _KUNENA_USRL_JOIN_DATE; ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=registerDate&amp;direction=ASC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/down.gif" border="0" alt="' . _KUNENA_USRL_ASC .'" />'); ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=registerDate&amp;direction=DESC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/up.gif" border="0" alt="' . _KUNENA_USRL_DESC .'" />'); ?>
                                     </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_lastvisitdate)
                                {
                                ?>

                                    <th class = "th-11  <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="center">
<?php echo _KUNENA_USRL_LAST_LOGIN; ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=lastvisitDate&amp;direction=ASC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/down.gif" border="0" alt="' . _KUNENA_USRL_ASC .'" />'); ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=lastvisitDate&amp;direction=DESC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/up.gif" border="0" alt="' . _KUNENA_USRL_DESC .'" />'); ?>
                                    </th>

                                <?php
                                }
                                ?>

								  <?php
                                if ($kunena_config->userlist_userhits)
                                {
                                ?>
								<th class = "th-12 lst <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="center">
<?php echo _KUNENA_USRL_HITS; ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=uhits&amp;direction=ASC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/down.gif" border="0" alt="' . _KUNENA_USRL_ASC .'" />'); ?>
<?php echo CKunenaLink::GetUserlistLink('&amp;orderby=uhits&amp;direction=DESC', '<img src="' . KUNENA_TMPLTMAINIMGURL . '/images/up.gif" border="0" alt="' . _KUNENA_USRL_DESC .'" />'); ?>
								</th>
                                <?php
                                }
                                ?>

                            </tr>

                            <?php
                            $i = 1;

                            foreach ($ulrows as $ulrow)
                            {
                                $evenodd = $i % 2;

                                if ($evenodd == 0) {
                                    $usrl_class = "sectiontableentry1";
                                }
                                else {
                                    $usrl_class = "sectiontableentry2";
                                }

                                $nr = $i + $limitstart;

                                // Avatar
                                $uslavatar = '';
                                if ($kunena_config->avatar_src == "clexuspm") {
                                    $uslavatar = '<img  border="0" class="usl_avatar" src="' . MyPMSTools::getAvatarLinkWithID($ulrow->id, "s") . '" alt="" />';
                                }
                                else if ($kunena_config->avatar_src == "cb")
                                {
                                	$kunenaProfile =& CKunenaCBProfile::getInstance();
									$uslavatar = $kunenaProfile->showAvatar($ulrow->id);
                                }
                                else if ($kunena_config->avatar_src == "aup") // integration AlphaUserPoints
                                {
                                	$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
                                	if ( file_exists($api_AUP)) {
                                		( $kunena_config->fb_profile=='aup' ) ? $showlink=1 : $showlink=0;
                                		 $uslavatar = AlphaUserPointsHelper::getAupAvatar( $ulrow->id, $showlink, $kunena_config->avatarsmallwidth, $kunena_config->avatarsmallheight );
                                	} // end integration AlphaUserPoints
                                }
                                else
                                {
                                    $kunena_db->setQuery("SELECT avatar FROM #__fb_users WHERE userid='{$ulrow->id}'");
                                    $avatar = $kunena_db->loadResult();

                                    if ($avatar != '') {

									if(!file_exists(KUNENA_PATH_UPLOADED .DS. 'avatars/s_' . $avatar)) {
										$uslavatar = '<img  border="0" class="usl_avatar" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/' . $avatar . '" alt="" />';
										}else {
                                        $uslavatar = '<img  border="0" class="usl_avatar" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/s_' . $avatar . '" alt="" />';
										}
                                    }
                                    else {$uslavatar = '<img  border="0" class="usl_avatar" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/s_nophoto.jpg" alt="" />'; }
                                }
                                //
                            ?>

                                <tr class = "<?php echo KUNENA_BOARD_CLASS; ?><?php echo $usrl_class ;?>  fbm">
                                    <td class = "td-1 frst fbs" align="center">
<?php echo $nr; ?>
                                    </td>

                                    <?php
                                    if ($kunena_config->userlist_online)
                                    {
                                    ?>

                                        <td class = "td-2">
                                            <?php // online - ofline status
                                            $sql = "SELECT COUNT(userid) FROM #__session WHERE userid='{$ulrow->id}'";
                                            $kunena_db->setQuery($sql);
                                            $isonline = $kunena_db->loadResult();



                                            if ($isonline && $ulrow->showOnline ==1 ) {
                                                echo isset($kunena_emoticons['onlineicon']) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons['onlineicon'] . '" border="0" alt="' . _MODLIST_ONLINE . '" />' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'onlineicon.gif" border="0"  alt="' . _MODLIST_ONLINE . '" />';
                                            }
                                            else {
                                                echo isset($kunena_emoticons['offlineicon']) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons['offlineicon'] . '" border="0" alt="' . _MODLIST_OFFLINE . '" />' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'offlineicon.gif" border="0"  alt="' . _MODLIST_OFFLINE . '" />';
                                            }
                                            ?>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($kunena_config->userlist_avatar)
                                    {
                                    ?>

                                        <td class = "td-3" align="center">
                                      <?php
                                      if(strlen($uslavatar)) {
						echo CKunenaLink::GetProfileLink($kunena_config, $ulrow->id, $uslavatar);
                                      }
                                      else { echo '&nbsp;'; }
                                      ?>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($kunena_config->userlist_name)
                                    {
                                    ?>

                                        <td class = "td-4  fbm" align="center">
						<?php echo CKunenaLink::GetProfileLink($kunena_config, $ulrow->id, $ulrow->name); ?>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($kunena_config->userlist_username)
                                    {
                                    ?>

                                        <td class = "td-5  fbm" align="center">
						<?php echo CKunenaLink::GetProfileLink($kunena_config, $ulrow->id, $ulrow->username); ?>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($kunena_config->userlist_posts)
                                    {
                                    ?>

                                        <td class = "td-7  fbs" align="center">
<?php echo $ulrow->posts; ?>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($kunena_config->userlist_karma)
                                    {
                                    ?>

                                        <td class = "td-7 fbs" align="center">
<?php echo $ulrow->karma; ?>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($kunena_config->userlist_email) {
                                        echo "\t\t<td class=\"td-8 fbs\"  align=\"center\"><a href=\"mailto:$ulrow->email\">$ulrow->email</a></td>\n";
                                    }

                                    if ($kunena_config->userlist_usertype) {
                                        echo "\t\t<td  class=\"td-9 fbs\"  align=\"center\">$ulrow->usertype</td>\n";
                                    }

                                    if ($kunena_config->userlist_joindate) {
                                        echo "\t\t<td  class=\"td-10 fbs\"  align=\"center\">" . convertDate($ulrow->registerDate) . "</td>\n";
                                    }

                                    if ($kunena_config->userlist_lastvisitdate) {
                                        echo "\t\t<td  class=\"td-11 fbs\"  align=\"center\">" . convertDate($ulrow->lastvisitDate) . "</td>\n";
                                    }
                                    ?>

                                    <td class = "td-12 lst fbs" align="center">
									 <?php
                                    if ($kunena_config->userlist_userhits)
                                    {
                                    ?>
									<?php echo $ulrow->uhits; ?>

                                    <?php
                                    }
                                    ?>
									 </td>

                            <?php
                                    echo "\t</tr>\n";
                                    $i++;
                            }
                            ?>

        </table>

		<form name = "usrlform" method = "post" action = "<?php echo CKunenaLink::GetUserlistURL(); ?>" onsubmit = "return false;">
        <table width = "100%"  class="fb_userlist_pagenav" border = "0" cellspacing = "0" cellpadding = "0">
            <tr class = "fb_sth  fbs">
                <th class = "th-1  fbm" align = "center" style = "text-align:center;">

                            <?php
                            // TODO: fxstein - Need to perform SEO cleanup
                            echo $pageNav->getPagesLinks(CKunenaLink::GetUserlistURL($query_ext)); ?>
                </th>
            </tr>
        </table>



        <table class = "fb_blocktable" id="fb_userlist_bottom" style="border-bottom:0px;margin:0;" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
                <tr>
                    <th  class = "th-right  fbs" align="right" style="text-align:right">
                     <?php echo $pageNav->getPagesCounter(); ?> | <?php echo _KUNENA_USRL_DISPLAY_NR; ?> <?php echo $pageNav->getLimitBox(CKunenaLink::GetUserlistURL($query_ext)); ?>
                </th>
            </tr>
        </table>
		</form>

        </td>
	</tr>
  </tbody>
</table>
        <!-- Finish: Listing -->

</div>
</div>
</div>
</div>
</div>
        <?php
        //(JJ) BEGIN: WHOISONLINE
        if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/who/whoisonline.php')) {
            include(KUNENA_ABSTMPLTPATH . '/plugin/who/whoisonline.php');
        }
        else {
            include(KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/who/whoisonline.php');
        }
        //(JJ) FINISH: WHOISONLINE
        ?>
        <!-- Begin: Forum Jump -->
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr1">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr2">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr3">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr4">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr5">
        <table class = "fb_blocktable" id="fb_bottomarea"   border = "0" cellspacing = "0" cellpadding = "0">
            <thead>
                <tr>
                    <th  class = "th-right">
                        <?php
                        //(JJ) FINISH: CAT LIST BOTTOM
                        if ($kunena_config->enableforumjump) {
                            require_once(KUNENA_PATH_LIB .DS. 'kunena.forumjump.php');
                        }
                        ?>
                    </th>
                </tr>
            </thead>
			<tbody><tr><td></td></tr></tbody>
		</table>
        </div>
</div>
</div>
</div>
</div>
        <!-- Finish: Forum Jump -->

<?php
    }
}
?>
