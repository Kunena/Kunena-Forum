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

if ($this->config->avposition == 'top') : ?>

<table <?php echo $this->class ?>>
	<thead>
		<tr class="ksth">
			<th colspan="2" class="view-th ksectiontableheader">
				<a name="<?php echo $this->id ?>"></a>
				<?php echo $this->numLink ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td valign="top" class="kunena-profile-top">
				<?php $this->displayProfile() ?>
			</td>
		</tr>
		<tr>
			<td class="kunena-message-top">
				<?php $this->displayContents() ?>
			</td>
		</tr>
		<tr>
			<td class="buttonbar-top">
				<?php $this->displayActions() ?>
			</td>
		</tr>
	</tbody>
</table>

<?php elseif ($this->config->avposition == 'bottom') : ?>

<table <?php echo $this->class ?>>
	<thead>
		<tr class="ksth">
			<th colspan="2" class="view-th ksectiontableheader">
				<a name="<?php echo $this->id ?>"></a>
				<?php echo $this->numLink ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="kunena-message-bottom">
				<?php $this->displayContents() ?>
			</td>
		</tr>
		<tr>
			<td class="buttonbar-bottom">
				<?php $this->displayActions() ?>
			</td>
		</tr>
		<tr>
			<td valign="top" class="kunena-profile-bottom">
				<?php $this->displayProfile() ?>
			</td>
		</tr>
	</tbody>
</table>

<?php elseif ($this->config->avposition == 'left') : ?>

<table <?php echo $this->class ?>>
	<thead>
		<tr class="ksth">
			<th colspan="2" class="view-th ksectiontableheader">
				<a name="<?php echo $this->id ?>"></a>
				<?php echo $this->numLink ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td rowspan="2" valign="top" class="kunena-profile-left">
				<?php $this->displayProfile() ?>
			</td>
			<td class="kunena-message-left">
				<?php $this->displayContents() ?>
			</td>
		</tr>
		<tr>
			<td class="buttonbar-left">
				<?php $this->displayActions() ?>
			</td>
		</tr>
	</tbody>
</table>

<?php else : ?>

<table <?php echo $this->class ?>>
	<thead>
		<tr class="ksth">
			<th colspan="2" class="view-th ksectiontableheader">
				<a name="<?php echo $this->id ?>"></a>
				<?php echo $this->numLink ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="kunena-message-right">
				<?php $this->displayContents() ?>
			</td>
			<td rowspan="2" class="kunena-profile-right">
				<?php $this->displayProfile() ?>
			</td>
		</tr>
		<tr>
			<td class="buttonbar-right">
				<?php $this->displayActions() ?>
			</td>
		</tr>
	</tbody>
</table>

<?php endif ?>

<!-- Begin: Message Module Position -->
<?php CKunenaTools::showModulePosition('kunena_msg_' . $this->mmm) ?>
<!-- Finish: Message Module Position -->