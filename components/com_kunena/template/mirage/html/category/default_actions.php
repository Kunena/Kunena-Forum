<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
if (!$this->getPagination(7) && empty($this->newTopicHtml) && empty($this->markReadHtml) && empty($this->subscribeCatHtml)) return;
?>
<div class="block-wrapper">
	<div class="buttonbar block">
		<ul class="list-unstyled buttons-topics">
			<?php if ($this->newTopicHtml) : ?><li class="button button-topics-new"><?php echo $this->newTopicHtml ?></li><?php endif ?>
			<?php if ($this->markReadHtml) : ?><li class="button button-topics-markread"><?php echo $this->markReadHtml ?></li><?php endif ?>
			<?php if ($this->subscribeCatHtml) : ?><li class="button button-topics-subscribe"><?php echo $this->subscribeCatHtml ?></li><?php endif ?>
		</ul>
	</div>
</div>