<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

$kunena_db = &JFactory::getDBO ();
$kunena_config = & CKunenaConfig::getInstance ();
$document = & JFactory::getDocument ();

$document->setTitle ( JText::_(COM_KUNENA_GEN_RULES) . ' - ' . stripslashes ( $kunena_config->board_title ) );

$kunena_db->setQuery ( "SELECT introtext, id FROM #__content WHERE id='{$kunena_config->rules_cid}'" );
$j_introtext = $kunena_db->loadResult ();
check_dberror ( "Unable to load introtext." );
?>
<!-- INSERT YOUR RULES IN HTML BEGINNING HERE -->
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
	<table class="kblocktable" id="kforumrules">
		<thead>
			<tr>
				<th>
					<div class="ktitle_cover"><span class="ktitle kl"><?php
					echo JText::_(COM_KUNENA_FORUM_RULES);
					?></span></div>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="krulesdesc">
					<?php
					echo $j_introtext;
					?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
</div>
</div>
</div>
</div>
<!-- THIS IS WHERE YOUR RULES FINISH -->
<!-- Begin: Forum Jump -->
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
	<table class="kblocktable" id="kbottomarea">
		<tr>
			<th class="th-right">
				<?php
				if ($kunena_config->enableforumjump) {
					if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'forumjump.php' )) {
						include (KUNENA_ABSTMPLTPATH . DS . 'forumjump.php');
					} else {
						include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'forumjump.php');
					}
				}
				?>
			</th>
		</tr>
	</table>
</div>
</div>
</div>
</div>
</div>
<!-- Finish: Forum Jump -->
