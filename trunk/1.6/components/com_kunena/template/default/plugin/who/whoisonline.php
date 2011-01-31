<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All rights reserved.
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
	$who_name = 	$this->getTitleWho ($totaluser, $totalguests);
?>

<div class="kblock kwhoisonline">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kwhoisonline"></a></span>
		<h2><span><?php
				//FIXME: remove link to who.php page
				$who_online = JText::_('COM_KUNENA_WHO_WHOIS_ONLINE');
				if (CKunenaTools::isModerator($this->my->id)) {
					echo CKunenaLink::GetWhoIsOnlineLink($who_online,''); }
				else {
					echo '<span class="ktitle km">'.$who_online.'</span>';
				}
				?>
			</span>
		</h2>
	</div>
	<div class="kcontainer" id="kwhoisonline">
		<div class="kbody">
	<table class = "kblocktable">
		<tr class = "krow2">
			<td class = "kcol-first">
				<div class="kwhoicon"></div>
			</td>
			<td class = "kcol-mid km">
				<div class="kwhoonline kwho-total ks">
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
					$onlineList = array();
					$hiddenList = array();
					foreach ($users as $user) {
						if ( $user->showOnline > 0 ) {
							$onlineList[] = CKunenaLink::GetProfileLink ( intval($user->id) );
						} else {
							$hiddenList[] = CKunenaLink::GetProfileLink ( intval($user->id) );
						}
					}
					echo implode (', &nbsp;', $onlineList);
					if (!empty($hiddenList) && CKunenaTools::isModerator($this->my->id)) : ?>
						<br />
						<span class="khidden-ktitle ks"><?php echo JText::_('COM_KUNENA_HIDDEN_USERS'); ?>: </span>
						<br />
						<?php echo implode (', &nbsp;', $hiddenList); ?>
					<?php endif; ?>
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
</table>
</div>
</div>
</div>
<?php } ?>