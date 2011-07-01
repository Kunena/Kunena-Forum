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
if (!$this->getPagination(7) && empty($this->newTopicHtml) && empty($this->markReadHtml) && empty($this->subscribeCatHtml)) return;
?>
		<div class="kbuttonbar">
			<div class="kpaginationbar">
				<?php echo $this->getPagination(7) ?>
			</div>
			<ul class="kmessage-buttons">
				<?php if ($this->newTopicHtml) : ?><li><?php echo $this->newTopicHtml ?></li><?php endif ?>
				<?php if ($this->markReadHtml) : ?><li><?php echo $this->markReadHtml ?></li><?php endif ?>
				<?php if ($this->subscribeCatHtml) : ?><li><?php echo $this->subscribeCatHtml ?></li><?php endif ?>
			</ul>
		</div>