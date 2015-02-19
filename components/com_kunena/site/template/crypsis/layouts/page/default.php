<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;
$config = KunenaFactory::getTemplate()->params->get('displayMenu');
?>
<div id="kunena" class="layout">
	<?php
	if ($config) {
		echo $this->subLayout('Widget/MenuBar');
	}
	echo $this->subLayout('Widget/Module')->set('position', 'kunena_top');
	echo $this->subLayout('Widget/Breadcrumb')->set('breadcrumb', $this->breadcrumb);
	echo $this->subRequest('Widget/Announcement');
	echo $this->subLayout('Widget/Module')->set('position', 'kunena_announcement');

	// Display current view/layout
	echo $this->content;

	echo $this->subLayout('Widget/Breadcrumb')->set('breadcrumb', $this->breadcrumb);
	echo $this->subLayout('Widget/Module')->set('position', 'kunena_bottom');
	echo $this->subLayout('Widget/Footer');
	?>
</div>
