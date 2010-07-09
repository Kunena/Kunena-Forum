<?php
/**
 * @version $Id: profilebox.php 2948 2010-07-08 00:23:46Z dragan $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
?>
<!-- Kunena Header -->
<div id="ktop">
	<?php if (JDocumentHTML::countModules ( 'kunena_menu' )) : ?>
		<!-- Kunena Module Position: kunena_menu -->
		<div id="ktopmenu">
			<div id="ktab"><?php CKunenaTools::displayMenu() ?></div>
		</div>
		<!-- /Kunena Module Position: kunena_menu -->
	<?php endif; ?>
	<span class="ktoggler fltrt"><a class="ktoggler close" rel="kprofilebox"></a></span>
</div>
<!-- /Kunena Header -->