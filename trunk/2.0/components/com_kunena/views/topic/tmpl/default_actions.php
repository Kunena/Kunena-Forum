<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<!-- B: Topic Actions -->
<table class="klist-actions">
	<tr>
		<td class="klist-actions-goto">
			<?php echo $this->goto ?>
		</td>
		<td class="klist-actions-forum">
		<?php if ($this->topic_reply || $this->topic_subscribe || $this->topic_favorite ) : ?>
			<div class="kmessage-buttons-row">
			<?php echo $this->topic_reply ?>
			<?php echo $this->topic_subscribe ?>
			<?php echo $this->topic_favorite ?>
			</div>
		<?php endif ?>
		<?php if ($this->topic_delete || $this->topic_sticky || $this->topic_lock) : ?>
			<div class="kmessage-buttons-row">
			<?php echo $this->topic_delete ?>
			<?php echo $this->topic_sticky ?>
			<?php echo $this->topic_lock ?>
			</div>
		<?php endif ?>
		</td>

		<td class="klist-actions-forum">
		<?php if (isset ( $this->topic_new )) : ?>
			<div class="kmessage-buttons-row">
			<?php echo $this->topic_new; ?>
			</div>
		<?php endif ?>
		<?php if (isset ( $this->topic_moderate )) : ?>
			<div class="kmessage-buttons-row">
			<?php echo $this->topic_moderate; ?>
			</div>
		<?php endif ?>
		<?php if (isset ( $this->topic_merge )) : ?>
			<div class="kmessage-buttons-row">
				<?php echo $this->topic_merge; ?>
			</div>
		<?php endif ?>
		</td>

		<td class="klist-pages-all">
			<?php echo $this->pagination; ?>
		</td>
	</tr>
</table>
<!-- F: Topic Actions -->