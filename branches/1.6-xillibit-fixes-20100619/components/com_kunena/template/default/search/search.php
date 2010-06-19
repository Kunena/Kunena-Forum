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

defined ( '_JEXEC' ) or die ();

if (empty ( $this->q ) && empty ( $this->quser )) {
	return;
}
?>
<table class="kblock" id="kforumsearch" >
	<thead>
		<tr>
			<th colspan="3">
				<h2><?php echo JText::_('COM_KUNENA_SEARCH_RESULTS'); ?></h2>
				<span><?php echo JText::sprintf ('COM_KUNENA_FORUM_SEARCH', $this->q ); ?></span>
			</th>
		</tr>
	</thead>

	<tbody>
		<tr class="ksth">
			<th class="kcol kcol-search-subject"><?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?></th>
			<th class="kcol kcol-search-author"><?php echo JText::_('COM_KUNENA_GEN_AUTHOR'); ?></th>
			<th class="kcol kcol-search-date"><?php echo JText::_('COM_KUNENA_GEN_DATE'); ?></th>
		</tr>

		<?php $k = 0;
		if ($this->total == 0 && $this->int_kunena_errornr) : ?>
		<tr class="k<?php echo $this->tabclass [$k] ?>" ><td colspan="3" style="text-align:center;font-weight:bold"><?php echo $this->str_kunena_errormsg ?></td></tr>
		<?php endif; ?>

		<?php foreach ( $this->results as $result ) : ?>
		<tr class="krow<?php echo $k ^= 1 . (isset ( $result->class_sfx ) ? ' krow' . $k ^ 1 . $result->class_sfx : '')?>">
			<td class="kcol kcol-search-subject">
			<?php echo CKunenaLink::GetThreadPageLink ( 'view', $result->catid, $result->id, NULL, NULL, $result->subject, $result->id )?>
			<br />
			<?php echo $result->message?>
			<br />
			<span style="font-size: x-small;"><?php
				echo JText::_('COM_KUNENA_CATEGORY') . ' ' . CKunenaLink::GetCategoryLink ( 'showcat', $result->catid, $result->catname, $rel = 'follow', $class = '', $title = '' )?></span>
			</td>
			<td class="kcol kcol-search-author"><?php echo kunena_htmlspecialchars ( $result->name )?></td>
			<td class="kcol kcol-search-date"><?php echo CKunenaTimeformat::showDate ( $result->time )?></td>
		</tr>
		<?php endforeach; ?>

		<tr class="ksth">
			<th colspan="3" class="kcenter">
			<?php
			$resStart = $this->limitstart + 1;
			$resStop = $this->limitstart + count ( $this->results );
			if ($resStart < $resStop)
				$resStartStop = ( string ) ($resStart) . ' - ' . ( string ) ($resStop);
			else
				$resStartStop = '0';
			printf ( JText::_('COM_KUNENA_FORUM_SEARCHRESULTS'), $resStartStop, $this->total );
			?>

			<?php if ($this->total > $this->limit) : ?>
			<?php echo $this->pagination; ?>
			<?php endif; ?>
			</th>
		</tr>
	</tbody>
</table>