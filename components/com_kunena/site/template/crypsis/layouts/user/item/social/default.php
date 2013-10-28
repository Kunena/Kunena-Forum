<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$showAll = isset($this->showAll) ? $this->showAll : false;
?>
<div>
	<?php echo $this->profile->socialButton('twitter', $showAll); ?>
	<?php echo $this->profile->socialButton('facebook', $showAll); ?>
	<?php echo $this->profile->socialButton('myspace', $showAll); ?>
	<?php echo $this->profile->socialButton('linkedin', $showAll); ?>
	<?php echo $this->profile->socialButton('skype', $showAll); ?>
</div>
<div>
	<?php echo $this->profile->socialButton('delicious', $showAll); ?>
	<?php echo $this->profile->socialButton('friendfeed', $showAll); ?>
	<?php echo $this->profile->socialButton('digg', $showAll); ?>
</div>
<div>
	<?php echo $this->profile->socialButton('yim', $showAll); ?>
	<?php echo $this->profile->socialButton('aim', $showAll); ?>
	<?php echo $this->profile->socialButton('gtalk', $showAll); ?>
	<?php echo $this->profile->socialButton('icq', $showAll); ?>
	<?php echo $this->profile->socialButton('msn', $showAll); ?>
</div>
<div>
	<?php echo $this->profile->socialButton('blogspot', $showAll); ?>
	<?php echo $this->profile->socialButton('flickr', $showAll); ?>
	<?php echo $this->profile->socialButton('bebo', $showAll); ?>
</div>
