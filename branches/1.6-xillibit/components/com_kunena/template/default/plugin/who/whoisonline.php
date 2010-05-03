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
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
    <table class = "kblocktable" id ="kwhoisonline"  border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th class="left">
                    <div class = "ktitle_cover km">
                        <?php
                        echo CKunenaLink::GetWhoIsOnlineLink($who_name, 'ktitle kl' );?>
                    </div>
                   <div class="fltrt">
						<span id="kwhoisonline_status"><a class="ktoggler close" rel="whoisonline_tbody"></a></span>
					</div>
                    <!-- <img id = "BoxSwitch_whoisonline__whoisonline_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/> -->
                </th>

            </tr>
        </thead>

        <tbody id = "whoisonline_tbody">
            <tr class = "ksectiontableentry1">
                <td class = "td-1 km" align="left">
                    <?php
                    foreach ($users as $user)
                    {
                         if ( $user->showOnline ){ ?>

                            <span title=" <?php echo CKunenaTimeformat::showDate ( $user->time, 'config_post_dateformat' ); ?> "><?php echo CKunenaLink::GetProfileLink ( $user->id, $user->username ); ?></span> &nbsp;

                		  <?php
                         }
                    }
                    if (CKunenaTools::isModerator($this->my->id)){

					 ?>

                    <br /><span class="ks"><?php echo JText::_('COM_KUNENA_HIDDEN_USERS'); ?>: </span>

                    <?php

					}

                    foreach ($users as $user)
                    {
                    	if ( CKunenaTools::isModerator($this->my->id) && $user->showOnline){ ?>

                            <span title=" <?php echo CKunenaTimeformat::showDate ( $user->time, 'config_post_dateformat' ); ?> "><?php echo CKunenaLink::GetProfileLink ( $user->id, $user->username ); ?></span> &nbsp;

                		  <?php
                    	  }
                    }
                    ?>

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
