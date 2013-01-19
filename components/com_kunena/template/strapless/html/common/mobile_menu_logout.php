<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip');
// Basic logic has been taken from Joomla! 2.5 (mod_menu)
// HTML output emulates default Joomla! 1.5 (mod_mainmenu), but only first level is supported

// Note. It is important to remove spaces between elements.
?>

<!-- user dropdown -->

<div class="kcontainer"> 
 <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> 
	<span class="icon-bar"></span> 
	<span class="icon-bar"></span> 
	<span class="icon-bar"></span> 
 </a>
  <div class="nav-collapse collapse"> <?php echo $this->getMenu() ?>
  </div>
</div>
