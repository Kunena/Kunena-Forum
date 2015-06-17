<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Search
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
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
									<ul class="kpost-profile">
										<li class="kpost-username">
											<?php echo $this->message->getAuthor()->getLink(null, null, 'nofollow', '', null, $this->topic->getCategory()->id) ?>
										</li>
										<li>
											<?php
											if ($this->useravatar) :
											?>

											<span class="kavatar">
											<?php echo $this->message->getAuthor()->getLink($this->useravatar, null, 'nofollow', '', null, $this->topic->getCategory()->id) ?>
											</span>

											<?php
												endif;
											?>
										</li>
									</ul>
								</td>
								<td class="kmessage-left resultmsg">
									<div class="kmsgbody">
										<div class="kmsgtitle kresult-title">
											<span class="kmsgtitle">
												<?php echo $this->getTopicLink($this->topic, $this->message, $this->subjectHtml); ?>
											</span>
										</div>
										<div class="kmsgtext resultmsg">
											<?php echo $this->messageHtml ?>
										</div>
										<div class="resultcat">
											<?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink ( $this->category, $this->escape($this->category->name))) ?>
										</div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
