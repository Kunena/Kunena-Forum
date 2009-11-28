<?php
/**
* @version $Id: legacy.admin.kunena.html.php 1115 2009-10-16 19:23:19Z mahagr $
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

class html_Kunena
{
	
	
// Begin: HEADER FUNC
function showKunenaHeader () {

?>
<style>
#kunenaadmin {
text-align:left;
}
#kunenaheader {
clear:both;
}
#kunenamenu {
margin-top:15px;
border-top:1px solid #ccc;
}
#kunenamenu a{
display:block;
font-size:11px;
border-left:1px solid #ccc;
border-bottom:1px solid #ccc;

}
.kunenamainmenu {
background:#FBFBFB;
padding:5px;
}
.kunenaactivemenu {
background:#fff;
padding:5px;
}
.kunenasubmenu {
background:#fff;
padding-left:10px;
padding:5px 5px 5px 15px;
}
.kunenaright {
border:1px solid #ccc;
background:#fff;
padding:5px;
}

.kunenafooter {
font-size:10px;
text-align: right;
padding:5px;
background:#FBFBFB;
border-bottom:1px solid #CCC;
border-left:1px solid #CCC;
border-right:1px solid #CCC;
}
.kunenafunctitle {
font-size:16px;
text-align: left;
padding:5px;
background:#FBFBFB;
border:1px solid #CCC;
font-weight:bold;
margin-bottom:10px;
clear:both;
}
.kunenafuncsubtitle {
font-size:14px;
text-align: left;
padding:5px;
border-bottom:3px solid  #7F9DB9;
font-weight:bold;
color:#7F9DB9;
margin:10px 0 10px 0;
}
.kunenarow0 td {
padding:8px 5px;
text-align:left;
border-bottom:1px  dotted #ccc;
}
.kunenarow1 td {
padding:8px 5px;
text-align:left;
border-bottom:1px dotted #ccc;
}
td.kunenatdtitle {
font-weight:bold;
padding-left:10px;
color:#666;
}
#kunenacongifcover fieldset {
border: 1px solid #CFDCEB;
}
#kunenacongifcover fieldset legend{
color:#666;
}

</style>




<?php
} // Finish: HEADER FUNC

// Begin: FOOTER FUNC
function showFbFooter () {
$kunenaConfig =& CKunenaConfig::getInstance();

require_once (KUNENA_PATH_LIB .DS. 'kunena.version.php');
?>

<!-- Finish: AdminRight -->

    </td>
  </tr>
  <tr><td></td><td>
 <!-- Footer -->
<div class="kunenafooter"><?php echo CKunenaVersion::versionHTML(); ?></div>
<!-- /Footer -->
  </td></tr>
</table>

</div><!-- Close div.kunenaadmin -->
<?php } // Finish: FOOTER FUNC


   

    function showAdministration($rows, $children, $pageNav, $option)
    {
        ?>
        <form action = "index.php?option=com_kunena&task=editadministration" method = "post" name = "adminForm">
            <table  cellpadding = "4" cellspacing = "0" border = "0" width = "100%">
                <tr>
                    <td  align="right">
                    </td>
                </tr>
            </table>

            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "adminlist">
              <tr>
                            
                        </tr>
                <tr>
                    <th width = "20">
                        #
                    </th>

                    <th width = "20">
                        <input type = "checkbox" name = "toggle" value = "" onclick = "checkAll(<?php echo count( $rows ); ?>);"/>
                    </th>

                    <th class = "title">
                      <?php echo _KUNENA_CATFOR; ?>
                    </th>

                    <th>
                      <small><?php echo _KUNENA_CATID; ?></small>
                    </th>

                    <th>
                      <small><?php echo _KUNENA_LOCKED; ?></small>
                    </th>

                    <th>
                      <small><?php echo _KUNENA_MODERATED; ?></small>
                    </th>

                    <th>
                      <small><?php echo _KUNENA_REVIEW; ?></small>
                    </th>

                    <th>
                      <small><?php echo _KUNENA_PUBLISHED; ?></small>
                    </th>

                    <th>
                      <small><?php echo _KUNENA_PUBLICACCESS; ?></small>
                    </th>

                    <th>
                      <small><?php echo _KUNENA_ADMINACCESS; ?></small>
                    </th>

                    <th>
                      <small><?php echo _KUNENA_CHECKEDOUT; ?></small>
                    </th>

                    <th colspan = "2">
                      <small><?php echo _KUNENA_REORDER; ?></small>
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
                            echo $row->id;
                        ?>
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
                            $groupname = _KUNENA_EVERYBODY;
                        }
                        else if ($row->pub_access == -1) {
                            $groupname = _KUNENA_ALLREGISTERED;
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

                        <td class="order" nowrap="nowrap">
							<span><?php echo $pageNav->orderUpIcon( $i, isset($children[$row->parent][$row->location-1]), 'orderup', 'Move Up', 1); ?></span>
							<span><?php echo $pageNav->orderDownIcon( $i, $n, isset($children[$row->parent][$row->location+1]), 'orderdown', 'Move Down', 1); ?></span>
                        </td>

                <?php
                            $k = 1 - $k;
                }
                ?>
                        </tr>

                        <tr>
                            <th align = "center" colspan = "13"> <?php
                            echo $pageNav->getLimitBox(); ?>&nbsp;
&nbsp;&nbsp; &nbsp;
                            <?php
                            echo $pageNav->getResultsCounter(); ?>&nbsp;
&nbsp;&nbsp; &nbsp;         <?php
                            echo $pageNav->getPagesLinks(); ?>
                            
                            </th>
                        </tr>

                       
            </table>

            <input type = "hidden" name = "option" value = "<?php echo $option; ?>"> <input type = "hidden" name = "task" value = "showAdministration"> <input type = "hidden" name = "boxchecked" value = "0">
            <?php echo '<input type = "hidden" name = "limitstart" value = "0">'; ?>
        </form>

<?php
    }

    function editForum(&$row, $categoryList, $moderatorList, $lists, $accessLists, $option)
    {
		?><form action = "index.php?option=com_kunena&task=editadministration" method = "POST" name = "adminForm">
        <?php
       if(!class_exists('JPane')) {
   jimport('joomla.html.pane');
   $pane =& JPane::getInstance('sliders');
}


$pane =& JPane::getInstance('Tabs');
echo $pane->startPane('EditForum');
{
	
echo $pane->startPanel('Basics', 'Basics');?>

           <fieldset>
           <legend> <?php echo _KUNENA_BASICS; ?></legend>
           <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" >


                    <tr>
                        <td width = "200" valign = "top"><?php echo _KUNENA_PARENT; ?>
                        </td>

                        <td>
<?php echo $categoryList; ?><br/>

                <br/><?php echo _KUNENA_PARENTDESC; ?>
                        </td>
                    </tr>
						<tr>
                        <td width = "200"><?php echo _KUNENA_NAMEADD; ?>
                        </td>

                        <td>
                            <input class = "inputbox" type = "text" name = "name" size = "25" maxlength = "100" value = "<?php echo stripslashes($row->name); ?>">
                        </td>
                    </tr>
                      <tr>
                        <td valign = "top"><?php echo _KUNENA_DESCRIPTIONADD; ?>
                        </td>

                        <td>
                            <textarea class = "inputbox" cols = "50" rows = "3" name = "description" id = "description" style = "width:500px" width = "500"><?php echo stripslashes($row->description); ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <td valign = "top"><?php echo _KUNENA_HEADERADD; ?>
                        </td>

                        <td>
                            <textarea class = "inputbox" cols = "50" rows = "3" name = "headerdesc" id = "headerdesc" style = "width:500px" width = "500"><?php echo stripslashes($row->headerdesc); ?></textarea>
                        </td>
                    </tr>
            </table>
                    </fieldset>
                    <?php
echo $pane->endPanel();
echo $pane->startPanel('Advanced', 'Advanced');?>
<fieldset>
           <legend> <?php echo _KUNENA_ADVANCEDDESCINFO; ?></legend>

            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%">

                <tr>
                    <td><?php echo _KUNENA_LOCKED1; ?>
                    </td>

                    <td> <?php echo $lists['forumLocked']; ?>
                    </td>

                    <td>
<?php echo _KUNENA_LOCKEDDESC; ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap = "nowrap" valign = "top"><?php echo _KUNENA_PUBACC; ?>
                    </td>

                    <td valign = "top"> <?php echo $accessLists['pub_access']; ?>
                    </td>

                    <td><?php echo _KUNENA_PUBACCDESC; ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap = "nowrap" valign = "top"><?php echo _KUNENA_CGROUPS; ?>
                    </td>

                    <td valign = "top"> <?php echo $lists['pub_recurse']; ?>
                    </td>

                    <td valign = "top"><?php echo _KUNENA_CGROUPSDESC; ?>
                    </td>
                </tr>

                <tr>
                    <td valign = "top"><?php echo _KUNENA_ADMINLEVEL; ?>
                    </td>

                    <td valign = "top"> <?php echo $accessLists['admin_access']; ?>
                    </td>

                    <td valign = "top"><?php echo _KUNENA_ADMINLEVELDESC; ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap = "nowrap" valign = "top"><?php echo _KUNENA_CGROUPS1; ?>
                    </td>

                    <td valign = "top"> <?php echo $lists['admin_recurse']; ?>
                    </td>

                    <td valign = "top"><?php echo _KUNENA_CGROUPS1DESC; ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap = "nowrap" valign = "top"><?php echo _KUNENA_REV; ?>
                    </td>

                    <td valign = "top"> <?php echo $lists['forumReview']; ?>
                    </td>

                    <td valign = "top"><?php echo _KUNENA_REVDESC; ?>
                    </td>
                </tr>
            </table>

           </fieldset>
<?php
echo $pane->endPanel();
echo $pane->startPanel('CSS', 'CSS');?>

<fieldset>
<legend> <?php echo _KUNENA_CSS; ?></legend>
<table cellpadding = "4" cellspacing = "0" border = "0" width = "100%">

                <tr>
                    <td><?php echo _KUNENA_CLASS_SFX; ?>
                    </td>

                    <td>
                        <input class = "inputbox" type = "text" name = "class_sfx" size = "20" maxlength = "20" value = "<?php echo $row->class_sfx; ?>">
                    </td>

                    <td>
<?php echo _KUNENA_CLASS_SFXDESC; ?>
                    </td>
                </tr>
            </table>
</fieldset>
<?php
echo $pane->endPanel();
echo $pane->startPanel('Moderation', 'Moderation');?>

<fieldset>
<legend> <?php echo _KUNENA_MOD_NEW; ?></legend>
<table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" >


                <tr>
                    <td nowrap = "nowrap" valign = "top"><?php echo _KUNENA_MOD; ?>
                    </td>

                    <td valign = "top"> <?php echo $lists['forumModerated']; ?>
                    </td>

                    <td valign = "top"><?php echo _KUNENA_MODDESC; ?>
                    </td>
                </tr>
            </table>

            <?php
            if ($row->moderated)
            {
            ?>


<div class="kunenafuncsubtitle"><?php echo _KUNENA_MODSASSIGNED; ?></div>

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

                        <th align = "center"><?php echo _KUNENA_PUBLISHED; ?>
                        </th>
                    </tr>

                    <?php
                    if (count($moderatorList) == 0) {
                        echo "<tr><td colspan=\"5\">" . _KUNENA_NOMODS . "</td></tr>";
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
<?php
echo $pane->endPanel();
}
echo $pane->endPane();

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
                    alert("<?php echo _KUNENA_ERROR1; ?>");
                }
                else
                {
                    submitform(pressbutton);
                }
            }
        </script>



            <input type = "hidden" name = "id" value = "<?php echo $row->id; ?>"> <input type = "hidden" name = "option" value = "<?php echo $option; ?>"> <input type = "hidden" name = "task" value = "showAdministration">

            <?php
            if ($row->ordering != 0) {
                echo '<input type="hidden" name="ordering" value="' . $row->ordering . '">';
            }
            ?>
        </form>

        <?php
    }

   function showInstructions($kunena_db, $option, $lang) {
?>

    <table width = "100%" border = "0" cellpadding = "2" cellspacing = "2" class = "adminheading">
        <TR>
            <th class = "info">
                &nbsp;<?php echo _KUNENA_INSTRUCTIONS; ?>
            </th>
        </tr>
    </table>

    <table width = "100%" border = "0" cellpadding = "2" cellspacing = "2" class = "adminform">
        <tr>
            <th><?php echo _KUNENA_FINFO; ?>
            </th>
        </tr>

        <tr>
            <td>
<?php echo _KUNENA_INFORMATION; ?>
            </td>
        </tr>
    </table>

<?php
    } //end function showInstructions

    function showCss($file, $option)
    {
        $f = fopen($file, "r");
        $content = fread($f, filesize($file));
        $content = kunena_htmlspecialchars($content);
?>
    <form action = "index2.php?" method = "post" name = "adminForm" class = "adminForm" id = "adminForm">


        <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "adminform">
            <tr>
	                <th colspan = "4">
					<?php echo _KUNENA_PATH; ?> <?php echo $file; ?>
            </tr>

            <tr>
                <td>
                    <textarea cols = "100" rows = "20" name = "csscontent"><?php echo $content; ?></textarea>
                </td>
            </tr>

            <tr>
                <td class = "error"><?php echo _KUNENA_CSSERROR; ?>
                </td>
            </tr>
        </table>

        <input type = "hidden" name = "file" value = "<?php echo $file; ?>"/>

        <input type = "hidden" name = "option" value = "<?php echo $option; ?>"> <input type = "hidden" name = "task" value = ""> <input type = "hidden" name = "boxchecked" value = "0">
    </form>


<?php
    } //end function showCss

    function showProfiles($option, $lang, &$profileList, $countPL, $pageNavSP, $order, $search)
    {
?>
        <form action = "index.php?option=com_kunena&task=editprofiles" method = "POST" name = "adminForm">
            <table  cellpadding = "4" cellspacing = "0" border = "0" width = "100%">
               
<th align = "center" colspan = "12">
        :: <a href = "index2.php?option=com_kunena&task=profiles&order=0"><?php
    echo _KUNENA_SORTID; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :: <a href = "index2.php?option=com_kunena&task=profiles&order=1"><?php echo _KUNENA_SORTMOD; ?></a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:: <a href = "index2.php?option=com_kunena&task=profiles&order=2"><?php echo _KUNENA_SORTNAME; ?></a>
        </th>
                <tr>


                    <td colspan = "4" nowrap>
                       
                    </td>
                </tr>
            </table>

            <table class = "adminlist" border = 0 cellspacing = 0 cellpadding = 3 width = "100%">
              
                <tr>
                    <th algin = "left" width = "20">
                        <input type = "checkbox" name = "toggle" value = "" onclick = "checkAll(<?php echo count( $profileList ); ?>);"/>
                    </th>

                    <th algin = "left" width = "10"><?php echo 'Userid'; ?>
                    </th>

                    <th algin = "left" width = "10"><?php echo _USRL_NAME; ?>
                    </th>

                    <th algin = "left" width = "100"><?php echo _GEN_EMAIL; ?>
                    </th>

                    <th algin = "left" width = "15"><?php echo _VIEW_MODERATOR; ?>
                    </th>

                    <th algin = "left" width = "10"><?php echo _KUNENA_VIEW; ?>
                    </th>

                    <th algin = "left" width = "100%"><?php echo _GEN_SIGNATURE; ?>
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
<?php echo html_entity_decode_utf8(stripslashes($pl->signature)); ?>&nbsp;
                                </td>
                            </tr>

                <?php
                    }
                }
                else {
                    echo "<tr><td colspan=\"7\">" . _KUNENA_NOUSERSFOUND . "</td></tr>";
                }
                ?>

    <input type = "hidden"
        name = "order" value = "<?php echo "$order";?>"> <input type = "hidden" name = "option" value = "<?php echo $option; ?>"> <input type = "hidden" name = "task" value = "showprofiles"> <input type = "hidden" name = "boxchecked" value = "0">
    <?php echo '<input type = "hidden" name = "limitstart" value = "0">'; ?>

    <tr>
        <th align = "center" colspan = "8">
        <?php echo  $pageNavSP->getLimitBox().$pageNavSP->getPagesLinks(); ?>&nbsp;
&nbsp;&nbsp; &nbsp;
		<?php echo $pageNavSP->getResultsCounter(); ?>
        </th>
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
                    <th width = "100%" class = "user"><?php echo _KUNENA_ADDMOD; ?> <?php echo $forumName; ?>
                    </th>

                    <td nowrap><?php echo _COM_A_DISPLAY; ?>
                    </td>

                    <td>
<?php echo $pageNav->getListFooter(); ?>
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

                    <th><?php echo _KUNENA_PUBLISHED; ?>
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
                                    <a href = "index2.php?option=com_kunena&task=userprofile&do=show&user_id=<?php echo $pl->id;?>"><?php echo $pl->id; ?>&nbsp;
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
                    echo "<tr><td align='left' colspan='7'>" . _KUNENA_NOMODSAV . "</td></tr>";
                }
                ?>

    <input type = "hidden"
        name = "option" value = "<?php echo $option; ?>"> <input type = "hidden" name = "id" value = "<?php echo $id; ?>"> <input type = "hidden" name = "boxchecked" value = "0"> <input type = "hidden" name = "task" value = "newmoderator">
    <?php echo '<input type = "hidden" name = "limitstart" value = "0">'; ?>

    <tr>
        <th align = "center" colspan = "7"> <?php
        echo $pageNav->getListFooter(); ?>
        </th>
    </tr>

    

    <tr>
        <td colspan = "7"><?php echo _KUNENA_NOTEUS; ?>
        </td>
    </tr>
            </table>
        </form>

<?php
    }

    //   function showUserProfile ($kunena_db,$mosLang,$user_id,$do,$deleteSig,$signature,$newview,$user_id,$thread,$moderator)
    //   {
    //
    //      include ('components/com_kunena/moderate_user.php');
    //   }
    function editUserProfile($user, $subslist, $selectRank, $selectPref, $selectMod, $selectOrder, $uid , $modCats)
    {
        $kunenaConfig =& CKunenaConfig::getInstance();
		$kunena_db = &JFactory::getDBO();
        //fill the variables needed later
            $signature = $user->signature;
            $username = $user->name;
            $avatar = $user->avatar;
            $ordering = $user->ordering;
        //that's what we got now; later the 'future_use' columns can be used..

        $csubslist = count($subslist);
//        include_once ('components/com_kunena/bb_adm.js');
?>

        <form action = "index2.php?option=<?php echo $option;?>" method = "POST" name = "adminForm">
            <table border = 0 cellspacing = 0 width = "100%" align = "center" class = "adminheading">
                <tr>
                    <th colspan = "3" class = "user">
<?php echo _KUNENA_PROFFOR; ?> <?php echo $username; ?>
                    </th>
                </tr>
            </table>

            <table border = 0 cellspacing = 0 width = "100%" align = "center" class = "adminlist">
                <tr>
                    <th colspan = "3" class = "title">
<?php echo _KUNENA_GENPROF; ?>

                </tr>

                <tr>
                    <td width = "150" class = "contentpane"><?php echo _KUNENA_PREFOR; ?>
                    </td>

                    <td align = "left" valign = "top" class = "contentpane">
<?php echo $selectOrder; ?>
                    </td>

                    <td>&nbsp;

                    </td>
                </tr>

                         <tr>
                    <td width = "150" class = "contentpane"><?php echo _KUNENA_RANKS; ?>
                    </td>

                    <td align = "left" valign = "top" class = "contentpane">
<?php echo $selectRank; ?>
                    </td>

                    <td>&nbsp;

                    </td>
                </tr>



                            <td width = "150" valign = "top" class = "contentpane">
<?php echo _GEN_SIGNATURE; ?>:
<?php /*
// FIXME: bbcode broken

        <br/> <?php echo $kunenaConfig->maxsig; ?>

        <input readonly type = text name = rem size = 3 maxlength = 3 value = "" class = "inputbox"> <?php echo _CHARS; ?><br/>
<?php echo _HTML_YES; ?>
*/ ?>
                            </td>

                            <td align = "left" valign = "top" class = "contentpane">
                                <textarea rows = "6"
                                    class = "inputbox"
                                    onMouseOver = "textCounter(this.form.message,this.form.rem,<?php echo $kunenaConfig->maxsig;?>);"
                                    onClick = "textCounter(this.form.message,this.form.rem,<?php echo $kunenaConfig->maxsig;?>);"
                                    onKeyDown = "textCounter(this.form.message,this.form.rem,<?php echo $kunenaConfig->maxsig;?>);"
                                    onKeyUp = "textCounter(this.form.message,this.form.rem,<?php echo $kunenaConfig->maxsig;?>);" cols = "50" type = "text" name = "message"><?php echo html_entity_decode_utf8(stripslashes($signature)); ?></textarea>

<?php /*
// FIXME: bbcode broken
                                <br/>

                                <input type = "button" class = "button" accesskey = "b" name = "addbbcode0" value = " B " style = "font-weight:bold; width: 30px" onClick = "bbstyle(0)" onMouseOver = "helpline('b')"/>

                                <input type = "button" class = "button" accesskey = "i" name = "addbbcode2" value = " i " style = "font-style:italic; width: 30px" onClick = "bbstyle(2)" onMouseOver = "helpline('i')"/>

                                <input type = "button" class = "button" accesskey = "u" name = "addbbcode4" value = " u " style = "text-decoration: underline; width: 30px" onClick = "bbstyle(4)" onMouseOver = "helpline('u')"/>

                                <input type = "button" class = "button" accesskey = "p" name = "addbbcode14" value = "Img" style = "width: 40px" onClick = "bbstyle(14)" onMouseOver = "helpline('p')"/>

                                <input type = "button" class = "button" accesskey = "w" name = "addbbcode16" value = "URL" style = "text-decoration: underline; width: 40px" onClick = "bbstyle(16)" onMouseOver = "helpline('w')"/>

                                <br/><?php echo _KUNENA_COLOR; ?>:

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
*/ ?>
                            </td>

                            <?php
                            if ($kunenaConfig->allowavatar)
                            {
                            ?>

                                <td class = "contentpane" align = "center">
<?php echo _KUNENA_UAVATAR; ?><br/>

<?php
if ($avatar != '')
{
   echo '<img src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/' . $avatar . '" ><br />';
   echo '<input type="hidden" value="' . $avatar . '" name="avatar">';
}
else
{
   echo "<em>" . _KUNENA_NS . "</em><br />";
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
                                <input type = "checkbox" value = "1" name = "deleteSig"><i><?php echo _KUNENA_DELSIG; ?></i>
                            </td>

                            <?php
                            if ($kunenaConfig->allowavatar)
                            {
                            ?>

                                <td class = "contentpane">
                                    <input type = "checkbox" value = "1" name = "deleteAvatar"><i><?php echo _KUNENA_DELAV; ?></i>
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
<?php echo _KUNENA_MOD_NEW; ?>

            </td>
            </tr>
                        </tr>

                        <tr>


    <td width = "150" class = "contentpane">
    <?php echo _KUNENA_ISMOD; ?>

                    <?php
                    //admins are always moderators
                    if (CKunenaTools::isModOrAdmin($uid))
                    {
                    echo _KUNENA_ISADM; ?> <input type = "hidden" name = "moderator" value = "1">
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

            <input type = "hidden" name = "option" value = "com_kunena"/>
        </form>

        <table border = 0 cellspacing = 0 width = "100%" align = "center" class = "adminform">
            <tr>
                <th colspan = "2" class = "title">
<?php echo _KUNENA_SUBFOR; ?> <?php echo $username; ?>

            </td>
            </tr>

            <?php
            $enum = 1; //reset value
            $k = 0;    //value for alternating rows

            if ($csubslist > 0)
            {
                foreach ($subslist as $subs)
                { //get all message details for each subscription
                    $kunena_db->setQuery("select * from #__kunena_messages where id=$subs->thread");
                    $subdet = $kunena_db->loadObjectList();
                        check_dberror("Unable to load subscription messages.");

                    foreach ($subdet as $sub)
                    {
                        $k = 1 - $k;
                        echo "<tr class=\"row$k\">";
                        echo "  <td>$enum: ".html_entity_decode_utf8(stripslashes($sub->subject))." by ".html_entity_decode_utf8(stripslashes($sub->name));
                        echo "  <td>&nbsp;</td>";
                        echo "</tr>";
                        $enum++;
                    }
                }
            }
            else {
                echo "<tr><td class=\"message\">" . _KUNENA_NOSUBS . "</td></tr>";
            }

            echo "</table>";
    }

    //**************************
    // Prune Forum
    //**************************
    function pruneforum($option, $forumList) {
            ?>
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
    <form action = "index2.php" method = "post" name = "adminForm">


        <table cellpadding = "4" class = "adminform" cellspacing = "0" border = "0" width = "100%">
            <tr>
                <th width = "100%" colspan = "2">&nbsp;

                </th>
            </tr>

            <tr>
                <td colspan = "2"><?php echo _KUNENA_SYNC_USERS_DESC ?>
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
        $kunena_db = &JFactory::getDBO();
        $map = JPATH_ROOT;
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
            echo $type ? _COM_A_IMGB_IMG_BROWSE : _COM_A_IMGB_FILE_BROWSE;
            echo '</div>';
            echo '<table class="adminform"><tr><td>';
            echo $type ? _COM_A_IMGB_TOTAL_IMG : _COM_A_IMGB_TOTAL_FILES;
            echo ': ' . count($uploaded) . '</td></tr>';
            echo '<tr><td>';
            echo $type ? _COM_A_IMGB_ENLARGE : _COM_A_IMGB_DOWNLOAD;
            echo '</td></tr><tr><td>';
            echo $type ? _COM_A_IMGB_DUMMY_DESC . '</td></tr><tr><td>' . _COM_A_IMGB_DUMMY . ':</td></tr><tr><td> <img src="'.KUNENA_LIVEUPLOADEDPATH.'/dummy.gif">' : '';
            echo '</td></tr></table>';
            echo '<table class="adminform"><tr>';

            for ($i = 0; $i < count($uploaded); $i++)
            {
                $j = $i + 1;
                //get the corresponding posting
                $query = "SELECT mesid FROM #__kunena_attachments where filelocation='".KUNENA_PATH_UPLOADED."/".($type?'images':'files')."/$uploaded[$i]'";
                $kunena_db->setQuery($query);
                $mesid = $kunena_db->loadResult();
                //get the catid for the posting
                $kunena_db->setQuery("SELECT catid FROM #__kunena_messages where id='$mesid'");
                $catid = $kunena_db->loadResult();
                echo $mesid == '' ? '<td>' : '<td>';
                echo '<table style="border: 1px solid #ccc;"><tr><td height="90" width="130" style="text-align: center">';
                echo $type ? '<a href="' . KUNENA_LIVEUPLOADEDPATH . '/images/' . $uploaded[$i] . '" target="_blank" title="' . _COM_A_IMGB_ENLARGE . '" alt="' . _COM_A_IMGB_ENLARGE . '"><img src="' . KUNENA_LIVEUPLOADEDPATH . '/images/' . $uploaded[$i]
                         . '" width="80" heigth="80" border="0"></a>' : '<a href="'
                         . KUNENA_LIVEUPLOADEDPATH . '/files/' . $uploaded[$i] . '" title="' . _COM_A_IMGB_DOWNLOAD . '" alt="' . _COM_A_IMGB_DOWNLOAD . '"><img src="../administrator/components/com_kunena/images/kunenafile.png"   border="0"></a>';
                echo '</td></tr><tr><td style="text-align: center">';
                //echo '<input type="radio" name="newAvatar" value="gallery/'.$uploaded[$i].'">';
                echo '<br /><small>';
                echo '<strong>' . _COM_A_IMGB_NAME . ': </strong> ' . $uploaded[$i] . '<br />';
                echo '<strong>' . _COM_A_IMGB_SIZE . ': </strong> ' . filesize($uploaded_path .DS . $uploaded[$i]) . ' bytes<br />';
                $type ? list($width, $height) = @getimagesize($uploaded_path .DS . $uploaded[$i]) : '';
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
                    if (!KUNENA_fmodReplace(($j), 5)) {
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
/**
* Get a pagination object
*
* @access public
* @return JPagination
*/
function getPagination()
{
if (empty($this->_pagination))
{
// import the pagination library
jimport('joomla.html.pagination');
// prepare the pagination values
$total = $this->getTotal();
$limitstart = $this->getState('limitstart');
$limit = $this->getState('limit');
// create the pagination object
$this->_pagination = new JPagination($total, $limitstart,
$limit);
}
return $this->_pagination;
}
/**
* Get number of items
*
* @access public
* @return integer
*/
function getTotal()
{
	if (empty($this->_total))
{
$query = $this->_buildQuery();
$this->_total = $this->_getListCount($query);
}
return $this->_total;
}

/**
         * Return the pagination footer
         *
         * @access      public
         * @return      string  Pagination footer
         * @since       1.0
         */
        function getListFooter()
{if (empty($this->_total))
{
$query = $this->_buildQuery();
$this->_total = $this->_getListCount($query);
}
return $this->_total;
}
/**
* Constructor
*
*/
function __construct()
{
global $mainframe;
parent::__construct();
// Get the pagination request variables
$limit = $mainframe->getUserStateFromRequest('global.list.limit',
'limit', $mainframe->getCfg('list_limit'));
$limitstart = $mainframe->getUserStateFromRequest
($option.'limitstart',
'limitstart', 0);
// set the state pagination variables
$this->setState('limit', $limit);
$this->setState('limitstart', $limitstart);
}
/**
* Get itemized data
*
* @access public
* @return array
*/
function getData()
{
if (empty($this->_data))
{
$query = $this->_buildQuery();
$limitstart = $this->getState('limitstart');
$limit = $this->getState('limit');
$this->_data = $this->_getList($query, $limitstart, $limit);
}
return $this->_data;
}
function showsmilies($option, $lang, &$smileytmp, $pageNavSP, $smileypath)
        {
?>
        <form action = "index.php?option=com_kunena&task=EditSmilie" method = "POST" name = "adminForm">
            

            <table class = "adminlist"  align="left" border = "0" cellspacing = "0" cellpadding = "3" width = "100%">
                <tr>
                    <th algin = "left"  width = "1%">
                        <input type = "checkbox" name = "toggle" value = "" onclick = "checkAll(<?php echo count( $smileytmp ); ?>);"/>
                    </th>

                    <th algin = "left" width = "10"><?php echo _ANN_ID; ?>
                    </th>

                    <th algin = "left" width = "200"><?php echo _KUNENA_EMOTICONS_SMILEY; ?>
                    </th>

                    <th algin = "left" width = "100"><?php echo _KUNENA_EMOTICONS_CODE; ?>
                    </th>

                    <th algin = "left" width = "200"><?php echo _KUNENA_EMOTICONS_URL; ?>
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
                    <tr class = "row<?php echo $k;?>" >
                                <td width = "20" align="left">
                                    <input type = "checkbox" id = "cb<?php echo $i;?>" name = "cid[]" value = "<?php echo $s->id; ?>" onClick = "isChecked(this.checked);">
                                </td>
                                <td width = "10" align="center">
                                    <a href = "#edit" onclick = "return listItemTask('cb<?php echo $i; ?>','editsmiley')"><?php echo $s->id; ?></a>
                                </td>

                                <td width = "200" >
                                    <a href = "#edit" onclick = "return listItemTask('cb<?php echo $i; ?>','editsmiley')"><img src="<?php echo ($smileypath['live'] .'/'. $s->location); ?>" alt="<?php echo $s->location; ?>"  border="0" /></a>
                                </td>

                                <td width = "100">
								<?php echo $s->code; ?>
                                </td>

                                <td width = "200">
									<?php echo $s->location; ?>
                                </td>
                                

                            </tr>


                     <?php
                    }
                ?>
            <tr>
        		<th align = "center" colspan = "6" width="101%" >
        <?php echo  $pageNavSP->getLimitBox().$pageNavSP->getPagesLinks(); ?>&nbsp;
&nbsp;&nbsp; &nbsp;
		<?php echo $pageNavSP->getResultsCounter(); ?>
        </th>
		    </tr>
            <tr>
        		
            </tr>
      	</table>
                <input type = "hidden" name = "option" value = "<?php echo $option; ?>"><input type = "hidden" name = "task" value = "showsmilies"><input type = "hidden" name = "boxchecked" value = "0">
                <?php echo '<input type = "hidden" name = "limitstart" value = "0">'; ?>
        </form>
<?php
        }//end function showsmilies

		function editsmiley($option, $lang, $smiley_edit_img, $filename_list, $smileypath, $smileycfg)
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
        <form action = "index2.php" method = "POST" name = "adminForm">
            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "adminform">

				<tr align="center">
					<td width="100"><?php echo _KUNENA_EMOTICONS_CODE; ?></td>
					<td width="200"><input class="post" type="text" name="smiley_code" value="<?php echo $smileycfg['code'];?>" /></td>
                    <td rowspan="3" width="50"><img name="smiley_image" src="<?php echo $smiley_edit_img; ?>" border="0" alt="" /> &nbsp;</td>
                    <td rowspan="3">&nbsp;</td>
				</tr>
				<tr align="center">
					<td width="100"><?php echo _KUNENA_EMOTICONS_URL; ?></td>
					<td><select name="smiley_url" onchange="update_smiley(this.options[selectedIndex].value);"><?php echo $filename_list; ?></select> &nbsp; </td>
				</tr>
                <tr>
					<td width="100"><?php echo _KUNENA_EMOTICONS_EMOTICONBAR; ?></td>
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
        <form action = "index2.php" method = "POST" name = "adminForm">
            <table cellpadding = "4" cellspacing = "0" border = "0" width = "100%" class = "adminform">

				<tr align="center">
					<td width="100"><?php echo _KUNENA_EMOTICONS_CODE; ?></td>
					<td width="200"><input class="post" type="text" name="smiley_code" value="" /></td>
                    <td rowspan="3" width="50"><img name="smiley_image" src="" border="0" alt="" /> &nbsp;</td>
                    <td rowspan="3">&nbsp;</td>
				</tr>
				<tr align="center">
					<td width="100"><?php echo _KUNENA_EMOTICONS_URL; ?></td>
					<td><select name="smiley_url" onchange="update_smiley(this.options[selectedIndex].value);"><?php echo $filename_list; ?></select> &nbsp; </td>
				</tr>
                <tr>
					<td width="100"><?php echo _KUNENA_EMOTICONS_EMOTICONBAR; ?></td>
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
 function showRanks( $option,$lang,&$ranks,$pageNavSP,$order,$rankpath )
		 {
   $kunena_db = &JFactory::getDBO();
   ?>
  <form action="index.php?option=com_kunena&task=EditRank" method="POST" name="adminForm">
  <table class="adminheading" cellpadding="4" cellspacing="0" border="0" width="100%">
  
  </table>
  <table class="adminlist" border=0 cellspacing=0 cellpadding=3 width="100%" >
    <tr>
      <th width="20" align="center">#</th>
      <th align="left"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($ranks); ?>);"/></th>
      <th align="left" ><?php echo _KUNENA_RANKSIMAGE;?></th>
      <th align="left" nowrap="nowrap"><?php echo _KUNENA_RANKS;?></th>
      <th align="left" nowrap="nowrap"><?php echo _KUNENA_RANKS_SPECIAL;?></th>
      <th algin="center" nowrap="nowrap"><?php echo _KUNENA_RANKSMIN;?></th>
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
      <td><a href = "#edit" onclick = "return listItemTask('cb<?php echo $id; ?>','editRank')"><img src="<?php echo ($rankpath['live'] .DS. $row->rank_image); ?>" alt="<?php echo $row->rank_image; ?>"  border="0" /></a></td>
      <td nowrap="nowrap"><a href = "#edit" onclick = "return listItemTask('cb<?php echo $id; ?>','editRank')"><?php echo $row->rank_title; ?></a></td>
      <td><?php if ($row->rank_special == 1 ) { echo _ANN_YES; } else { echo _ANN_NO; } ?></td>
      <td align="center"><?php echo $row->rank_min; ?></td>
      <td width="100%">&nbsp;</td>
    </tr>
   
    <?php }  ?>

    <input type="hidden" name="option" value="<?php echo $option; ?>">
   	<input type="hidden" name="boxchecked" value="0">
   	<input type="hidden" name="task" value="ranks">
   	<?php echo '<input type = "hidden" name = "limitstart" value = "0">'; ?>

    <tr>
      <th align = "center" colspan = "12" width="111%">
        <?php echo  $pageNavSP->getLimitBox().$pageNavSP->getPagesLinks(); ?>&nbsp;
&nbsp;&nbsp; &nbsp;
		<?php echo $pageNavSP->getResultsCounter(); ?>
        </th>
    
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
			document.rank_image.src = "<?php echo $rankpath; ?>" +newimage;
		}
		//-->
		</script>
  <form action="index2.php" method="POST" name="adminForm">
  <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">

			<tr align="center">
				<td width="100"><?php echo _KUNENA_RANKS; ?></td>
				<td width="200"><input class="post" type="text" name="rank_title" value="" /></td>
			</tr>
   <tr>
				<td width="100"><?php echo _KUNENA_RANKSIMAGE; ?></td>
    <td><select name="rank_image" onchange="update_rank(this.options[selectedIndex].value);"><?php echo $filename_list; ?></select> &nbsp; <img name="rank_image" src="" border="0" alt="" /></td>
			<tr>
   <tr>
				<td width="100"><?php echo _KUNENA_RANKSMIN; ?></td>
    <td><input class="post" type="text" name="rank_min" value="1" /></td>
			<tr>
			<tr>
				<td width="100"><?php echo _KUNENA_RANKS_SPECIAL; ?></td>
				<td><input type="checkbox" name="rank_special" value="1" /></td>
			</tr>
					<td colspan="2" align="center"><input type = "hidden" name = "option" value = "<?php echo $option; ?>"> <input type = "hidden" name = "task" value = "showRanks"> <input type = "hidden" name = "boxchecked" value = "0">
					</td>
			</tr>
		</table>
  </form>

<?php }//end function edit rank

		function editrank($option, $lang, $edit_img, $filename_list, $path, $row)
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
  <form action = "index2.php" method = "POST" name = "adminForm">
  <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">

			<tr align="center">
				<td width="100"><?php echo _KUNENA_RANKS; ?></td>
				<td width="200"><input class="post" type="text" name="rank_title" value="<?php echo $row->rank_title;?>" /></td>
			</tr>
			<tr align="center">
				<td width="100"><?php echo _KUNENA_RANKSIMAGE; ?></td>
				<td><select name="rank_image" onchange="update_rank(this.options[selectedIndex].value);"><?php echo $filename_list; ?></select> &nbsp; <img name="rank_image" src="<?php echo $edit_img; ?>" border="0" alt="" /></td>
			</tr>
   <tr>
				<td width="100"><?php echo _KUNENA_RANKSMIN; ?></td>
    <td><input class="post" type="text" name="rank_min" value="<?php echo $row->rank_min;?>" /></td>
			<tr>
			<tr>
				<td width="100"><?php echo _KUNENA_RANKS_SPECIAL; ?></td>
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
