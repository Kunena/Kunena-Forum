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
**/

defined( '_JEXEC' ) or die('Restricted access');
?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "fb_blocktable " id="fb_userinfo" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th>
                <div class = "fb_title_cover fbm">
                    <span class="fb_title fbl"> <?php echo $msg_username; ?>  <?php echo _KUNENA_USERPROFILE_PROFILE; ?></span>
                </div>
            </th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td class = "<?php echo $boardclass; ?>profileinfo" align="center">
                <div class = "fb-usrprofile-misc">
                    <span class = "view-username"> <?php echo $msg_username; ?></span> <?php  if ( $fbConfig->userlist_usertype ) { ?><span class = "msgusertype">(<?php echo $msg_usertype; ?>)</span><?php } ?>

                    <br/> <?php echo $msg_avatar; ?>

                <?php /*
                    $gr_title=getFBGroupName($lists["userid"]);

                    if ($gr_title->id > 1)
                        {
                ?>

                        <span class = "view-group_<?php echo $gr_title->id;?>"> <?php echo $gr_title->title; ?></span>

                <?php
                        }
*/                ?>

                        <div class = "viewcover">
                        <?php
                            if (isset($msg_userrank))
                                echo $msg_userrank;
                        ?>
                        </div>

                        <div class = "viewcover">
                        <?php
                            if (isset($msg_userrankimg))
                                echo $msg_userrankimg;
                        ?>
                        </div>
						<div class="viewcover">
						<?php echo _KUNENA_USERPROFILE_PROFILEHITS; ?>:
						<?php echo $msg_userhits; ?>
						</div>
                    <?php
                        if (isset($msg_posts))
                            echo $msg_posts;
                    ?>

                    <?php
                        if (isset($myGraph))
                            $myGraph->BarGraphHoriz();
                    ?>

                    <?php
                        if (isset($msg_icq))
                            echo $msg_icq;
                    ?>

                        <?php echo $msg_online; ?>

                    <?php
                        if (isset($msg_pms))
                            echo $msg_pms;
                    ?>

                    <?php
                        if (isset($msg_icq))
                            echo $msg_icq;
                    ?>

                    <?php
                        if (isset($msg_msn))
                            echo $msg_msn;
                    ?>

                    <?php
                        if (isset($msg_yahoo))
                            echo $msg_yahoo;
                    ?>

                    <?php
                        if (isset($msg_regdate))
                            echo "<br />Join Date: " . strip_tags($msg_date) . "<br />";
                    ?>

                    <?php
                        if (isset($msg_loc))
                            echo "Location: $msg_loc<br />";
                    ?>
                </div>

                <span class = "msgkarma">

            <?php
                if (isset($msg_karma))
                    echo $msg_karma . '&nbsp;&nbsp;' . $msg_karmaplus . ' ' . $msg_karmaminus;
                else
                    echo '&nbsp;';
            ?></span>
            </td>
        </tr>
    </tbody>
</table>

</div>
</div>
</div>
</div>
</div>
