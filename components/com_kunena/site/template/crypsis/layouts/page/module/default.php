<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<!-- Module position: <?php echo $this->position ?> -->
<div class="well well-small">
	MODULE: <?php echo $this->position ?>
	<?php echo $this->renderPosition(); ?>
</div>