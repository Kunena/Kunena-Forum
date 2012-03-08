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
		<?php //echo implode(' ', $this->topicButtons->getProperties()) ?>
	</div>
</div>