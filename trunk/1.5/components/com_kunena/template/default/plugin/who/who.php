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

$app =& JFactory::getApplication();
$fbConfig =& CKunenaConfig::getInstance();
?>

<?php
if ($fbConfig->showwhoisonline > 0)
{
?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
    <table class = "fb_blocktable " id="fb_whoispage" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th colspan = "4">
                   <div class = "fb_title_cover">
                        <span class="fb_title"><?php echo $app->getCfg('sitename'); ?> - <?php echo _WHO_WHOIS_ONLINE; ?></span>
                    </div>
            </tr>
        </thead>

        <tbody>
            <tr class = "fb_sth">
                <th class = "th-1 <?php echo $boardclass; ?>sectiontableheader">
<?php echo _WHO_ONLINE_USER; ?>

                </td>

                <th class = "th-2 <?php echo $boardclass; ?>sectiontableheader"><?php echo _WHO_ONLINE_TIME; ?>
                </th>

                <th class = "th-3 <?php echo $boardclass; ?>sectiontableheader"><?php echo _WHO_ONLINE_FUNC; ?>
                </th>
            </tr>

            <?php
            $query = "SELECT w.*, u.username FROM #__fb_whoisonline AS w" . "\n LEFT JOIN #__users AS u ON u.id=w.userid ORDER BY w.time DESC";
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
                }

                $time = date("H:i:s", $user->time);
            ?>

                <tr class = "<?php echo $boardclass.''.$tabclass[$k] ;?>">
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
				echo CKunenaLink::GetProfileLink($fbConfig, $user->userid, $user->username);
                        }
                        ?>

                        </span>

                        <?php
                        if ($kunena_my->gid > 1)
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
