<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
//FIXME: not done
?>
<div class="kblock ksearchresult">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="ksearchresult"></a></span>
		<h2>
			<span>
				<?php echo JText::_('COM_KUNENA_SEARCH_RESULTS'); ?>
			</span>
		</h2>
		<div class="ksearchresult-desc km">
			<span><?php echo JText::sprintf ('COM_KUNENA_FORUM_SEARCH', $this->escape($this->state->get('searchwords')) ); ?></span>
		</div>
	</div>
	<div class="kcontainer" id="ksearchresult">
		<div class="kbody">
	<?php if ($this->error) : ?>
		<div>
			<?php echo $this->error; ?>
		</div>
	<?php endif; ?>

<table>
	<tbody>
		<tr>
			<td>
				<?php  foreach ( $this->results as $result ) : ?>
					<table>
						<thead>
							<tr class="ksth">
								<th colspan="2">
									<span class="kmsgdate">
										<?php echo KunenaDate::getInstance($result->time)->toKunena() ?>
									</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td rowspan="2" valign="top" class="kprofile-left kresultauthor">
								<p><?php echo $this->escape($result->name) ?></p>
								</td>
								<td class="kmessage-left resultmsg">
									<div class="kmsgbody">
										<div class="kmsgtitle kresult-title">
											<span class="kmsgtitle">
												<?php echo CKunenaLink::GetThreadPageLink ( 'view', intval($result->catid), intval($result->id), NULL, NULL, $result->htmlsubject, intval($result->id) )?>
											</span>
										</div>
										<div class="kmsgtext resultmsg">
											<?php echo $result->htmlmessage ?>
										</div>
										<div class="resultcat">
											<?php echo JText::_('COM_KUNENA_CATEGORY') . ' ' . CKunenaLink::GetCategoryLink ( 'showcat', intval($result->catid), $this->escape($result->catname), $rel = 'follow', $class = '', $title = '' )?>
										</div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				<?php endforeach; ?>
			</td>
		</tr>
		<tr class="ksth">
			<th colspan="3">
			<?php echo $this->getPagination(5); ?>
			</th>
		</tr>
	</tbody>
</table>
</div>
</div>
</div>