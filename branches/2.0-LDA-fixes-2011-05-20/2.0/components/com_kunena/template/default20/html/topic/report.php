<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
	<div id="kunena">
	<?php
		$this->displayMenu ();
		$this->displayLoginBox ();
		$this->displayBreadcrumb ();
		include 'report_embed.php';
		$this->displayFooter ();
	?>
	</div>