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

// Display current view/layout
$this->displayLayout();

$this->displayBreadcrumb();
echo KunenaLayout::factory('Page/Footer');
?>
</div>
