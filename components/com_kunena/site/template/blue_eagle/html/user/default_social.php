<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage User
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

if (!isset($this->showUnusedSocial)) $this->showUnusedSocial = false;
?>

<div class="kiconrow">
	<?php echo $this->profile->socialButton('twitter', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('facebook', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('myspace', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('linkedin', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('skype', $this->showUnusedSocial); ?>
</div>
<div class="kiconrow">
	<?php echo $this->profile->socialButton('delicious', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('friendfeed', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('digg', $this->showUnusedSocial); ?>
</div>
<div class="clr"></div>
<div class="kiconrow">
	<?php echo $this->profile->socialButton('yim', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('aim', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('gtalk', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('icq', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('msn', $this->showUnusedSocial); ?>
</div>
<div class="kiconrow">
	<?php echo $this->profile->socialButton('blogspot', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('flickr', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('bebo', $this->showUnusedSocial); ?>
</div>
