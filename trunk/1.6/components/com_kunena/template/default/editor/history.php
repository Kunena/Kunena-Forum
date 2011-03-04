<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

$k = 0;
?>
<div class="kblock khistory">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="khistory"></a></span>
		<h2><span><?php echo JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY' )?>: <?php echo $this->escape($this->subject) ?></span></h2>
		<div class="ktitle-desc km">
			<?php echo JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY_MAX' ) . ' ' . $this->escape($this->config->historylimit) . ' ' . JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY_LAST' )?>
		</div>
	</div>
	<div class="kcontainer" id="khistory">
		<div class="kbody">
			<?php foreach ( $this->messages as $mes ):?>
			<table>
				<thead>
					<tr class="ksth">
						<th colspan="2">
							<span class="kmsgdate khistory-msgdate" title="<?php echo CKunenaTimeformat::showDate($mes->time, 'config_post_dateformat_hover') ?>">
								<?php echo CKunenaTimeformat::showDate($mes->time, 'config_post_dateformat') ?>
							</span>
							<a name="<?php echo intval($mes->id) ?>"></a>
							<?php echo $this->getNumLink($mes->id,$this->replycount--) ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td rowspan="2" valign="top" class="kprofile-left  kauthor">
							<p><?php echo $this->escape( $mes->name ) ?></p>
							<p><?php
								$profile = KunenaFactory::getUser(intval($mes->userid));
								$useravatar = $profile->getAvatarLink('','','profile');
								if ($useravatar) :
									echo CKunenaLink::GetProfileLink ( intval($mes->userid), $useravatar );
								endif;
							?></p>
						</td>
						<td class="kmessage-left khistorymsg">
							<div class="kmsgbody">
								<div class="kmsgtext">
									<?php echo KunenaParser::parseBBCode( $mes->message, $this ) ?>
								</div>
							</div>
							<?php if ( !empty($this->attachmentslist[$mes->id]) ) $this->displayAttachments($this->attachmentslist[$mes->id]); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<?php endforeach; ?>
		</div>
	</div>
</div>