<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div id="kunena" class="layout">
<?php
$this->displayMenu ();

// Display current view/layout
$this->displayLayout();

$this->displayBreadcrumb ();
$this->displayFooter ();
?>
</div>
