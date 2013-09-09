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
echo $this->subLayout('Page/MenuBar')->set('me', $this->me);
echo $this->subLayout('Page/Module')->set('position', 'kunena_top');
echo $this->subRequest('Page/Announcement');
echo $this->subLayout('Page/Module')->set('position', 'kunena_announcement');

// Display current view/layout
echo $this->content;

echo $this->subLayout('Page/Breadcrumb')->set('breadcrumb', $this->breadcrumb);
echo $this->subLayout('Page/Module')->set('position', 'kunena_bottom');
echo $this->subLayout('Page/Footer');
?>
</div>
