<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
defined( '_JEXEC' ) or die();
?>

<div class="iconrow">
	<?php echo $this->profile->socialButton('twitter', true); ?>
	<?php echo $this->profile->socialButton('facebook', true); ?>
	<?php echo $this->profile->socialButton('myspace', true); ?>
	<?php echo $this->profile->socialButton('linkedin', true); ?>
	<?php echo $this->profile->socialButton('skype', true); ?>
</div>
<div class="iconrow">
	<?php echo $this->profile->socialButton('delicious', true); ?>
	<?php echo $this->profile->socialButton('friendfeed', true); ?>
	<?php echo $this->profile->socialButton('digg', true); ?>
</div>
<div class="clr"></div>
<div class="iconrow">

	<?php echo $this->profile->socialButton('yim', true); ?>
	<?php echo $this->profile->socialButton('aim', true); ?>
	<?php echo $this->profile->socialButton('gtalk', true); ?>
	<?php echo $this->profile->socialButton('icq', true); ?>
	<?php echo $this->profile->socialButton('msn', true); ?>
</div>
<div class="iconrow">
	<?php echo $this->profile->socialButton('blogspot', true); ?>
	<?php echo $this->profile->socialButton('flickr', true); ?>
	<?php echo $this->profile->socialButton('bebo', true); ?>
</div>