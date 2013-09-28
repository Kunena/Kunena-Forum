<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Search
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<?php if($this->results): ?>
	<div class="well"> <span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="ksearchresult"></a></span>
		<h2> <span> <?php echo JText::_('COM_KUNENA_SEARCH_RESULTS'); ?> </span> </h2>
		<div class="ksearchresult-desc km"> <span><?php echo JText::sprintf ('COM_KUNENA_FORUM_SEARCH', $this->escape($this->state->get('searchwords')) ); ?></span> </div>
		<div class="row-fluid column-row">
			<div class="span12 column-item">
				<?php if ($this->error) : ?>
					<div> <?php echo $this->error; ?> </div>
				<?php endif; ?>
				<table class="table">
					<tbody>
						<tr>
							<td>
								<?php $this->displayRows() ?>
							</td>
						</tr>
						<tr class="ksth">
							<th colspan="3">
								<?php
								$resStart = $this->pagination->limitstart + 1;
								$resStop = $this->pagination->limitstart + count ( $this->results );
								if ($resStart < $resStop)
									$resStartStop = ( string ) ($resStart) . ' - ' . ( string ) ($resStop);
								else
									$resStartStop = '0';
								printf ( JText::_('COM_KUNENA_FORUM_SEARCHRESULTS'), $resStartStop, intval($this->total) );
								?>
								<?php echo $this->subLayout('Pagination/List')->set('pagination', $this->pagination); ?>
							</th>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?php endif; ?>
