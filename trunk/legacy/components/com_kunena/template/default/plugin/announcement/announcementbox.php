<?php
/**
* @version $Id: announcementbox.php 947 2008-08-11 01:56:01Z fxstein $
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
$user_fields = @explode(',', $fbConfig->annmodid);

if (in_array($my->id, $user_fields) || $my->usertype == 'Administrator' || $my->usertype == 'Super Administrator') {
    $is_editor = true;
    }
else {
    $is_editor = false;
    }

$is_user = (strtolower($my->usertype) <> '');
$showlink = 'index.php?option=com_fireboard&amp;func=announcement&amp;do=show';
$addlink = 'index.php?option=com_fireboard&amp;func=announcement&amp;do=add';
$readlink = 'index.php?option=com_fireboard&amp;func=announcement&amp;do=read&amp;id=';
$editlink = 'index.php?option=com_fireboard&amp;func=announcement&amp;do=edit&amp;id=';
$deletelink = 'index.php?option=com_fireboard&amp;func=announcement&amp;do=delete&amp;id=';
?>

<?php
// BEGIN: BOX ANN
$database->setQuery("SELECT id,title,sdescription,description, created ,published,showdate" . "\n FROM #__fb_announcement  WHERE  published = 1 ORDER BY created DESC LIMIT 1");

$anns = $database->loadObjectList();
	check_dberror("Unable to load announcements.");
$ann = $anns[0];
$annID = $ann->id;
$anntitle = $ann->title;
$annsdescription = $ann->sdescription;
$anndescription = $ann->description;
$anncreated = $ann->created;
$annpublished = $ann->published;
$annshowdate = $ann->showdate;

if ($annID > 0) {
?>
    <!-- ANNOUNCEMENTS BOX -->
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
    <table class = "fb_blocktable" id = "fb_announcement" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th align="left">
                    <div class = "fb_title_cover fbm">
                        <span class = "fb_title fbl"><?php echo $anntitle; ?></span>
                    </div>

                    <img id = "BoxSwitch_announcements__announcements_tbody" class = "hideshow" src = "<?php echo JB_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                </th>
            </tr>
        </thead>

        <tbody id = "announcements_tbody">
            <?php
            if ($is_editor) {
            ?>

                    <tr class = "fb_sth">
                        <th class = "th-1 <?php echo $boardclass ;?>sectiontableheader fbm" align="left">
                            <a href = "<?php echo $editlink;?><?php echo $annID; ?>"><?php echo _ANN_EDIT; ?> </a> |
                        <a href = "<?php echo $deletelink;?><?php echo $annID; ?>"><?php echo _ANN_DELETE; ?> </a> | <a href = "<?php echo $addlink;?>"><?php echo _ANN_ADD; ?> </a> | <a href = "<?php echo $showlink;?>"><?php echo _ANN_CPANEL; ?> </a>
                        </th>
                    </tr>

            <?php
                }
            ?>

                <tr class = "<?php echo $boardclass ;?>sectiontableentry2">
                    <td class = "td-1 fbm" align="left">
                        <?php
                        if ($annshowdate > 0) {
                        ?>

                            <div class = "anncreated">
<?php echo $anncreated; ?>
                            </div>

                        <?php
                            }
                        ?>

                        <div class = "anndesc">
<?php echo $annsdescription; ?>

<?php
if ($anndescription != "") {
?>

    &nbsp;&nbsp;&nbsp;<a href = "<?php echo $readlink;?><?php echo $annID;?>"> <?php echo _ANN_READMORE; ?></a>

<?php
    }
?>
                        </div>
                    </td>
                </tr>
        </tbody>
    </table>
    </div>
</div>
</div>
</div>
</div>
    <!-- / ANNOUNCEMENTS BOX -->

<?php
    }
// FINISH: BOX ANN
?>