<?php
/**
 * @version $Id: login.php 4336 2011-01-31 06:05:12Z severdia $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
?>
<div class="kblock klogin">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_PROFILE_DISABLED'); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
			<?php echo JText::_('COM_KUNENA_PROFILE_DISABLED').' '.JText::_('COM_KUNENA_NO_ACCESS') ?>
		</div>
	</div>
</div>