<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

$kunena_db = JFactory::getDBO ();
$kunena_config = KunenaFactory::getConfig ();
$document = JFactory::getDocument ();

$document->setTitle ( JText::_('COM_KUNENA_GEN_RULES') . ' - ' . $kunena_config->board_title );

$kunena_db->setQuery ( "SELECT introtext, id FROM #__content WHERE id='{$kunena_config->rules_cid}'" );
$introtext = $kunena_db->loadResult ();
KunenaError::checkDatabaseError();
?>
<div class="kblock">
	<div class="ktitle">
				<h1><?php echo JText::_('COM_KUNENA_FORUM_RULES'); ?></h1>
	</div>
	<div class="kcontainer">
		<div class="khelprulescontent">
				<?php echo $introtext; ?>
		</div>
	</div>
</div>
<!-- Begin: Forum Jump -->
<div class="kblock">
	<div class="kcontainer">
		<div class="khelprulesjump">
			<?php
			if ($kunena_config->enableforumjump) {
				CKunenaTools::loadTemplate('/forumjump.php');
			}
			?>
		</div>
	</div>
</div>
<!-- Finish: Forum Jump -->
