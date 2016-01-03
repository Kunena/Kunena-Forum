<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$showAll = isset($this->showAll) ? $this->showAll : false;
?>
<div class="inline">
	<?php if (!empty($this->profile->twitter)) : ?>
	<?php echo $this->profile->socialButton('twitter', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->facebook)) : ?>
	<?php echo $this->profile->socialButton('facebook', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->myspace)) : ?>
	<?php echo $this->profile->socialButton('myspace', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->linkedin)) : ?>
	<?php echo $this->profile->socialButton('linkedin', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->skype)) : ?>
	<?php echo $this->profile->socialButton('skype', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->delicious)) : ?>
	<?php echo $this->profile->socialButton('delicious', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->friendfeed)) : ?>
	<?php echo $this->profile->socialButton('friendfeed', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->digg)) : ?>
	<?php echo $this->profile->socialButton('digg', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->yim)) : ?>
	<?php echo $this->profile->socialButton('yim', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->aim)) : ?>
	<?php echo $this->profile->socialButton('aim', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->gtalk)) : ?>
	<?php echo $this->profile->socialButton('gtalk', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->icq)) : ?>
	<?php echo $this->profile->socialButton('icq', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->msn)) : ?>
	<?php echo $this->profile->socialButton('msn', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->blogspot)) : ?>
	<?php echo $this->profile->socialButton('blogspot', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->flicker)) : ?>
	<?php echo $this->profile->socialButton('flickr', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->bebo)) : ?>
	<?php echo $this->profile->socialButton('bebo', $showAll); ?>
	<?php endif; ?>
</div>
