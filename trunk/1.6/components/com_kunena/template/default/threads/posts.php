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
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

// url of current page that user will be returned to after bulk operation
$kuri = JURI::getInstance ();
$Breturn = $kuri->toString ( array ('path', 'query', 'fragment' ) );
$this->app->setUserState( "com_kunena.ActionBulk", JRoute::_( $Breturn ) );
?>
<div class="k-bt-cvr1">
<div class="k-bt-cvr2">
<div class="k-bt-cvr3">
<div class="k-bt-cvr4">
<div class="k_bt_cvr5">
<form action="index.php" method="post" name="kBulkActionForm">
<table class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->escape($this->objCatInfo->class_sfx) : ''; ?>" id="kflattable">
	<thead>
		<tr>
			<th colspan="<?php echo intval($this->columns) ?>">
				<div class="ktitle-cover km"><span class="ktitle kl"><?php if (!empty($this->header)) echo $this->escape($this->header); ?></span></div>
				<?php if (CKunenaTools::isModerator($this->my->id)) : ?>
				<div class="kcheckbox select-toggle"><input id="kcbcheckall" type="checkbox" name="toggle" value="" /></div>
				<?php endif; ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$k = 0;
		$counter = 0;
		if (!count ( $this->customreply )) { ?>
		<tr class="ksectiontableentry2">
			<td class="td-0 km kcenter"><?php echo $this->func=='showcat' ? JText::_('COM_KUNENA_VIEW_NO_POSTS') : JText::_('COM_KUNENA_NO_POSTS') ?></td>
		</tr>
		<?php
		} else
			foreach ( $this->customreply as $message ) {
				$lastreply = $this->lastreply[$message->thread];
				$firstpost = $this->threads[$message->thread];
		?>
		<tr class="k<?php echo $this->tabclass [$k^=1];
		if ($firstpost->ordering != 0) {
			echo '-stickymsg';
		}
		if ($firstpost->class_sfx) {
			echo ' k' . $this->tabclass [$k^1];
			if ($firstpost->ordering != 0) {
				echo '-stickymsg';
			}
			echo $this->escape($firstpost->class_sfx);
		}
		if ($message->hold == 1) echo ' kunapproved';
		else if ($message->hold) echo ' kdeleted';
		?>">
			<td class="td-0 kcenter">
				<?php echo CKunenaTools::topicIcon($message) ?>
			</td>

			<td class="td-3">
			<?php
				$curMessageNo = $message->msgcount - ($message->unread ? $message->unread - 1 : 0);
				$threadPages = ceil ( $message->msgcount / $this->config->messages_per_page );
				$unreadPage = ceil ( $curMessageNo / $this->config->messages_per_page );

				if ($message->attachments) {
					echo CKunenaTools::showIcon ( 'ktopicattach', JText::_('COM_KUNENA_ATTACH') );
				}
			?>
				<div class="ktopic-title-cover">
					<?php echo CKunenaLink::GetThreadLink ( 'view', intval($message->catid), intval($message->id), KunenaParser::parseText ($message->subject), KunenaParser::stripBBCode ($message->message), 'follow', 'ktopic-title km' ) ?>
				</div>
				<div style="display:none"><?php echo KunenaParser::parseBBCode ($message->message);?></div>
			</td>

			<td class="td-3">
				<?php echo CKunenaLink::GetThreadLink ( 'view', intval($firstpost->catid), intval($firstpost->id), KunenaParser::parseText ($firstpost->subject), KunenaParser::stripBBCode ($firstpost->message), 'follow', 'ktopic-title km' ) ?>
				<?php
				if ($message->favcount ) {
					if ($message->myfavorite) {
						echo CKunenaTools::showIcon ( 'kfavoritestar', JText::_('COM_KUNENA_FAVORITE') );
					} else {
						echo CKunenaTools::showIcon ( 'kfavoritestar-grey', JText::_('COM_KUNENA_FAVORITE') );
					}
				}
				?>
				<?php
				if ($message->unread) {
					echo CKunenaLink::GetThreadPageLink ( 'view', intval($message->catid), intval($message->id), $unreadPage, intval($this->config->messages_per_page), '<sup class="knewchar">&nbsp;(' . intval($message->unread) . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>', intval($message->lastread) );
				}
				if ($message->locked != 0) {
					echo CKunenaTools::showIcon ( 'ktopiclocked', JText::_('COM_KUNENA_GEN_LOCKED_TOPIC') );
				}
				?>
				<div class="ks">
					<!-- Category -->
					<span class="ktopic-category">
						<?php echo JText::_('COM_KUNENA_CATEGORY') . ' ' . CKunenaLink::GetCategoryLink ( 'showcat', intval($message->catid), $this->escape( $message->catname ) ) ?>
					</span>
					<!-- /Category -->
				</div>
			</td>
			<td class="td-5 ks">
				<div class="klatest-post-info">
					<!--  Sticky   -->
					<?php
					if ($this->messages[$message->id]->ordering != 0) {
						echo CKunenaTools::showIcon ( 'ktopicsticky', JText::_('COM_KUNENA_GEN_ISSTICKY') );
					}
					?>
					<!--  /Sticky   -->
					<!-- Avatar -->
					<?php
					if ($this->config->avataroncat > 0) :
						$profile = KunenaFactory::getUser((int)$this->messages[$message->id]->userid);
						$useravatar = $profile->getAvatarLink('klist-avatar', 'list');
						if ($useravatar) :
					?>
					<span class="ktopic-latest-post-avatar">
					<?php echo CKunenaLink::GetProfileLink ( intval($this->messages[$message->id]->userid), $useravatar ) ?>
					</span>
					<?php
						endif;
					endif;
					?>
					<!-- /Avatar -->
					<!-- By -->
					<span class="ktopic-posted-time" title="<?php echo CKunenaTimeformat::showDate($message->time, 'config_post_dateformat_hover'); ?>">
						<?php echo JText::_('COM_KUNENA_POSTED_AT') . ' ' . CKunenaTimeformat::showDate($message->time, 'config_post_dateformat'); ?>&nbsp;
					</span>

					<?php if ($message->name) : ?>
					<br />
					<span class="ktopic-by"><?php echo JText::_('COM_KUNENA_GEN_BY') . ' ' . CKunenaLink::GetProfileLink ( intval($message->userid), $this->escape($message->name) ); ?></span>
					<?php endif; ?>
					<!-- /By -->
				</div>
			</td>

			<?php if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) : ?>
			<td class="td-7 kcenter">
				<input class ="kDelete_bulkcheckboxes" type="checkbox" name="cb[<?php echo intval($message->id) ?>]" value="0" />
			</td>
			<?php endif; ?>
		</tr>

		<?php } ?>
		<?php  if ( CKunenaTools::isModerator ( $this->my->id, $this->catid ) ) : ?>
		<!-- Moderator Bulk Actions -->
		<tr class="ksectiontableentry1">
			<td colspan="7" align="right" class="td-1 ks">
				<select name="do" id="kBulkChooseActions" class="inputbox ks">
					<option value="">&nbsp;</option>
					<option value="bulkDel"><?php echo JText::_('COM_KUNENA_DELETE_SELECTED'); ?></option>
					<option value="bulkMove"><?php echo JText::_('COM_KUNENA_MOVE_SELECTED'); ?></option>
				</select>
				<?php CKunenaTools::showBulkActionCats (); ?>
				<input type="submit" name="kBulkActionsGo" class="kbutton ks" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
			</td>
		</tr>
		<!-- /Moderator Bulk Actions -->
		<?php endif; ?>
	</tbody>
</table>
<input type="hidden" name="option" value="com_kunena" />
<input type="hidden" name="func" value="bulkactions" />
</form>
</div>
</div>
</div>
</div>
</div>