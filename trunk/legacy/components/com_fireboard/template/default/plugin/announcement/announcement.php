<?php
/**
* @version $Id: announcement.php 947 2008-08-11 01:56:01Z fxstein $
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
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

global $fbConfig;
# Check for Editor rights  $fbConfig->annmodid
$do = mosGetParam($_REQUEST, "do", "");
$id = intval(mosGetParam($_REQUEST, "id", ""));
$user_fields = @explode(',', $fbConfig->annmodid);

if (in_array($my->id, $user_fields) || $my->usertype == 'Administrator' || $my->usertype == 'Super Administrator') {
    $is_editor = true;
    }
else {
    $is_editor = false;
    }

$is_user = (strtolower($my->usertype) <> '');

$showlink = sefRelToAbs('index.php?option=com_fireboard&amp;func=announcement' . FB_FB_ITEMID_SUFFIX . '&amp;do=show');
$addlink = sefRelToAbs('index.php?option=com_fireboard&amp;func=announcement' . FB_FB_ITEMID_SUFFIX . '&amp;do=add');
$readlink = 'index.php?option=com_fireboard&amp;func=announcement' . FB_FB_ITEMID_SUFFIX . '&amp;do=read&amp;id=';
$editlink = 'index.php?option=com_fireboard&amp;func=announcement' . FB_FB_ITEMID_SUFFIX . '&amp;do=edit&amp;id=';
$deletelink = 'index.php?option=com_fireboard&amp;func=announcement' . FB_FB_ITEMID_SUFFIX . '&amp;do=delete&amp;id=';

// BEGIN: READ ANN
if ($do == "read") {
    $database->setQuery("SELECT id,title,description,created ,published,showdate  FROM #__fb_announcement  WHERE id=$id AND published = 1 ");
    $anns_ = $database->loadObjectList();
    	check_dberror("Unable to load announcements.");

    $ann = $anns_[0];
    $annID = $ann->id;
    $anntitle = $ann->title;
    $anndescription = $ann->description;
    $anncreated = $ann->created;
    $annpublished = $ann->published;
    $annshowdate = $ann->showdate;

    if ($annpublished > 0) {
?>

        <table class = "fb_blocktable" id = "fb_announcement " border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
                <tr>
                    <th>
                        <div class = "fb_title_cover fbm">
                            <span class = "fb_title fbl"> <?php echo $mosConfig_sitename; ?> <?php echo _ANN_ANNOUNCEMENTS; ?></span>
                        </div>
                    </th>
                </tr>
            </thead>

            <tbody id = "announcement_tbody">
                <tr class = "fb_sth fbs">
                    <th class = "th-1 <?php echo $boardclass; ?>sectiontableheader" align="left" >
                        <?php
                        if ($is_editor) {
                        ?>

                                <a href = "<?php echo $editlink;?><?php echo $annID; ?>"><?php echo _ANN_EDIT; ?> </a> |
                    <a href = "<?php echo $deletelink;?><?php echo $annID; ?>"><?php echo _ANN_DELETE; ?> </a> | <a href = "<?php echo $addlink;?>"><?php echo _ANN_ADD; ?> </a> | <a href = "<?php echo $showlink;?>"><?php echo _ANN_CPANEL; ?> </a>

                        <?php
                            }
                        ?>
                    </th>
                </tr>

                <tr>
                    <td class = "fb_anndesc" valign="top">
                        <h3> <?php echo $anntitle; ?> </h3>

                        <?php
                        if ($annshowdate > 0) {
                        ?>

                            <div class = "anncreated fbs">
<?php echo $anncreated; ?>
                            </div>

                        <?php
                            }
                        ?>

    <div class = "anndesc">
<?php echo $anndescription; ?>
    </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php
        }
    }

// FINISH: READ ANN
if ($is_editor) {
        ?>
        <!-- announcement-->
        <?php
    // BEGIN: SHOW ANN
    if ($do == "show") {
        $deletelink = 'index.php?option=com_fireboard&amp;func=announcement&amp;do=delete&amp;id=';
        $editlink = 'index.php?option=com_fireboard&amp;func=announcement&amp;do=edit&amp;id=';
        ?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
            <table class = "fb_blocktable" id = "fb_announcement " border = "0" cellspacing = "0" cellpadding = "0" width="100%">
                <thead>
                    <tr>
                        <th colspan = "6">
                            <div class = "fb_title_cover fbm">
                                <span class = "fb_title fbl"> <?php echo $mosConfig_sitename; ?> <?php echo _ANN_ANNOUNCEMENTS; ?> | <a href = "<?php echo $addlink;?>"><?php echo _ANN_ADD; ?></a></span>
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody id = "announcement_tbody">
                    <tr class = "fb_sth fbs">
                        <th class = "th-1 <?php echo $boardclass; ?>sectiontableheader"  width="1%" align="center"> <?php echo _ANN_ID; ?>
                        </th>

                        <th class = "th-2 <?php echo $boardclass; ?>sectiontableheader" width="15%" align="left"> <?php echo _ANN_DATE; ?>
                        </th>

                        <th class = "th-3 <?php echo $boardclass; ?>sectiontableheader" width="54%" align="left"> <?php echo _ANN_TITLE; ?>
                        </th>

                        <th class = "th-4 <?php echo $boardclass; ?>sectiontableheader" width="10%"  align="center"> <?php echo _ANN_PUBLISH; ?>
                        </th>

                        <th class = "th-5 <?php echo $boardclass; ?>sectiontableheader"  width="10%"  align="center"> <?php echo _ANN_EDIT; ?>
                        </th>

                        <th class = "th-6 <?php echo $boardclass; ?>sectiontableheader" width="10%"  align="center"> <?php echo _ANN_DELETE; ?>
                        </th>
                    </tr>

                    <?php
                    $query = "SELECT id, title, created, published FROM #__fb_announcement" . "\n ORDER BY created DESC ";
                    $database->setQuery($query);
                    $rows = $database->loadObjectList();
                    	check_dberror("Unable to load announcements.");

                    $tabclass = array
                    (
                        "sectiontableentry1",
                        "sectiontableentry2"
                    );

                    $k = 0;

                    if (count($rows) > 0) {
                        foreach ($rows as $row) {
                            $k = 1 - $k;
                    ?>

                            <tr class = "<?php echo $boardclass.''.$tabclass[$k];?>">
                                <td class = "td-1"  align="center">
                                    #<?php echo $row->id; ?>
                                </td>

                                <td class = "td-2" align="left">
<?php echo $row->created; ?>
                                </td>

                                <td class = "td-3"  align="left">
                                    <a href = "<?php echo $readlink;?><?php echo $row->id; ?>"><?php echo $row->title; ?></a>
                                </td>

                                <td class = "td-4"  align="center">
                                    <?php
                                    if ($row->published > 0) {
                                        echo _ANN_PUBLISHED;
                                        }
                                    else {
                                        echo _ANN_UNPUBLISHED;
                                        }
                                    ?>
                                </td>

                                <td class = "td-5"  align="center">
                                    <a href = "<?php echo sefRelToAbs($editlink.$row->id); ?>"><?php echo _ANN_EDIT; ?> </a>
                                </td>

                                <td class = "td-6"  align="center">
                                    <a href = "<?php echo sefRelToAbs($deletelink.$row->id); ?>"><?php echo _ANN_DELETE; ?></a>
                                </td>
                            </tr>

                    <?php
                            }
                        }
                    ?>
                </tbody>
            </table>
</div>
</div>
</div>
</div>
</div>
            <?php
        }

    // FINISH: SHOW ANN
    // BEGIN: ADD ANN
    if ($do == "doadd") {
        mosMakeHtmlSafe ($_POST);
        $title = mosGetParam($_REQUEST, "title", "");
        $description = mosGetParam($_REQUEST, "description", "", _MOS_ALLOWRAW);
        $sdescription = mosGetParam($_REQUEST, "sdescription", "", _MOS_ALLOWRAW);
        $created = mosGetParam($_REQUEST, "created", "");
        $published = mosGetParam($_REQUEST, "published", 0);
        $showdate = mosGetParam($_REQUEST, "showdate", "");
        # Clear any HTML
        $query1 = "INSERT INTO #__fb_announcement VALUES ('', '$title', '$sdescription', '$description', " . (($created <> '')?"'$created'":"NOW()") . ", '$published', '$ordering','$showdate')";
        $database->setQuery($query1);

        $database->query() or trigger_dberror("Unable to insert announcement.");
        mosRedirect($showlink, _ANN_SUCCESS_ADD);
    }

    if ($do == "add") {
        if (!$is_editor) {
            die ("Hacking attempt");
            }

        mosCommonHTML::loadCalendar();
            ?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "fb_blocktable" id = "fb_announcement " border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th>
                <div class = "fb_title_cover fbm">
                    <span class = "fb_title fbl"> <?php echo _ANN_ANNOUNCEMENTS; ?>: <?php echo _ANN_ADD; ?> | <a href = "<?php echo $showlink;?>"><?php echo _ANN_CPANEL; ?></a></span>
                </div>
            </th>
        </tr>
    </thead>

    <tbody id = "announcement_tbody">
        <tr>
            <td class = "fb_anndesc" valign="top">
                <form action = "<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=announcement&amp;do=doadd'); ?>" method = "post" name = "addform">
                    <strong><?php echo _ANN_TITLE; ?>:</strong>

                    <br/>

                    <input type = "text" name = "title" size = "40" maxlength = "150"/>

                    <br/>

                    <strong><?php echo _ANN_SORTTEXT; ?>:</strong>

                    <br/>

                    <textarea id = "textarea1" name = "sdescription" rows = "8" cols = "60" style = "width:95%; height:300px;"></textarea>

                    <br/>

                    <hr/>

                    <strong><?php echo _ANN_LONGTEXT; ?></strong>

                    <br/>

                    <textarea id = "textarea2" name = "description" rows = "30" cols = "60" style = "width:95%; height:550px;"></textarea>

                    <br/>

                    <strong><?php echo _ANN_DATE; ?>:</strong>

                    <input type = "text" name = "created" id = "anncreated" size = "40" maxlength = "150" value = "<?php echo $anncreated ;?>"/>

                    <input type = "reset" class = "button" value = "..." onclick = "return showCalendar('anncreated', '%Y-%m-%d');"/>

                    <br/>

                    <strong><?php echo _ANN_PUBLISH; ?></strong>

                    <select name = "published">
                        <option value = "1"><?php echo _ANN_YES; ?></option>

                        <option value = "0"><?php echo _ANN_NO; ?></option>
                    </select>

                    <br/>

                    <strong><?php echo _ANN_SHOWDATE; ?></strong>

                    <select name = "showdate">
                        <option value = "1"><?php echo _ANN_YES; ?></option>

                        <option value = "0"><?php echo _ANN_NO; ?></option>
                    </select>

                    <br/>

                    <strong><?php echo _ANN_ORDER; ?>:</strong>

                    <input type = "text" name = "ordering" size = "5" value = "0"/>

                    <br/>

                    <INPUT TYPE = 'hidden' NAME = "do" value = "doadd">

                    <input name = "submit" type = "submit" value = "<?php echo _ANN_SAVE; ?>"/>
                </form>
            </td>
        </tr>
    </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<?php
        }
    // FINISH: ADD ANN
?>

<?php
    // BEGIN: EDIT ANN
    if ($do == "doedit") {
        mosMakeHtmlSafe ($_POST);
        $title = mosGetParam($_REQUEST, "title", "");
        $description = mosGetParam($_REQUEST, "description", "", _MOS_ALLOWRAW);
        $sdescription = mosGetParam($_REQUEST, "sdescription", "", _MOS_ALLOWRAW);
        $created = mosGetParam($_REQUEST, "created", "");
        $published = mosGetParam($_REQUEST, "published", 0);
        $showdate = mosGetParam($_REQUEST, "showdate", "");

        $database->setQuery("UPDATE #__fb_announcement SET title='$title', description='$description', sdescription='$sdescription',  created=" . (($created <> '')?"'$created'":"NOW()") . ", published='$published', showdate='$showdate' WHERE id=$id");

        if ($database->query()) {
            mosRedirect($showlink, _ANN_SUCCESS_EDIT);
            }
        }

    if ($do == "edit") {
        $database->setQuery("SELECT * FROM #__fb_announcement WHERE id=$id");
        $anns = $database->loadObjectList();
        check_dberror("Unable to load announcements.");

        $ann = $anns[0];
        $annID = $ann->id;
        $anntitle = $ann->title;
        $annsdescription = $ann->sdescription;
        $anndescription = $ann->description;
        $anncreated = $ann->created;
        $annpublished = $ann->published;
        $annordering = $ann->ordering;
        $annshowdate = $ann->showdate;
        mosCommonHTML::loadCalendar();
        //$mainframe->addCustomHeadTag('<link rel="stylesheet" type="text/css" media="all" href="' . $mosConfig_live_site . '/includes/js/calendar/calendar-mos.css" title="green" />');
?>
<script type = "text/javascript">
    <!--
    function validate_form()
    {
        valid = true;

        if (document.editform.title.value == "")
        {
            alert("Please fill in the 'Title' box.");
            valid = false;
        }

        if (document.editform.sdescription.value == "")
        {
            alert("Please fill in the 'Short Desc' box.");
            valid = false;
        }

        return valid;
    }
            //-->
</script>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "fb_blocktable" id = "fb_announcement " border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th>
                <div class = "fb_title_cover fbm">
                    <span class = "fb_title fbl"> <?php echo _ANN_ANNOUNCEMENTS; ?>: <?php echo _ANN_EDIT; ?> | <a href = "<?php echo $showlink;?>"><?php echo _ANN_CPANEL; ?></a></span>
                </div>
            </th>
        </tr>
    </thead>

    <tbody id = "announcement_tbody">
        <tr>
            <td class = "fb_anndesc" valign="top">
                <form action = "<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=announcement&amp;do=doedit'); ?>" method = "post" name = "editform" onSubmit = "return validate_form ( );">
                    <strong>#<?php echo $annID; ?> : <?php echo $anntitle; ?></strong>

                    <br/>

                    <strong><?php echo _ANN_TITLE; ?>:</strong>

                    <br/>

                    <input type = "text" name = "title" size = "40" maxlength = "150" value = "<?php echo $anntitle ;?>"/>

                    <br/>

                    <strong><?php echo _ANN_SORTTEXT; ?>:</strong>

                    <br/>

                    <textarea id = "textarea1" name = "sdescription" rows = "8" cols = "60" style = "width:95%; height:300px;"><?php echo $annsdescription; ?></textarea>

                    <br/>

                    <hr/>

                    <strong><?php echo _ANN_LONGTEXT; ?>:</strong>

                    <br/>

                    <textarea id = "textarea2" name = "description" rows = "30" cols = "60" style = "width:95%; height:550px;"><?php echo $anndescription; ?></textarea>

                    <br/>

                    <strong><?php echo _ANN_DATE; ?>:</strong>

                    <input type = "text" name = "created" id = "anncreated" size = "40" maxlength = "150" value = "<?php echo $anncreated ;?>"/>

                    <input type = "reset" class = "button" value = "..." onclick = "return showCalendar('anncreated', '%Y-%m-%d');"/>

                    <br/>

                    <strong><?php echo _ANN_SHOWDATE; ?>: &nbsp;&nbsp;&nbsp;</strong>

                    <select name = "showdate">
                        <option value = "1"<?php echo ($annshowdate == 1 ? 'selected="selected"' : ''); ?>><?php echo _ANN_YES; ?></option>

                        <option value = "0"<?php echo ($annshowdate == 0 ? 'selected="selected"' : ''); ?>><?php echo _ANN_NO; ?></option>
                    </select>

                    <br/>

                    <strong><?php echo _ANN_PUBLISH; ?>: &nbsp;&nbsp;&nbsp;</strong>

                    <select name = "published">
                        <option value = "1"<?php echo ($annpublished == 1 ? 'selected="selected"' : ''); ?>><?php echo _ANN_YES; ?></option>

                        <option value = "0"<?php echo ($annpublished == 0 ? 'selected="selected"' : ''); ?>><?php echo _ANN_NO; ?></option>
                    </select>

                    <br/>

                    <INPUT TYPE = 'hidden' NAME = "do" value = "doedit"/>

                    <INPUT TYPE = 'hidden' NAME = "id" value = "<?php echo $annID ;?>"/>

                    <input name = "submit" type = "submit" value = "<?php echo _ANN_SAVE; ?>"/>

                    <br/>

                    <br/>

                    <br/>
                </form>
            </td>
        </tr>
    </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<?php
        }

    // FINISH: EDIT ANN
    // BEGIN: delete ANN
    if ($do == "delete")
    {
        $query1 = "DELETE FROM #__fb_announcement WHERE id=$id ";
        $database->setQuery($query1);
        $database->query() or trigger_dberror("Unable to delete announcement.");

        mosRedirect($showlink, _ANN_DELETED);
    }
    // FINISH: delete ANN
?>
<!-- /announcement-->
<?php
    }
?>
