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
	$users = 		$this->getActiveUsersList();
	$totaluser = 	$this->getTotalRegistredUsers ();
	$totalguests = 	$this->getTotalGuestUsers ();
	$who_name = 	$this->getTitleWho ($totaluser,$totalguests);
?>

<!-- WHOIS ONLINE -->
<div class="k-bt-cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
    <table class = "kblocktable" id ="kwhoisonline"  border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th class="left" colspan="3"><div class = "ktitle-cover km">
							<?php
							$who_online = JText::_('COM_KUNENA_WHO_WHOIS_ONLINE');
							if (CKunenaTools::isModerator($this->my->id)) {
                            echo CKunenaLink::GetWhoIsOnlineLink($who_online,'ktitle kl'); }
							else {
							echo '<span class="ktitle kl">'.$who_online.'</span>';
							}
							?></div>
                   <div class="fltrt">
						<span id="kwhoisonline-status"><a class="ktoggler close" rel="kwhoisonline-tbody"></a></span>
					</div>
                </th>

            </tr>
        </thead>

        <tbody id = "kwhoisonline-tbody">
            <tr class = "ksectiontableentry1">
               <td class = "td-1" width="1%">
					<div class="kwhoicon"></div>
                </td>
                <td class = "td-1 km" align="left">

                    <div class="kwhoonline ks">
                        <?php
							//$totalhiden = '';
							$totalusers = ($totaluser + $totalguests);
						    $who_name = JText::_('COM_KUNENA_WHO_TOTAL') ;
						    $who_name .= '<strong>&nbsp;'.$totalusers.'</strong>&nbsp;';
                            if($totalusers==1) { $who_name .= JText::_('COM_KUNENA_WHO_USER').'&nbsp;'; } else { $who_name .= JText::_('COM_KUNENA_WHO_USERS').'&nbsp;'; }
						    $who_name .= JText::_('COM_KUNENA_WHO_TOTAL_USERS_ONLINE').'&nbsp;&nbsp;::&nbsp;&nbsp;';
						    $who_name .= '<strong>'.$totaluser.' </strong>';
                            if($totaluser==1) { $who_name .= JText::_('COM_KUNENA_WHO_ONLINE_MEMBER').'&nbsp;'; } else { $who_name .= JText::_('COM_KUNENA_WHO_ONLINE_MEMBERS').'&nbsp;'; }
                            $who_name .= JText::_('COM_KUNENA_WHO_AND');
                            $who_name .= '<strong> '. $totalguests.' </strong>';
                            if($totalguests==1) { $who_name .= JText::_('COM_KUNENA_WHO_ONLINE_GUEST').'&nbsp;'; } else { $who_name .= JText::_('COM_KUNENA_WHO_ONLINE_GUESTS').'&nbsp;'; }
							echo $who_name;
						?>
                    </div>
					<div>
                        <?php
							foreach ($users as $user) {
							if ( $user->showOnline > 0 ){
							echo CKunenaLink::GetProfileLink ( $user->id, $user->username ); ?> &nbsp;
							<?php } }
							if (CKunenaTools::isModerator($this->my->id)) { ?>
							<br /> <span class="ks"><?php echo JText::_('COM_KUNENA_HIDDEN_USERS'); ?>: </span><br />
							<?php }
							foreach ($users as $user) {
							if ( CKunenaTools::isModerator($this->my->id) && $user->showOnline =='0' ) {
							echo CKunenaLink::GetProfileLink ( $user->id, $user->username ); ;?> &nbsp;
							<?php } }
						?>
					</div>
                    <div class="kwholegend ks">
						<span><?php echo JText::_('COM_KUNENA_LEGEND'); ?> :: </span>&nbsp;
						<span class = "kwho-admin" title = "<?php echo JText::_('COM_KUNENA_COLOR_ADMINISTRATOR'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_ADMINISTRATOR'); ?></span>,&nbsp;
						<span class = "kwho-globalmoderator" title = "<?php echo JText::_('COM_KUNENA_COLOR_GLOBAL_MODERATOR'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_GLOBAL_MODERATOR'); ?></span>,&nbsp;
						<span class = "kwho-moderator" title = "<?php echo JText::_('COM_KUNENA_COLOR_MODERATOR'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_MODERATOR'); ?></span>,&nbsp;
						<span class = "kwho-user" title = "<?php echo JText::_('COM_KUNENA_COLOR_USER'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_USER'); ?></span>,&nbsp;
						<span class = "kwho-guest" title = "<?php echo JText::_('COM_KUNENA_COLOR_GUEST'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_GUEST'); ?></span>
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
<!-- WHOIS ONLINE -->

<?php
}
?>
