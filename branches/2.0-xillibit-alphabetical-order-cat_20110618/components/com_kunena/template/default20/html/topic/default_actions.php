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
		<div class="kbuttonbar-topic">
			<div class="kpaginationbar">
				<?php echo $this->getPagination(4) ?>
			</div>
			<ul class="kmessage-buttons">
				<!-- User buttons -->
				<?php if (!empty($this->topic_new)) : ?><li><?php echo $this->topic_new ?></li><?php endif ?>
				<?php if (!empty($this->topic_reply)) : ?><li><?php echo $this->topic_reply ?></li><?php endif ?>
				<?php if (!empty($this->topic_subscribe)) : ?><li><?php echo $this->topic_subscribe ?></li><?php endif ?>
				<?php if (!empty($this->topic_favorite)) : ?><li><?php echo $this->topic_favorite ?></li><?php endif ?>
				<!-- Moderator buttons -->
				<?php if (!empty($this->topic_lock)) : ?><li><?php echo $this->topic_lock ?></li><?php endif ?>
				<?php if (!empty($this->topic_sticky)) : ?><li><?php echo $this->topic_sticky ?></li><?php endif ?>
				<?php if (!empty($this->topic_moderate)) : ?><li><?php echo $this->topic_moderate ?></li><?php endif ?>
				<?php if (!empty($this->topic_delete)) : ?><li><?php echo $this->topic_delete ?></li><?php endif ?>
				<?php if (!empty($this->layout_buttons)) : ?>
				<li><?php echo implode('</li> <li>', $this->layout_buttons) ?></li>
				<?php endif ?>
			</ul>
		</div>