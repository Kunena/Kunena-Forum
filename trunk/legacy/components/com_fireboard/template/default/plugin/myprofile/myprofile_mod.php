<?php
/**
* @version $Id: myprofile_mod.php 895 2008-08-03 06:15:11Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');
?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "fb_blocktable" id = "fb_forumprofile_mod" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
	<thead>
		<tr>
			<th>
				<div class = "fb_title_cover fbm">
					<span class = "fb_title fbl"><?php echo _USER_MODERATOR; ?>:</span>
				</div>
			</th>
		</tr>
	</thead>

	<tbody>
		<?php
		if (!$is_admin)
		{
			$enum = 1; //reset value
			$tabclass = array
			(
				"sectiontableentry1",
				"sectiontableentry2"
			);         //alternating row CSS classes

			$k    = 0; //value for alternating rows

			if ($cmodslist > 0)
			{
				foreach ($modslist as $mods)
				{ //get all moderator details for each moderation
					$k = 1 - $k;
		?>

					<tr class = "<?php echo $boardclass .''. $tabclass[$k] ; ?>">
						<td class = "td-1" align="left"><?php echo $enum . ': ' . $mods->name; ?></td>
					</tr>

					<?php
					$enum++;
				}
			}
			else
			{
					?>

				<tr class = "<?php echo $boardclass .''. $tabclass[$k] ; ?>"><td class = "td-1" align="left"><?php echo _USER_MODERATOR_NONE; ?></td>
				</tr>

		<?php
			}
		}
		else
		{
		?>

			<tr class = "<?php echo $boardclass .''. $tabclass[$k] ; ?>"><td class = "td-1" align="left"><?php echo _USER_MODERATOR_ADMIN; ?></td>
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