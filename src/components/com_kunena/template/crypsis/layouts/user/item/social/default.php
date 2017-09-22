<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

$showAll = isset($this->showAll) ? $this->showAll : false;
?>
<div class="inline">
	<?php if (!empty($this->profile->twitter))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('twitter', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->facebook))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('facebook', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->instagram))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('instagram', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->skype))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('skype', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->linkedin))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('linkedin', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->delicious))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('delicious', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->google))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('google', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->qq))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('qq', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->qzone))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('qzone', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->weibo))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('weibo', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->wechat))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('wechat', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->microsoft))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('microsoft', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->apple))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('apple', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->vk))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('vk', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->telegram))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('telegram', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->icq))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('icq', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->myspace))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('myspace', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->blogspot))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('blogspot', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->flickr))
	:
	?>
	<?php echo $this->profile->socialButtonsTemplate('flickr', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->friendfeed))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('friendfeed', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->digg))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('digg', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->bebo))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('bebo', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->aim))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('aim', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->whatsapp))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('whatsapp', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->yim))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('yim', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->youtube))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('youtube', $showAll); ?>
	<?php endif; ?>
	<?php
	if (!empty($this->profile->ok))
	:
	?>
		<?php echo $this->profile->socialButtonsTemplate('ok', $showAll); ?>
	<?php endif; ?>
</div>
