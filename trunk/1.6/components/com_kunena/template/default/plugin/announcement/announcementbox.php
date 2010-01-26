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
$kunena_config =& CKunenaConfig::getInstance();

# Check for Editor rights  $kunena_config->annmodid
$user_fields = @explode(',', $kunena_config->annmodid);

if (in_array($kunena_my->id, $user_fields) || CKunenaTools::isAdmin()) {
    $is_editor = true;
    }
else {
    $is_editor = false;
    }
?>

<?php
// BEGIN: BOX ANN
$kunena_db->setQuery("SELECT id, title, sdescription, description, created, published, showdate FROM #__fb_announcement WHERE published='1' ORDER BY created DESC", 0, 1);

$anns = $kunena_db->loadObjectList();
check_dberror("Unable to load announcements.");
if (count($anns) == 0) return;
$ann = $anns[0];
$annID = $ann->id;
$anntitle = CKunenaTools::parseText ($ann->title);
$annsdescription = CKunenaTools::parseBBCode ($ann->sdescription);

$anndescription = CKunenaTools::parseBBCode ($ann->description);

$annpublished = $ann->published;
$annshowdate = $ann->showdate;

if ($annID > 0) {
?>
    <!-- ANNOUNCEMENTS BOX -->
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
    <table class = "kblocktable" id = "kannouncement" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th align="left">
                    <div class = "ktitle_cover km">
                        <span class = "ktitle kl"><?php echo $anntitle; ?></span>
                    </div>
					<div class="fltrt"><span id="ann_status"><a class="ktoggler close" rel="announcement"></a></span></div>
                </th>
            </tr>
        </thead>

        <tbody id = "announcement">
            <?php
            if ($is_editor) {
            ?>

                    <tr class = "ksth">
                        <th class = "th-1 ksectiontableheader km" align="left">
                            <?php echo CKunenaLink::GetAnnouncementLink($kunena_config, 'edit', $annID, _ANN_EDIT, _ANN_EDIT); ?> |
                            <?php echo CKunenaLink::GetAnnouncementLink($kunena_config, 'delete', $annID, _ANN_DELETE, _ANN_DELETE); ?> |
							<?php echo CKunenaLink::GetAnnouncementLink($kunena_config, 'add',NULL, _ANN_ADD, _ANN_ADD); ?> |
							<?php echo CKunenaLink::GetAnnouncementLink($kunena_config, 'show', NULL, _ANN_CPANEL, _ANN_CPANEL); ?>
                        </th>
                    </tr>

            <?php
                }
            ?>

                <tr class = "ksectiontableentry2">
                    <td class = "td-1 km" align="left">
                        <?php
                        if ($annshowdate > 0) {
                        ?>

                            <div class = "anncreated">
<?php echo CKunenaTimeformat::showDate($ann->created, 'date_today'); ?>
                            </div>

                        <?php
                            }
                        ?>

                        <div class = "anndesc">
<?php echo $annsdescription; ?>

<?php
if (!empty($anndescription)) {
?>

    &nbsp;&nbsp;&nbsp;<?php echo CKunenaLink::GetAnnouncementLink($kunena_config, 'read', $annID, _ANN_READMORE, _ANN_READMORE,'follow'); ?>

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
