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

if (empty($this->categoryButtons)) return;
?>
<div class="block-wrapper">
	<div class="buttonbar block">
		<?php echo implode(' ', $this->categoryButtons) ?>
	</div>
</div>