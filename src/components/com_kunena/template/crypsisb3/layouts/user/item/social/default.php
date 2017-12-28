<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$showAll = isset($this->showAll) ? $this->showAll : false;
?>
<div class="inline pull-right">
	<?php if (!empty($this->profile->twitter)) : ?>
		<?php echo $this->profile->socialButton('twitter', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->facebook)) : ?>
		<?php echo $this->profile->socialButton('facebook', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->instagram)) : ?>
		<?php echo $this->profile->socialButton('instagram', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->skype)) : ?>
		<?php echo $this->profile->socialButton('skype', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->linkedin)) : ?>
		<?php echo $this->profile->socialButton('linkedin', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->delicious)) : ?>
		<?php echo $this->profile->socialButton('delicious', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->google)) : ?>
		<?php echo $this->profile->socialButton('google', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->qq)) : ?>
		<?php echo $this->profile->socialButton('qq', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->qzone)) : ?>
		<?php echo $this->profile->socialButton('qzone', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->weibo)) : ?>
		<?php echo $this->profile->socialButton('weibo', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->wechat)) : ?>
		<?php echo $this->profile->socialButton('wechat', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->microsoft)) : ?>
		<?php echo $this->profile->socialButton('microsoft', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->apple)) : ?>
		<?php echo $this->profile->socialButton('apple', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->vk)) : ?>
		<?php echo $this->profile->socialButton('vk', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->telegram)) : ?>
		<?php echo $this->profile->socialButton('telegram', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->icq)) : ?>
		<?php echo $this->profile->socialButton('icq', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->myspace)) : ?>
		<?php echo $this->profile->socialButton('myspace', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->blogspot)) : ?>
		<?php echo $this->profile->socialButton('blogspot', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->flickr)) : ?>
		<?php echo $this->profile->socialButton('flickr', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->friendfeed)) : ?>
		<?php echo $this->profile->socialButton('friendfeed', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->digg)) : ?>
		<?php echo $this->profile->socialButton('digg', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->bebo)) : ?>
		<?php echo $this->profile->socialButton('bebo', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->aim)) : ?>
		<?php echo $this->profile->socialButton('aim', $showAll); ?>
	<?php endif; ?>
	<?php if (!empty($this->profile->yim)) : ?>
		<?php echo $this->profile->socialButton('yim', $showAll); ?>
	<?php endif; ?>
</div>
