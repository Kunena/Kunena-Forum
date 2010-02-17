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

global $kunena_icons;

?>
<!-- Pathway -->
<?php
$this->displayPathway ();
?>
<!-- / Pathway -->

<?php
$this->displaySubCategories ();
?>

<?php
if ($this->objCatInfo->headerdesc) {
	?>
<table
	class="kforum-headerdesc<?php
	echo isset ( $this->objCatInfo->class_sfx ) ? ' kforum-headerdesc' . $this->objCatInfo->class_sfx : '';
	?>"
	>
	<tr>
		<td><?php
	echo $this->headerdesc;
	?>
		</td>
	</tr>
</table>
<?php
}
?>

<!-- B: List Actions -->

<table class="klist_actions">
	<tr>
		<td class="klist_actions_goto"><?php
		//go to bottom
		echo '<a name="forumtop" /> ';
		echo CKunenaLink::GetSamePageAnkerLink ( 'forumbottom', isset ( $kunena_icons ['bottomarrow'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['bottomarrow'] . '" border="0" alt="' . JText::_('COM_KUNENA_GEN_GOTOBOTTOM') . '" title="' . JText::_('COM_KUNENA_GEN_GOTOBOTTOM') . '"/>' : JText::_('COM_KUNENA_GEN_GOTOBOTTOM') );
		?>
		</td>
		<td class="klist_actions_forum" width="100%">
		<?php
		if (isset ( $this->forum_new ) || isset ( $this->forum_markread ) || isset ( $this->thread_subscribecat )) {
			echo '<div class="kmessage_buttons_row">';
			if (isset ( $this->forum_new ))
				echo $this->forum_new;
			if (isset ( $this->forum_markread ))
				echo ' ' . $this->forum_markread;
			if (isset ( $this->thread_subscribecat ))
				echo ' ' . $this->thread_subscribecat;
			echo '</div>';
		}
		?>
		</td>
		<td class="klist_pages_all nowrap">
		<?php
		//pagination 1
		if (count ( $this->messages ) > 0) {
			$maxpages = 9 - 2; // odd number here (# - 2)
			echo $pagination = $this->getPagination ( $this->catid, $this->page, $this->totalpages, $maxpages );
		}
		?>
		</td>
	</tr>
</table>

<!-- F: List Actions -->

<?php
	$this->displayFlat ();
?>

<!-- B: List Actions Bottom -->

<table class="klist_actions_bottom" >
	<tr>
		<td class="klist_actions_goto"><?php
		//go to top
		echo '<a name="forumbottom" />';
		echo CKunenaLink::GetSamePageAnkerLink ( 'forumtop', isset ( $kunena_icons ['toparrow'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['toparrow'] . '" border="0" alt="' . JText::_('COM_KUNENA_GEN_GOTOTOP') . '" title="' . JText::_('COM_KUNENA_GEN_GOTOTOP') . '"/>' : JText::_('COM_KUNENA_GEN_GOTOTOP') );
		?>

		</td>
		<td class="klist_actions_forum" width="100%"><?php
		if (isset ( $this->forum_new ) || isset ( $this->forum_markread ) || isset ( $this->thread_subscribecat )) {
			echo '<div class="kmessage_buttons_row">';
			if (isset ( $this->forum_new ))
				echo $this->forum_new;
			if (isset ( $this->forum_markread ))
				echo ' ' . $this->forum_markread;
			if (isset ( $this->thread_subscribecat ))
				echo ' ' . $this->thread_subscribecat;
			echo '</div>';
		}
		?>

		</td>
		<td class="klist_pages_all nowrap"><?php
		//pagination 2
		if (count ( $this->messages ) > 0) {
			echo $pagination;
		}
		?>
		</td>
	</tr>
</table>
<?php
echo '<div class = "kforum-pathway-bottom">';
echo $this->kunena_pathway1;
echo '</div>';
?>

<!-- F: List Actions Bottom -->

<!-- B: Category List Bottom -->

<table class="klist_bottom">
	<tr>
		<td class="klist_moderators"><!-- Mod List --> <?php

		if (count ( $this->modslist ) > 0) :
			?>

		<div class="kbox-bottomarea-modlist"><?php
			echo '' . JText::_('COM_KUNENA_GEN_MODERATORS') . ": ";
			foreach ( $this->modslist as $mod ) {
				echo CKunenaLink::GetProfileLink ( $this->config, $mod->userid, $mod->username ) . '&nbsp; ';
			}
			?>
		</div>



		<?php endif;
		?> <!-- /Mod List --></td>
		<td class="klist_categories"><?php
		$this->displayForumJump ();
		?>
		</td>
	</tr>
</table>

<!-- F: Category List Bottom -->
