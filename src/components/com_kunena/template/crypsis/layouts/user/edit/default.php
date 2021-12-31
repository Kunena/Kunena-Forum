<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$this->profile = KunenaFactory::getUser($this->user->id);
$this->me      = KunenaUserHelper::getMyself();
$tabs          = $this->getTabsEdit();
$avatar        = KunenaFactory::getAvatarIntegration();
?>
<h2>
	<?php echo Text::_('COM_KUNENA_USER_PROFILE'); ?> <?php echo $this->escape($this->profile->getName()); ?>

	<?php echo $this->profile->getLink(
		KunenaIcons::back() . ' ' . Text::_('COM_KUNENA_BACK'),
		Text::_('COM_KUNENA_BACK'), 'nofollow', '', 'btn pull-right'
	); ?>
</h2>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user'); ?>" method="post"
      enctype="multipart/form-data" name="kuserform"
      class="form-validate" id="kuserform">
	<input type="hidden" name="task" value="save"/>
	<input type="hidden" name="userid" value="<?php echo (int) $this->user->id; ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>

	<div class="tabs">
		<ul id="KunenaUserEdit" class="nav nav-tabs">

			<?php foreach ($tabs as $name => $tab)
				:
				?>
				<?php if ($name == 'avatar' && !$avatar instanceof KunenaAvatarKunena): ?>
			<?php else : ?>
				<li<?php echo $tab->active ? ' class="active"' : ''; ?>>
					<a href="#edit<?php echo $name; ?>" data-toggle="tab" rel="nofollow"><?php echo $tab->title; ?></a>
				</li>
			<?php endif; ?>
			<?php endforeach; ?>

		</ul>
		<div class="tab-content">

			<?php foreach ($tabs as $name => $tab)
				:
				?>
				<?php if ($name == 'avatar' && !$avatar instanceof KunenaAvatarKunena): ?>
			<?php else : ?>
				<div class="tab-pane fade<?php echo $tab->active ? ' in active' : ''; ?>" id="edit<?php echo $name; ?>">
					<div>
						<?php echo $tab->content; ?>
					</div>
				</div>
			<?php endif; ?>
			<?php endforeach; ?>

		</div>
		<br/>

		<div class="center">
			<button class="btn btn-primary validate" type="submit">
				<?php echo KunenaIcons::save(); ?><?php echo Text::_('COM_KUNENA_SAVE'); ?>
			</button>
			<button class="btn btn-default" type="button" name="cancel" onclick="window.history.back();"
			        title="<?php echo Text::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL'); ?>">
				<?php echo KunenaIcons::cancel(); ?><?php echo Text::_('COM_KUNENA_CANCEL'); ?>
			</button>
		</div>
	</div>
</form>
