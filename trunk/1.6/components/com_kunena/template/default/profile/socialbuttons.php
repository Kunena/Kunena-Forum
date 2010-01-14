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
	<?php echo $this->profile->socialButton('twitter'); ?>
	<?php echo $this->profile->socialButton('facebook'); ?>
	<?php echo $this->profile->socialButton('myspace'); ?>
	<?php echo $this->profile->socialButton('linkedin'); ?>
</div>
<div class="iconrow">
	<?php echo $this->profile->socialButton('delicious'); ?>
	<?php echo $this->profile->socialButton('friendfeed'); ?>
	<?php echo $this->profile->socialButton('digg'); ?>
</div>
<div class="clr"></div>
<div class="iconrow">
	<?php echo $this->profile->socialButton('skype'); ?>
	<?php echo $this->profile->socialButton('yim'); ?>
	<?php echo $this->profile->socialButton('aim'); ?>
	<?php echo $this->profile->socialButton('gtalk'); ?>
</div>
<div class="iconrow">
	<?php echo $this->profile->socialButton('blogspot'); ?>
	<?php echo $this->profile->socialButton('flickr'); ?>
	<?php echo $this->profile->socialButton('bebo'); ?>
</div>
