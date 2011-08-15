<?php
/**
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<?php foreach ( $this->messages as $id=>$message ) : ?>
	<div class="kpost-indent" style="padding-left: <?php echo (max(0,count($message->indent)-3)*2) ?>%"><?php $this->displayMessage($id, $message, 'message'); ?></div>
<?php endforeach ?>
