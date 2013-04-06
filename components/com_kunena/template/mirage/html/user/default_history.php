<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$j=count($this->banhistory);
?>

<div class="kmodule user-history">
	<div class="kbox-wrapper kbox-full">
		<div class="user-history-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><a rel="kbanhistory-detailsbox"><?php echo JText::sprintf('COM_KUNENA_BAN_BANHISTORYFOR', $this->escape($this->profile->name)); ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="detailsbox banhistory">
					<ul class="list-unstyled topic-list">
						<li class="header kbox-hover_header-row kbox-full">
							<dl>
								<dd class="kcol-first kid"> # </dd>
								<dd class="kcol-mid kbanfrom"><?php echo JText::_('COM_KUNENA_BAN_BANNEDFROM'); ?></dd>
								<dd class="kcol-mid kbanstart"><?php echo JText::_('COM_KUNENA_BAN_STARTTIME'); ?></dd>
								<dd class="kcol-mid kbanexpire"><?php echo JText::_('COM_KUNENA_BAN_EXPIRETIME'); ?></dd>
								<dd class="kcol-mid kbancreate"><?php echo JText::_('COM_KUNENA_BAN_CREATEDBY'); ?></dd>
								<dd class="kcol-last kbanmodify"><?php echo JText::_('COM_KUNENA_BAN_MODIFIEDBY'); ?></dd>
							</dl>
						</li>
					</ul>
				<table>
				<?php
					if ( !empty($this->banhistory) ) :
						foreach ($this->banhistory as $userban) :
				?>
				<tr class="krow1">
					<td class="kcol-first kid">
						<?php echo $j--; ?>
					</td>
					<td class="kcol-mid  kbanfrom">
						<span><?php echo $userban->blocked ? JText::_('COM_KUNENA_BAN_BANLEVEL_JOOMLA') : JText::_('COM_KUNENA_BAN_BANLEVEL_KUNENA') ?></span>
					</td>
					<td class="kcol-mid kbanstart">
						<span><?php  if( $userban->created_time ) echo KunenaDate::getInstance($userban->created_time)->toKunena('datetime'); ?></span>
					</td>
					<td class="kcol-mid kbanexpire">
						<span><?php echo $userban->isLifetime() ? JText::_('COM_KUNENA_BAN_LIFETIME') : KunenaDate::getInstance($userban->expiration)->toKunena('datetime'); ?></span>
					</td>
					<td class="kcol-mid kbancreate">
						<span><?php echo $userban->getCreator()->getLink() ?></span>
					</td>
					<td class="kcol-last kbanmodify">
						<?php if ( $userban->modified_by && $userban->modified_time) { ?>
						<span>
							<?php echo $userban->getModifier()->getLink() ?>
							<?php echo KunenaDate::getInstance($userban->modified_time)->toKunena('datetime'); } ?>
						</span>
					</td>
				</tr>
				<?php if($userban->reason_public) : ?>
				<tr class="krow2">
					<td colspan="2" class="kcol-first kpublic-reason-label"><b><?php echo JText::_('COM_KUNENA_BAN_PUBLICREASON'); ?></b> :</td>
					<td colspan="4" class="kcol-mid  kpublic-reason-field"><?php echo KunenaHtmlParser::parseText ($userban->reason_public); ?></td>
				</tr>
				<?php endif; ?>
				<?php if($userban->reason_private) : ?>
				<tr class="krow2">
					<td colspan="2" class="kcol-first kprivate-reason-label"><b><?php echo JText::_('COM_KUNENA_BAN_PRIVATEREASON'); ?></b> :</td>
					<td colspan="4" class="kcol-mid kprivate-reason-field"><?php echo KunenaHtmlParser::parseText ($userban->reason_private); ?></td>
				</tr>
				<?php endif; ?>
				<?php if (is_array($userban->comments)) foreach ($userban->comments as $comment) : ?>
				<tr class="krow2">
					<td colspan="2" class="kcol-first kcommentby-label"><b><?php echo JText::sprintf('COM_KUNENA_BAN_COMMENT_BY', KunenaFactory::getUser(intval($comment->userid))->getLink()) ?></b> :</td>
					<td colspan="1" class="kcol-mid kcommenttime-field"><?php echo KunenaDate::getInstance($comment->time)->toKunena(); ?></td>
					<td colspan="3" class="kcol-mid kcomment-field"><?php echo KunenaHtmlParser::parseText ($comment->comment); ?></td>
				</tr>
				<?php endforeach; ?>
				<?php endforeach; ?>
				<?php else : ?>
				<tr class="krow1">
					<td colspan="6" class="kcol-first"><?php echo JText::sprintf('COM_KUNENA_BAN_USER_NOHISTORY', $this->escape($this->profile->name)); ?></td>
				</tr>
				<?php endif; ?>
			</table>
			</div>
			</div>
		</div>
	</div>
</div>
