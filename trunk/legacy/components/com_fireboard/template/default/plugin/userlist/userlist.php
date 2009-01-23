<?php
/**
* @version $Id: userlist.php 1025 2008-08-23 09:17:43Z racoon $
* Fireboard Component
* @package Fireboard
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
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

global $base_url;
$base_url = "index.php?option=com_fireboard&amp;func=userlist" . FB_FB_ITEMID_SUFFIX; // Base URL string

list_users();

function list_users()
{
    global $database, $mosConfig_lang, $fbConfig;

    require_once("includes/pageNavigation.php");

    $orderby = mosGetParam($_REQUEST, 'orderby', 'registerDate');
    $direction = mosGetParam($_REQUEST, 'direction', 'ASC');
    $search = mosGetParam($_REQUEST, 'search', '');
    $limitstart = (int) mosGetParam($_REQUEST, 'limitstart', 0);
    $limit = (int) mosGetParam($_REQUEST, 'limit', $fbConfig->userlist_rows);

    // Total
    $database->setQuery("SELECT count(id) FROM #__users");
    $total_results = $database->loadResult();

    // Search total
    $query = "SELECT count(id) FROM #__users";

    if ($search != "") {
        $query .= " WHERE (name LIKE '%$search%' OR username LIKE '%$search%')";
    }

    $database->setQuery($query);
    $total = $database->loadResult();

    if ($limit > $total) {
        $limitstart = 0;
    }

    $query_ext = "";
    // Select query
    $query
        = "SELECT u.id, u.name, u.username , u.usertype , u.email , u.registerDate, u.lastvisitDate ,fu.showOnline, fu.group_id, fu.posts ,fu.karma , fu.uhits , g.title  "
        . "\nFROM #__users AS u " . "\nINNER JOIN #__fb_users AS fu ON fu.userid = u.id" . "\nINNER JOIN #__fb_groups AS g ON g.id = fu.group_id ";

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

    $database->setQuery($query);
    $ulrows = $database->loadObjectList();

    // echo "<pre>"; print_r($ulrows); die;
    $pageNav = new mosPageNav($total, $limitstart, $limit);
    HTML_userlist_content::showlist($ulrows, $total_results, $pageNav, $limitstart, $query_ext, $search);
}

function convertDate($date)
{
	// used for non-FB dates only!
    $format = _FB_USRL_DATE_FORMAT;

    if ($date != "0000-00-00 00:00:00" && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs))
    {
        $date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
        $date = $date > -1 ? strftime($format, FBTools::fbGetShowTime($date, 'UTC')) : '-';
    }
    else {
        $date = _FB_USRL_NEVER;
    }

    return $date;
}
?>

<?php
class HTML_userlist_content
{
    function showlist($ulrows, $total_results, $pageNav, $limitstart, $query_ext, $search = "")
    {
        global $base_url, $mosConfig_sitename, $fbConfig, $database;

        if ($search == "") {
            $search = _FB_USRL_SEARCH;
        }
?>

        <script type = "text/javascript">
            <!--
            function validate()
            {
                if ((document.usrlform.search == "") || (document.usrlform.search.value == ""))
                {
                    alert('<?php echo _FB_USRL_SEARCH_ALERT; ?>');
                    return false;
                }
                else
                {
                    return true;
                }
            }
                    //-->
        </script>

        <?php
        if ($fbConfig->joomlastyle < 1) {
            $boardclass = "fb_";
        }
        ?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
        <table class = "fb_blocktable" id ="fb_userlist" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
                <tr>
                    <th>
                        <table width = "100%" border = "0" cellspacing = "0" cellpadding = "0">
                            <tr>
                                <td align = "left">
                                    <div class = "fb_title_cover  fbm">
                                        <span class="fb_title fbl"> <?php echo _FB_USRL_USERLIST; ?></span>

                                        <?php
                                        printf(_FB_USRL_REGISTERED_USERS, $mosConfig_sitename, $total_results);
                                        ?>
                                    </div>
                                </td>

                                <td align = "right">
                                    <form name = "usrlform" method = "post" action = "<?php echo sefRelToAbs("$base_url"); ?>" onSubmit = "return validate()">
                                        <input type = "text"
                                            name = "search"
                                            class = "inputbox"
                                            style = "width:150px"
                                            maxlength = "100" value = "<?php echo $search; ?>" onblur = "if(this.value=='') this.value='<?php echo $search; ?>';" onFocus = "if(this.value=='<?php echo $search; ?>') this.value='';"/>

                                        <input type = "image" src = <?php echo  JB_TMPLTMAINIMGURL ."/images/usl_search_icon.gif" ;?> alt = "<?php echo _FB_USRL_SEARCH; ?>" align = "top" style = "border: 0px;"/>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td class = "<?php echo $boardclass; ?>fb-userlistinfo">
                        <!-- Begin: Listing -->
                        <table width = "100%" border = "0" cellspacing = "0" cellpadding = "0">
                            <tr class = "fb_sth  fbs">
                                <th class = "th-1 frst <?php echo $boardclass; ?>sectiontableheader" align="center">
                                </th>

                                <?php
                                if ($fbConfig->userlist_online)
                                {
                                ?>

                                    <th class = "th-2 <?php echo $boardclass; ?>sectiontableheader" align="center">
<?php echo _FB_USRL_ONLINE; ?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($fbConfig->userlist_avatar)
                                {
                                ?>

                                    <th class = "th-3 <?php echo $boardclass; ?>sectiontableheader" align="center">
<?php echo _FB_USRL_AVATAR; ?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($fbConfig->userlist_name)
                                {
                                ?>

                                    <th class = "th-4 <?php echo $boardclass; ?>sectiontableheader" align="center">
<?php echo _FB_USRL_NAME; ?> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=name&amp;direction=ASC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/down.gif"; ?> border = "0" alt = "<?php echo _FB_USRL_ASC; ?>"/></a> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=name&amp;direction=DESC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/up.gif";?>  border = "0" alt = "<?php echo _FB_USRL_DESC; ?>"/></a>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($fbConfig->userlist_username)
                                {
                                ?>

                                    <th class = "th-5 <?php echo $boardclass; ?>sectiontableheader" align="center">
<?php echo _FB_USRL_USERNAME; ?> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=username&amp;direction=ASC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/down.gif" ; ?> border = "0" alt = "<?php echo _FB_USRL_ASC; ?>"/></a> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=username&amp;direction=DESC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/up.gif" ;?> border = "0" alt = "<?php echo _FB_USRL_DESC; ?>"/></a>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($fbConfig->userlist_group)
                                {
                                ?>

                                    <th class = "th-6 <?php echo $boardclass; ?>sectiontableheader" align="center">
<?php echo _FB_USRL_GROUP; ?> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=group_id&amp;direction=ASC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/down.gif" ;?>  border = "0" alt = "<?php echo _FB_USRL_ASC; ?>"/></a> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=group_id&amp;direction=DESC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/up.gif";?> border = "0" alt = "<?php echo _FB_USRL_DESC; ?>"/></a>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($fbConfig->userlist_posts)
                                {
                                ?>

                                    <th class = "th-7 <?php echo $boardclass; ?>sectiontableheader" align="center">
<?php echo _FB_USRL_POSTS; ?> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=posts&amp;direction=ASC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/down.gif" ;?>  border = "0" alt = "<?php echo _FB_USRL_ASC; ?>"/></a> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=posts&amp;direction=DESC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/up.gif";?> border = "0" alt = "<?php echo _FB_USRL_DESC; ?>"/></a>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($fbConfig->userlist_karma)
                                {
                                ?>

                                    <th class = "th-7 <?php echo $boardclass; ?>sectiontableheader" align="center">
<?php echo _FB_USRL_KARMA; ?> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=karma&amp;direction=ASC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/down.gif" ;?>  border = "0" alt = "<?php echo _FB_USRL_ASC; ?>"/></a> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=karma&amp;direction=DESC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/up.gif";?> border = "0" alt = "<?php echo _FB_USRL_DESC; ?>"/></a>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($fbConfig->userlist_email)
                                {
                                ?>

                                    <th class = "th-8 <?php echo $boardclass; ?>sectiontableheader" align="center">
<?php echo _FB_USRL_EMAIL; ?> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=email&amp;direction=ASC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/down.gif"  ;?> border = "0" alt = "<?php echo _FB_USRL_ASC; ?>"/></a> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=email&amp;direction=DESC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/up.gif" ;?> border = "0" alt = "<?php echo _FB_USRL_DESC; ?>"/></a>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($fbConfig->userlist_usertype)
                                {
                                ?>

                                    <th class = "th-9 <?php echo $boardclass; ?>sectiontableheader" align="center">
<?php echo _FB_USRL_USERTYPE; ?> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=usertype&amp;direction=ASC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/down.gif" ;?> border = "0" alt = "<?php echo _FB_USRL_ASC; ?>"/></a> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=usertype&amp;direction=DESC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/up.gif" ;?> border = "0" alt = "<?php echo _FB_USRL_DESC; ?>"/></a>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($fbConfig->userlist_joindate)
                                {
                                ?>

                                    <th class = "th-10 <?php echo $boardclass; ?>sectiontableheader" align="center">
<?php echo _FB_USRL_JOIN_DATE; ?> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=registerDate&amp;direction=ASC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/down.gif" ;?>  border = "0" alt = "<?php echo _FB_USRL_ASC; ?>"/></a> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=registerDate&amp;direction=DESC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/up.gif";?> border = "0" alt = "<?php echo _FB_USRL_DESC; ?>"/></a>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($fbConfig->userlist_lastvisitdate)
                                {
                                ?>

                                    <th class = "th-11  <?php echo $boardclass; ?>sectiontableheader" align="center">
<?php echo _FB_USRL_LAST_LOGIN; ?> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=lastvisitDate&amp;direction=ASC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/down.gif" ;?> border = "0" alt = "<?php echo _FB_USRL_ASC; ?>"/></a> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=lastvisitDate&amp;direction=DESC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/up.gif" ;?> border = "0" alt = "<?php echo _FB_USRL_DESC; ?>"/></a>
                                    </th>

                                <?php
                                }
                                ?>

								 <th class = "th-12 lst <?php echo $boardclass; ?>sectiontableheader" align="center">
								  <?php
                                if ($fbConfig->userlist_userhits)
                                {
                                ?>
<?php echo _FB_USRL_HITS; ?> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=uhits&amp;direction=ASC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/down.gif" ;?> border = "0" alt = "<?php echo _FB_USRL_ASC; ?>"/></a> <a href = "<?php echo sefRelToAbs("$base_url&amp;orderby=uhits&amp;direction=DESC"); ?>">

    <img src = <?php echo JB_TMPLTMAINIMGURL ."/images/up.gif" ;?> border = "0" alt = "<?php echo _FB_USRL_DESC; ?>"/></a>

                                <?php
                                }
                                ?>
								</th>

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

                                // Profile Link
                                $profilelink =  sefRelToAbs(FB_PROFILE_LINK_SUFFIX."".$ulrow->id);

                                // Avatar
                                $uslavatar = '';
                                if ($fbConfig->avatar_src == "clexuspm") {
                                    $uslavatar = '<img  border="0" class="usl_avatar" src="' . MyPMSTools::getAvatarLinkWithID($ulrow->id, "s") . '" alt="" />';
                                }
                                else if ($fbConfig->avatar_src == "cb")
                                {
                                    $database->setQuery("SELECT avatar FROM #__comprofiler WHERE user_id='$ulrow->id' AND avatarapproved='1'");
                                    $avatar = $database->loadResult();

                                    //This now  has the right path to the upload directory and also handles the thumbnail and gallery photos.
                                    $imgpath = JB_JLIVEURL . '/images/comprofiler/';
                                    if ($avatar != '')
                                    {

                                        if (eregi("gallery/", $avatar) == false) {
                                            $imgpath .= "tn" . $avatar;
                                        }
                                        else {
                                            $imgpath .= $avatar;
                                        }

                                        $uslavatar = '<img  border="0" class="usl_avatar" src="' . $imgpath . '" alt="" />';
                                    }
                                    else {
                                        $imgpath = JB_JLIVEURL."/components/com_comprofiler/plugin/language/default_language/images/tnnophoto.jpg";
                                        $uslavatar = '<img  border="0" class="usl_avatar" src="' . $imgpath . '" alt="" />';
                                    }
                                }
                                else
                                {
                                    $database->setQuery("SELECT avatar FROM #__fb_users WHERE userid='$ulrow->id'");
                                    $avatar = $database->loadResult();

                                    if ($avatar != '') {

									if(!file_exists(FB_ABSUPLOADEDPATH . '/avatars/s_' . $avatar)) {
										$uslavatar = '<img  border="0" class="usl_avatar" src="' . FB_LIVEUPLOADEDPATH . '/avatars/' . $avatar . '" alt="" />';
										}else {
                                        $uslavatar = '<img  border="0" class="usl_avatar" src="' . FB_LIVEUPLOADEDPATH . '/avatars/s_' . $avatar . '" alt="" />';
										}
                                    }
                                    else {$uslavatar = '<img  border="0" class="usl_avatar" src="' . FB_LIVEUPLOADEDPATH . '/avatars/s_nophoto.jpg" alt="" />'; }
                                }
                                //
                            ?>

                                <tr class = "<?php echo $boardclass; ?><?php echo $usrl_class ;?>  fbm">
                                    <td align = "center" class = "td-1 frst  fbs" align="center">
<?php echo $nr; ?>
                                    </td>

                                    <?php
                                    if ($fbConfig->userlist_online)
                                    {
                                    ?>

                                        <td class = "td-2">
                                            <?php // online - ofline status
                                            $sql = "SELECT count(userid) FROM #__session WHERE userid=" . $ulrow->id;
                                            $database->setQuery($sql);
                                            $isonline = $database->loadResult();



                                            if ($isonline && $ulrow->showOnline ==1 ) {
                                                echo $fbIcons['onlineicon'] ? '<img src="' . JB_URLICONSPATH
                                                         . '' . $fbIcons['onlineicon'] . '" border="0" alt="' . _MODLIST_ONLINE . '" />' : '  <img src="' . JB_URLEMOTIONSPATH . 'onlineicon.gif" border="0"  alt="' . _MODLIST_ONLINE . '" />';
                                            }
                                            else {
                                                echo $fbIcons['offlineicon'] ? '<img src="' . JB_URLICONSPATH
                                                         . '' . $fbIcons['offlineicon'] . '" border="0" alt="' . _MODLIST_OFFLINE . '" />' : '  <img src="' . JB_URLEMOTIONSPATH . 'offlineicon.gif" border="0"  alt="' . _MODLIST_OFFLINE . '" />';
                                            }
                                            ?>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($fbConfig->userlist_avatar)
                                    {
                                    ?>

                                        <td class = "td-3" align="center">
                                      <?php
                                      if(strlen($uslavatar)) {
                                      ?>
                                            <a href = "<?php echo $profilelink ;?>"> <?php echo $uslavatar; ?></a>
                                      <?php
                                      }
                                      else { echo '&nbsp;'; }
                                      ?>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($fbConfig->userlist_name)
                                    {
                                    ?>

                                        <td class = "td-4  fbm" align="center">
                                            <a href = "<?php echo $profilelink ;?>"> <?php echo $ulrow->name; ?></a>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($fbConfig->userlist_username)
                                    {
                                    ?>

                                        <td class = "td-5  fbm" align="center">
                                            <a href = "<?php echo $profilelink ;?>"> <?php echo $ulrow->username; ?></a>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($fbConfig->userlist_group)
                                    {
                                    ?>

                                        <td class = "td-6  fbs" align="center">
                                            <span class = "view-group_<?php echo $ulrow->group_id; ?>"> <?php echo $ulrow->title; ?> </span>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($fbConfig->userlist_posts)
                                    {
                                    ?>

                                        <td class = "td-7  fbs" align="center">
<?php echo $ulrow->posts; ?>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($fbConfig->userlist_karma)
                                    {
                                    ?>

                                        <td class = "td-7 fbs" align="center">
<?php echo $ulrow->karma; ?>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($fbConfig->userlist_email) {
                                        echo "\t\t<td class=\"td-8 fbs\"  align=\"center\"><a href=\"mailto:$ulrow->email\">$ulrow->email</a></td>\n";
                                    }

                                    if ($fbConfig->userlist_usertype) {
                                        echo "\t\t<td  class=\"td-9 fbs\"  align=\"center\">$ulrow->usertype</td>\n";
                                    }

                                    if ($fbConfig->userlist_joindate) {
                                        echo "\t\t<td  class=\"td-10 fbs\"  align=\"center\">" . convertDate($ulrow->registerDate) . "</td>\n";
                                    }

                                    if ($fbConfig->userlist_lastvisitdate) {
                                        echo "\t\t<td  class=\"td-11 fbs\"  align=\"center\">" . convertDate($ulrow->lastvisitDate) . "</td>\n";
                                    }
                                    ?>

                                    <td class = "td-12 lst fbs" align="center">
									 <?php
                                    if ($fbConfig->userlist_userhits)
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

        <table width = "100%"  class="fb_userlist_pagenav" border = "0" cellspacing = "0" cellpadding = "0">
            <tr class = "fb_sth  fbs">
                <th class = "th-1  fbm" align = "center" style = "text-align:center;">

                            <?php
                            // TODO: fxstein - Need to perform SEO cleanup
                            echo $pageNav->writePagesLinks("$base_url$query_ext"); ?>
                </th>
            </tr>
        </table>



        <table class = "fb_blocktable" id="fb_userlist_bottom" style="border-bottom:0px;margin:0;" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
             <thead>
                <tr>
                    <th  class = "th-right  fbs" align="right" style="text-align:right">
                     <?php echo $pageNav->writePagesCounter(); ?> | <?php echo _FB_USRL_DISPLAY_NR; ?> <?php echo $pageNav->writeLimitBox("$base_url$query_ext"); ?>
                </th>
            </tr>
            </thead>
        </table>


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
        if (file_exists(JB_ABSTMPLTPATH . '/plugin/who/whoisonline.php')) {
            include(JB_ABSTMPLTPATH . '/plugin/who/whoisonline.php');
        }
        else {
            include(JB_ABSPATH . '/template/default/plugin/who/whoisonline.php');
        }
        //(JJ) FINISH: WHOISONLINE
        ?>
        <!-- Begin: Forum Jump -->
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
        <table class = "fb_blocktable" id="fb_bottomarea"   border = "0" cellspacing = "0" cellpadding = "0">
            <thead>
                <tr>
                    <th  class = "th-right">
                        <?php
                        //(JJ) FINISH: CAT LIST BOTTOM
                        if ($fbConfig->enableforumjump) {
                            require_once(JB_ABSSOURCESPATH . 'fb_forumjump.php');
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