<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
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

	if ($config->get('displayAnnouncement'))
	{
		echo $this->subRequest('Widget/Announcement');if ($config->get('displayMenu'));
	}

	if ($config->get('displayModule'))
	{
		echo $this->subLayout('Widget/Module')->set('position', 'kunena_announcement');
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
