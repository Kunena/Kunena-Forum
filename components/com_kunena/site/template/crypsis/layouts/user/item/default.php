<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$tabs = $this->getTabs();
?>

<h2>
	<?php echo JText::_('COM_KUNENA_USER_PROFILE'); ?>
	<?php echo $this->escape($this->profile->getName()); ?>
</h2>

<div class="span3">
	<div class="center">
		<?php echo $this->profile->getAvatarImage('thumbnail', 200); ?>
	</div>
</div>
<div class="span4">
	<p>
		<?php echo JText::_($this->profile->getType()); ?>
	</p>
	<p>
		<strong><?php echo $this->escape($this->profile->getName()); ?></strong>
	</p>
	<p>
		<span class="badge badge-warning"><?php echo intval($this->profile->thankyou); ?> Thanks</span>
		<span class="badge badge-info"><?php echo intval($this->profile->posts); ?> Messages</span>
	</p>
</div>
<div class="span5">
	<?php $this->subLayout('User/Item/Social')->set('profile', $this->profile); ?>
</div>

<div class="clearfix"></div>

<div class="tabs-left">
	<ul class="nav nav-tabs">
		<?php foreach ($tabs as $name=>$tab) : ?>
		<li<?php echo $tab->active ? ' class="active"' : ''; ?>><a href="#<?php echo $name; ?>" data-toggle="tab"><?php echo $tab->title; ?></a></li>
		<?php endforeach; ?>
	</ul>
	<div class="tab-content">
		<?php foreach ($tabs as $name=>$tab) : ?>
		<div class="tab-pane fade<?php echo $tab->active ? ' in active' : ''; ?>" id="<?php echo $name; ?>">
			<div>
				<?php echo $tab->content; ?>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
</div>

<div class="clearfix"></div>
