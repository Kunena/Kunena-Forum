<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div id="topic-buttonbar" class="buttonbar">
	<div class="paginationbar">
		<?php echo $this->getPagination(4) ?>
	</div>
	<ul class="message-buttons">
		<!-- User buttons -->
		<?php if (!empty($this->topic_reply)) : ?><li class="button topic-replytopic"><dd class="buttonbox-hover"><?php echo $this->topic_reply ?></dd></li><?php endif ?>
		<?php if (!empty($this->topic_subscribe)) : ?><li class="button topic-subscribe"><dd class="buttonbox-hover"><?php echo $this->topic_subscribe ?></dd></li><?php endif ?>
		<?php if (!empty($this->topic_favorite)) : ?><li class="button topic-favorite"><dd class="buttonbox-hover"><?php echo $this->topic_favorite ?></dd></li><?php endif ?>
		<!-- Moderator buttons -->
		<?php if (!empty($this->topic_lock)) : ?><li class="button topic-lock"><dd class="buttonbox-hover"><?php echo $this->topic_lock ?></dd></li><?php endif ?>
		<?php if (!empty($this->topic_sticky)) : ?><li class="button topic-sticky"><dd class="buttonbox-hover"><?php echo $this->topic_sticky ?></dd></li><?php endif ?>
		<?php if (!empty($this->topic_moderate)) : ?><li class="button topic-moderatetopic"><dd class="buttonbox-hover"><?php echo $this->topic_moderate ?></dd></li><?php endif ?>
		<?php if (!empty($this->topic_delete)) : ?><li class="button topic-delete"><dd class="buttonbox-hover"><?php echo $this->topic_delete ?></dd></li><?php endif ?>
		<?php if (!empty($this->layout_buttons)) : ?>
		<li><?php echo implode('</li> <li>', $this->layout_buttons) ?></li>
		<?php endif ?>
	</ul>
</div>
<div class="spacer"></div>