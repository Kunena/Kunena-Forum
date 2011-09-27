<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default
 * @subpackage Search
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
					<table>
						<thead>
							<tr class="ksth">
								<th colspan="2">
									<span class="kmsgdate">
										<?php echo KunenaDate::getInstance($this->message->time)->toSpan() ?>
									</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td rowspan="2" valign="top" class="kprofile-left kresultauthor">
								<p><?php echo $this->escape($this->message->name) ?></p>
								</td>
								<td class="kmessage-left resultmsg">
									<div class="kmsgbody">
										<div class="kmsgtitle kresult-title">
											<span class="kmsgtitle">
												<?php echo CKunenaLink::GetThreadPageLink ( 'view', intval($this->category->id), intval($this->topic->id), NULL, NULL, $this->subjectHtml, intval($this->message->id) )?>
											</span>
										</div>
										<div class="kmsgtext resultmsg">
											<?php echo $this->messageHtml ?>
										</div>
										<div class="resultcat">
											<?php echo JText::_('COM_KUNENA_CATEGORY') . ' ' . $this->getCategoryLink ( $this->category, $this->escape($this->category->name))?>
										</div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>