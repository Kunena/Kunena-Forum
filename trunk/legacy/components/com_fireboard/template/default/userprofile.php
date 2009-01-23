<?php
/**
* @version $Id: userprofile.php 947 2008-08-11 01:56:01Z fxstein $
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
// Dont allow direct linking
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

global $fbConfig;
$rowItemid = mosGetParam($_REQUEST, 'Itemid');

if ($my->id)
{
    //we got a valid and logged on user so we can go on
    //What should we do?
    if ($do == "show")
    { //show it is..
        if (!$fbConfig->cb_profile)
        {
            //first we gather some information about this person - bypass if (s)he is a guest
            unset($user);
            $database->setQuery("SELECT * FROM #__fb_users as su "
            . "\nLEFT JOIN #__users as u on u.id=su.userid WHERE su.userid={$my->id}");

            $database->loadObject($user);

                $prefview = $user->view;
                $signature = $user->signature;
                $username = $user->name;
                $moderator = $user->moderator;
                $fbavatar = $user->avatar;
                $ordering = $user->ordering;

                list($avWidth, $avHeight) = @getimagesize($avatar);

            //use mypms pro avatar if configured
            if ($fbConfig->avatar_src == "pmspro")
            {
                $database->setQuery("SELECT picture FROM #__mypms_profiles WHERE name='$username'");
                $avatar = $database->loadResult();
            }
            elseif ($fbConfig->avatar_src == "cb")
            {
                $database->setQuery("SELECT avatar FROM #__comprofiler WHERE user_id='$my->id'");
                $avatar = $database->loadResult();
                	check_dberror("Unable to load CB avatar.");
            }
            else {
                $avatar = $fbavatar;
            }
        }

        //get all subscriptions for this user
        $database->setQuery("select thread from #__fb_subscriptions where userid=$my->id");
        $subslist = $database->loadObjectList();
        	check_dberror("Unable to load subscriptions.");
        $csubslist = count($subslist);

        //get all favorites for this user
        $database->setQuery("select thread from #__fb_favorites where userid=$my->id");
        $favslist = $database->loadObjectList();
        	check_dberror("Unable to load favorites.");
        $cfavslist = count($favslist);

        //get all forums for which this user is assigned as moderator, BUT only if the user isn't an admin
        //since these are moderators for all forums (regardless if a forum is set to be moderated)
        if (!$is_admin)
        {
            $database->setQuery("select #__fb_moderation.catid,#__fb_categories.name from #__fb_moderation left join #__fb_categories on #__fb_categories.id=#__fb_moderation.catid where #__fb_moderation.userid=$my->id");
            $modslist = $database->loadObjectList();
            	check_dberror("Unable to load moderators.");
            $cmodslist = count($modslist);
        }

        //here we go:
        include(JB_ABSSOURCESPATH . 'fb_bb.js.php');

        if (file_exists(JB_ABSTMPLTPATH . '/fb_pathway.php')) {
            require_once(JB_ABSTMPLTPATH . '/fb_pathway.php');
        }
        else {
            require_once(JB_ABSPATH . '/template/default/fb_pathway.php');
        }
?>

<?php
        if (!$fbConfig->cb_profile)
        {
?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
            <form action = "<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=userprofile&amp;do=update'); ?>" method = "post" name = "postform">
                <input type = "hidden" name = "do" value = "update">

                <table class = "fb_blocktable" id ="fb_forumprofile"  border = "0" cellspacing = "0" cellpadding = "0" width="100%">
                    <thead>
                        <tr>
                            <th colspan = "3">
                                <div class = "fb_title_cover">
                        <span class="fb_title" ><?php echo _USER_PROFILE; ?> <?php echo $username; ?></span>
                                </div>
                    </thead>

                    <tbody>
                        <tr class = "fb_sth">
                            <th colspan = "3" class = "th-1 <?php echo $boardclass; ?>sectiontableheader"><?php echo _USER_GENERAL; ?>
                            </th>
                        </tr>

                        <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
                            <td class = "td-1" colspan = "2">
                                <strong><?php echo _USER_PREFERED; ?>*</strong>:
                            </td>

                            <td class = "td-2">
                                <?php
                                // make the select list for the view type
                                $yesno[] = mosHTML::makeOption('flat', _GEN_FLAT);
                                $yesno[] = mosHTML::makeOption('threaded', _GEN_THREADED);
                                // build the html select list
                                $tosend = mosHTML::selectList($yesno, 'newview', 'class="inputbox" size="2"', 'value', 'text', $prefview);
                                echo $tosend;
                                ?>
                            </td>
                        </tr>

                        <tr class = "<?php echo $boardclass; ?>sectiontableentry2">
                            <td class = "td-1">
                                <strong><?php echo _USER_ORDER; ?>*</strong>:
                            </td>

                            <td class = "td-2" colspan = "2">
                                <?php
                                // make the select list for the view type
                                $yesno1[] = mosHTML::makeOption(0, _USER_ORDER_ASC);
                                $yesno1[] = mosHTML::makeOption(1, _USER_ORDER_DESC);
                                // build the html select list
                                $tosend = mosHTML::selectList($yesno1, 'neworder', 'class="inputbox" size="2"', 'value', 'text', $ordering);
                                echo $tosend;
                                echo '<br /><font size="1"><em>*' . _USER_CHANGE_VIEW . '</em></font>';
                                ?>
                            </td>
                        </tr>

                        <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
                            <td class = "td-1" colspan = "2">
                                <strong><?php echo _GEN_SIGNATURE; ?></strong>:

                                <br/>

                                <i><?php echo $fbConfig->maxsig; ?> <?php echo _CHARS; ?></i>

                                <br/>

                                <input readonly type = text name = "counter" size = "3" maxlength = 3 value = "">

                                <br/>
<?php echo _HTML_YES; ?>
                            </td>

                            <td class = "td-2">
                                <textarea style = "width: <?php echo $fbConfig->rtewidth?>px; height: 60px;"
                                    class = "inputbox"
                                    onMouseOver = "textCounter(this.form.message,this.form.counter,<?php echo $fbConfig->maxsig;?>);"
                                    onClick = "textCounter(this.form.message,this.form.counter,<?php echo $fbConfig->maxsig;?>);"
                                    onKeyDown = "textCounter(this.form.message,this.form.counter,<?php echo $fbConfig->maxsig;?>);"
                                    onKeyUp = "textCounter(this.form.message,this.form.counter,<?php echo $fbConfig->maxsig;?>);" type = "text" name = "message"><?php echo $signature; ?></textarea>

                                <br/>

                                <input type = "button" class = "button" accesskey = "b" name = "addbbcode0" value = " B " style = "font-weight:bold; width: 30px" onClick = "bbstyle(0)" onMouseOver = "helpline('b')"/>

                                <input type = "button" class = "button" accesskey = "i" name = "addbbcode2" value = " i " style = "font-style:italic; width: 30px" onClick = "bbstyle(2)" onMouseOver = "helpline('i')"/>

                                <input type = "button" class = "button" accesskey = "u" name = "addbbcode4" value = " u " style = "text-decoration: underline; width: 30px" onClick = "bbstyle(4)" onMouseOver = "helpline('u')"/>

                                <input type = "button" class = "button" accesskey = "p" name = "addbbcode14" value = "Img" style = "width: 40px" onClick = "bbstyle(14)" onMouseOver = "helpline('p')"/>

                                <input type = "button" class = "button" accesskey = "w" name = "addbbcode16" value = "URL" style = "text-decoration: underline; width: 40px" onClick = "bbstyle(16)" onMouseOver = "helpline('w')"/>

                                <br/><?php echo _SMILE_COLOUR; ?>:

                    <select name = "addbbcode20" onChange = "bbfontstyle('[color=' + this.form.addbbcode20.options[this.form.addbbcode20.selectedIndex].value + ']', '[/color]');this.selectedIndex=0;" onMouseOver = "helpline('s')">
                        <option style = "color:black;  background-color: #FAFAFA" value = ""><?php echo _COLOUR_DEFAULT; ?></option>

                        <option style = "color:red;    background-color: #FAFAFA" value = "#FF0000"><?php echo _COLOUR_RED; ?></option>

                        <option style = "color:blue;   background-color: #FAFAFA" value = "#0000FF"><?php echo _COLOUR_BLUE; ?></option>

                        <option style = "color:green;  background-color: #FAFAFA" value = "#008000"><?php echo _COLOUR_GREEN; ?></option>

                        <option style = "color:yellow; background-color: #FAFAFA" value = "#FFFF00"><?php echo _COLOUR_YELLOW; ?></option>

                        <option style = "color:orange; background-color: #FAFAFA" value = "#FF6600"><?php echo _COLOUR_ORANGE; ?></option>
                    </select>
<?php echo _SMILE_SIZE; ?>:

                    <select name = "addbbcode22" onChange = "bbfontstyle('[size=' + this.form.addbbcode22.options[this.form.addbbcode22.selectedIndex].value + ']', '[/size]')" onMouseOver = "helpline('f')">
                        <option value = "1"><?php echo _SIZE_VSMALL; ?></option>

                        <option value = "2"><?php echo _SIZE_SMALL; ?></option>

                        <option value = "3" selected = "selected"><?php echo _SIZE_NORMAL; ?></option>

                        <option value = "4"><?php echo _SIZE_BIG; ?></option>

                        <option value = "5"><?php echo _SIZE_VBIG; ?></option>
                    </select>

                    <br/>

                    <input type = "text" name = "helpbox" size = "45" maxlength = "100" style = "width: <?php echo $fbConfig->rtewidth?>px; font-size:9px" class = "helpline" value = "<?php echo _BBCODE_HINT;?>"/>

                    <br/>

                    <a href = "javascript: bbstyle(-1)"onMouseOver = "helpline('a')"><small><?php echo _BBCODE_CLOSA; ?></small></a>

                    <br/>

                    <input type = "checkbox" value = "1" name = "deleteSig"><i> <?php echo _USER_DELETE; ?></i>
                            </td>
                        </tr>

                        <tr class = "<?php echo $boardclass; ?>sectiontableentry2">
                            <td colspan = "2" class = "td-1">&nbsp;

                            </td>

                            <td class = "td-2">&nbsp;

                            </td>
                        </tr>

                        <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
                            <td class = "td-1" colspan = "2">
                                <?php
                                if ($fbConfig->allowavatar)
                                {
                                ?>

                                <?php
                                    echo _YOUR_AVATAR . "</td><td class=\"td-2\">";

                                    if ($fbConfig->avatar_src == "clexuspm")
                                    {
                                ?>

                                        <img src = "<?php echo MyPMSTools::getAvatarLinkWithID($my->id)?>" alt="" />

                                        <br/> <a href = "<?php echo sefRelToAbs('index.php?option=com_mypms&amp;task=upload&amp;Itemid='._CLEXUSPM_ITEMID);?>"><?php echo _SET_NEW_AVATAR; ?></a>

                                <?php
                                    }
                                    elseif ($fbConfig->avatar_src == "cb")
                                    {
                                        if ($avatar != "")
                                        {
                                ?>

                                            <img src = "components/com_comprofiler/images/<?php echo $avatar;?>" alt="" />

                                            <br/> <a href = "<?php echo sefRelToAbs('index.php?option=com_comprofiler&amp;Itemid=117&amp;task=userAvatar');?>"><?php echo _SET_NEW_AVATAR; ?></a>

                                <?php
                                        }
                                        else {
                                            echo _NON_SELECTED;
                                ?>

                                        <a href = "<?php echo sefRelToAbs('index.php?option=com_comprofiler&amp;Itemid=117&amp;task=userAvatar');?>"><?php echo _SET_NEW_AVATAR; ?></a>

                                <?php
                                        }
                                    }
                                    else
                                    {
                                        if ($avatar != "")
                                        {
                                ?>

                                            <img src = "components/com_fireboard/avatars/<?php echo $avatar;?>" alt="" />

                                            <br/>

                                            <a href = "<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=upload');?>"> <?php echo _SET_NEW_AVATAR; ?></a>

                                            <br/> <input type = "checkbox" value = "1" name = "deleteAvatar"><i> <?php echo _USER_DELETEAV; ?></i>

                                <?php
                                        }
                                        else {
                                            echo _NON_SELECTED;
                                ?>

                                        <a href = "<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=upload');?>"> <?php echo _SET_NEW_AVATAR; ?></a>

                                <?php
                                        }
                                ?>

                                    <input type = "hidden" value = "<?php echo $avatar;?>" name = "avatar">

                                <?php
                                    }
                                ?>
                            </td>

                                <?php
                                }
                                else
                                {
                                    echo "<td>&nbsp;";
                                    echo '<input type="hidden" value="" name="avatar"></td>';
                                }
                                ?>
                        </tr>

                        <tr class = "<?php echo $boardclass; ?>sectiontableentry2">
                            <td colspan = "2" class = "td-1">&nbsp;

                            </td>

                            <td class = "td-2">&nbsp;

                            </td>
                        </tr>

                        <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
                            <td colspan = 3 class = "td-1">
                                <input type = "checkbox" name = "unsubscribeAll" value = "1"><i><?php echo _USER_UNSUBSCRIBE_ALL; ?></i>
                            </td>
                        </tr>

                        <tr class = "<?php echo $boardclass; ?>sectiontableentry2">
                            <td colspan = 3 class = "td-1">
                                <input type = "checkbox" name = "unfavoriteAll" value = "1"><i><?php echo _USER_UNFAVORITE_ALL; ?></i>
                            </td>
                        </tr>

                        <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
                            <td colspan = "3" class = "td-1">
                                &nbsp;<input type = "submit" class = "button" value = "<?php echo _GEN_SUBMIT;?>">
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
<?php
        }
?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
        <table class = "fb_blocktable" id ="fb_forumprofile_sub"  border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
                <tr>
                    <th colspan = "2">
                        <div class = "fb_title_cover">
                            <span class="fb_title"><?php echo _USER_SUBSCRIPTIONS; ?></span>
                        </div>
                    </th>
                </tr>
            </thead>

            <tbody>
                <?php
                        $enum = 1; //reset value
                        $tabclass = array
                        (
                            "sectiontableentry1",
                            "sectiontableentry2"
                        );      //alternating row CSS classes

                        $k = 0; //value for alternating rows

                        if ($csubslist > 0)
                        {
                            foreach ($subslist as $subs)
                            { //get all message details for each subscription
                                $database->setQuery("select * from #__fb_messages where id=$subs->thread");
                                $subdet = $database->loadObjectList();
                                	check_dberror("Unable to load messages.");

                                foreach ($subdet as $sub)
                                {
                                    $k = 1 - $k;
                                    echo '<tr class="' . $boardclass . '' . $tabclass[$k] . '" >';
                                    echo '<td class="td-1" >' . $enum. ': <a href="'.sefRelToAbs(JB_LIVEURLREL. '&amp;func=view&amp;catid=' . $sub->catid . '&amp;id=' . $sub->id ).'">' . $sub->subject . '</a> - ' . _GEN_BY . '  ' . $sub->name  .'</td>';
                                    echo '<td class="td-2" ><a href="'.sefRelToAbs(JB_LIVEURLREL. '&amp;func=userprofile&amp;do=unsubscribe&amp;thread=' . $subs->thread) . '">' . _THREAD_UNSUBSCRIBE . '</a></td>';
                                    echo "</tr>";
                                    $enum++;
                                }
                            }
                        }
                        else {
                            echo '<tr class="' . $boardclass . '' . $tabclass[$k] . '"><td class="td-1" colspan = "2" >' . _USER_NOSUBSCRIPTIONS . '</td></tr>';
                        }
                ?>
            </tbody>
        </table>
</div>
</div>
</div>
</div>
</div>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
        <table class = "fb_blocktable" id ="fb_forumprofile_fav" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
                <tr>
                    <th colspan = "2">
                        <div class = "fb_title_cover">
                            <span class="fb_title"><?php echo _USER_FAVORITES; ?></span>
                        </div>
                      </th>
                </tr>
            </thead>

            <tbody>
                <?php
                $enum = 1; //reset value
                $tabclass = array
                (
                    "sectiontableentry1",
                    "sectiontableentry2"
                );      //alternating row CSS classes

                $k = 0; //value for alternating rows

                if ($cfavslist > 0)
                {
                    foreach ($favslist as $favs)
                    { //get all message details for each favorite
                        $database->setQuery("select * from #__fb_messages where id=$favs->thread");
                        $favdet = $database->loadObjectList();
                        	check_dberror("Unable to load messages.");

                        foreach ($favdet as $fav)
                        {
                            $k = 1 - $k;
                            echo '<tr class="' . $boardclass . '' . $tabclass[$k] . '">';
                            echo '<td class="td-1">' . $enum . ': <a href="'.sefRelToAbs(JB_LIVEURLREL. '&amp;func=view&amp;catid=' . $fav->catid . '&amp;id=' . $fav->id) . '">' . $fav->subject . '</a> - ' . _GEN_BY . ' ' . $fav->name .'</td>';
                            echo '<td class="td-2"><a href="'.sefRelToAbs(JB_LIVEURLREL. '&amp;func=userprofile&amp;do=unfavorite&amp;thread=' . $favs->thread) . '">' . _THREAD_UNFAVORITE . '</a></td>';
                            echo "</tr>";
                            $enum++;
                        }
                    }
                }
                else {
                    echo '<tr class="' . $boardclass . '' . $tabclass[$k] . '"><td class="td-1" colspan = "2">' . _USER_NOFAVORITES . '</td></tr>';
                }
                ?>
            </tbody>
        </table>
</div>
</div>
</div>
</div>
</div>
        <table class = "fb_blocktable" id ="fb_forumprofile_mod" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
                <tr>
                    <th >
                        <div class = "fb_title_cover">
                            <span class="fb_title"><?php echo _USER_MODERATOR; ?>:</span>
                        </div>
                       </th>
                </tr>
            </thead>

            <tbody>
                <?php
                if (!$is_admin)
                {
                    $enum = 1; //reset value
                    $tabclass = array
                    (
                        "sectiontableentry1",
                        "sectiontableentry2"
                    );      //alternating row CSS classes

                    $k = 0; //value for alternating rows

                    if ($cmodslist > 0)
                    {
                        foreach ($modslist as $mods)
                        { //get all moderator details for each moderation
                            $k = 1 - $k;
                            echo "<tr class=\"" . $boardclass . '' . $tabclass[$k] . "\">";
                            echo ' <td class="td-1">' . $enum . ': ' . $mods->name . '</td>';
                            echo "</tr>";
                            $enum++;
                        }
                    }
                    else {
                        echo "<tr class=\"" . $boardclass . '' . $tabclass[$k] . "\"><td class=\"td-1\">" . _USER_MODERATOR_NONE . "</td></tr>";
                    }
                }
                else {
                    echo "<tr class=\"" . $boardclass . '' . $tabclass[$k] . "\"><td class=\"td-1\">" . _USER_MODERATOR_ADMIN . "</td></tr>";
                }

                echo "</tbody></table>";
    }
    else if ($do == "update")
    { //we update anything
        $rowItemid = mosGetParam($_REQUEST, 'Itemid');
        $deleteAvatar = mosGetParam($_POST, 'deleteAvatar', 0);
        $deleteSig = mosGetParam($_POST, 'deleteSig', 0);
        $unsubscribeAll = mosGetParam($_POST, 'unsubscribeAll', 0);
        $unfavoriteAll = mosGetParam($_POST, 'unfavoriteAll', 0);
        $signature = mosGetParam($_POST, 'message', '');
        $newview = mosGetParam($_POST, 'newview', 'flat');
        $avatar = mosGetParam($_POST, 'avatar', '');
        (int)$neworder = mosGetParam($_POST, 'neworder', 0);

        if ($deleteSig == 1) {
            $signature = "";
        }

        $signature = trim(addslashes($signature));
        //parse the message for some preliminary bbcode and stripping of HTML
        //$signature = smile::bbencode_first_pass($signature);

        if ($deleteAvatar == 1) {
            $avatar = "";
        }

        $database->setQuery("UPDATE #__fb_users set signature='$signature', view='$newview', avatar='$avatar', ordering='$neworder'  where userid=$my_id");
        setcookie("fboard_settings[current_view]", $newview);

        if (!$database->query()) {
            echo _USER_PROFILE_NOT_A . " <strong><font color=\"red\">" . _USER_PROFILE_NOT_B . "</font></strong> " . _USER_PROFILE_NOT_C . ".<br /><br />";
        }
        else {
            echo _USER_PROFILE_UPDATED . "<br /><br />";
        }

        echo _USER_RETURN_A . ' <a href="'.sefRelToAbs(JB_LIVEURLREL."&amp;func=userprofile&amp;do=show").'">' . _USER_RETURN_B . "</a><br /><br />";

        if ($unsubscribeAll)
        {
            $database->setQuery("DELETE FROM #__fb_subscriptions WHERE userid=$my_id");
            $database->query();
        }

        if ($unfavoriteAll)
        {
            $database->setQuery("DELETE FROM #__fb_favorites WHERE userid='$my_id'");
            $database->query();
        }
                ?>

    <script language = "javascript">
        setTimeout("location='<?php echo sefRelToAbs(JB_LIVEURLREL . '&func=userprofile&do=show');?>'", 3500);
    </script>

    <?php
    }
    else if ($do == "unsubscribe")
    { //ergo, ergo delete
        $database->setQuery("DELETE from #__fb_subscriptions where userid=$my->id and thread=$thread");

        if (!$database->query()) {
            echo _USER_UNSUBSCRIBE_A . " <strong><font color=\"red\">" . _USER_UNSUBSCRIBE_B . "</font></strong> " . _USER_UNSUBSCRIBE_C . ".<br /><br />";
        }
        else {
            echo _USER_UNSUBSCRIBE_YES . ".<br /><br />";
        }

        if ($fbConfig->cb_profile) {
            echo _USER_RETURN_A . " <a href=\"index.php?option=com_comprofiler&amp;Itemid='".FB_CB_ITEMID."'&amp;tab=getForumTab\">" . _USER_RETURN_B . "</a><br /><br />";
    ?>

        <script language = "javascript">
            setTimeout("location='index.php?option=com_comprofiler<?php echo FB_CB_ITEMID_SUFFIX; ?>&tab=getForumTab'", 3500);
        </script>

<?php
        }
        else {
            echo _USER_RETURN_A . " <a href=\"". sefRelToAbs(JB_LIVEURLREL . '&amp;func=userprofile&amp;do=show')."\">" . _USER_RETURN_B . "</a><br /><br />";
?>

        <script language = "javascript">
        setTimeout("location='<?php echo sefRelToAbs(JB_LIVEURLREL . '&func=userprofile&do=show');?>'", 3500);
        </script>

<?php
        }
    }
    else if ($do == "unfavorite")
    { //ergo, ergo delete
        $database->setQuery("DELETE from #__fb_favorites where userid=$my->id and thread=$thread");

        if (!$database->query()) {
            echo _USER_UNFAVORITE_A . " <strong><font color=\"red\">" . _USER_UNFAVORITE_B . "</font></strong> " . _USER_UNFAVORITE_C . ".<br /><br />";
        }
        else {
            echo _USER_UNFAVORITE_YES . ".<br /><br />";
        }

        if ($fbConfig->cb_profile) {
            echo _USER_RETURN_A . " <a href=\"index.php?option=com_comprofiler".FB_CB_ITEMID_SUFFIX."&amp;tab=getForumTab\">" . _USER_RETURN_B . "</a><br /><br />";
?>

        <script language = "javascript">
            setTimeout("location='index.php?option=com_comprofiler".FB_CB_ITEMID_SUFFIX."&tab=getForumTab'", 3500);
        </script>

<?php
        }
        else {
            echo _USER_RETURN_A . " <a href=\"index.php?option=com_fireboard&amp;Itemid=$Itemid&amp;func=userprofile&amp;do=show\">" . _USER_RETURN_B . "</a><br /><br />";
?>

        <script language = "javascript">
               setTimeout("location='<?php echo sefRelToAbs(JB_LIVEURLREL . '&func=userprofile&do=show');?>'", 3500);
        </script>

<?php
        }
    }
    else
    { //you got me there... don't know what to $do
        echo _USER_ERROR_A;
        echo _USER_ERROR_B . "<br /><br />";
        echo _USER_ERROR_C . "<br /><br />" . _USER_ERROR_D . ": <code>fb001-up-02NoDO</code><br /><br />";
    }
}
else
{ //get outa here, you fraud!
    echo _USER_ERROR_A;
    echo _USER_ERROR_B . "<br /><br />";
    echo _USER_ERROR_C . "<br /><br />" . _USER_ERROR_D . ": <code>fb001-up-01NLO</code><br /><br />";
//that should scare 'em off enough... ;-)
}
?>
<!-- Begin: Forum Jump -->
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "fb_blocktable" id="fb_bottomarea"  border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th  class = "th-right">
                <?php
                //(JJ) FINISH: CAT LIST BOTTOM
                if ($fbConfig->enableforumjump)
                    require_once(JB_ABSSOURCESPATH . 'fb_forumjump.php');
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