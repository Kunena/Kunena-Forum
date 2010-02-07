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
defined( '_JEXEC' ) or die();


?>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<table class = "kblocktable" id = "kforumprofile_mod" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
	<thead>
		<tr>
			<th>
				<div class = "ktitle_cover km">
					<span class = "ktitle kl"><?php echo JText::_('COM_KUNENA_USER_MODERATOR'); ?>:</span>
				</div>
			</th>
		</tr>
	</thead>

	<tbody>
		<?php

		if (!CKunenaTools::isAdmin())
		{
			$enum = 1; //reset value
			$tabclass = array
			(
				"sectiontableentry1",
				"sectiontableentry2"
			);         //alternating row CSS classes

			$k    = 0; //value for alternating rows

			if ($this->kunena_cmodslist > 0)
			{
				foreach ($this->kunena_modslist as $mods)
				{ //get all moderator details for each moderation
					$k = 1 - $k;
		?>

					<tr class = "k<?php echo $tabclass[$k] ; ?>">
						<td class = "td-1" align="left"><?php echo $enum . ': ' . $mods->name; ?></td>
					</tr>

					<?php
					$enum++;
				}
			}
			else
			{
					?>

				<tr class = "k<?php echo $tabclass[$k] ; ?>"><td class = "td-1" align="left"><?php echo JText::_('COM_KUNENA_USER_MODERATOR_NONE'); ?></td>
				</tr>

		<?php
			}
		}
		else
		{
		?>

			<tr class = "k<?php echo $tabclass[$k] ; ?>"><td class = "td-1" align="left"><?php echo JText::_('COM_KUNENA_USER_MODERATOR_ADMIN'); ?></td>
			</tr>

		<?php
		}
		?>
	</tbody>
</table></div>
</div>
</div>
</div>
</div>