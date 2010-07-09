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
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
//TODO: Split this file
if ($this->params->get('avatarPosition') == 'top') : ?>

<table <?php echo $this->class ?>>
	<thead>
		<tr class="ksth">
			<th colspan="2">
				<span class="kmsgdate" title="<?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat_hover') ?>">
					<?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat') ?>
				</span>
				<a name="<?php echo intval($this->id) ?>"></a>
				<?php echo $this->numLink ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td valign="top" class="kprofile-top">
				<?php $this->displayProfile() ?>
			</td>
		</tr>
		<tr>
			<td class="kmessage-top">
				<?php $this->displayContents() ?>
			</td>
		</tr>
		<tr>
			<td class="kbuttonbar-top">
				<?php $this->displayActions() ?>
				<?php $this->displayThankyou() ?>
			</td>
		</tr>
	</tbody>
</table>

<?php elseif ($this->params->get('avatarPosition') == 'bottom') : ?>

<table <?php echo $this->class ?>>
	<thead>
		<tr class="ksth">
			<th colspan="2">
				<span class="kmsgdate" title="<?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat_hover') ?>">
					<?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat') ?>
				</span>
				<a name="<?php echo intval($this->id) ?>"></a>
				<?php echo $this->numLink ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="kmessage-bottom">
				<?php $this->displayContents() ?>
			</td>
		</tr>
		<tr>
			<td class="kbuttonbar-bottom">
				<?php $this->displayActions() ?>
				<?php $this->displayThankyou() ?>
			</td>
		</tr>
		<tr>
			<td valign="top" class="kprofile-bottom">
				<?php $this->displayProfile() ?>
			</td>
		</tr>
	</tbody>
</table>

<?php elseif ($this->params->get('avatarPosition') == 'left') : ?>

<table <?php echo $this->class ?>>
	<thead>
		<tr class="ksth">
			<th colspan="2">
				<span class="kmsgdate" title="<?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat_hover') ?>">
					<?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat') ?>
				</span>
				<a name="<?php echo intval($this->id) ?>"></a>
				<?php echo $this->numLink ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td rowspan="2" valign="top" class="kprofile-left">
				<?php $this->displayProfile() ?>
			</td>
			<td class="kmessage-left">
				<?php $this->displayContents() ?>
			</td>
		</tr>
		<tr>
			<td class="kbuttonbar-left">
				<?php $this->displayActions() ?>
				<?php $this->displayThankyou() ?>
			</td>
		</tr>
	</tbody>
</table>

<?php else : ?>

<table <?php echo $this->class ?>>
	<thead>
		<tr class="ksth">
			<th colspan="2">
				<span class="kmsgdate" title="<?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat_hover') ?>">
					<?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat') ?>
				</span>
				<a name="<?php echo intval($this->id) ?>"></a>
				<?php echo $this->numLink ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="kmessage-right">
				<?php $this->displayContents() ?>
			</td>
			<td rowspan="2" class="kprofile-right">
				<?php $this->displayProfile() ?>
			</td>
		</tr>
		<tr>
			<td class="kbuttonbar-right">
				<?php $this->displayActions() ?>
				<?php $this->displayThankyou() ?>
			</td>
		</tr>
	</tbody>
</table>

<?php endif ?>

<!-- Begin: Message Module Position -->
	<?php CKunenaTools::showModulePosition('kunena_msg_' . $this->mmm) ?>
<!-- Finish: Message Module Position -->