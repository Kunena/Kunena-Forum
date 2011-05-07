<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
?>
<!-- Kunena Menu -->
<div id="ktop">
	<?php if (JDocumentHTML::countModules ( 'kunena_menu' )) : ?>
		<!-- Kunena Module Position: kunena_menu -->
		<div id="ktopmenu">
			<div id="ktab"><?php CKunenaTools::displayMenu() ?></div>
		</div>
		<!-- /Kunena Module Position: kunena_menu -->
	<?php endif; ?>
	<span class="ktoggler fltrt"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kprofilebox"></a></span>
</div>
<!-- /Kunena Menu -->