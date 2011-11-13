<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div id="Kunena">
<?php
$this->displayMenu ();
$this->displayLoginBox ();

// Display current view/layout
$this->displayLayout();

$this->displayFooter ();
?>
</div>