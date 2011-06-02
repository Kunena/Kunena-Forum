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
		include 'create_form.php';
		$this->displayFooter ();
	?>
	</div>
	<script type="text/javascript">
	/*<![CDATA[*/
	window.addEvent("domready", function() {
		var JTooltips = new Tips($$(".hasTip"), { maxTitleChars: 50, fixed: false});
	});
	/*]]>*/
	</script>