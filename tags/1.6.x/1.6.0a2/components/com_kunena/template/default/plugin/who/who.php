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

if ($this->config->showwhoisonline)
{
	$users=$this->getUsersList();
?>
<div class="k-bt-cvr1">
<div class="k-bt-cvr2">
<div class="k-bt-cvr3">
<div class="k-bt-cvr4">
<div class="k_bt_cvr5">
    <table class = "kblocktable " id="kwhoispage" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th colspan = "4">
                   <div class = "ktitle-cover">
                        <span class="ktitle"><?php echo $this->app->getCfg('sitename'); ?> - <?php echo JText::_('COM_KUNENA_WHO_WHOIS_ONLINE'); ?></span>
                    </div>
            </tr>
        </thead>

        <tbody>
            <tr class = "ksth">
                <th class = "th-1 ksectiontableheader">
<?php echo JText::_('COM_KUNENA_WHO_ONLINE_USER'); ?>

                </th>

                <th class = "th-2 ksectiontableheader"><?php echo JText::_('COM_KUNENA_WHO_ONLINE_TIME'); ?>
                </th>

                <th class = "th-3 ksectiontableheader"><?php echo JText::_('COM_KUNENA_WHO_ONLINE_FUNC'); ?>
                </th>
            </tr>

            <?php
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
                    $user->username = JText::_('COM_KUNENA_GUEST');
                } else if ($user->showOnline < 1 && !CKunenaTools::isModerator($this->my->id)) {
                	continue;
                }
            ?>

                <tr class = "k<?php echo $tabclass[$k];?>">
                    <td class = "td-1">
                        <div style = "float: right; width: 14ex;">
                        </div>

                        <span>

                        <?php
                        if ($user->userid == 0) {
                            echo $user->username;
                        } else {
							echo CKunenaLink::GetProfileLink($user->userid, $user->username);
                        }
                        ?>

                        </span>

                        <?php
                        if (CKunenaTools::isAdmin($this->my->id) && $this->config->hide_ip) {
                        ?>

                            (<?php echo $user->userip; ?>)

                        <?php
                        } elseif (CKunenaTools::isModerator($this->my->id) && !$this->config->hide_ip) {
                       	?>
							(<?php echo $user->userip; ?>)
						<?php
                        }
                        ?>
                    </td>
                    <td class = "td-2" nowrap = "nowrap"><?php echo ' <span title="' . CKunenaTimeformat::showDate ( $user->time, 'config_post_dateformat_hover' ) . '">' . CKunenaTimeformat::showDate ( $user->time, 'config_post_dateformat' ) . '</span>'; ?>
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
        <h1><?php echo JText::_('COM_KUNENA_WHO_IS_ONLINE_NOT_ACTIVE'); ?></h1>
    </div>

<?php
}
?>
