<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div id="kunena" class="layout">
<?php
echo KunenaLayout::factory('Page/MenuBar');
echo KunenaLayout::factory('Page/Module')->set('position', 'kunena_top');

	// Display current view/layout
echo $this->content;

echo KunenaLayout::factory('Page/Breadcrumb')->set('breadcrumb', $this->breadcrumb);
echo KunenaLayout::factory('Page/Module')->set('position', 'kunena_bottom');
echo KunenaLayout::factory('Page/Footer');
?>
</div>
