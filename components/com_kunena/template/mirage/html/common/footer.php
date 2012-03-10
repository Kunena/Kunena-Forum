<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<?php echo $this->getModulePosition( 'kunena_bottom' ); ?>

<?php if (($time = $this->getTime()) !== null) : ?>
<div class="box-module">
	<div class="box-wrapper">
		<div class="credit block">
			<p><?php echo JText::sprintf('COM_KUNENA_VIEW_COMMON_FOOTER_TIME', $time) ?></p>
		</div>
	</div>
</div>
<?php endif; ?>
