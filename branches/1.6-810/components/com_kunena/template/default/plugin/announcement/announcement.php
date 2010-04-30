<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
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
// Dont allow direct linking
defined( '_JEXEC' ) or die();

$kunena_my = JFactory::getUser ();
$kunena_db = &JFactory::getDBO();
$kunena_app =& JFactory::getApplication();
$document =& JFactory::getDocument();
$kunena_config =& CKunenaConfig::getInstance();

$document->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . stripslashes($kunena_config->board_title));

# Check for Editor rights  $kunena_config->annmodid
$do = JRequest::getVar("do", "");
$id = intval(JRequest::getVar("id", ""));
$user_fields = @explode(',', $kunena_config->annmodid);

if (in_array($kunena_my->id, $user_fields) || CKunenaTools::isAdmin()) {
    $is_editor = true;
} else {
    $is_editor = false;
}

// BEGIN: READ ANN
if ($do == "read") {
    $kunena_db->setQuery("SELECT id, title, sdescription, description, created, published, showdate FROM #__fb_announcement WHERE id='{$id}' AND published='1'");
    $anns_ = $kunena_db->loadObjectList();
    	check_dberror("Unable to load announcements.");

    $ann = $anns_[0];
    $annID = $ann->id;
    $anntitle = KunenaParser::parseText ($ann->title);
	$annsdescription = KunenaParser::parseBBCode ($ann->sdescription);

	$anndescription = KunenaParser::parseBBCode ($ann->description);

    $annpublished = $ann->published;
    $annshowdate = $ann->showdate;

    if ($annpublished > 0) {
?>

        <table class = "kblocktable" id = "kannouncement" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
                <tr>
                    <th>
                        <div class = "ktitle_cover km">
                            <span class = "ktitle kl"> <?php echo $kunena_app->getCfg('sitename'); ?> <?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?></span>
                        </div>
                    </th>
                </tr>
            </thead>

            <tbody id = "announcement_tbody">
                <tr class = "ksth ks">
                    <th class = "th-1 ksectiontableheader" align="left" >
                        <?php
                        if ($is_editor) {
                        	echo CKunenaLink::GetAnnouncementLink('edit', $annID, JText::_('COM_KUNENA_ANN_EDIT'), JText::_('COM_KUNENA_ANN_EDIT')).' | ';
                    		echo CKunenaLink::GetAnnouncementLink('delete', $annID, JText::_('COM_KUNENA_ANN_DELETE'), JText::_('COM_KUNENA_ANN_DELETE')).' | ';
                    		echo CKunenaLink::GetAnnouncementLink('add', NULL, JText::_('COM_KUNENA_ANN_ADD'), JText::_('COM_KUNENA_ANN_ADD')).' | ';
                    		echo CkunenaLink::GetAnnouncementLink('show', NULL, JText::_('COM_KUNENA_ANN_CPANEL'), JText::_('COM_KUNENA_ANN_CPANEL'));
                        }
                        ?>
                    </th>
                </tr>

                <tr>
                    <td class = "kanndesc" valign="top">
                        <h3> <?php echo $anntitle; ?> </h3>

                        <?php
                        if ($annshowdate > 0) {
                        ?>

                            <div class = "anncreated ks" title="<?php echo CKunenaTimeformat::showDate($ann->created, 'ago'); ?>">
<?php echo CKunenaTimeformat::showDate($ann->created, 'date_today'); ?>
                            </div>

                        <?php
                            }
                        ?>

    <div class = "anndesc">
<?php echo !empty($anndescription) ? $anndescription : $annsdescription; ?>
    </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php
        }
    }

// FINISH: READ ANN
        ?>
        <!-- announcement-->
        <?php
    // BEGIN: SHOW ANN
    if ($do == "show") {
    	if ($is_editor) {
        ?>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
            <table class = "kblocktable" id = "kannouncement" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
                <thead>
                    <tr>
                        <th colspan = "6">
                            <div class = "ktitle_cover km">
                                <span class = "ktitle kl"> <?php echo $kunena_app->getCfg('sitename'); ?> <?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?> | <?php echo CKunenaLink::GetAnnouncementLink('add', NULL, JText::_('COM_KUNENA_ANN_ADD'), JText::_('COM_KUNENA_ANN_ADD')); ?></span>
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody id = "announcement_tbody">
                    <tr class = "ksth ks">
                        <th class = "th-1 ksectiontableheader"  width="1%" align="center"> <?php echo JText::_('COM_KUNENA_ANN_ID'); ?>
                        </th>

                        <th class = "th-2 ksectiontableheader" width="15%" align="left"> <?php echo JText::_('COM_KUNENA_ANN_DATE'); ?>
                        </th>

                        <th class = "th-3 ksectiontableheader" width="54%" align="left"> <?php echo JText::_('COM_KUNENA_ANN_TITLE'); ?>
                        </th>

                        <th class = "th-4 ksectiontableheader" width="10%"  align="center"> <?php echo JText::_('COM_KUNENA_ANN_PUBLISH'); ?>
                        </th>

                        <th class = "th-5 ksectiontableheader"  width="10%"  align="center"> <?php echo JText::_('COM_KUNENA_ANN_EDIT'); ?>
                        </th>

                        <th class = "th-6 ksectiontableheader" width="10%"  align="center"> <?php echo JText::_('COM_KUNENA_ANN_DELETE'); ?>
                        </th>
                    </tr>

                    <?php
                    $query = "SELECT id, title, created, published FROM #__fb_announcement ORDER BY created DESC";
                    $kunena_db->setQuery($query);
                    $rows = $kunena_db->loadObjectList();
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

                            <tr class = "k<?php echo $tabclass[$k];?>">
                                <td class = "td-1"  align="center">
                                    #<?php echo $row->id; ?>
                                </td>

                                <td class = "td-2" align="left">
									<?php echo CKunenaTimeformat::showDate($row->created, 'date_today'); ?>
                                </td>

                                <td class = "td-3"  align="left">
                                    <?php echo CKunenaLink::GetAnnouncementLink('read', $row->id, KunenaParser::parseText ($row->title), KunenaParser::parseText ($row->title), 'follow'); ?>
                                </td>

                                <td class = "td-4"  align="center">
                                    <?php
                                    if ($row->published > 0) {
                                        echo JText::_('COM_KUNENA_ANN_PUBLISHED');
                                        }
                                    else {
                                        echo JText::_('COM_KUNENA_ANN_UNPUBLISHED');
                                        }
                                    ?>
                                </td>

                                <td class = "td-5"  align="center">
                                    <?php echo CKunenaLink::GetAnnouncementLink('edit', $row->id, JText::_('COM_KUNENA_ANN_EDIT'),JText::_('COM_KUNENA_ANN_EDIT')); ?>
                                </td>

                                <td class = "td-6"  align="center">
                                    <?php echo CKunenaLink::GetAnnouncementLink('delete', $row->id, JText::_('COM_KUNENA_ANN_DELETE'), JText::_('COM_KUNENA_ANN_DELETE')); ?>
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
    		} else {
    			$kunena_app->redirect ( CKunenaLink::GetKunenaURL(false), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
    		}
        }

    // FINISH: SHOW ANN
    // BEGIN: ADD ANN
    if ($do == "doadd") {
    	if ($is_editor) {
        	$title = addslashes(JRequest::getVar("title", ""));
        	$description = addslashes(JRequest::getVar('description', '', 'string', JREQUEST_ALLOWRAW));
        	$sdescription = addslashes(JRequest::getVar('sdescription', '', 'string', JREQUEST_ALLOWRAW));
        	$created = addslashes(JRequest::getVar("created", ""));
        	$published = JRequest::getInt("published", 0);
        	$ordering = 0;
        	$showdate = addslashes(JRequest::getVar("showdate", ""));
        	# Clear any HTML
        	$query1 = "INSERT INTO #__fb_announcement VALUES ('', '$title', '$sdescription', '$description', " . (($created <> '')?"'$created'":"NOW()") . ", '$published', '$ordering','$showdate')";
        	$kunena_db->setQuery($query1);

        	$kunena_db->query();
        	check_dberror("Unable to insert announcement.");
        	$kunena_app->redirect(CKunenaLink::GetAnnouncementURL('show',null, false), JText::_('COM_KUNENA_ANN_SUCCESS_ADD'));
    	} else {
    		$kunena_app->redirect ( CKunenaLink::GetKunenaURL(false), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
    	}
    }

    if ($do == "add") {
    	if ($is_editor) {
			$calendar = JHTML::_('calendar', '', 'created', 'addcreated');
            ?>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<table class = "kblocktable" id = "kannouncement" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th>
                <div class = "ktitle_cover km">
                    <span class = "ktitle kl"> <?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?>: <?php echo JText::_('COM_KUNENA_ANN_ADD'); ?> | <?php echo CKunenaLink::GetAnnouncementLink('show',NULL, JText::_('COM_KUNENA_ANN_CPANEL'), JText::_('COM_KUNENA_ANN_CPANEL')); ?></span>
                </div>
            </th>
        </tr>
    </thead>

    <tbody id = "announcement_tbody">
        <tr>
            <td class = "kanndesc" valign="top">
                <form action = "<?php echo CKunenaLink::GetAnnouncementURL('doadd'); ?>" method = "post" name = "addform">
                    <strong><?php echo JText::_('COM_KUNENA_ANN_TITLE'); ?>:</strong>

                    <br/>

                    <input type = "text" name = "title" size = "40" maxlength = "150"/>

                    <br/>

                    <strong><?php echo JText::_('COM_KUNENA_ANN_SORTTEXT'); ?>:</strong>

                    <br/>

                    <textarea id = "textarea1" name = "sdescription" rows = "8" cols = "60" style = "width:95%; height:300px;"></textarea>

                    <br/>

                    <hr/>

                    <strong><?php echo JText::_('COM_KUNENA_ANN_LONGTEXT'); ?></strong>

                    <br/>

                    <textarea id = "textarea2" name = "description" rows = "30" cols = "60" style = "width:95%; height:550px;"></textarea>

                    <br/>

                    <strong><?php echo JText::_('COM_KUNENA_ANN_DATE'); ?>:</strong>

					<?php echo $calendar; ?>

                    <br/>

                    <strong><?php echo JText::_('COM_KUNENA_ANN_PUBLISH'); ?></strong>

                    <select name = "published">
                        <option value = "1"><?php echo JText::_('COM_KUNENA_ANN_YES'); ?></option>

                        <option value = "0"><?php echo JText::_('COM_KUNENA_ANN_NO'); ?></option>
                    </select>

                    <br/>

                    <strong><?php echo JText::_('COM_KUNENA_ANN_SHOWDATE'); ?></strong>

                    <select name = "showdate">
                        <option value = "1"><?php echo JText::_('COM_KUNENA_ANN_YES'); ?></option>

                        <option value = "0"><?php echo JText::_('COM_KUNENA_ANN_NO'); ?></option>
                    </select>

                    <br/>

                    <strong><?php echo JText::_('COM_KUNENA_ANN_ORDER'); ?>:</strong>

                    <input type = "text" name = "ordering" size = "5" value = "0"/>

                    <br/>

                    <INPUT TYPE = 'hidden' NAME = "do" value = "doadd">

                    <input name = "submit" type = "submit" value = "<?php echo JText::_('COM_KUNENA_ANN_SAVE'); ?>"/>
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
    		} else {
    			$kunena_app->redirect ( CKunenaLink::GetKunenaURL(false), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
    		}
        }
    // FINISH: ADD ANN
?>

<?php
    // BEGIN: EDIT ANN
    if ($do == "doedit") {
    	if ($is_editor) {
       		$title = JRequest::getVar("title", "");
        	$description = JRequest::getVar('description', '', 'string', JREQUEST_ALLOWRAW);
        	$sdescription = JRequest::getVar('sdescription', '', 'string', JREQUEST_ALLOWRAW);
        	$created = JRequest::getVar("created", "");
        	$published = JRequest::getVar("published", 0);
        	$showdate = JRequest::getVar("showdate", "");

        	$kunena_db->setQuery("UPDATE #__fb_announcement SET title=". $kunena_db->Quote ( $title ) .", description=". $kunena_db->Quote ( $description ) .", sdescription=". $kunena_db->Quote ( $sdescription ) .",  created=" . (($created <> '')?"'$created'":"NOW()") . ", published='$published', showdate='$showdate' WHERE id=$id");

       		if ($kunena_db->query()) {
            	$kunena_app->redirect(CKunenaLink::GetAnnouncementURL('show', null, false), JText::_('COM_KUNENA_ANN_SUCCESS_EDIT'));
            }
        	check_dberror("Unable to update announcement.");
    	} else {
    		$kunena_app->redirect ( CKunenaLink::GetKunenaURL(false), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
    	}
    }

    if ($do == "edit") {
    	if ($is_editor) {
        	$kunena_db->setQuery("SELECT * FROM #__fb_announcement WHERE id='{$id}'");
        	$anns = $kunena_db->loadObjectList();
        	check_dberror("Unable to load announcements.");

        	$ann = $anns[0];
        	$annID = $ann->id;
        	$anntitle = kunena_htmlspecialchars($ann->title);
        	$annsdescription = kunena_htmlspecialchars($ann->sdescription);
        	$anndescription = kunena_htmlspecialchars($ann->description);
        	$anncreated = $ann->created;
        	$annpublished = $ann->published;
        	$annordering = $ann->ordering;
        	$annshowdate = $ann->showdate;
        	$calendar = JHTML::_('calendar', $anncreated, 'created', 'addcreated');
        	//$document->addCustomTag('<link rel="stylesheet" type="text/css" media="all" href="' . JURI::root() . '/includes/js/calendar/calendar-mos.css" title="green" />');

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
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<table class = "kblocktable" id = "kannouncement" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th>
                <div class = "ktitle_cover km">
                    <span class = "ktitle kl"> <?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?>: <?php echo JText::_('COM_KUNENA_ANN_EDIT'); ?> | <?php echo CKunenaLink::GetAnnouncementLink('show',NULL, JText::_('COM_KUNENA_ANN_CPANEL'), JText::_('COM_KUNENA_ANN_CPANEL')); ?></span>
                </div>
            </th>
        </tr>
    </thead>

    <tbody id = "announcement_tbody">
        <tr>
            <td class = "kanndesc" valign="top">
                <form action = "<?php echo CKunenaLink::GetAnnouncementURL('doedit'); ?>" method = "post" name = "editform" onSubmit = "return validate_form ( );">
                    <strong>#<?php echo $annID; ?> : <?php echo $anntitle; ?></strong>

                    <br/>

                    <strong><?php echo JText::_('COM_KUNENA_ANN_TITLE'); ?>:</strong>

                    <br/>

                    <input type = "text" name = "title" size = "40" maxlength = "150" value = "<?php echo $anntitle ;?>"/>

                    <br/>

                    <strong><?php echo JText::_('COM_KUNENA_ANN_SORTTEXT'); ?>:</strong>

                    <br/>

                    <textarea id = "textarea1" name = "sdescription" rows = "8" cols = "60" style = "width:95%; height:300px;"><?php echo $annsdescription; ?></textarea>

                    <br/>

                    <hr/>

                    <strong><?php echo JText::_('COM_KUNENA_ANN_LONGTEXT'); ?>:</strong>

                    <br/>

                    <textarea id = "textarea2" name = "description" rows = "30" cols = "60" style = "width:95%; height:550px;"><?php echo $anndescription; ?></textarea>

                    <br/>

                    <strong><?php echo JText::_('COM_KUNENA_ANN_DATE'); ?>:</strong>

                    <?php echo $calendar;?>

                    <br/>

                    <strong><?php echo JText::_('COM_KUNENA_ANN_SHOWDATE'); ?>: &nbsp;&nbsp;&nbsp;</strong>

                    <select name = "showdate">
                        <option value = "1"<?php echo ($annshowdate == 1 ? 'selected="selected"' : ''); ?>><?php echo JText::_('COM_KUNENA_ANN_YES'); ?></option>

                        <option value = "0"<?php echo ($annshowdate == 0 ? 'selected="selected"' : ''); ?>><?php echo JText::_('COM_KUNENA_ANN_NO'); ?></option>
                    </select>

                    <br/>

                    <strong><?php echo JText::_('COM_KUNENA_ANN_PUBLISH'); ?>: &nbsp;&nbsp;&nbsp;</strong>

                    <select name = "published">
                        <option value = "1"<?php echo ($annpublished == 1 ? 'selected="selected"' : ''); ?>><?php echo JText::_('COM_KUNENA_ANN_YES'); ?></option>

                        <option value = "0"<?php echo ($annpublished == 0 ? 'selected="selected"' : ''); ?>><?php echo JText::_('COM_KUNENA_ANN_NO'); ?></option>
                    </select>

                    <br/>

                    <INPUT TYPE = 'hidden' NAME = "do" value = "doedit"/>

                    <INPUT TYPE = 'hidden' NAME = "id" value = "<?php echo $annID ;?>"/>

                    <input name = "submit" type = "submit" value = "<?php echo JText::_('COM_KUNENA_ANN_SAVE'); ?>"/>

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
    		} else {
    			$kunena_app->redirect ( CKunenaLink::GetKunenaURL(false), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
    		}
        }

    // FINISH: EDIT ANN
    // BEGIN: delete ANN
    if ($do == "delete") {
    	if ($is_editor) {
        	$query1 = "DELETE FROM #__fb_announcement WHERE id=$id ";
        	$kunena_db->setQuery($query1);
        	$kunena_db->query();
        	check_dberror("Unable to delete announcement.");

        	$kunena_app->redirect(CKunenaLink::GetAnnouncementURL('show', null, false), JText::_('COM_KUNENA_ANN_DELETED'));
    	} else {
    		$kunena_app->redirect ( CKunenaLink::GetKunenaURL(false), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
    	}
    }
    // FINISH: delete ANN
?>
<!-- /announcement-->

