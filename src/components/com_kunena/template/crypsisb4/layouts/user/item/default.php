<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$tabs = $this->getTabs();
?>

<h1 class="float-left">
	<?php echo Text::_('COM_KUNENA_USER_PROFILE'); ?>
	<?php echo $this->escape($this->profile->getName()); ?>
</h1>

<h2 class="float-right">
	<?php if ($this->profile->isAuthorised('edit'))
		:
		?>
		<?php echo $this->profile->getLink(
		KunenaIcons::edit() . ' ' . Text::_('COM_KUNENA_EDIT'),
		Text::_('COM_KUNENA_EDIT'), 'nofollow', 'edit', 'btn'
	); ?>
	<?php endif; ?>
</h2>

<?php
echo $this->subLayout('User/Item/Summary')
->set('profile', $this->profile)
->set('config', $this->config)
->set('candisplaymail', $this->candisplaymail);
?>

<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_summary'); ?>

<div class="tabs">
	<ul class="nav nav-tabs">
		<?php foreach ($tabs as $name => $tab) : ?>
			<li class="nav-item">
				<a <?php echo $tab->active ? ' class="nav-link active"' : ' class="nav-link"'; ?>
						href="#<?php echo $name; ?>" data-toggle="tab" rel="nofollow"><?php echo $tab->title; ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="tab-content">
		<?php foreach ($tabs as $name => $tab) : ?>
			<div class="tab-pane fade<?php echo $tab->active ? ' in active show' : ''; ?>" id="<?php echo $name; ?>">
				<div>
					<?php echo $tab->content; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<div class="clearfix"></div>

