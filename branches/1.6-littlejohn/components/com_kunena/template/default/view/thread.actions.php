<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

global $kunena_icons;
?>

<!-- B: List Actions -->
<table class="klist_actions">
	<tr>
		<td class="klist_actions_goto"><a name="forumtop"></a>
			<?php
			echo CKunenaLink::GetSamePageAnkerLink ( 'forumbottom', isset ( $kunena_icons ['bottomarrow'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['bottomarrow'] . '" border="0" alt="' . _GEN_GOTOBOTTOM . '" title="' . _GEN_GOTOBOTTOM . '"/>' : _GEN_GOTOBOTTOM );
			?>
		</td>
		<?php
		if (CKunenaTools::isModerator ( $this->my->id, $this->catid ) || isset ( $this->thread_reply ) || isset ( $this->thread_subscribe ) || isset ( $this->thread_favorite )) :
			?>
		<td class="klist_actions_forum">
		<div class="kmessage_buttons_row">
			<?php
			if (isset ( $this->thread_reply ))
				echo $this->thread_reply;
			if (isset ( $this->thread_subscribe ))
				echo ' ' . $this->thread_subscribe;
			if (isset ( $this->thread_favorite ))
				echo ' ' . $this->thread_favorite;
			?>
			</div>
			<?php
			if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) :
				?>
			<div class="kmessage_buttons_row">
			<?php
				echo $this->thread_delete;
				echo ' ' . $this->thread_move;
				echo ' ' . $this->thread_sticky;
				echo ' ' . $this->thread_lock;
				?>
			</div>

			<?php endif;
			?>
		</td>

		<?php endif;
		?>
		<td class="klist_actions_forum">
		<?php
		if (isset ( $this->thread_new )) :
			?>
			<div class="kmessage_buttons_row">
			<?php
			echo $this->thread_new;
			?>
			</div>

		<?php endif;
		if (isset ( $this->thread_merge )) :
			?>
			<div class="kmessage_buttons_row">
			<?php
			echo $this->thread_merge;
			?>
			</div>

		<?php endif;
		?>
		</td>

		<td class="klist_pages_all nowrap">
		<?php
		echo $this->pagination;
		?>
		</td>
	</tr>
</table>
<!-- F: List Actions -->
