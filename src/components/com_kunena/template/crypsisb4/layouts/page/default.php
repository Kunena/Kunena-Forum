<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
$config = KunenaFactory::getTemplate()->params;
?>

<div id="kunena" class="layout <?php echo $this->options->get('pageclass_sfx'); ?>">
	<?php

	if ($config->get('displayMenu'))
	{
		echo $this->subLayout('Widget/MenuBar');
	}

	if ($config->get('displayModule'))
	{
		echo $this->subLayout('Widget/Module')->set('position', 'kunena_top');
	}

	if ($config->get('displayBreadcrumb'))
	{
		echo $this->subLayout('Widget/Breadcrumb')->set('breadcrumb', $this->breadcrumb);
	}

	echo $this->subLayout('Widget/Module')->set('position', 'kunena_announcement');

	if ($config->get('displayAnnouncement'))
	{
		echo $this->subRequest('Widget/Announcement');
	}

	// Display current view/layout
	echo $this->content;

	if ($config->get('displayBreadcrumb'))
	{
		echo $this->subLayout('Widget/Breadcrumb')->set('breadcrumb', $this->breadcrumb);
	}

	if ($config->get('displayModule'))
	{
		echo $this->subLayout('Widget/Module')->set('position', 'kunena_bottom');
	}

	if ($config->get('displayFooter'))
	{
		echo $this->subLayout('Widget/Footer');
	}
	?>
</div>
