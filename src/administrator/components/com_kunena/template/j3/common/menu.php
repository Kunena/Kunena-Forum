<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Template
 * @subpackage      Common
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$view = Factory::getApplication()->input->getCmd('view', 'cpanel');
?>
<!-- Main navigation -->
<ul class="nav nav-list">
	<li<?php if ($view == 'cpanel')
{
		echo ' class="active"';
} ?>>
		<a href="<?php echo Route::_('index.php?option=com_kunena'); ?>">
			<i class="icon-dashboard"></i> <?php echo Text::_('COM_KUNENA_MENU_DESC_DASHBOARD'); ?>
		</a>
	</li>
	<li<?php if ($view == 'categories' || $view == 'category')
{
		echo ' class="active"';
} ?>>
		<a href="<?php echo Route::_('index.php?option=com_kunena&view=categories'); ?>">
			<i class="icon-list-view"></i> <?php echo Text::_('COM_KUNENA_C_FORUM'); ?>
		</a></li>
	<li<?php if ($view == 'users' || $view == 'user')
{
		echo ' class="active"';
} ?>>
		<a href="<?php echo Route::_('index.php?option=com_kunena&view=users'); ?>">
			<i class="icon-user"></i> <?php echo Text::_('COM_KUNENA_C_USER'); ?>
		</a></li>
	<li<?php if ($view == 'attachments' || $view == 'attachment')
{
		echo ' class="active"';
} ?>>
		<a href="<?php echo Route::_('index.php?option=com_kunena&view=attachments'); ?>">
			<i class="icon-folder-open"></i> <?php echo Text::_('COM_KUNENA_MENU_DESC_FILEMANAGER'); ?>
		</a></li>
	<li<?php if ($view == 'smilies' || $view == 'smiley')
{
		echo ' class="active"';
} ?>>
		<a href="<?php echo Route::_('index.php?option=com_kunena&view=smilies'); ?>">
			<i class="icon-thumbs-up"></i> <?php echo Text::_('COM_KUNENA_EMOTICONS_EMOTICON_MANAGER'); ?>
		</a></li>
	<li<?php if ($view == 'ranks' || $view == 'rank')
{
		echo ' class="active"';
} ?>>
		<a href="<?php echo Route::_('index.php?option=com_kunena&view=ranks'); ?>">
			<i class="icon-star-2"></i> <?php echo Text::_('COM_KUNENA_A_RANK_MANAGER'); ?>
		</a></li>
	<!--<li<?php /*
	if ($view == 'labels' || $view == 'label')
	{
		echo ' class="active"';
	} */ ?>>
		<a href="<?php // echo Route::_('index.php?option=com_kunena&view=labels');
	?>">
			<i class="icon-tags-2"></i> <?php // Echo Text::_('COM_KUNENA_A_LABELS_MANAGER');
	?>
		</a></li>
	<li<?php /*
	if ($view == 'icons' || $view == 'icon')
	{
		echo ' class="active"';
	} */ ?>>
		<a href="<?php // echo Route::_('index.php?option=com_kunena&view=icons');
	?>">
			<i class="icon-grid-2"></i> <?php // Echo Text::_('COM_KUNENA_A_ICONS_MANAGER');
	?>
		</a></li>-->
	<li<?php if ($view == 'templates' || $view == 'template')
{
		echo ' class="active"';
} ?>>
		<a href="<?php echo Route::_('index.php?option=com_kunena&view=templates'); ?>">
			<i class="icon-color-palette"></i> <?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER'); ?>
		</a></li>
	<li<?php if ($view == 'config')
{
		echo ' class="active"';
} ?>>
		<a href="<?php echo Route::_('index.php?option=com_kunena&view=config'); ?>">
			<i class="icon-wrench"></i> <?php echo Text::_('COM_KUNENA_MENU_DESC_CONFIGURATION'); ?>
		</a></li>
	<li<?php if ($view == 'plugins')
{
		echo ' class="active"';
} ?>>
		<a href="<?php echo Route::_('index.php?option=com_kunena&view=plugins'); ?>">
			<i class="icon-puzzle"></i> <?php echo Text::_('COM_KUNENA_PLUGIN_MANAGER'); ?>
		</a></li>
	<!--<li<?php /*
	if ($view == 'email')
	{
		echo ' class="active"';
	} */ ?>>
		<a href="<?php // echo Route::_('index.php?option=com_kunena&view=email');
	?>">
			<i class="icon-mail"></i> <?php // Echo Text::_('COM_KUNENA_A_EMAIL_MANAGER');
	?>
		</a></li>
	<li<?php /*
	if ($view == 'blockips' || $view == 'blockip')
	{
		echo ' class="active"';
	} */ ?>>
		<a href="<?php // echo Route::_('index.php?option=com_kunena&view=blockips');
	?>">
			<i class="icon-compass"></i> <?php // Echo Text::_('COM_KUNENA_A_BLOCKIP_MANAGER');
	?>
		</a></li>
	<li<?php /*
	if ($view == 'badwords' || $view == 'badword')
	{
		echo ' class="active"';
	} */ ?>>
		<a href="<?php // echo Route::_('index.php?option=com_kunena&view=badwords');
	?>">
			<i class="icon-smiley-sad-2"></i> <?php // Echo Text::_('COM_KUNENA_A_BADWORDS_MANAGER');
	?>
		</a></li>-->
	<li<?php if ($view == 'logs')
{
		echo ' class="active"';
} ?>>
		<a href="<?php echo Route::_('index.php?option=com_kunena&view=logs'); ?>">
			<i class="icon-search"></i> <?php echo Text::_('COM_KUNENA_LOG_MANAGER'); ?>
		</a></li>
	<li<?php if ($view == 'statistics')
{
		echo ' class="active"';
}
	?>>
		<a href="<?php echo Route::_('index.php?option=com_kunena&view=statistics'); ?>">
			<i class="icon-chart"></i> <?php echo Text::_('COM_KUNENA_MENU_STATISTICS'); ?>
		</a></li>
	<li<?php if ($view == 'tools')
{
		echo ' class="active"';
} ?>>
		<a href="<?php echo Route::_('index.php?option=com_kunena&view=tools'); ?>">
			<i class="icon-tools"></i> <?php echo Text::_('COM_KUNENA_A_VIEW_TOOLS'); ?>
		</a></li>
	<li<?php if ($view == 'trash')
{
		echo ' class="active"';
} ?>>
		<a href="<?php echo Route::_('index.php?option=com_kunena&view=trash'); ?>">
			<i class="icon-trash"></i> <?php echo Text::_('COM_KUNENA_TRASH_VIEW'); ?>
		</a></li>
</ul>
<!-- /Main navigation -->
