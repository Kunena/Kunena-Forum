<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/
// Dont allow direct linking
defined( '_JEXEC' ) or die();

$Breturn = $this->uri->toString ( array ('path', 'query', 'fragment' ) );
$this->app->setUserState( "com_kunena.ReviewURL", JRoute::_( $Breturn ) );
?>
<div class="kblock kflat">
	<div class="kheader">
		<?php if (CKunenaTools::isModerator($this->my->id)) : ?>
		<span class="kcheckbox select-toggle"><input id="kcbcheckall" type="checkbox" name="toggle" value="" /></span>
		<?php endif; ?>
		<h2><span><?php if (!empty($this->header)) echo $this->header; ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
			<form action="index.php" method="post" name="kApproveMessagesForm">
			<table class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->escape($this->objCatInfo->class_sfx) : ''; ?>" id="kflattable">
			<?php if (!count ( $this->MessagesToApprove )) { ?>
		<tr class="krow2">
			<td class="kcol-first">
				<?php echo JText::_('COM_KUNENA_MOD_NOTHING') ?>
			</td>
		</tr>
	<?php } else {
		$k = 0;
		foreach ( $this->MessagesToApprove as $mes ) { ?>
			<tr class="k<?php echo $this->tabclass [$k^=1];?>" >
				<td class="kcol-mid kcol-ktopicicon">
				<?php echo CKunenaLink::GetThreadPageLink ( 'view', intval($mes->catid), intval($mes->id), '1', intval($this->config->messages_per_page), CKunenaTools::topicIcon($mes), '' ) ?>
				</td>
				<td class="kcol-mid kcol-ktopictitle">
				<div class="ktopic-title-cover"><?php echo CKunenaLink::GetThreadLink ( 'view', intval($mes->catid), intval($mes->id), KunenaParser::parseText ($mes->subject), KunenaParser::stripBBCode ( $mes->message, 500), 'follow', 'ktopic-title km' ); ?>

				</div>

				<div class="ktopic-details">
					<!-- By -->
					<!-- Category -->
					<span class="ktopic-category"> <?php echo JText::_('COM_KUNENA_CATEGORY') . ' ' . CKunenaLink::GetCategoryLink ( 'showcat', intval($mes->catid), $this->escape( $mes->catname) ) ?></span>
					<!-- /Category -->
					<span class="divider fltlft">|</span>
					<span class="ktopic-posted-time" title="<?php echo CKunenaTimeformat::showDate($leaf->time, 'config_post_dateformat_hover'); ?>">
						<?php echo JText::_('COM_KUNENA_TOPIC_STARTED_ON') ?>
						<?php echo CKunenaTimeformat::showDate($mes->time, 'config_post_dateformat');?>&nbsp;
					</span>

					<?php if ($mes->name) : ?>
					<span class="ktopic-by ks"><?php echo JText::_('COM_KUNENA_GEN_BY') . ' ' . CKunenaLink::GetProfileLink ( intval($mes->userid), $this->escape($mes->name) ); ?></span>
					<?php endif; ?>
					<!-- /By -->
				</div>
				</td>
				<td class="kcol-mid kcol-ktopicviews">
					<!-- Views -->
					<span class="ktopic-views-number"><?php echo CKunenaTools::formatLargeNumber ( intval($mes->hits) );?></span>
					<span class="ktopic-views"> <?php echo JText::_('COM_KUNENA_GEN_HITS');?> </span>
					<!-- /Views -->
				</td>
				<td class="kcol-mid">
					<?php echo smile::smileReplace($mes->message, 0, $this->config->disemoticons, smile::getEmoticons("")); ?>
				</td>
				<?php if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) : ?>
				<td class="kcol-mid ktopicmoderation">
					<input class ="kDelete_bulkcheckboxes" type="checkbox" name="cb[<?php echo intval($mes->id)?>]" value="0" />
				</td>
				<?php endif; ?>
			</tr>
	<?php }
	} ?>
			<tr class="krow1">
			<td colspan="7" class="kcol-first krowmoderation">
				<?php if ($this->embedded) echo CKunenaLink::GetReviewLink(JText::_('COM_KUNENA_MORE')); ?>
				<select name="do" id="kApproveChooseActions" class="inputbox">
					<option value="">&nbsp;</option>
					<option value="modapprove"><?php echo JText::_('COM_KUNENA_APPROVE_SELECTED'); ?></option>
					<option value="moddelete"><?php echo JText::_('COM_KUNENA_DELETE_SELECTED'); ?></option>
				</select>

			<input type="submit" name="kBulkActionsGo" class="kbutton" value="<?php echo JText::_('COM_KUNENA_GO'); ?>" /></td>
			</tr>
			</table>
			<input type="hidden" name="option" value="com_kunena" />
			<input type="hidden" name="func" value="review" />
			<?php echo JHTML::_( 'form.token' ); ?>
			</form>
		</div>
	</div>
</div>
