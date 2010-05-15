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

$kunena_db = &JFactory::getDBO ();
$kunena_config = & CKunenaConfig::getInstance ();
$document = & JFactory::getDocument ();

$document->setTitle ( JText::_('COM_KUNENA_GEN_HELP') . ' - ' . $kunena_config->board_title );

$kunena_db->setQuery ( "SELECT introtext, id FROM #__content WHERE id='{$kunena_config->help_cid}'" );
$introtext = $kunena_db->loadResult ();
check_dberror ( "Unable to load introtext." );
?>
<table class="kblock" id="kforumhelp">
	<thead>
		<tr>
			<th>
				<h1><?php echo JText::_('COM_KUNENA_FORUM_HELP'); ?></h1>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="khelpdesc">
				<?php echo $introtext; ?>
			</td>
		</tr>
	</tbody>
</table>
<!-- Begin: Forum Jump -->
<table class="kblock" id="kbottomarea">
	<tr>
		<th class="th-right">
		<?php
		if ($kunena_config->enableforumjump) {
			CKunenaTools::loadTemplate('/forumjump.php');
		}
		?>
		</th>
	</tr>
</table>
<!-- Finish: Forum Jump -->
