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
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

$kunena_db = &JFactory::getDBO();
$kunena_app =& JFactory::getApplication();
$kunena_config =& CKunenaConfig::getInstance();
?>

<?php
if ($kunena_config->showwhoisonline > 0)
{
?>
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr1">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr2">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr3">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr4">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr5">
    <table class = "fb_blocktable " id="fb_whoispage" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th colspan = "4">
                   <div class = "fb_title_cover">
                        <span class="fb_title"><?php echo $kunena_app->getCfg('sitename'); ?> - <?php echo _WHO_WHOIS_ONLINE; ?></span>
                    </div>
            </tr>
        </thead>

        <tbody>
            <tr class = "fb_sth">
                <th class = "th-1 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader">
<?php echo _WHO_ONLINE_USER; ?>

                </th>

                <th class = "th-2 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader"><?php echo _WHO_ONLINE_TIME; ?>
                </th>

                <th class = "th-3 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader"><?php echo _WHO_ONLINE_FUNC; ?>
                </th>
            </tr>

            <?php
            $query = "SELECT w.*, u.id, u.username, f.showOnline FROM #__fb_whoisonline AS w LEFT JOIN #__users AS u ON u.id=w.userid LEFT JOIN #__fb_users AS f ON u.id=f.userid ORDER BY w.time DESC";
            $kunena_db->setQuery($query);
            $users = $kunena_db->loadObjectList();
            $k = 0; //for alternating rows
            $tabclass = array
            (
                "sectiontableentry1",
                "sectiontableentry2"
            );

            foreach ($users as $user)
            {
                $k = 1 - $k;

                if ($user->userid == 0) {
                    $user->username = _KUNENA_GUEST;
                } else if ($user->showOnline < 1 && !$kunena_is_moderator) {
                	continue;
                }

                $time = date("H:i:s", $user->time + $kunena_config->board_ofset*3600);
            ?>

                <tr class = "<?php echo KUNENA_BOARD_CLASS.''.$tabclass[$k] ;?>">
                    <td class = "td-1">
                        <div style = "float: right; width: 14ex;">
                        </div>

                        <span>

                        <?php
                        if ($user->userid == 0) {
                            echo $user->username;
                        }
                        else
                        {
				echo CKunenaLink::GetProfileLink($kunena_config, $user->userid, $user->username);
                        }
                        ?>

                        </span>

                        <?php
                        if ($kunena_is_moderator)
                        {
                        ?>

                            (<?php echo $user->userip; ?>)

                        <?php
                        }
                        ?>
                    </td>

                    <td class = "td-2" nowrap = "nowrap"><?php echo $time; ?>
                    </td>

                    <td class = "td-3">
                        <strong><a href = "<?php echo $user->link;?>" target = "_blank"><?php echo $user->what; ?></a></strong>
                    </td>
                </tr>

        <?php
            }
        ?>
    </table>
</div>
</div>
</div>
</div>
</div>
<?php
}
else
{
?>

    <div style = "border:1px solid #FF6600; background: #FF9966; padding:20px; text-align:center;">
        <h1>Not Active</h1>
    </div>

<?php
}
?>
