<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
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
		<?php if ($this->topicButtons->get('reply') || $this->topicButtons->get('subscribe') || $this->topicButtons->get('favorite') ) : ?>
			<div class="kmessage-buttons-row">
			<?php echo $this->topicButtons->get('reply') ?>
			<?php echo $this->topicButtons->get('subscribe') ?>
			<?php echo $this->topicButtons->get('favorite') ?>
			</div>
		<?php endif ?>
		<?php if ($this->topicButtons->get('delete') || $this->topicButtons->get('moderate') || $this->topicButtons->get('sticky') || $this->topicButtons->get('lock')) : ?>
			<div class="kmessage-buttons-row">
			<?php echo $this->topicButtons->get('delete') ?>
			<?php echo $this->topicButtons->get('moderate') ?>
			<?php echo $this->topicButtons->get('sticky') ?>
			<?php echo $this->topicButtons->get('lock') ?>
			</div>
		<?php endif ?>
		</td>
		<?php if ($this->topicButtons->get('flat') || $this->topicButtons->get('threaded') || $this->topicButtons->get('indented')) : ?>
		<td class="klist-actions-forum">
			<div class="kmessage-buttons-row">
			<?php echo $this->topicButtons->get('flat') ?>
			<?php echo $this->topicButtons->get('threaded') ?>
			<?php echo $this->topicButtons->get('indented') ?>
			</div>
		</td>
		<?php endif ?>

		<td class="klist-pages-all">
			<?php echo $this->pagination; ?>
		</td>
	</tr>
</table>
<!-- F: Topic Actions -->