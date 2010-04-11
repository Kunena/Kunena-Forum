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
 * Based on Fireboard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/

defined ( '_JEXEC' ) or die ();

if (empty ( $this->q ) && empty ( $this->quser )) {
	return;
}
?>
<div class="k_bt_cvr1">
<div class="kbt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<table class="kblocktable" id="kforumsearch" >
	<thead>
		<tr>
			<th colspan="3">
			<div class="ktitle_cover"><span class="ktitle fbl"><?php
			echo JText::_('COM_KUNENA_SEARCH_RESULTS');
			?></span> <b><?php
		printf ( JText::_('COM_KUNENA_FORUM_SEARCH'), $this->q );
		?></b></div>
			</th>
		</tr>
	</thead>

	<tbody>
		<tr class="ksth">
			<th class="th-2 ksectiontableheader">
			<?php
			echo JText::_('COM_KUNENA_GEN_SUBJECT');
			?></th>

			<th class="th-3 ksectiontableheader">
			<?php
			echo JText::_('COM_KUNENA_GEN_AUTHOR');
			?></th>

			<th class="th-4 ksectiontableheader">
			<?php
			echo JText::_('COM_KUNENA_GEN_DATE');
			?></th>
		</tr>

		<?php
		$k = 0;
		if ($this->total == 0 && $this->int_kunena_errornr) {
			echo '<tr class="k' . $this->tabclass [$k] . '" ><td colspan="3" style="text-align:center;font-weight:bold">' . $this->str_kunena_errormsg . '</td></tr>';
		}

		foreach ( $this->results as $result ) {
			?>
			<tr
			class="k<?php
			echo $this->tabclass [$k ^= 1] . (isset ( $result->class_sfx ) ? ' k' . $this->tabclass [$k ^ 1] . $result->class_sfx : '')?>">
			<td class="td-2"><?php
			echo CKunenaLink::GetThreadPageLink ( 'view', $result->catid, $result->id, NULL, NULL, $result->subject, $result->id )?>
			<br /><?php
			echo $result->message?>
			<br />
			<span style="font-size: x-small;"><?php
			echo JText::_('COM_KUNENA_CATEGORY') . ' ' . CKunenaLink::GetCategoryLink ( 'showcat', $result->catid, $result->catname, $rel = 'follow', $class = '', $title = '' )?></span>
			</td>
			<td class="td-3"><?php
			echo kunena_htmlspecialchars ( stripslashes ( $result->name ) )?></td>
			<td class="td-4"><?php
			echo CKunenaTimeformat::showDate ( $result->time )?></td>
		</tr>
			<?php
		}

		if ($this->total > $this->limit) {
			?>

		<tr class="ksth">
			<th colspan="3" class="th-1 ksectiontableheader center">
			<?php
			echo $this->pagination;
			?>
			</th>
		</tr>

		<?php
		}
		?>

		<tr class="ksth">
			<th colspan="3" class="th-1 ksectiontableheader center">
			<?php
			$resStart = $this->limitstart + 1;
			$resStop = $this->limitstart + count ( $this->results );
			if ($resStart < $resStop)
				$resStartStop = ( string ) ($resStart) . ' - ' . ( string ) ($resStop);
			else
				$resStartStop = '0';
			printf ( JText::_('COM_KUNENA_FORUM_SEARCHRESULTS'), $resStartStop, $this->total );
			?>
			</th>
		</tr>
	</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
