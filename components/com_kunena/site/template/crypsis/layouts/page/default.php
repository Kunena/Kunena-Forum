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
echo $this->subLayout('Page/MenuBar');
echo $this->subLayout('Page/Module')->set('position', 'kunena_top');

	// Display current view/layout
echo $this->content;

echo $this->subLayout('Page/Breadcrumb')->set('breadcrumb', $this->breadcrumb);
echo $this->subLayout('Page/Module')->set('position', 'kunena_bottom');
echo $this->subLayout('Page/Footer');
?>
</div>
