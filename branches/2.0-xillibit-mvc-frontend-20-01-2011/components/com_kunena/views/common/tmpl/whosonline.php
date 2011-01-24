<?php
/**
* @version $Id: whoisonline.php 3901 2010-11-15 14:14:02Z mahagr $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
*
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

?>
<div class="kblock kwhoisonline">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kwhoisonline"></a></span>
		<h2><span><?php
					echo '<span class="ktitle km">'.$who_online.'</span>';
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
					$totalusers = ($this->totaluser + $this->totalguests);
					$this->who_name = JText::_('COM_KUNENA_WHO_TOTAL') ;
					$this->who_name .= '<strong>&nbsp;'.$totalusers.'</strong>&nbsp;';
					if($totalusers==1) { $this->who_name .= JText::_('COM_KUNENA_WHO_USER').'&nbsp;'; } else { $this->who_name .= JText::_('COM_KUNENA_WHO_USERS').'&nbsp;'; }
					$this->who_name .= JText::_('COM_KUNENA_WHO_TOTAL_USERS_ONLINE').'&nbsp;&nbsp;::&nbsp;&nbsp;';
					$this->who_name .= '<strong>'.$this->totaluser.' </strong>';
					if($this->totaluser==1) { $this->who_name .= JText::_('COM_KUNENA_WHO_ONLINE_MEMBER').'&nbsp;'; } else { $this->who_name .= JText::_('COM_KUNENA_WHO_ONLINE_MEMBERS').'&nbsp;'; }
					$this->who_name .= JText::_('COM_KUNENA_WHO_AND');
					$this->who_name .= '<strong> '. $this->totalguests.' </strong>';
					if($totalguests==1) { $this->who_name .= JText::_('COM_KUNENA_WHO_ONLINE_GUEST').'&nbsp;'; } else { $this->who_name .= JText::_('COM_KUNENA_WHO_ONLINE_GUESTS').'&nbsp;'; }
					echo $this->who_name;
					?>
				</div>
				<div>
					<?php
					$onlineList = array();
					$hiddenList = array();
					foreach ($this->users as $user) {
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
