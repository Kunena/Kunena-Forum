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
defined ( '_JEXEC' ) or die ();
?>

<?php
$this->displayAnnouncement ();
CKunenaTools::showModulePosition ( 'kunena_announcement' );
?>
<!-- B: List Actions -->
<table class="klist_actions">
	<tr>
		<td class="klist_actions_info_all"><strong><?php
		echo $this->total?></strong> <?php
		echo _KUNENA_DISCUSSIONS?>
		</td>
		<?php
		if ($this->func != 'mylatest' && $this->func != 'noreplies') :
			?>
		<td class="klist_times_all"><select class="inputboxusl"
			onchange="document.location.href=this.options[this.selectedIndex].value;"
			size="1" name="select">
			<?php
			if ($this->my->id) :
				?>
		<option <?php
				if ($this->show_list_time == '0') :
					?>
				selected="selected"
				<?php
				endif;
				?>
				value="<?php
				echo CKunenaLink::GetShowLatestThreadsURL(0)?>"><?php
				echo _SHOW_LASTVISIT?></option>

			<?php endif;
			?>
									  <option <?php
			if ($this->show_list_time == '4') :
				?>
				selected="selected"
			<?php
			endif;
			?>
				value="<?php
			echo CKunenaLink::GetShowLatestThreadsURL(4);
			?>"><?php
			echo _SHOW_4_HOURS?></option>
			<option <?php
			if ($this->show_list_time == '8') :
				?>
				selected="selected"
			<?php
			endif;
			?>
				value="<?php
			echo CKunenaLink::GetShowLatestThreadsURL(8);
			?>"><?php
			echo _SHOW_8_HOURS?></option>
			<option <?php
			if ($this->show_list_time == '12') :
				?>
				selected="selected"
			<?php
			endif;
			?>
				value="<?php
			echo CKunenaLink::GetShowLatestThreadsURL(12);
			?>"><?php
			echo _SHOW_12_HOURS?></option>
			<option <?php
			if ($this->show_list_time == '24') :
				?>
				selected="selected"
			<?php
			endif;
			?>
				value="<?php
			echo CKunenaLink::GetShowLatestThreadsURL(24);
			?>"><?php
			echo _SHOW_24_HOURS?></option>
			<option <?php
			if ($this->show_list_time == '48') :
				?>
				selected="selected"
			<?php
			endif;
			?>
				value="<?php
			echo CKunenaLink::GetShowLatestThreadsURL(48);
			?>"><?php
			echo _SHOW_48_HOURS?></option>
			<option <?php
			if ($this->show_list_time == '168') :
				?>
				selected="selected"
			<?php
			endif;
			?>
				value="<?php
			echo CKunenaLink::GetShowLatestThreadsURL(168);
			?>"><?php
			echo _SHOW_WEEK?></option>
			<option <?php
			if ($this->show_list_time == '720') :
				?>
				selected="selected"
			<?php
			endif;
			?>
				value="<?php
			echo CKunenaLink::GetShowLatestThreadsURL(720);
			?>"><?php
			echo _SHOW_MONTH?></option>
			<option <?php
			if ($this->show_list_time == '8760') :
				?>
				selected="selected"
			<?php
			endif;
			?>
				value="<?php
			echo CKunenaLink::GetShowLatestThreadsURL(8760)?>"><?php
			echo _SHOW_YEAR?></option>
		</select></td>

		<?php
		endif;
		?>
<td class="klist_jump_all">

<?php
$this->displayForumJump ();
?>

</td>

<?php
//pagination 1
if (count ( $this->messages ) > 0) :
	echo '<td class="klist_pages_all">';
	$maxpages = 5 - 2; // odd number here (# - 2)
	echo $pagination = $this->getPagination ( $this->func, $this->show_list_time, $this->page, $this->totalpages, $maxpages );
	echo '</td>';

endif;
?>

		</tr>
</table>
<!-- F: List Actions -->
<?php
if (count ( $this->threadids ) > 0) :
	$this->displayFlat ();
	?>
<!-- B: List Actions -->
<table class="klist_actions">
	<tr>
		<td class="klist_actions_info_all"><strong><?php
	echo $this->total?></strong> <?php
	echo _KUNENA_DISCUSSIONS?>
			</td>

			<?php
	//pagination 1
	if (count ( $this->messages ) > 0) :
		echo '<td class="klist_pages_all nowrap">';
		echo $pagination;
		echo '</td>';

	endif;
	?>
		</tr>
</table>
<!-- F: List Actions -->

<?php
endif;
?>
<div class="clr"></div>
<?php
$this->displayStats ();
$this->displayWhoIsOnline ();
?>
