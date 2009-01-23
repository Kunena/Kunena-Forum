<?php
/**
* @version $Id: admin.fireboard.html.php 1070 2008-10-06 08:11:18Z fxstein $
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

class HTML_SIMPLEBOARD
{

// Begin: HEADER FUNC
function showFbHeader () {
?>
<style>
#fbadmin {
text-align:left;
}
#fbheader {
clear:both;
}
#fbmenu {
margin-top:15px;
border-top:1px solid #ccc;
}
#fbmenu a{
display:block;
font-size:11px;
border-left:1px solid #ccc;
border-bottom:1px solid #ccc;

}
.fbmainmenu {
background:#FBFBFB;
padding:5px;
}
.fbactivemenu {
background:#fff;
padding:5px;
}
.fbsubmenu {
background:#fff;
padding-left:10px;
padding:5px 5px 5px 15px;
}
.fbright {
border:1px solid #ccc;
background:#fff;
padding:5px;
}
.fbfooter {
font-size:10px;
text-align: right;
padding:5px;
background:#FBFBFB;
border-bottom:1px solid #CCC;
border-left:1px solid #CCC;
border-right:1px solid #CCC;
}
.fbfunctitle {
font-size:16px;
text-align: left;
padding:5px;
background:#FBFBFB;
border:1px solid #CCC;
font-weight:bold;
margin-bottom:10px;
clear:both;
}
.fbfuncsubtitle {
font-size:14px;
text-align: left;
padding:5px;
border-bottom:3px solid  #7F9DB9;
font-weight:bold;
color:#7F9DB9;
margin:10px 0 10px 0;
}
.fbrow0 td {
padding:8px 5px;
text-align:left;
border-bottom:1px  dotted #ccc;
}
.fbrow1 td {
padding:8px 5px;
text-align:left;
border-bottom:1px dotted #ccc;
}
td.fbtdtitle {
font-weight:bold;
padding-left:10px;
color:#666;
}
#fbcongifcover fieldset {
border: 1px solid #CFDCEB;
}
#fbcongifcover fieldset legend{
color:#666;
}
</style>

<div id="fbadmin">
 <!-- Header -->
 <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="170" valign="top">
    <!-- Begin: AdminLeft -->
        <div id="fbheader">
        <a href = "index2.php?option=com_fireboard"><img src = "components/com_fireboard/images/logo.png"  border="0" alt = "<?php echo _COM_C_BACK; ?>"/></a>
        </div>
        <!-- Begin : Fireboard Left Menu -->
        <div id="fbmenu">

        <?php $stask=mosGetParam($_REQUEST, "task", null);	?>
        <a class="fbmainmenu" href = "index2.php?option=com_fireboard"><?php echo _FB_CP; ?></a>

           <a class="fbmainmenu" href = "index2.php?option=com_fireboard&task=showconfig"><?php echo _COM_C_FBCONFIG; ?></a>
           <?php if ( $stask == 'showconfig' ) { ; ?>
           		<a class="fbsubmenu" href = "#basics"><?php echo _COM_A_BASICS; ?></a>
                <a class="fbsubmenu" href = "#frontend"><?php echo _COM_A_FRONTEND; ?></a>
                <a class="fbsubmenu" href = "#security"><?php echo _COM_A_SECURITY; ?></a>
                <a class="fbsubmenu" href = "#avatars"><?php echo _COM_A_AVATARS; ?></a>
                <a class="fbsubmenu" href = "#uploads"><?php echo _COM_A_UPLOADS; ?></a>
                <a class="fbsubmenu" href = "#ranking"><?php echo _COM_A_RANKING; ?></a>
                <a class="fbsubmenu" href = "#bbcode"><?php echo _COM_A_BBCODE; ?></a>
                <a class="fbsubmenu" href = "#integration"><?php echo _COM_A_INTEGRATION; ?></a>
                <a class="fbsubmenu" href = "#plugins"><?php echo _FB_ADMIN_CONFIG_PLUGINS; ?></a>
            <?php } ?>
           <a class="fbmainmenu"  href = "index2.php?option=com_fireboard&task=showAdministration"><?php echo _COM_C_FORUM; ?></a>
           <a class="fbmainmenu"  href = "index2.php?option=com_fireboard&task=showprofiles"><?php echo _COM_C_USER; ?></a>
           <a class="fbmainmenu"  href = "index2.php?option=com_fireboard&task=showsmilies"><?php echo _FB_EMOTICONS_EDIT_SMILIES;?></a>
           <a class="fbmainmenu"  href = "index2.php?option=com_fireboard&task=ranks"><?php echo _FB_RANKS_MANAGE; ?></a>
           <a class="fbmainmenu"  href = "index2.php?option=com_fireboard&task=browseFiles"><?php echo _COM_C_FILES; ?> </a>
           <a class="fbmainmenu"  href = "index2.php?option=com_fireboard&task=browseImages"><?php echo _COM_C_IMAGES; ?></a>
           <a class="fbmainmenu"  href = "index2.php?option=com_fireboard&task=showCss"><?php echo _COM_C_CSS; ?></a>
           <a class="fbmainmenu"  href = "index2.php?option=com_fireboard&task=pruneforum"><?php echo _COM_C_PRUNETAB; ?></a>
           <a class="fbmainmenu"  href = "index2.php?option=com_fireboard&task=syncusers"><?php echo _FB_SYNC_USERS; ?></a>
           <a class="fbmainmenu"  href = "index2.php?option=com_fireboard&task=loadSample"><?php echo _COM_C_LOADSAMPLE; ?></a>
           <a class="fbmainmenu"  href = "index2.php?option=com_fireboard&task=removeSample" onclick="return confirm('<?php echo _FB_CONFIRM_REMOVESAMPLEDATA?>');"><?php echo _COM_C_REMOVESAMPLE; ?></a>
           <a class="fbmainmenu"  href = "index2.php?option=com_fireboard&task=recount"><?php echo _FB_RECOUNTFORUMS; ?></a>
           <a class="fbmainmenu"  href = "http://www.bestofjoomla.com" target = "_blank" ><?php echo _COM_C_SUPPORT; ?></a>


        </div>
        <!-- Finish : Fireboard Left Menu -->

    <!-- Finish: AdminLeft -->
    </td>
    <td  valign="top" class="fbright">
    <!-- Begin: AdminRight -->



<?php
} // Finish: HEADER FUNC

// Begin: FOOTER FUNC
function showFbFooter () {
global $mainframe;
global $fbConfig;

include ($mainframe->getCfg('absolute_path') . '/components/com_fireboard/sources/fb_version.php');
// << $fbversion
?>

<!-- Finish: AdminRight -->

    </td>
  </tr>
  <tr><td></td><td>
 <!-- Footer -->
<div class="fbfooter">
Installed version:  <?php echo $fbversion; ?> |
&copy; Copyright: <a href = "http://www.bestofjoomla.com" target = "_blank">Best of Joomla</a>  |
License: <a href = "http://www.gnu.org/copyleft/gpl.html" target = "_blank">GNU GPL</a>
</div>
<!-- /Footer -->
  </td></tr>
</table>

</div><!-- Close div.fbadmin -->
<?php } // Finish: FOOTER FUNC


    function controlPanel()
    {
        global $mainframe;
?>

        <div class="fbfunctitle"><?php echo _FB_CP; ?></div>


        <?php
        $path = $mainframe->getCfg('absolute_path') . "/administrator/components/com_fireboard/fb_cpanel.php";

        if (file_exists($path)) {
            require $path;
        }
        else
        {
            echo '<br />mcap==: ' . $mainframe->getCfg('absolute_path') . ' .... help!!';
            mosLoadAdminModules('cpanel', 1);
        }
    }

    function showAdministration($rows, $pageNav, $option)
    {
        ?>
<div class="fbfunctitle"><?php echo _FB_ADMIN; ?></div>
        <form action = "index2.php" method = "post" name = "adminForm">
            <table  cellpadding = "4" cellspacing = "0" border = "0" width = "100%">
                <tr>
                    <td  align="right">
                  <?php echo _COM_A_DISPLAY; ?> <?php echo $pageNav->writeLimitBox(); ?>
                    </td>
                </tr>
            </table>

            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "adminlist">
                <tr>
                    <th width = "20">
                        #
                    </th>

                    <th width = "20">
                        <input type = "checkbox" name = "toggle" value = "" onclick = "checkAll(<?php echo count( $rows ); ?>);"/>
                    </th>

                    <th class = "title">
                      <?php echo _FB_CATFOR; ?>
                    </th>

                    <th>
                      <small><?php echo _FB_LOCKED; ?></small>
                    </th>

                    <th>
                      <small><?php echo _FB_MODERATED; ?></small>
                    </th>

                    <th>
                      <small><?php echo _FB_REVIEW; ?></small>
                    </th>

                    <th>
                      <small><?php echo _FB_PUBLISHED; ?></small>
                    </th>

                    <th>
                      <small><?php echo _FB_PUBLICACCESS; ?></small>
                    </th>

                    <th>
                      <small><?php echo _FB_ADMINACCESS; ?></small>
                    </th>

                    <th>
                      <small><?php echo _FB_CHECKEDOUT; ?></small>
                    </th>

                    <th colspan = "2">
                      <small><?php echo _FB_REORDER; ?></small>
                    </th>
                </tr>

                <?php
                $k = 0;
                $i = 0;

                for ($i = 0, $n = count($rows); $i < $n; $i++)
                {
                    $row = $rows[$i];

                    if ($row->parent == 0)
                    {
                ?>

                        <tr bgcolor = "#D4D4D4">
                <?php
                    }
                    else
                    {
                ?>

                        <tr class = "row<?php echo $k; ?>">
                <?php
                    }
                ?>

                    <td width = "20" align = "right"><?php echo $i + $pageNav->limitstart + 1; ?>
                    </td>

                    <td width = "20">
                        <input type = "checkbox" id = "cb<?php echo $i;?>" name = "cid[]" value = "<?php echo $row->id; ?>" onClick = "isChecked(this.checked);">
                    </td>

                    <td width = "70%">
                        <a href = "#edit" onclick = "return listItemTask('cb<?php echo $i; ?>','edit')">

                        <?php
                            //echo ($row->category ? "$row->category/$row->name" : "$row->name");
                            echo ($row->treename);
                        ?>

                        </a>
                    </td>

                    <td align = "center">
                        <?php
                            echo (!$row->category ? "&nbsp;" : ($row->locked == 1 ? "<img src=\"images/tick.png\">" : "<img src=\"images/publish_x.png\">"));
                        ?>
                    </td>

                    <td align = "center"><?php echo ($row->moderated == 1 ? "<img src=\"images/tick.png\">" : "<img src=\"images/publish_x.png\">"); ?>
                    </td>

                    <td align = "center">
                        <?php
                            echo (!$row->category ? "&nbsp;" : ($row->review == 1 ? "<img src=\"images/tick.png\">" : "<img src=\"images/publish_x.png\">"));
                        ?>
                    </td>

                    <?php
                        $task = $row->published ? 'unpublish' : 'publish';
                        $img = $row->published ? 'publish_g.png' : 'publish_x.png';

                        if ($row->pub_access == 0) {
                            $groupname = _FB_EVERYBODY;
                        }
                        else if ($row->pub_access == -1) {
                            $groupname = _FB_ALLREGISTERED;
                        }
                        else {
                            $groupname = $row->groupname == "" ? "&nbsp;" : $row->groupname;
                        }

                        $adm_groupname = $row->admingroup == "" ? "&nbsp;" : $row->admingroup;
                    ?>

                        <td width = "10%" align = "center">
                            <a href = "javascript: void(0);" onclick = "return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">

                            <img src = "images/<?php echo $img;?>" width = "12" height = "12" border = "0" alt = ""/></a>
                        </td>

                        <td width = "" align = "center"><?php echo $groupname; ?>
                        </td>

                        <td width = "" align = "center"><?php echo $adm_groupname; ?>
                        </td>

                        <td width = "15%" align = "center">
<?php echo $row->editor; ?>&nbsp;
                        </td>

                        <td>
                            <?php
                            if ($i > 0 || ($i + $pageNav->limitstart > 0))
                            {
                            ?>

                                <a href = "#reorder" onClick = "return listItemTask('cb<?php echo $i;?>','orderup')"> <img src = "images/uparrow.png" width = "12" height = "12" border = "0" alt = "<?php echo _FB_MOVEUP; ?>"> </a>

                            <?php
                            }
                            ?>
                        </td>

                        <td>
                            <?php
                            if ($i < $n - 1 || $i + $pageNav->limitstart < $pageNav->total - 1)
                            {
                            ?>

                                <a href = "#reorder" onClick = "return listItemTask('cb<?php echo $i;?>','orderdown')"> <img src = "images/downarrow.png" width = "12" height = "12" border = "0" alt = "<?php echo _FB_MOVEDOWN; ?>"> </a>

                            <?php
                            }
                            ?>
                        </td>

                <?php
                            $k = 1 - $k;
                }
                ?>
                        </tr>

                        <tr>
                            <th align = "center" colspan = "12"> <?php
                            // TODO: fxstein - Need to perform SEO cleanup
                            echo $pageNav->writePagesLinks(); ?>
                            </th>
                        </tr>

                        <tr>
                            <td align = "center" colspan = "12"> <?php echo $pageNav->writePagesCounter(); ?>
                            </td>
                        </tr>
            </table>

            <input type = "hidden" name = "option" value = "<?php echo $option; ?>"> <input type = "hidden" name = "task" value = "showAdministration"> <input type = "hidden" name = "boxchecked" value = "0">
            <?php if (FBTools::isJoomla15()) echo '<input type = "hidden" name = "limitstart" value = "0">'; ?>
        </form>

<?php
    }

    function editForum(&$row, $categoryList, $moderatorList, $lists, $accessLists, $option)
    {
        $tabs = new mosTabs(3);
?>

        <style>
            .hideable
            {
                position: relative;
                visibility: hidden;
            }
        </style>

        <script language = "javascript" type = "text/javascript">
            function submitbutton(pressbutton)
            {
                var form = document.adminForm;

                if (pressbutton == 'cancel')
                {
                    submitform(pressbutton);
                    return;
                }

                // do field validation
                try
                {
                    document.adminForm.onsubmit();
                }
                catch (e)
                {
                }

                if (form.name.value == "")
                {
                    alert("<?php echo _FB_ERROR1; ?>");
                }
                else
                {
                    submitform(pressbutton);
                }
            }
        </script>

        <div class="fbfunctitle"><?php echo $row->id ? _FB_EDIT : _FB_ADD; ?> <?php echo _FB_CATFOR; ?></div>

        <form action = "index2.php" method = "POST" name = "adminForm">
           <div class="fbfuncsubtitle"><?php echo _FB_BASICSFORUM; ?></div>
           <fieldset>
           <legend> <?php echo _FB_BASICSFORUMINFO; ?></legend>
            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" >


                    <tr>
                        <td width = "200" valign = "top"><?php echo _FB_PARENT; ?>
                        </td>

                        <td>
<?php echo $categoryList; ?><br/>

                <br/><?php echo _FB_PARENTDESC; ?>
                        </td>
                    </tr>

                    <tr>
                        <td width = "200"><?php echo _FB_NAMEADD; ?>
                        </td>

                        <td>
                            <input class = "inputbox" type = "text" name = "name" size = "25" maxlength = "100" value = "<?php echo $row->name; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td valign = "top"><?php echo _FB_DESCRIPTIONADD; ?>
                        </td>

                        <td>
                            <textarea class = "inputbox" cols = "50" rows = "3" name = "description" id = "description" style = "width:500px" width = "500"><?php echo $row->description; ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <td valign = "top"><?php echo _FB_HEADERADD; ?>
                        </td>

                        <td>
                            <textarea class = "inputbox" cols = "50" rows = "3" name = "headerdesc" id = "headerdesc" style = "width:500px" width = "500"><?php echo $row->headerdesc; ?></textarea>
                        </td>
                    </tr>
            </table>
</fieldset>
           <div class="fbfuncsubtitle"><?php echo _FB_ADVANCEDDESC; ?></div>
           <fieldset>
           <legend> <?php echo _FB_ADVANCEDDESCINFO; ?></legend>

            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%">

                <tr>
                    <td><?php echo _FB_LOCKED1; ?>
                    </td>

                    <td> <?php echo $lists['forumLocked']; ?>
                    </td>

                    <td>
<?php echo _FB_LOCKEDDESC; ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap = "nowrap" valign = "top"><?php echo _FB_PUBACC; ?>
                    </td>

                    <td valign = "top"> <?php echo $accessLists['pub_access']; ?>
                    </td>

                    <td><?php echo _FB_PUBACCDESC; ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap = "nowrap" valign = "top"><?php echo _FB_CGROUPS; ?>
                    </td>

                    <td valign = "top"> <?php echo $lists['pub_recurse']; ?>
                    </td>

                    <td valign = "top"><?php echo _FB_CGROUPSDESC; ?>
                    </td>
                </tr>

                <tr>
                    <td valign = "top"><?php echo _FB_ADMINLEVEL; ?>
                    </td>

                    <td valign = "top"> <?php echo $accessLists['admin_access']; ?>
                    </td>

                    <td valign = "top"><?php echo _FB_ADMINLEVELDESC; ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap = "nowrap" valign = "top"><?php echo _FB_CGROUPS1; ?>
                    </td>

                    <td valign = "top"> <?php echo $lists['admin_recurse']; ?>
                    </td>

                    <td valign = "top"><?php echo _FB_CGROUPS1DESC; ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap = "nowrap" valign = "top"><?php echo _FB_REV; ?>
                    </td>

                    <td valign = "top"> <?php echo $lists['forumReview']; ?>
                    </td>

                    <td valign = "top"><?php echo _FB_REVDESC; ?>
                    </td>
                </tr>
            </table>

           </fieldset>
           <fieldset>
           <legend> <?php echo _FB_ADVANCEDDISPINFO; ?></legend>

            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%">

                <tr>
                    <td><?php echo _FB_CLASS_SFX; ?>
                    </td>

                    <td>
                        <input class = "inputbox" type = "text" name = "class_sfx" size = "20" maxlength = "20" value = "<?php echo $row->class_sfx; ?>">
                    </td>

                    <td>
<?php echo _FB_CLASS_SFXDESC; ?>
                    </td>
                </tr>
            </table>
           </fieldset>

           <div class="fbfuncsubtitle"><?php echo _FB_MODNEWDESC; ?></div>
           <fieldset>
           <legend> <?php echo _FB_MODHEADER; ?></legend>

            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" >


                <tr>
                    <td nowrap = "nowrap" valign = "top"><?php echo _FB_MOD; ?>
                    </td>

                    <td valign = "top"> <?php echo $lists['forumModerated']; ?>
                    </td>

                    <td valign = "top"><?php echo _FB_MODDESC; ?>
                    </td>
                </tr>
            </table>

            <?php
            if ($row->moderated)
            {
            ?>


<div class="fbfuncsubtitle"><?php echo _FB_MODSASSIGNED; ?></div>

                <table class = "adminlist" border = "0" cellspacing = "0" cellpadding = "3" width = "100%">
                    <tr>
                        <th width = "20">
                            #
                        </th>

                        <th width = "20">
                            <input type = "checkbox" name = "toggle" value = "" onclick = "checkAll(<?php echo count( $moderatorList ); ?>);"/>
                        </th>

                        <th align = "left"><?php echo _USRL_NAME; ?>
                        </th>

                        <th align = "left"><?php echo _USRL_USERNAME; ?>
                        </th>

                        <th align = "left"><?php echo _USRL_EMAIL; ?>
                        </th>

                        <th align = "centercase "edit":"><?php echo _FB_PUBLISHED; ?>
                        </th>
                    </tr>

                    <?php
                    if (count($moderatorList) == 0) {
                        echo "<tr><td colspan=\"5\">" . _FB_NOMODS . "</td></tr>";
                    }
                    else
                    {
                        $k = 1;
                        $i = 0;

                        foreach ($moderatorList as $ml)
                        {
                            $k = 1 - $k;
                    ?>

                                <tr class = "row<?php echo $k;?>">
                                    <td width = "20"><?php echo $i + 1; ?>
                                    </td>

                                    <td width = "20">
                                        <input type = "checkbox" id = "cb<?php echo $i;?>" name = "cid[]" value = "<?php echo $ml->id; ?>" onClick = "isChecked(this.checked);">
                                    </td>

                                    <td><?php echo $ml->name; ?>
                                    </td>

                                    <td><?php echo $ml->username; ?>
                                    </td>

                                    <td><?php echo $ml->email; ?>
                                    </td>

                                    <td align = "center">
                                        <img src = "images/tick.png">
                                    </td>
                                </tr>

                    <?php


                    $i++;
                        }
                    }
                    ?>
                </table>

            <?php
            }
            ?>

           </fieldset>

            <input type = "hidden" name = "id" value = "<?php echo $row->id; ?>"> <input type = "hidden" name = "option" value = "<?php echo $option; ?>"> <input type = "hidden" name = "task" value = "showAdministration">

            <?php
            if ($row->ordering != 0) {
                echo '<input type="hidden" name="ordering" value="' . $row->ordering . '">';
            }
            ?>
        </form>

        <?php
    }

    function showConfig(&$fbConfig, &$lists, $option)
    {
        global $mosConfig_live_site;
        $tabs = new mosTabs(2);
        ?>
<div id="fbcongifcover">
<div class="fbfunctitle"><?php echo _COM_A_CONFIG ?></div>
        <form action = "index2.php" method = "post" name = "adminForm">
		<div class="fbfuncsubtitle"><?php echo _COM_A_BASICS ?><a name="basics" id="basics" > </a></div>

        <fieldset>
			<legend> <?php echo _COM_A_BASIC_SETTINGS ?></legend>

            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"   width="25%"><?php echo _COM_A_BOARD_TITLE ?>
                    </td>

                    <td align = "left" valign = "top"  width="25%" >
                        <input type = "text" name = "cfg_board_title" value = "<?php echo $fbConfig->board_title; ?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_BOARD_TITLE_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"  ><?php echo _COM_A_EMAIL ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_email" value = "<?php echo $fbConfig->email; ?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_EMAIL_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle" >
                    <td align = "left" valign = "top"  ><?php echo _COM_A_BOARD_OFFLINE ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['board_offline']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_BOARD_OFFLINE_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"  ><?php echo _COM_A_BOARD_OFSET ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_board_ofset" value = "<?php echo $fbConfig->board_ofset; ?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_BOARD_OFSET_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"  ><?php echo _COM_A_FB_SESSION_TIMEOUT ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_fbsessiontimeout" value = "<?php echo $fbConfig->fbsessiontimeout; ?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_FB_SESSION_TIMEOUT_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle" >
                    <td align = "left" valign = "top"  ><?php echo _COM_A_BOARD_OFFLINE_MES ?>
                    </td>

                    <td align = "left" valign = "top" colspan = "2">
                        <textarea name = "cfg_offline_message" rows = "3" cols = "50"><?php echo $fbConfig->offline_message; ?></textarea>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"  ><?php echo _COM_A_VIEW_TYPE ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['default_view']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_VIEW_TYPE_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle" >
                    <td align = "left" valign = "top"  ><?php echo _COM_A_RSS ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['enablerss']; ?>
                    </td>

                    <td align = "left" valign = "top">
                        <img src = "<?php echo $mosConfig_live_site;?>/images/M_images/rss.png"/> <?php echo _COM_A_RSS_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"  ><?php echo _COM_A_RSS_TYPE ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['rsstype']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_RSS_TYPE_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"  ><?php echo _COM_A_RSS_HISTORY ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['rsshistory']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_RSS_HISTORY_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"  ><?php echo _COM_A_PDF ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['enablepdf']; ?>
                    </td>

                    <td align = "left" valign = "top">
                        <img src = "<?php echo $mosConfig_live_site;?>/images/M_images/pdf_button.png"/> <?php echo _COM_A_PDF_DESC ?>
                    </td>
                </tr>
            </table>
		</fieldset>

		<div class="fbfuncsubtitle"><?php echo _COM_A_FRONTEND ?> <a name="frontend" id="frontend" > </a></div>

          <fieldset>
			<legend> <?php echo _COM_A_LOOKS ?></legend>
            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _COM_A_THREADS ?>
                    </td>

                    <td align = "left" valign = "top" width="25%">
                        <input type = "text" name = "cfg_threads_per_page" value = "<?php echo $fbConfig->threads_per_page; ?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_THREADS_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_MESSAGES ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_messages_per_page" value = "<?php echo $fbConfig->messages_per_page; ?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_MESSAGES_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_MESSAGES_SEARCH ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_messages_per_page_search" value = "<?php echo $fbConfig->messages_per_page_search; ?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_MESSAGES_DESC_SEARCH ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_HISTORY ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['showhistory']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_HISTORY_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_HISTLIM ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_historylimit" value = "<?php echo $fbConfig->historylimit;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_HISTLIM_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_SHOWNEW ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['shownew']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_SHOWNEW_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_NEWCHAR ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_newchar" value = "<?php echo $fbConfig->newchar;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_NEWCHAR_DESC ?>
                    </td>
                </tr>

				<tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_MAMBOT_SUPPORT ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['jmambot']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_MAMBOT_SUPPORT_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_DISEMOTICONS ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['disemoticons']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_DISEMOTICONS_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_TEMPLATE ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['template']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_TEMPLATE_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_TEMPLATE_IMAGE_PATH ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['templateimagepath']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_TEMPLATE_IMAGE_PATH_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"  ><?php echo _COM_A_FBDEFAULT_PAGE ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['fbdefaultpage']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_FBDEFAULT_PAGE_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_USE_JOOMLA_STYLE ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['joomlastyle']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_USE_JOOMLA_STYLE_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_SHOW_ANNOUNCEMENT ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['showannouncement']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_SHOW_ANNOUNCEMENT_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_SHOW_AVATAR_ON_CAT ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['avataroncat']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_SHOW_AVATAR_ON_CAT_DESC ?>
                    </td>
                </tr>
                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_CATIMAGEPATH ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_catimagepath" value = "<?php echo $fbConfig->catimagepath;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_CATIMAGEPATH_DESC ?>
                    </td>
                </tr>
				<tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_SHOW_CHILD_CATEGORY_COLON ?>
                    </td>
					 <td align = "left" valign = "top">
                    <input type = "text" name = "cfg_numchildcolumn" value = "<?php echo $fbConfig->numchildcolumn;?>"/>
                    </td>
                    <td align = "left" valign = "top"><?php echo _FB_SHOW_CHILD_CATEGORY_COLONDESC ?>
                    </td>
                </tr>
                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_SHOW_CHILD_CATEGORY_ON_LIST ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['showchildcaticon']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_SHOW_CHILD_CATEGORY_ON_LIST_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_ANN_MODID ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_annmodid" value = "<?php echo $fbConfig->annmodid;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_ANN_MODID_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_TAWIDTH ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_rtewidth" value = "<?php echo $fbConfig->rtewidth;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_TAWIDTH_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_TAHEIGHT ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_rteheight" value = "<?php echo $fbConfig->rteheight;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_TAHEIGHT_DESC ?>
                    </td>
                </tr>

               <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_RULESPAGE ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['enablerulespage']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_RULESPAGE_DESC ?>
                    </td>
                </tr>
                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_RULESPAGE_IN_FB ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['rules_infb']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_RULESPAGE_IN_FB_DESC ?>
                    </td>
                </tr>
                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_RULESPAGE_CID ?>
                    </td>

                    <td align = "left" valign = "top"><input type = "text" name = "cfg_rules_cid" value = "<?php echo $fbConfig->rules_cid;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_RULESPAGE_CID_DESC ?>
                    </td>
                </tr>
                 <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_RULESPAGE_LINK ?>
                    </td>

                    <td align = "left" valign = "top"><input type = "text" name = "cfg_rules_link" value = "<?php echo $fbConfig->rules_link;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_RULESPAGE_LINK_DESC ?>
                    </td>
                </tr>
     			<tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_HELPPAGE ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['enablehelppage']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_HELPPAGE_DESC ?>
                    </td>
                </tr>
                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_HELPPAGE_IN_FB ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['help_infb']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_HELPPAGE_IN_FB_DESC ?>
                    </td>
                </tr>
                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_HELPPAGE_CID ?>
                    </td>

                    <td align = "left" valign = "top"><input type = "text" name = "cfg_help_cid" value = "<?php echo $fbConfig->help_cid;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_HELPPAGE_CID_DESC ?>
                    </td>
                </tr>
                 <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_HELPPAGE_LINK ?>
                    </td>

                    <td align = "left" valign = "top"><input type = "text" name = "cfg_help_link" value = "<?php echo $fbConfig->help_link;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_HELPPAGE_LINK_DESC ?>
                    </td>
                </tr>
                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_FORUM_JUMP ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['enableforumjump']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_FORUM_JUMP_DESC ?>
                    </td>
                </tr>
 				<tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_COM_A_REPORT ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['reportmsg']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_COM_A_REPORT_DESC ?>
                    </td>
                </tr>
            </table>
		</fieldset>

          <fieldset>
			<legend> <?php echo _COM_A_USERS ?></legend>
             <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _COM_A_USERNAME ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['username']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_USERNAME_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_ASK_EMAIL ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['askemail']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_ASK_EMAIL_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_SHOWMAIL ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['showemail']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_SHOWMAIL_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_USERSTATS ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['showuserstats']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_USERSTATS_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_POSTSTATSBAR ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['poststats']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_POSTSTATSBAR_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_POSTSTATSCOLOR ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_statscolor" value = "<?php echo $fbConfig->statscolor;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_POSTSTATSCOLOR_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td colspan = 2>&nbsp;

                    </td>

                    <td align = "left" valign = "top">
                        <table size = 100%>
                            <tr>
                              <td>
                                1: <img src = "<?php echo $mosConfig_live_site;?>/components/com_fireboard/template/<?php echo $fbConfig->template ;?>/images/english/graph/col1m.png" width = "15" height = "4">
                              </td>
                              <td>
                                2: <img src = "<?php echo $mosConfig_live_site;?>/components/com_fireboard/template/<?php echo $fbConfig->template ;?>/images/english/graph/col2m.png" width = "15" height = "4">
                              </td>
                              <td>
                                3: <img src = "<?php echo $mosConfig_live_site;?>/components/com_fireboard/template/<?php echo $fbConfig->template ;?>/images/english/graph/col3m.png" width = "15" height = "4">
                              </td>
                              <td>
                                4: <img src = "<?php echo $mosConfig_live_site;?>/components/com_fireboard/template/<?php echo $fbConfig->template ;?>/images/english/graph/col4m.png" width = "15" height = "4">
                              </td>
                              <td>
                                5: <img src = "<?php echo $mosConfig_live_site;?>/components/com_fireboard/template/<?php echo $fbConfig->template ;?>/images/english/graph/col5m.png" width = "15" height = "4">
                              </td>
                              <td>
                                6: <img src = "<?php echo $mosConfig_live_site;?>/components/com_fireboard/template/<?php echo $fbConfig->template ;?>/images/english/graph/col6m.png" width = "15" height = "4">
                              </td>
                            </tr>

                            <tr>
                              <td>
                                7: <img src = "<?php echo $mosConfig_live_site;?>/components/com_fireboard/template/<?php echo $fbConfig->template ;?>/images/english/graph/col7m.png" width = "15" height = "4">
                              </td>
                              <td>
                                8: <img src = "<?php echo $mosConfig_live_site;?>/components/com_fireboard/template/<?php echo $fbConfig->template ;?>/images/english/graph/col8m.png" width = "15" height = "4">
                              </td>
                              <td>
                                9: <img src = "<?php echo $mosConfig_live_site;?>/components/com_fireboard/template/<?php echo $fbConfig->template ;?>/images/english/graph/col9m.png" width = "15" height = "4">
                              </td>
                              <td>
                                10: <img src = "<?php echo $mosConfig_live_site;?>/components/com_fireboard/template/<?php echo $fbConfig->template ;?>/images/english/graph/col10m.png" width = "15" height = "4">
                              </td>
                              <td>
                                11: <img src = "<?php echo $mosConfig_live_site;?>/components/com_fireboard/template/<?php echo $fbConfig->template ;?>/images/english/graph/col11m.png" width = "15" height = "4">
                              </td>
                              <td>
                                12: <img src = "<?php echo $mosConfig_live_site;?>/components/com_fireboard/template/<?php echo $fbConfig->template ;?>/images/english/graph/col12m.png" width = "15" height = "4">
                              </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_KARMA ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['showkarma']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_KARMA_DESC ?>

                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_USER_EDIT ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['useredit']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_USER_EDIT_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_USER_EDIT_TIME ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_useredittime" value = "<?php echo $fbConfig->useredittime;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_USER_EDIT_TIME_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_USER_EDIT_TIMEGRACE ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_useredittimegrace" value = "<?php echo $fbConfig->useredittimegrace;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_USER_EDIT_TIMEGRACE_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_USER_MARKUP ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['editmarkup']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_USER_MARKUP_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_SUBSCRIPTIONS ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['allowsubscriptions']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_SUBSCRIPTIONS_DESC ?>
                    </td>
                </tr>
				<tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_SUBSCRIPTIONSCHECKED ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['subscriptionschecked']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_SUBSCRIPTIONSCHECKED_DESC ?>
                    </td>
                </tr>
                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_FAVORITES ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['allowfavorites']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_FAVORITES_DESC ?>
                  </td>
                </tr>
            </table>
		</fieldset>

          <fieldset>
			<legend> <?php echo _COM_A_LENGTHS ?></legend>
             <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"  width="25%"><?php echo _COM_A_WRAP ?>
                    </td>

                    <td align = "left" valign = "top" width="25%">
                        <input type = "text" name = "cfg_wrap" value = "<?php echo $fbConfig->wrap;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_WRAP_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_SUBJECTLENGTH ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_maxsubject" value = "<?php echo $fbConfig->maxsubject;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_SUBJECTLENGTH_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_SIGNATURE ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_maxsig" value = "<?php echo $fbConfig->maxsig;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_SIGNATURE_DESC ?>
                    </td>
                </tr>
            </table>
	</fieldset>
			<div class="fbfuncsubtitle"><?php echo _COM_A_SECURITY ?>  <a name="security" id="security" > </a></div>
            <fieldset>
			<legend> <?php echo _COM_A_SECURITY_SETTINGS ?></legend>
            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _COM_A_REGISTERED_ONLY ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['regonly']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_REG_ONLY_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_CHANGENAME ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['changename']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_CHANGENAME_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_PUBWRITE ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['pubwrite']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_PUBWRITE_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_FLOOD ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_floodprotection" value = "<?php echo $fbConfig->floodprotection;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_FLOOD_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_MODERATION ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['mailmod']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_MODERATION_DESC ?>
                    </td>
                </tr>
                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_A_MAIL_ADMIN ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['mailadmin']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_A_MAIL_ADMIN_DESC ?>
                    </td>
                </tr>
                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_CAPTCHA_ON ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['captcha']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_CAPTCHA_DESC ?>
                    </td>
                </tr>
                 <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_MAILFULL; ?>
                    </td>
                    <td align = "left" valign = "top"><?php echo $lists['mailfull']; ?>
                    </td>
                    <td align = "left" valign = "top"><?php echo _FB_MAILFULL_DESC; ?>
                    </td>
                </tr>
            </table>
          </fieldset>
			<div class="fbfuncsubtitle"><?php echo _COM_A_AVATARS ?><a name="avatars" id="avatars" > </a></div>
            <fieldset>
			<legend> <?php echo _COM_A_AVATAR_SETTINGS ?></legend>
            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _COM_A_AVATAR ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['allowavatar']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_AVATAR_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_AVATARUPLOAD ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['allowavatarupload']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_AVATARUPLOAD_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_AVATARGALLERY ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['allowavatargallery']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_AVATARGALLERY_DESC ?>
                    </td>
                </tr>
				<tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_IMAGE_PROCESSOR ?>
                    </td>

                    <td align = "left" valign = "top">
                        <?php echo $lists['imageprocessor']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php

				$fb_gd = intval(FB_gdVersion());
				if ($fb_gd > 0) {
				   $fbmsg = _FB_GD_INSTALLED .  $fb_gd ;
  				} elseif ($gdver == -1) {
  				   $fbmsg = _FB_GD_NO_VERSION;
  			    } else {
   				   $fbmsg = _FB_GD_NOT_INSTALLED . '<a href="http://www.php.net/gd" target="_blank">http://www.php.net/gd</a>';
 			    }

				    echo $fbmsg;

                    ?>
                    </td>
                </tr>
                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_AVATAR_SMALL_HEIGHT ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_avatarsmallheight" value = "<?php echo $fbConfig->avatarsmallheight;?>"/>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_AVATAR_SMALL_WIDTH ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_avatarsmallwidth" value = "<?php echo $fbConfig->avatarsmallwidth;?>"/>
                    </td>
                </tr>
                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_AVATAR_MEDIUM_HEIGHT ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_avatarheight" value = "<?php echo $fbConfig->avatarheight;?>"/>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_AVATAR_MEDIUM_WIDTH ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_avatarwidth" value = "<?php echo $fbConfig->avatarwidth;?>"/>
                    </td>
                </tr>
                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_AVATAR_LARGE_HEIGHT ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_avatarlargeheight" value = "<?php echo $fbConfig->avatarlargeheight;?>"/>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_AVATAR_LARGE_WIDTH ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_avatarlargewidth" value = "<?php echo $fbConfig->avatarlargewidth;?>"/>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_AVSIZE ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_avatarsize" value = "<?php echo $fbConfig->avatarsize;?>"/>
                    </td>
                </tr>
                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_AVATAR_QUALITY ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_avatarquality" value = "<?php echo $fbConfig->avatarquality;?>"/> %
                    </td>
                </tr>
            </table>
 			</fieldset>
			<div class="fbfuncsubtitle"><?php echo _COM_A_UPLOADS ?><a name="uploads" id="uploads" > </a></div>
            <fieldset>
			<legend> <?php echo _COM_A_IMAGE ?></legend>
            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">


                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _COM_A_IMAGEUPLOAD ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['allowimageupload']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_IMAGEUPLOAD_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_IMAGEREGUPLOAD ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['allowimageregupload']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_IMAGEREGUPLOAD_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_IMGHEIGHT ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_imageheight" value = "<?php echo $fbConfig->imageheight;?>"/>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_IMGWIDTH ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_imagewidth" value = "<?php echo $fbConfig->imagewidth;?>"/>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_IMGSIZE ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_imagesize" value = "<?php echo $fbConfig->imagesize;?>"/>
                    </td>
                </tr>
                </table>
                </fieldset>
 <fieldset>
			<legend> <?php echo _COM_A_FILE ?></legend>
            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _COM_A_FILEUPLOAD ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['allowfileupload']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_FILEUPLOAD_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_FILEREGUPLOAD ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['allowfileregupload']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_FILEREGUPLOAD_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_FILEALLOWEDTYPES ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_filetypes" value = "<?php echo $fbConfig->filetypes;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_FILEALLOWEDTYPES_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_FILESIZE ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_filesize" value = "<?php echo $fbConfig->filesize;?>"/>
                    </td>
                </tr>
            </table>

                </fieldset>

			<div class="fbfuncsubtitle"><?php echo _COM_A_RANKING ?><a name="ranking" id="ranking" > </a></div>

          <fieldset>
			<legend> <?php echo _COM_A_RANKING_SETTINGS ?></legend>
            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">
                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _COM_A_RANKING ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['showranking']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_RANKING_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_RANKINGIMAGES ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['rankimages']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_RANKINGIMAGES_DESC ?>
                    </td>
                </tr>
            </table>
		</fieldset>



		<div class="fbfuncsubtitle"><?php echo _COM_A_BBCODE ?><a name="bbcode" id="bbcode" > </a></div>

          <fieldset>
			<legend> <?php echo _COM_A_BBCODE_SETTINGS ?></legend>
            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">
                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _COM_A_SHOWSPOILERTAG ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['showspoilertag']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_SHOWSPOILERTAG_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _COM_A_SHOWVIDEOTAG ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['showvideotag']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_SHOWVIDEOTAG_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _COM_A_SHOWEBAYTAG ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['showebaytag']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_SHOWEBAYTAG_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_EBAYLANGUAGECODE ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_ebaylanguagecode" value = "<?php echo $fbConfig->ebaylanguagecode;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_EBAYLANGUAGECODE_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _COM_A_TRIMLONGURLS ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['trimlongurls']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_TRIMLONGURLS_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_TRIMLONGURLSFRONT ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_trimlongurlsfront" value = "<?php echo $fbConfig->trimlongurlsfront;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_TRIMLONGURLSFRONT_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_TRIMLONGURLSBACK ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_trimlongurlsback" value = "<?php echo $fbConfig->trimlongurlsback;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_TRIMLONGURLSBACK_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_AUTOEMBEDYOUTUBE ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['autoembedyoutube']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_AUTOEMBEDYOUTUBE_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_AUTOEMBEDEBAY ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['autoembedebay']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_AUTOEMBEDEBAY_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_HIGHLIGHTCODE ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['highlightcode']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_HIGHLIGHTCODE_DESC ?>
                    </td>
                </tr>

            </table>
		</fieldset>

			<div class="fbfuncsubtitle"><?php echo _COM_A_INTEGRATION ?><a name="integration" id="integration" > </a></div>
         <fieldset>
			<legend> <?php echo _COM_A_AVATAR_INTEGRATION ?></legend>
            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">


                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _COM_A_AVATAR_SRC ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['avatar_src']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_AVATAR_SRC_DESC ?>
                    </td>
                </tr>
			</table>
			</fieldset>
			<fieldset>
			<legend> <?php echo _FB_FORUMPRF_TITLE ?></legend>
     		<table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _FB_FORUMPRF ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['fb_profile']; ?>
                    </td>


                    <td align = "left" valign = "top"><?php echo _FB_FORUMPRRDESC ?>
                    </td>
                </tr>
			</table>
			</fieldset>
			<fieldset>
			<legend> <?php echo _COM_A_PMS_TITLE ?></legend>
   			<table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">


                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _COM_A_PMS ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['pm_component']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_PMS_DESC ?>
                    </td>
                </tr>
</table>
</fieldset>
<fieldset>
			<legend> <?php echo _COM_A_COMBUILDER_TITLE ?></legend>
   <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">


                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _COM_A_COMBUILDER ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['cb_profile']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_COMBUILDER_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _COM_A_COMBUILDER_PROFILE ?>
                    </td>

                    <td align = "left" valign = "top">
                        <a href = "index2.php?option=com_fireboard&amp;task=loadCBprofile" style = "text-decoration:none;" title = "<?php echo _COM_A_COMBUILDER_PROFILE_DESC;?>"><?php echo _COM_A_COMBUILDER_PROFILE_CLICK ?></a>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_COMBUILDER_PROFILE_DESC ?>
                    </td>
                </tr>
</table>
</fieldset>
<fieldset>
			<legend> <?php echo _COM_A_BADWORDS_TITLE ?></legend>
   <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">


                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _COM_A_BADWORDS ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['badwords']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _COM_A_BADWORDS_DESC ?>
                    </td>
                </tr>
</table>
</fieldset>
<fieldset>
			<legend> <?php echo _COM_A_MOSBOT_TITLE ?></legend>
   <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">


                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _COM_A_MOSBOT ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['discussbot']; ?>
                    </td>

                    <td align = "left" valign = "top">
<?php echo _COM_A_MOSBOT_DESC ?><br/>

            <br/>
            <!-- Start Arno code test -->
            <script language = "javascript">
                <!--
                function openWin(url)
                {
                    var newWin = window.open(url, "popup", "toolbar=no,scrollbars=yes,width=500,height=250,left=0,top=0");
                    newWin.focus();
                }
                        //-->
            </script>

            <input type = "button" class = "button" value = "<?php echo _COM_A_BOT_REFERENCE;?>" onClick = "openWin('components/com_fireboard/fireboard_mosbot_help.php')">
                    <!-- End Arno code test -->
                    </td>
                </tr>
            </table>
</fieldset>


            <div class="fbfuncsubtitle"><?php echo _FB_ADMIN_CONFIG_PLUGINS ?><a name="plugins" id="plugins"> </a></div>
<fieldset>
			<legend> <?php echo _FB_ADMIN_CONFIG_USERLIST ?></legend>
   <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">


                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _FB_ADMIN_CONFIG_USERLIST_ROWS ?>
                    </td>

                    <td align = "left" valign = "top" width="25%">
                        <input type = "text" name = "cfg_userlist_rows" value = "<?php echo $fbConfig->userlist_rows;?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_ROWS_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_USERONLINE ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['userlist_online']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_USERONLINE_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_AVATAR ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['userlist_avatar']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_NAME ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['userlist_name']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_name_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_USERNAME ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['userlist_username']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_USERNAME_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_GROUP ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['userlist_group']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_GROUP_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_POSTS ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['userlist_posts']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_POSTS_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_KARMA ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['userlist_karma']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_KARMA_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_EMAIL ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['userlist_email']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_EMAIL_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_USERTYPE ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['userlist_usertype']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_USERTYPE_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_JOINDATE ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['userlist_joindate']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_JOINDATE_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_LASTVISITDATE ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['userlist_lastvisitdate']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC ?>
                    </td>
                </tr>

				 <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_HITS ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['userlist_userhits']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_ADMIN_CONFIG_USERLIST_HITS_DESC ?>
                    </td>
                </tr>
		</table>
        </fieldset>
        <fieldset>
			<legend> <?php echo _FB_RECENT_POSTS ?></legend>
   <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _FB_SHOW_LATEST_MESSAGES ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['showlatest']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_SHOW_LATEST_MESSAGES_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_NUMBER_OF_LATEST_MESSAGES ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_latestcount" value = "<?php echo $fbConfig->latestcount; ?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_NUMBER_OF_LATEST_MESSAGES_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_COUNT_PER_PAGE_LATEST_MESSAGES ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_latestcountperpage" value = "<?php echo $fbConfig->latestcountperpage; ?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_COUNT_PER_PAGE_LATEST_MESSAGES_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_LATEST_CATEGORY ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_latestcategory" value = "<?php echo $fbConfig->latestcategory; ?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_LATEST_CATEGORY_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_SHOW_LATEST_SINGLE_SUBJECT ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['latestsinglesubject']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_SHOW_LATEST_SINGLE_SUBJECT_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_SHOW_LATEST_REPLY_SUBJECT ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['latestreplysubject']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_SHOW_LATEST_REPLY_SUBJECT_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_LATEST_SUBJECT_LENGTH ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_latestsubjectlength" value = "<?php echo $fbConfig->latestsubjectlength; ?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_LATEST_SUBJECT_LENGTH_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_SHOW_LATEST_DATE ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['latestshowdate']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_SHOW_LATEST_DATE_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_SHOW_LATEST_HITS ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['latestshowhits']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_SHOW_LATEST_HITS_DESC ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_SHOW_AUTHOR ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_latestshowauthor" value = "<?php echo $fbConfig->latestshowauthor; ?>" size = "1"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_SHOW_AUTHOR_DESC ?>
                    </td>
                </tr>
			</table>
            </fieldset>

               <fieldset>
			<legend> <?php echo _FB_STATS ?></legend>
   <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">


                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _FB_SHOWSTATS; ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['showstats']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_SHOWSTATSDESC; ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_SHOWWHOIS; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['showwhoisonline']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_SHOWWHOISDESC; ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_STATSGENERAL; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['showgenstats']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_STATSGENERALDESC; ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_USERSTATS; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['showpopuserstats']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_USERSTATSDESC; ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_USERNUM; ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_popusercount" value = "<?php echo $fbConfig->popusercount; ?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_USERNUM; ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_USERPOPULAR; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo $lists['showpopsubjectstats']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_USERPOPULARDESC; ?>
                    </td>
                </tr>

                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top"><?php echo _FB_NUMPOP; ?>
                    </td>

                    <td align = "left" valign = "top">
                        <input type = "text" name = "cfg_popsubjectcount" value = "<?php echo $fbConfig->popsubjectcount; ?>"/>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_NUMPOP; ?>
                    </td>
                </tr>
                </table>
                </fieldset>

               <fieldset>
			<legend> <?php echo _FB_MYPROFILE_PLUGIN_SETTINGS ?></legend>
   <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "fbadminform">


                <tr align = "center" valign = "middle">
                    <td align = "left" valign = "top" width="25%"><?php echo _FB_USERNAMECANCHANGE; ?>
                    </td>

                    <td align = "left" valign = "top" width="25%"><?php echo $lists['usernamechange']; ?>
                    </td>

                    <td align = "left" valign = "top"><?php echo _FB_USERNAMECANCHANGE_DESC; ?>
                    </td>
                </tr>


            </table>

           </fieldset>



    <input type = "hidden" name = "task" value = "showConfig"/>

    <input type = "hidden" name = "option" value = "<?php echo $option; ?>"/>
        </form>
</div><!-- closed div#fnconfigcover -->
<?php
    }

    function showInstructions($database, $option, $mosConfig_lang) {
?>

    <table width = "100%" border = "0" cellpadding = "2" cellspacing = "2" class = "adminheading">
        <TR>
            <th class = "info">
                &nbsp;<?php echo _FB_INSTRUCTIONS; ?>
            </th>
        </tr>
    </table>

    <table width = "100%" border = "0" cellpadding = "2" cellspacing = "2" class = "adminform">
        <tr>
            <th><?php echo _FB_FINFO; ?>
            </th>
        </tr>

        <tr>
            <td>
<?php echo _FB_INFORMATION; ?>
            </td>
        </tr>
    </table>

<?php
    } //end function showInstructions

    function showCss($file, $option)
    {
        $file = stripslashes($file);
        $f = fopen($file, "r");
        $content = fread($f, filesize($file));
        $content = htmlspecialchars($content);
?>
<div class="fbfunctitle"><?php echo _FB_CSSEDITOR; ?></div>
    <form action = "index2.php?" method = "post" name = "adminForm" class = "adminForm" id = "adminForm">


        <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "adminform">
            <tr>
	                <th colspan = "4">
					<?php echo _FB_PATH; ?> <?php echo $file; ?>
            </tr>

            <tr>
                <td>
                    <textarea cols = "100" rows = "20" name = "csscontent"><?php echo $content; ?></textarea>
                </td>
            </tr>

            <tr>
                <td class = "error"><?php echo _FB_CSSERROR; ?>
                </td>
            </tr>
        </table>

        <input type = "hidden" name = "file" value = "<?php echo $file; ?>"/>

        <input type = "hidden" name = "option" value = "<?php echo $option; ?>"> <input type = "hidden" name = "task" value = ""> <input type = "hidden" name = "boxchecked" value = "0">
    </form>


<?php
    } //end function showCss

    function showProfiles($option, $mosConfig_lang, &$profileList, $countPL, $pageNavSP, $order, $search)
    {
?>
<div class="fbfunctitle"><?php echo _FB_FUM; ?></div>
        <form action = "index2.php" method = "POST" name = "adminForm">
            <table  cellpadding = "4" cellspacing = "0" border = "0" width = "100%">
                <tr>


                    <td nowrap align = "right"><?php echo _COM_A_DISPLAY; ?>
                    </td>

                    <td nowrap align = "right">
<?php echo $pageNavSP->writeLimitBox(); ?>
                    </td>

                    <td nowrap align = "right">
<?php echo _USRL_SEARCH_BUTTON; ?>:
                    </td>

                    <td nowrap align = "right">
                        <input type = "text" name = "search" value = "<?php echo $search;?>" class = "inputbox" onChange = "document.adminForm.submit();"/>
                    </td>
                </tr>

                <tr>


                    <td colspan = "4" nowrap>
                        :: <a href = "index2.php?option=com_fireboard&task=profiles&order=0"><?php
    echo _FB_SORTID; ?></a> :: <a href = "index2.php?option=com_fireboard&task=profiles&order=1"><?php echo _FB_MOD; ?></a> :: <a href = "index2.php?option=com_fireboard&task=profiles&order=2"><?php echo _FB_SORTNAME; ?></a>
                    </td>
                </tr>
            </table>

            <table class = "adminlist" border = 0 cellspacing = 0 cellpadding = 3 width = "100%">
                <tr>
                    <th algin = "left" width = "20">
                        <input type = "checkbox" name = "toggle" value = "" onclick = "checkAll(<?php echo count( $profileList ); ?>);"/>
                    </th>

                    <th algin = "left" width = "10"><?php echo _ANN_ID; ?>
                    </th>

                    <th algin = "left" width = "10"><?php echo _USRL_NAME; ?>
                    </th>

                    <th algin = "left" width = "100"><?php echo _GEN_EMAIL; ?>
                    </th>

                    <th algin = "left" width = "15"><?php echo _VIEW_MODERATOR; ?>
                    </th>

                    <th algin = "left" width = "10"><?php echo _FB_VIEW; ?>
                    </th>

                    <th algin = "left" width = "*"><?php echo _GEN_SIGNATURE; ?>
                    </th>
                </tr>

                <?php
                if ($countPL > 0)
                {
                    $k = 0;
                    //foreach ($profileList as $pl)
                    $i = 0;

                    for ($i = 0, $n = count($profileList); $i < $n; $i++)
                    {
                        $pl = &$profileList[$i];
                        $k = 1 - $k;
                ?>

                            <tr class = "row<?php echo $k;?>">
                                <td width = "20">
                                    <input type = "checkbox" id = "cb<?php echo $i;?>" name = "uid[]" value = "<?php echo $pl->id; ?>" onClick = "isChecked(this.checked);">
                                </td>

                                <td width = "10">
                                    <a href = "#edit" onclick = "return listItemTask('cb<?php echo $i; ?>','userprofile')"><?php echo $pl->userid; ?></a>
                                </td>

                                <td width = "100">
                                    <a href = "#edit" onclick = "return listItemTask('cb<?php echo $i; ?>','userprofile')"><?php echo $pl->name; ?></a>
                                </td>

                                <td width = "100">
<?php echo $pl->email; ?>&nbsp;
                                </td>

                                <td align = "center" width = "15">
                                    <?php
                                    if ($pl->moderator) {
                                        echo _ANN_YES;
                                    }
                                    else {
                                        echo _ANN_NO;
                                    }

                                    ;
                                    ?>

                        &nbsp;
                                </td>

                                <td align = "center" width = "10">
<?php echo $pl->view; ?>&nbsp;
                                </td>

                                <td width = "*">
<?php echo $pl->signature; ?>&nbsp;
                                </td>
                            </tr>

                <?php
                    }
                }
                else {
                    echo "<tr><td colspan=\"7\">" . _FB_NOUSERSFOUND . "</td></tr>";
                }
                ?>

    <input type = "hidden"
        name = "order" value = "<?php echo "$order";?>"> <input type = "hidden" name = "option" value = "<?php echo $option; ?>"> <input type = "hidden" name = "task" value = "showprofiles"> <input type = "hidden" name = "boxchecked" value = "0">
    <?php if (FBTools::isJoomla15()) echo '<input type = "hidden" name = "limitstart" value = "0">'; ?>

    <tr>
        <th align = "center" colspan = "7"> <?php
        // TODO: fxstein - Need to perform SEO cleanup
        echo $pageNavSP->writePagesLinks(); ?>
        </th>
    </tr>

    <tr>
        <td align = "center" colspan = "7"> <?php echo $pageNavSP->writePagesCounter(); ?>
        </td>
    </tr>
            </table>
        </form>

<?php
    } //end function showProfiles

    function newModerator($option, $id, $moderators, &$modIDs, $forumName, &$userList, $countUL, $pageNav)
    {
?>

        <form action = "index2.php" method = "post" name = "adminForm">
            <table cellpadding = "4" class = "adminheading" cellspacing = "0" border = "0" width = "100%">
                <tr>
                    <th width = "100%" class = "user"><?php echo _FB_ADDMOD; ?> <?php echo $forumName; ?>
                    </th>

                    <td nowrap><?php echo _COM_A_DISPLAY; ?>
                    </td>

                    <td>
<?php echo $pageNav->writeLimitBox(); ?>
                    </td>

                    <td>&nbsp;

                    </td>

                    <td>&nbsp;

                    </td>
                </tr>
            </table>

            <table class = "adminlist" border = 0 cellspacing = 0 cellpadding = 3 width = "100%">
                <tr>
                    <th width = "20">
                        #
                    </th>

                    <th width = "20">
                        <input type = "checkbox" name = "toggle" value = "" onclick = "checkAll(<?php echo count( $userList ); ?>);"/>
                    </th>

                    <th><?php echo _ANN_ID; ?>
                    </th>

                    <th align = "left"><?php echo _USRL_NAME; ?>
                    </th>

                    <th align = "left"><?php echo _GEN_EMAIL; ?>
                    </th>

                    <th><?php echo _FB_PUBLISHED; ?>
                    </th>

                    <th>&nbsp;

                    </th>
                </tr>

                <?php
                if ($countUL > 0)
                {
                    $k = 0;
                    $i = 0;

                    for ($i = 0, $n = count($userList); $i < $n; $i++)
                    {
                        $pl = &$userList[$i];
                        $k = 1 - $k;
                ?>

                            <tr class = "row<?php echo $k;?>">
                                <td width = "20" align = "right"><?php echo $i + $pageNav->limitstart + 1; ?>
                                </td>

                                <td width = "20">
                                    <input type = "checkbox" id = "cb<?php echo $i;?>" name = "cid[]" value = "<?php echo $pl->id; ?>" onClick = "isChecked(this.checked);">
                                </td>

                                <td width = "20">
                                    <a href = "index2.php?option=com_fireboard&task=userprofile&do=show&user_id=<?php echo $pl->id;?>"><?php echo $pl->id; ?>&nbsp;
                                </td>

                                <td>
<?php echo $pl->name; ?>&nbsp;
                                </td>

                                <td>
<?php echo $pl->email; ?>&nbsp;
                                </td>

                                <td align = "center">
                                    <?php
                                    if ($moderators)
                                    {
                                        if (in_array($pl->id, $modIDs)) {
                                            echo "<img src=\"images/tick.png\">";
                                        }
                                        else {
                                            echo "<img src=\"images/publish_x.png\">";
                                        }
                                    }
                                    else {
                                        echo "<img src=\"images/publish_x.png\">";
                                    }
                                    ?>
                                </td>

                                <td>&nbsp;

                                </td>
                            </tr>

                <?php
                    }
                }
                else {
                    echo "<tr><td align='left' colspan='7'>" . _FB_NOMODSAV . "</td></tr>";
                }
                ?>

    <input type = "hidden"
        name = "option" value = "<?php echo $option; ?>"> <input type = "hidden" name = "id" value = "<?php echo $id; ?>"> <input type = "hidden" name = "boxchecked" value = "0"> <input type = "hidden" name = "task" value = "newmoderator">
    <?php if (FBTools::isJoomla15()) echo '<input type = "hidden" name = "limitstart" value = "0">'; ?>

    <tr>
        <th align = "center" colspan = "7"> <?php
        // TODO: fxstein - Need to perform SEO cleanup
        echo $pageNav->writePagesLinks(); ?>
        </th>
    </tr>

    <tr>
        <td align = "center" colspan = "7"> <?php echo $pageNav->writePagesCounter(); ?>
        </td>
    </tr>

    <tr>
        <td colspan = "7"><?php echo _FB_NOTEUS; ?>
        </td>
    </tr>
            </table>
        </form>

<?php
    }

    //   function showUserProfile ($database,$mosLang,$user_id,$do,$deleteSig,$signature,$newview,$user_id,$thread,$moderator)
    //   {
    //
    //      include ('components/com_fireboard/moderate_user.php');
    //   }
    function editUserProfile($user, $subslist, $selectRank, $selectPref, $selectMod, $selectOrder, $uid , $modCats)
    {
        global $fbConfig, $database;
        //fill the variables needed later
            $signature = $user->signature;
            $username = $user->name;
            $avatar = $user->avatar;
            $ordering = $user->ordering;
        //that's what we got now; later the 'future_use' columns can be used..

        $csubslist = count($subslist);
        include_once ('components/com_fireboard/bb_adm.js');
?>

        <form action = "index2.php?option=<?php echo $option;?>" method = "POST" name = "adminForm">
            <table border = 0 cellspacing = 0 width = "100%" align = "center" class = "adminheading">
                <tr>
                    <th colspan = "3" class = "user">
<?php echo _FB_PROFFOR; ?> <?php echo $username; ?>
                    </th>
                </tr>
            </table>

            <table border = 0 cellspacing = 0 width = "100%" align = "center" class = "adminlist">
                <tr>
                    <th colspan = "3" class = "title">
<?php echo _FB_GENPROF; ?>

                </tr>

                <tr>
                    <td width = "150" class = "contentpane"><?php echo _FB_PREFVIEW; ?>
                    </td>

                    <td align = "left" valign = "top" class = "contentpane">
<?php echo $selectPref; ?>
                    </td>

                    <td>&nbsp;

                    </td>
                </tr>

                <tr>
                    <td width = "150" class = "contentpane"><?php echo _FB_PREFOR; ?>
                    </td>

                    <td align = "left" valign = "top" class = "contentpane">
<?php echo $selectOrder; ?>
                    </td>

                    <td>&nbsp;

                    </td>
                </tr>

                         <tr>
                    <td width = "150" class = "contentpane"><?php echo _FB_RANKS; ?>
                    </td>

                    <td align = "left" valign = "top" class = "contentpane">
<?php echo $selectRank; ?>
                    </td>

                    <td>&nbsp;

                    </td>
                </tr>



                            <td width = "150" valign = "top" class = "contentpane">
<?php echo _GEN_SIGNATURE; ?>:

        <br/> <?php echo $fbConfig->maxsig; ?>

        <input readonly type = text name = rem size = 3 maxlength = 3 value = "" class = "inputbox"> <?php echo _CHARS; ?><br/>
<?php echo _HTML_YES; ?>
                            </td>

                            <td align = "left" valign = "top" class = "contentpane">
                                <textarea rows = "6"
                                    class = "inputbox"
                                    onMouseOver = "textCounter(this.form.message,this.form.rem,<?php echo $fbConfig->maxsig;?>);"
                                    onClick = "textCounter(this.form.message,this.form.rem,<?php echo $fbConfig->maxsig;?>);"
                                    onKeyDown = "textCounter(this.form.message,this.form.rem,<?php echo $fbConfig->maxsig;?>);"
                                    onKeyUp = "textCounter(this.form.message,this.form.rem,<?php echo $fbConfig->maxsig;?>);" cols = "50" type = "text" name = "message"><?php echo $signature; ?></textarea>

                                <br/>

                                <input type = "button" class = "button" accesskey = "b" name = "addbbcode0" value = " B " style = "font-weight:bold; width: 30px" onClick = "bbstyle(0)" onMouseOver = "helpline('b')"/>

                                <input type = "button" class = "button" accesskey = "i" name = "addbbcode2" value = " i " style = "font-style:italic; width: 30px" onClick = "bbstyle(2)" onMouseOver = "helpline('i')"/>

                                <input type = "button" class = "button" accesskey = "u" name = "addbbcode4" value = " u " style = "text-decoration: underline; width: 30px" onClick = "bbstyle(4)" onMouseOver = "helpline('u')"/>

                                <input type = "button" class = "button" accesskey = "p" name = "addbbcode14" value = "Img" style = "width: 40px" onClick = "bbstyle(14)" onMouseOver = "helpline('p')"/>

                                <input type = "button" class = "button" accesskey = "w" name = "addbbcode16" value = "URL" style = "text-decoration: underline; width: 40px" onClick = "bbstyle(16)" onMouseOver = "helpline('w')"/>

                                <br/><?php echo _FB_COLOR; ?>:

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

            <option value = "3" selected><?php echo _SIZE_NORMAL; ?></option>

            <option value = "4"><?php echo _SIZE_BIG; ?></option>

            <option value = "5"><?php echo _SIZE_VBIG; ?></option>
        </select>

        <a href = "javascript: bbstyle(-1)"onMouseOver = "helpline('a')"><small><?php echo _BBCODE_CLOSA; ?></small></a>

        <br/>

        <input type = "text" name = "helpbox" size = "45" maxlength = "100" style = "width:400px; font-size:8px" class = "options" value = "<?php echo _BBCODE_HINT;?>"/>
                            </td>

                            <?php
                            if ($fbConfig->allowavatar)
                            {
                            ?>

                                <td class = "contentpane" align = "center">
<?php echo _FB_UAVATAR; ?><br/>

<?php
if ($avatar != '')
{
   echo '<img src="' . FB_LIVEUPLOADEDPATH . '/avatars/' . $avatar . '" ><br />';
   echo '<input type="hidden" value="' . $avatar . '" name="avatar">';
}
else
{
   echo "<em>" . _FB_NS . "</em><br />";
   echo '<input type="hidden" value="$avatar" name="avatar">';
}
?>
                                </td>

                            <?php
                            }
                            else
                            {
                                echo "<td>&nbsp;</td>";
                                echo '<input type="hidden" value="" name="avatar">';
                            }
                            ?>
                        </tr>

                        <tr>
                            <td colspan = "2" class = "contentpane">
                                <input type = "checkbox" value = "1" name = "deleteSig"><i><?php echo _FB_DELSIG; ?></i>
                            </td>

                            <?php
                            if ($fbConfig->allowavatar)
                            {
                            ?>

                                <td class = "contentpane">
                                    <input type = "checkbox" value = "1" name = "deleteAvatar"><i><?php echo _FB_DELAV; ?></i>
                                </td>

                            <?php
                            }
                            else {
                                echo "<td>&nbsp;</td>";
                            }
                            ?>
                        </tr>

                        <tr cellspacing = "3" colspan = "2">
                            &nbsp;

                            </td>
                        </tr>
            </table>

        <table border = 0 cellspacing = 0 width = "100%" align = "center" class = "adminform">
            <tr>
                <th colspan = "2" class = "title">
<?php echo _FB_MOD_NEW; ?>

            </td>
            </tr>
                        </tr>

                        <tr>


    <td width = "150" class = "contentpane">
    <?php echo _FB_ISMOD; ?>

                    <?php
                    //admins are always moderators
                    if (FBTools::isModOrAdmin($uid))
                    {
                    echo _FB_ISADM; ?> <input type = "hidden" name = "moderator" value = "1">
                    <?php
                    }
                    else
                    {
                            echo $selectMod;
                    }
                    ?>
                        </td>
                        <td>
<?php echo $modCats;?>
                        </td>
                        </tr>

            </table>
            <input type = "hidden" name = "uid" value = "<?php echo $uid;?>">

            <input type = "hidden" name = "task" value = ""/>

            <input type = "hidden" name = "option" value = "com_fireboard"/>
        </form>

        <table border = 0 cellspacing = 0 width = "100%" align = "center" class = "adminform">
            <tr>
                <th colspan = "2" class = "title">
<?php echo _FB_SUBFOR; ?> <?php echo $username; ?>

            </td>
            </tr>

            <?php
            $enum = 1; //reset value
            $k = 0;    //value for alternating rows

            if ($csubslist > 0)
            {
                foreach ($subslist as $subs)
                { //get all message details for each subscription
                    $database->setQuery("select * from #__fb_messages where id=$subs->thread");
                    $subdet = $database->loadObjectList();
                        check_dberror("Unable to load subscription messages.");

                    foreach ($subdet as $sub)
                    {
                        $k = 1 - $k;
                        echo "<tr class=\"row$k\">";
                        echo "  <td>$enum: $sub->subject by $sub->name";
                        echo "  <td>&nbsp;</td>";
                        echo "</tr>";
                        $enum++;
                    }
                }
            }
            else {
                echo "<tr><td class=\"message\">" . _FB_NOSUBS . "</td></tr>";
            }

            echo "</table>";
    }

    //**************************
    // Prune Forum
    //**************************
    function pruneforum($option, $forumList) {
            ?>
<div class="fbfunctitle"><?php echo _COM_A_PRUNE; ?></div>
    <form action = "index2.php" method = "post" name = "adminForm">


        <table class = "adminform" cellpadding = "4" cellspacing = "0" border = "0" width = "100%">
            <tr>
                <th width = "100%" colspan = "2">&nbsp;

                </th>
            </tr>

            <tr>
                <td colspan = "2"><?php echo _COM_A_PRUNE_DESC ?>
                </td>
            </tr>

            <tr>
                <td nowrap width = "10%"><?php echo _COM_A_PRUNE_NAME ?>
                </td>

                <td nowrap><?php echo $forumList['forum'] ?>
                </td>
            </tr>

            <tr>
                <td nowrap width = "10%"><?php echo _COM_A_PRUNE_NOPOSTS ?>
                </td>

                <td nowrap>
                    <input type = "text" name = "prune_days" value = "30"> <?php echo _COM_A_PRUNE_DAYS ?>
                </td>
            </tr>
        </table>

        <input type = "hidden" name = "task" value = ""/>

        <input type = "hidden" name = "option" value = "<?php echo $option; ?>"/>
    </form>

<?php
    }

    //**************************
    // Sync Users
    //**************************
    function syncusers($option) {
?>
<div class="fbfunctitle"><?php echo _FB_SYNC_USERS; ?></div>
    <form action = "index2.php" method = "post" name = "adminForm">


        <table cellpadding = "4" class = "adminform" cellspacing = "0" border = "0" width = "100%">
            <tr>
                <th width = "100%" colspan = "2">&nbsp;

                </th>
            </tr>

            <tr>
                <td colspan = "2"><?php echo _FB_SYNC_USERS_DESC ?>
                </td>
            </tr>
        </table>

        <input type = "hidden" name = "task" value = ""/>

        <input type = "hidden" name = "option" value = "<?php echo $option; ?>"/>
    </form>

<?php
    }

    //***************************************
    // uploaded Image browser
    //***************************************
    function browseUploaded($option, $uploaded, $uploaded_path, $type)
    {
        global $database, $mainframe;
        $map = $mainframe->getCfg('absolute_path');
?>

        <SCRIPT LANGUAGE = "Javascript">
            <!---
            function decision(message, url)
            {
                if (confirm(message))
                    location.href = url;
            }
                    // --->
        </SCRIPT>

            <?php
            echo ' <div class="fbfunctitle">';
            echo $type ? _COM_A_IMGB_IMG_BROWSE : _COM_A_IMGB_FILE_BROWSE;
            echo '</div>';
            echo '<table class="adminform"><tr><td>';
            echo $type ? _COM_A_IMGB_TOTAL_IMG : _COM_A_IMGB_TOTAL_FILES;
            echo ': ' . count($uploaded) . '</td></tr>';
            echo '<tr><td>';
            echo $type ? _COM_A_IMGB_ENLARGE : _COM_A_IMGB_DOWNLOAD;
            echo '</td></tr><tr><td>';
            echo $type ? _COM_A_IMGB_DUMMY_DESC . '</td></tr><tr><td>' . _COM_A_IMGB_DUMMY . ':</td></tr><tr><td> <img src="'.FB_LIVEUPLOADEDPATH.'/dummy.gif">' : '';
            echo '</td></tr></table>';
            echo '<table class="adminform"><tr>';

            for ($i = 0; $i < count($uploaded); $i++)
            {
                $j = $i + 1;
                //get the corresponding posting
                $query = "SELECT mesid FROM #__fb_attachments where filelocation='".FB_ABSUPLOADEDPATH."/".($type?'images':'files')."/$uploaded[$i]'";
                $database->setQuery($query);
                $mesid = $database->loadResult();
                //get the catid for the posting
                $database->setQuery("SELECT catid FROM #__fb_messages where id='$mesid'");
                $catid = $database->loadResult();
                echo $mesid == '' ? '<td>' : '<td>';
                echo '<table style="border: 1px solid #ccc;"><tr><td height="90" width="130" style="text-align: center">';
                echo $type ? '<a href="' . FB_LIVEUPLOADEDPATH . '/images/' . $uploaded[$i] . '" target="_blank" title="' . _COM_A_IMGB_ENLARGE . '" alt="' . _COM_A_IMGB_ENLARGE . '"><img src="' . FB_LIVEUPLOADEDPATH . '/images/' . $uploaded[$i]
                         . '" width="80" heigth="80" border="0"></a>' : '<a href="'
                         . FB_LIVEUPLOADEDPATH . '/files/' . $uploaded[$i] . '" title="' . _COM_A_IMGB_DOWNLOAD . '" alt="' . _COM_A_IMGB_DOWNLOAD . '"><img src="../administrator/components/com_fireboard/images/fbfile.png"   border="0"></a>';
                echo '</td></tr><tr><td style="text-align: center">';
                //echo '<input type="radio" name="newAvatar" value="gallery/'.$uploaded[$i].'">';
                echo '<br /><small>';
                echo '<strong>' . _COM_A_IMGB_NAME . ': </strong> ' . $uploaded[$i] . '<br />';
                echo '<strong>' . _COM_A_IMGB_SIZE . ': </strong> ' . filesize($uploaded_path . '/' . $uploaded[$i]) . ' bytes<br />';
                $type ? list($width, $height) = @getimagesize($uploaded_path . '/' . $uploaded[$i]) : '';
                echo $type ? '<strong>' . _COM_A_IMGB_DIMS . ': </strong> ' . $width . 'x' . $height . '<br />' : '';
                echo $type ? '<a href="index2.php?option=' . $option . '&task=replaceImage&OxP=1&img=' . $uploaded[$i] . '">' . _COM_A_IMGB_REPLACE . '</a><br />' : '';
                echo $type ? '<a href="javascript:decision(\'' . _COM_A_IMGB_CONFIRM . '\',\'index2.php?option=' . $option . '&task=replaceImage&OxP=2&img=' . $uploaded[$i] . '\')">'
                         . _COM_A_IMGB_REMOVE . '</a><br />' : '<a href="javascript:decision(\'' . _COM_A_IMGB_CONFIRM . '\',\'index2.php?option=' . $option . '&task=deleteFile&fileName=' . $uploaded[$i] . '\')">' . _COM_A_IMGB_REMOVE . '</a><br />';

                if ($mesid != '') {
                    echo '<a href="../index.php?option=' . $option . '&func=view&catid=' . $catid . '&id=' . $mesid . '#' . $mesid . '" target="_blank">' . _COM_A_IMGB_VIEW . '</a>';
                }
                else {
                    echo _COM_A_IMGB_NO_POST;
                }

                echo '</td></tr></table>';
                echo '</td>';

                if (function_exists('fmod'))
                {
                    if (!fmod(($j), 5)) {
                        echo '</tr><tr align="center" valign="middle">';
                    }
                }
                else
                {
                    if (!FB_fmodReplace(($j), 5)) {
                        echo '</tr><tr align="center" valign="middle">';
                    }
                }
            }

            echo '</tr></table>';
            ?>

<?php
                }

	//***************************************
    // show smilies
    //***************************************

function showsmilies($option, $mosConfig_lang, &$smileytmp, $pageNavSP, $smileypath)
        {
?>
<div class="fbfunctitle"><?php echo _FB_EMOTICONS; ?></div>
        <form action = "index2.php" method = "POST" name = "adminForm">
            <table class = "adminheading" cellpadding = "4" cellspacing = "0" border = "0" width = "100%">
                <tr>


                    <td nowrap align = "right"><?php echo _COM_A_DISPLAY; ?>

                    </td>

                    <td nowrap align = "right">
					<?php echo $pageNavSP->writeLimitBox(); ?>
                    </td>
                </tr>


            </table>

            <table class = "adminlist" border = "0" cellspacing = "0" cellpadding = "3" width = "100%">
                <tr>
                    <th algin = "left" width = "20">
                        <input type = "checkbox" name = "toggle" value = "" onclick = "checkAll(<?php echo count( $smileytmp ); ?>);"/>
                    </th>

                    <th algin = "right" width = "10"><?php echo _ANN_ID; ?>
                    </th>

                    <th algin = "center" width = "200"><?php echo _FB_EMOTICONS_SMILEY; ?>
                    </th>

                    <th algin = "center" width = "100"><?php echo _FB_EMOTICONS_CODE; ?>
                    </th>

                    <th algin = "right" width = "200"><?php echo _FB_EMOTICONS_URL; ?>
                    </th>
                    <th width = "*">&nbsp;
                    </th>
                </tr>
                <?php

                	$k = 0;
                    $i = 0;

                    for ($i = 0, $n = count($smileytmp); $i < $n; $i++)
                    {
                    	$k = 1 - $k;
                        $s = &$smileytmp[$i];
                	?>
                    <tr class = "row<?php echo $k;?>">
                                <td width = "20">
                                    <input type = "checkbox" id = "cb<?php echo $i;?>" name = "cid[]" value = "<?php echo $s->id; ?>" onClick = "isChecked(this.checked);">
                                </td>
                                <td width = "10">
                                    <a href = "#edit" onclick = "return listItemTask('cb<?php echo $i; ?>','editsmiley')"><?php echo $s->id; ?></a>
                                </td>

                                <td width = "200">
                                    <a href = "#edit" onclick = "return listItemTask('cb<?php echo $i; ?>','editsmiley')"><img src="<?php echo ($smileypath['live'] . '/'. $s->location); ?>" alt="<?php echo $s->location; ?>"  border="0" /></a>
                                </td>

                                <td width = "100">
								<?php echo $s->code; ?>&nbsp;
                                </td>

                                <td width = "200">
									<?php echo $s->location; ?>&nbsp;
                                </td>
                                <td>&nbsp;
                                </td>

                            </tr>


                     <?php
                    }
                ?>
            <tr>
        		<th align = "center" colspan = "6"> <?php
        		// TODO: fxstein - Need to perform SEO cleanup
        		echo $pageNavSP->writePagesLinks(); ?>
		        </th>
		    </tr>
            <tr>
        		<td align = "center" colspan = "6"> <?php echo $pageNavSP->writePagesCounter(); ?>
		        </td>
            </tr>
      	</table>
                <input type = "hidden" name = "option" value = "<?php echo $option; ?>"><input type = "hidden" name = "task" value = "showsmilies"><input type = "hidden" name = "boxchecked" value = "0">
                <?php if (FBTools::isJoomla15()) echo '<input type = "hidden" name = "limitstart" value = "0">'; ?>
        </form>
<?php
        }//end function showsmilies

		function editsmiley($option, $mosConfig_lang, $smiley_edit_img, $filename_list, $smileypath, $smileycfg)
		{
        ?>
        <script language="javascript" type="text/javascript">
		<!--
		function update_smiley(newimage)
		{
			document.smiley_image.src = "<?php echo $smileypath; ?>" + newimage;
		}
		//-->
		</script>
        <div class="fbfunctitle"><?php echo _FB_EMOTICONS_EDIT_SMILEY; ?></div>
        <form action = "index2.php" method = "POST" name = "adminForm">
            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "adminform">

				<tr align="center">
					<td width="100"><?php echo _FB_EMOTICONS_CODE; ?></td>
					<td width="200"><input class="post" type="text" name="smiley_code" value="<?php echo $smileycfg['code'];?>" /></td>
                    <td rowspan="3" width="50"><img name="smiley_image" src="<?php echo $smiley_edit_img; ?>" border="0" alt="" /> &nbsp;</td>
                    <td rowspan="3">&nbsp;</td>
				</tr>
				<tr align="center">
					<td width="100"><?php echo _FB_EMOTICONS_URL; ?></td>
					<td><select name="smiley_url" onchange="update_smiley(this.options[selectedIndex].value);"><?php echo $filename_list; ?></select> &nbsp; </td>
				</tr>
                <tr>
					<td width="100"><?php echo _FB_EMOTICONS_EMOTICONBAR; ?></td>
                    <td><input type="checkbox" name="smiley_emoticonbar" value="1"<?php if($smileycfg['emoticonbar'] == 1) { echo 'checked="checked"';} ?> /></td>
				<tr>
				<!--<tr align="center">
					<td width="100">Smiley emotion</td>
					<td><input class="post" type="text" name="smiley_emotion" value="{SMILEY_EMOTICON}" /></td>
				</tr>-->
				<tr>
					<td colspan="2" align="center"><input type = "hidden" name = "option" value = "<?php echo $option; ?>"> <input type = "hidden" name = "task" value = "showsmilies"> <input type = "hidden" name = "boxchecked" value = "0"><input type = "hidden" name = "id" value = "<?php echo $smileycfg['id']; ?>">
					</td>
				</tr>
			</table>
        </form>

        <?php
		}//end function editmilies


		function newsmiley($option, $filename_list, $smileypath)
		{
        ?>
        <script language="javascript" type="text/javascript">
		<!--
		function update_smiley(newimage)
		{
			document.smiley_image.src = "<?php echo $smileypath; ?>" + newimage;
		}
		//-->
		</script>
         <div class="fbfunctitle"><?php echo _FB_EMOTICONS_NEW_SMILEY; ?></div>
        <form action = "index2.php" method = "POST" name = "adminForm">
            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "adminform">

				<tr align="center">
					<td width="100"><?php echo _FB_EMOTICONS_CODE; ?></td>
					<td width="200"><input class="post" type="text" name="smiley_code" value="" /></td>
                    <td rowspan="3" width="50"><img name="smiley_image" src="" border="0" alt="" /> &nbsp;</td>
                    <td rowspan="3">&nbsp;</td>
				</tr>
				<tr align="center">
					<td width="100"><?php echo _FB_EMOTICONS_URL; ?></td>
					<td><select name="smiley_url" onchange="update_smiley(this.options[selectedIndex].value);"><?php echo $filename_list; ?></select> &nbsp; </td>
				</tr>
                <tr>
					<td width="100"><?php echo _FB_EMOTICONS_EMOTICONBAR; ?></td>
                    <td><input type="checkbox" name="smiley_emoticonbar" value="1" /></td>
				<tr>
					<td colspan="2" align="center"><input type = "hidden" name = "option" value = "<?php echo $option; ?>"> <input type = "hidden" name = "task" value = "showsmilies"> <input type = "hidden" name = "boxchecked" value = "0">
					</td>
				</tr>
			</table>
        </form>

        <?php

		}//end function newsmilies

/// Dan Syme/IGD Rank Administration
 function showRanks( $option,$mosConfig_lang,&$ranks,$pageNavSP,$order,$rankpath )
		 {
   global $database, $mainframe;
   ?>
  <div class="fbfunctitle"><?php echo _FB_RANKS_MANAGE; ?></div>
  <form action="index2.php" method="POST" name="adminForm">
  <table class="adminheading" cellpadding="4" cellspacing="0" border="0" width="100%">
    <tr>
      <td nowrap="nowrap" align="right"><?php echo _COM_A_DISPLAY;?></td>
      <td nowrap="nowrap"  align="right"><?php echo $pageNavSP->writeLimitBox(); ?></td>
    </tr>
  </table>
  <table class="adminlist" border=0 cellspacing=0 cellpadding=3 width="100%" >
    <tr>
      <th width="20" align="center">#</th>
      <th align="left"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($ranks); ?>);"/></th>
      <th align="left" ><?php echo _FB_RANKSIMAGE;?></th>
      <th align="left" nowrap="nowrap"><?php echo _FB_RANKS;?></th>
      <th align="left" nowrap="nowrap"><?php echo _FB_RANKS_SPECIAL;?></th>
      <th algin="center" nowrap="nowrap"><?php echo _FB_RANKSMIN;?></th>
      <th width="100%">&nbsp;</th>
    </tr>
    <?php
					$k=0;
					$i = 0; foreach ($ranks as $id=>$row) {
					$k = 1-$k;
				?>
    <tr class="row<?php echo $k;?>">
      <td width="20" align="center"><?php echo ($id+$pageNavSP->limitstart+1);?></td>
      <td width="20" align="center"><input type="checkbox" id="cb<?php echo $id;?>" name="cid[]" value="<?php echo $row->rank_id; ?>" onClick="isChecked(this.checked);"></td>
      <td><a href = "#edit" onclick = "return listItemTask('cb<?php echo $id; ?>','editRank')"><img src="<?php echo ($rankpath['live'] . '/'. $row->rank_image); ?>" alt="<?php echo $row->rank_image; ?>"  border="0" /></a></td>
      <td nowrap="nowrap"><a href = "#edit" onclick = "return listItemTask('cb<?php echo $id; ?>','editRank')"><?php echo $row->rank_title; ?></a></td>
      <td><?php if ($row->rank_special == 1 ) { echo _ANN_YES; } else { echo _ANN_NO; } ?></td>
      <td align="center"><?php echo $row->rank_min; ?></td>
      <td width="100%">&nbsp;</td>
    </tr>
    <?php }  ?>

    <input type="hidden" name="option" value="<?php echo $option; ?>">
   	<input type="hidden" name="boxchecked" value="0">
   	<input type="hidden" name="task" value="ranks">
   	<?php if (FBTools::isJoomla15()) echo '<input type = "hidden" name = "limitstart" value = "0">'; ?>

    <tr>
      <th align="center" colspan="7"><?php
      // TODO: fxstein - Need to perform SEO cleanup
      echo $pageNavSP->writePagesLinks(); ?></th>
    </tr>
    <tr>
      <td align="center" colspan="7"><?php echo $pageNavSP->writePagesCounter(); ?></td>
	</tr>
  </table>
  </form>

<?php } //end function showRanks

		function newRank($option, $filename_list, $rankpath)
		{
  ?>
  <script language="javascript" type="text/javascript">
		<!--
		function update_rank(newimage)
		{
			document.rank_image.src = "<?php echo $rankpath; ?>" + newimage;
		}
		//-->
		</script>
        <div class="fbfunctitle"><?php echo _FB_NEW_RANK; ?></div>
  <form action="index2.php" method="POST" name="adminForm">
  <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">

			<tr align="center">
				<td width="100"><?php echo _FB_RANKS; ?></td>
				<td width="200"><input class="post" type="text" name="rank_title" value="" /></td>
			</tr>
   <tr>
				<td width="100"><?php echo _FB_RANKSIMAGE; ?></td>
    <td><select name="rank_image" onchange="update_rank(this.options[selectedIndex].value);"><?php echo $filename_list; ?></select> &nbsp; <img name="rank_image" src="" border="0" alt="" /></td>
			<tr>
   <tr>
				<td width="100"><?php echo _FB_RANKSMIN; ?></td>
    <td><input class="post" type="text" name="rank_min" value="1" /></td>
			<tr>
			<tr>
				<td width="100"><?php echo _FB_RANKS_SPECIAL; ?></td>
				<td><input type="checkbox" name="rank_special" value="1" /></td>
			</tr>
					<td colspan="2" align="center"><input type = "hidden" name = "option" value = "<?php echo $option; ?>"> <input type = "hidden" name = "task" value = "showRanks"> <input type = "hidden" name = "boxchecked" value = "0">
					</td>
			</tr>
		</table>
  </form>

<?php }//end function edit rank

		function editrank($option, $mosConfig_lang, $edit_img, $filename_list, $path, $row)
		{
?>
  <script language="javascript" type="text/javascript">
		<!--
		function update_rank(newimage)
		{
			document.rank_image.src = "<?php echo $path; ?>" + newimage;
		}
		//-->
		</script>
          <div class="fbfunctitle"><?php echo _FB_RANKS_EDIT; ?></div>
  <form action = "index2.php" method = "POST" name = "adminForm">
  <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">

			<tr align="center">
				<td width="100"><?php echo _FB_RANKS; ?></td>
				<td width="200"><input class="post" type="text" name="rank_title" value="<?php echo $row->rank_title;?>" /></td>
			</tr>
			<tr align="center">
				<td width="100"><?php echo _FB_RANKSIMAGE; ?></td>
				<td><select name="rank_image" onchange="update_rank(this.options[selectedIndex].value);"><?php echo $filename_list; ?></select> &nbsp; <img name="rank_image" src="<?php echo $edit_img; ?>" border="0" alt="" /></td>
			</tr>
   <tr>
				<td width="100"><?php echo _FB_RANKSMIN; ?></td>
    <td><input class="post" type="text" name="rank_min" value="<?php echo $row->rank_min;?>" /></td>
			<tr>
			<tr>
				<td width="100"><?php echo _FB_RANKS_SPECIAL; ?></td>
				<td><input type="checkbox" name="rank_special" value="1"<?php if($row->rank_special == 1) { echo 'checked="checked"';} ?> /></td>
			</tr>
			</tr>
					<td colspan="2" align="center"><input type = "hidden" name = "option" value = "<?php echo $option; ?>"> <input type = "hidden" name = "task" value = "showRanks"> <input type = "hidden" name = "boxchecked" value = "0"><input type = "hidden" name = "id" value = "<?php echo $row->rank_id; ?>">
			</tr>
		</table>
  </form>

        <?php
		}//end function newrank
} //end class
?>
