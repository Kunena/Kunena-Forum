<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$k = 0;
?>
<div class="kblock khistory">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="khistory"></a></span>
		<h2><span><?php echo JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY' )?>: <?php echo $this->escape($this->topic->subject) ?></span></h2>
		<div class="ktitle-desc km">
			<?php echo JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY_MAX' ) . ' ' . $this->escape($this->config->historylimit) . ' ' . JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY_LAST' )?>
		</div>
	</div>
	<div class="kcontainer" id="khistory">
		<div class="kbody">
			<?php foreach ( $this->history as $this->message ):?>
			<table>
				<thead>
					<tr class="ksth">
						<th colspan="2">
							<span class="kmsgdate khistory-msgdate" title="<?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat_hover') ?>">
								<?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat') ?>
							</span>
							<a id="<?php echo intval($this->message->id) ?>"></a>
							<?php echo $this->getNumLink($this->message->id,$this->replycount--) ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td rowspan="2" valign="top" class="kprofile-left  kauthor">
							<p><?php echo $this->message->getAuthor()->getLink() ?></p>
							<p><?php
								$profile = KunenaFactory::getUser(intval($this->message->userid));
								$useravatar = $profile->getAvatarImage('','','profile');
								if ($useravatar) :
									echo $this->message->getAuthor()->getLink( $useravatar );
								endif;
							?></p>
						</td>
						<td class="kmessage-left khistorymsg">
							<div class="kmsgbody">
								<div class="kmsgtext">
									<?php echo KunenaHtmlParser::parseBBCode( $this->message->message, $this ) ?>
								</div>
							</div>
							<?php
							$this->attachments = $this->message->getAttachments();
							if (!empty($this->attachments)) : ?>
							<div class="kmsgattach">
								<?php echo JText::_('COM_KUNENA_ATTACHMENTS');?>
								<ul class="kfile-attach">
								<?php foreach($this->attachments as $attachment) : ?>
									<li>
										<?php echo $attachment->getThumbnailLink(); ?>
										<span>
											<?php echo $attachment->getTextLink(); ?>
										</span>
									</li>
								<?php endforeach; ?>
								</ul>
							</div>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
			<?php endforeach; ?>
		</div>
	</div>
</div>
