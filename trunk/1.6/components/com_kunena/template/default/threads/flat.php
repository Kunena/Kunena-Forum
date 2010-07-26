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
<div class="kblock kflat">
	<div class="kheader">
		<?php if (CKunenaTools::isModerator($this->my->id)) : ?>
		<span class="kcheckbox select-toggle"><input id="kcbcheckall" type="checkbox" name="toggle" value="" /></span>
		<?php endif; ?>
		<h2><span><?php if (!empty($this->header)) echo $this->header; ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
<form action="index.php" method="post" name="kBulkActionForm">
<table class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->escape($this->objCatInfo->class_sfx) : ''; ?>" id="kflattable">
	<?php
	$k = 0;
	$counter = 0;
	if (!count ( $this->threads ) && !$this->hasSubCats) { ?>
		<tr class="krow2">
			<td class="kcol-first">
				<?php echo $this->func=='showcat' ? JText::_('COM_KUNENA_VIEW_NO_POSTS') : JText::_('COM_KUNENA_NO_POSTS') ?>
			</td>
		</tr>
	<?php
	} else foreach ( $this->threads as $leaf ) {
		if ($leaf->moved == 1) $leaf->topic_emoticon = 3;
		$curMessageNo = $leaf->msgcount - ($leaf->unread ? $leaf->unread - 1 : 0);
		$threadPages = ceil ( $leaf->msgcount / $this->config->messages_per_page );
		$unreadPage = ceil ( $curMessageNo / $this->config->messages_per_page );

		if ($this->highlight && $counter == $this->highlight) {
			$k = 0;
	?>
		<tr>
			<td class="kcontenttablespacer" colspan="<?php echo intval($this->columns) ?>">&nbsp;</td>
		</tr>
	<?php
		}
		$counter ++;
	?>

		<tr class="k<?php echo $this->tabclass [$k^=1];
		if ($leaf->ordering != 0 || ($leaf->myfavorite && $this->func == 'mylatest')) {
			echo '-stickymsg';
		}
		if ($leaf->class_sfx) {
			echo ' k' . $this->tabclass [$k^1];
			if ($leaf->ordering != 0 || ($leaf->myfavorite && $this->func == 'mylatest')) {
				echo '-stickymsg';
			}
			echo $this->escape($leaf->class_sfx);
		}
		if ($leaf->hold == 1) echo ' kunapproved';
		else if ($leaf->hold) echo ' kdeleted';
		?>">
			<td class="kcol-first kcol-ktopicreplies">
				<strong> <?php echo CKunenaTools::formatLargeNumber ( $leaf->msgcount-1 ); ?> </strong><?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?>
			</td>

			<td class="kcol-mid kcol-ktopicicon">
				<?php echo CKunenaLink::GetThreadPageLink ( 'view', intval($leaf->catid), intval($leaf->id), $unreadPage, intval($this->config->messages_per_page), CKunenaTools::topicIcon($leaf), intval($leaf->lastread) ) ?>
			</td>

			<td class="kcol-mid kcol-ktopictitle">
				<?php if ($leaf->attachments) echo CKunenaTools::showIcon ( 'ktopicattach', JText::_('COM_KUNENA_ATTACH') ); ?>
				<div class="ktopic-title-cover"><?php echo CKunenaLink::GetThreadLink ( 'view', intval($leaf->catid), intval($leaf->id), KunenaParser::parseText ($leaf->subject), KunenaParser::stripBBCode ( $leaf->message, 500), 'follow', 'ktopic-title km' ); ?>
				<?php
				if ($leaf->favcount ) {
					if ($leaf->myfavorite) {
						echo CKunenaTools::showIcon ( 'kfavoritestar', JText::_('COM_KUNENA_FAVORITE') );
					} else {
						echo CKunenaTools::showIcon ( 'kfavoritestar-grey', JText::_('COM_KUNENA_FAVORITE') );
					}
				}
				?>
				<?php
				if ($leaf->unread) {
					echo CKunenaLink::GetThreadPageLink ( 'view', intval($leaf->catid), intval($leaf->id), $unreadPage, intval($this->config->messages_per_page), '<sup class="knewchar">(' . intval($leaf->unread) . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>', intval($leaf->lastread) );
				}
				if ($leaf->locked != 0) {
					echo CKunenaTools::showIcon ( 'ktopiclocked', JText::_('COM_KUNENA_GEN_LOCKED_TOPIC') );
				}
				?>
				</div>

				<?php if ($leaf->msgcount > $this->config->messages_per_page) : ?>
				<ul class="kpagination">
					<li class="page"><?php echo JText::_('COM_KUNENA_PAGE') ?></li>
					<li><?php echo CKunenaLink::GetThreadPageLink ( 'view', intval($leaf->catid), intval($leaf->id), 1, intval($this->config->messages_per_page), 1 ) ?></li>
					<?php if ($threadPages > 3) : $startPage = $threadPages - 2; ?>
					<li class="more">...</li>
					<?php else: $startPage = 2; endif;
					for($hopPage = $startPage; $hopPage <= $threadPages; $hopPage ++) : ?>
					<li><?php echo CKunenaLink::GetThreadPageLink ( 'view', intval($leaf->catid), intval($leaf->thread), $hopPage, intval($this->config->messages_per_page), $hopPage ) ?></li>
					<?php endfor; ?>
				</ul>
				<?php endif; ?>

				<div class="ktopic-details">
					<!-- By -->
					<?php if ($this->func != 'showcat') : ?>
					<!-- Category -->
					<span class="ktopic-category"> <?php echo JText::_('COM_KUNENA_CATEGORY') . ' ' . CKunenaLink::GetCategoryLink ( 'showcat', intval($leaf->catid), $this->escape( $leaf->catname) ) ?></span>
					<!-- /Category -->
					<span class="divider fltlft">|</span>
					<?php endif; ?>
					<span class="ktopic-posted-time" title="<?php echo CKunenaTimeformat::showDate($leaf->time, 'config_post_dateformat_hover'); ?>">
						<?php echo JText::_('COM_KUNENA_TOPIC_STARTED_ON') ?>
						<?php echo CKunenaTimeformat::showDate($leaf->time, 'config_post_dateformat');?>&nbsp;
					</span>

					<?php if ($leaf->name) : ?>
					<span class="ktopic-by ks"><?php echo JText::_('COM_KUNENA_GEN_BY') . ' ' . CKunenaLink::GetProfileLink ( intval($leaf->userid), $this->escape($leaf->name) ); ?></span>
					<?php endif; ?>
					<!-- /By -->
				</div>
			</td>
			<td class="kcol-mid kcol-ktopicviews">
				<!-- Views -->
				<span class="ktopic-views-number"><?php echo CKunenaTools::formatLargeNumber ( intval($leaf->hits) );?></span>
				<span class="ktopic-views"> <?php echo JText::_('COM_KUNENA_GEN_HITS');?> </span>
				<!-- /Views -->
			</td>
			<?php if ($this->showposts):?>
			<td class="kcol-mid kmycount">
				<!-- Posts -->
				<span class="ktopic-views-number"><?php echo CKunenaTools::formatLargeNumber ( intval($leaf->mycount) ); ?></span>
				<span class="ktopic-views"> <?php echo JText::_('COM_KUNENA_MY_POSTS'); ?> </span>
				<!-- /Posts -->
			</td>
			<?php endif; ?>
			<td class="kcol-mid kcol-ktopiclastpost">
				<div class="klatest-post-info">
					<?php
					if ($leaf->ordering != 0) :
						echo CKunenaTools::showIcon ( 'ktopicsticky', JText::_('COM_KUNENA_GEN_ISSTICKY') );
					endif; ?>
					<!-- Avatar -->
					<?php
					if ($this->config->avataroncat > 0) :
						$profile = KunenaFactory::getUser(intval($this->lastreply [$leaf->thread]->userid));
						$useravatar = $profile->getAvatarLink('klist-avatar', 'list');
						if ($useravatar) :
					?>
					<span class="ktopic-latest-post-avatar"> <?php echo CKunenaLink::GetProfileLink ( intval($this->lastreply [$leaf->thread]->userid), $useravatar ) ?></span>
					<?php
						endif;
					endif;
					?>
					<!-- /Avatar -->
					<!-- Latest Post -->
					<span class="ktopic-latest-post">
					<?php
					if ($leaf->moved) :
						echo JText::_('COM_KUNENA_MOVED');
					// FIXME: use user setting!
					elseif ($this->config->default_sort == 'asc') :
						echo CKunenaLink::GetThreadPageLink ( 'view', intval($leaf->catid), intval($leaf->thread), $threadPages, intval($this->config->messages_per_page), JText::_('COM_KUNENA_GEN_LAST_POST'), intval($this->lastreply [$leaf->thread]->id) );
					else :
						echo CKunenaLink::GetThreadPageLink ( 'view', intval($leaf->catid), intval($leaf->thread), 1, intval($this->config->messages_per_page), JText::_('COM_KUNENA_GEN_LAST_POST'), intval($this->lastreply [$leaf->thread]->id) );
					endif;

					if ($leaf->name)
						echo ' ' . JText::_('COM_KUNENA_GEN_BY') . ' ' . CKunenaLink::GetProfileLink ( intval($this->lastreply [$leaf->thread]->userid), $this->escape($this->lastreply [$leaf->thread]->name), '', 'nofollow' );
					?>
					</span>
					<!-- /Latest Post -->
					<br />
					<!-- Latest Post Date -->
					<span class="ktopic-date" title="<?php echo CKunenaTimeformat::showDate($this->lastreply [$leaf->thread]->time, 'config_post_dateformat_hover'); ?>">
						<?php echo CKunenaTimeformat::showDate($this->lastreply [$leaf->thread]->time, 'config_post_dateformat'); ?>
					</span>
					<!-- /Latest Post Date -->
				</div>
			</td>

			<?php if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) : ?>
			<td class="kcol-mid ktopicmoderation">
				<input class ="kDelete_bulkcheckboxes" type="checkbox" name="cb[<?php echo intval($leaf->id)?>]" value="0" />
			</td>
			<?php endif; ?>
		</tr>

		<?php } ?>
		<?php if ( CKunenaTools::isModerator ( $this->my->id, $this->catid ) ) : ?>
		<!-- Actions -->
		<tr class="krow1">
			<td colspan="7" class="kcol-first krowmoderation">
				<select name="do" id="kBulkChooseActions" class="inputbox">
				<option value="">&nbsp;</option>
				<option value="bulkDel"><?php echo JText::_('COM_KUNENA_DELETE_SELECTED'); ?></option>
				<option value="bulkMove"><?php echo JText::_('COM_KUNENA_MOVE_SELECTED'); ?></option>
				<?php if ( $this->func == 'favorites' ) : ?>
				<option value="bulkFavorite"><?php echo JText::_('COM_KUNENA_DELETE_FAVORITE'); ?></option>
				<?php elseif ( $this->func == 'subscriptions' ) : ?>
				<option value="bulkSub"><?php echo JText::_('COM_KUNENA_DELETE_SUBSCRIPTION'); ?></option>
				<?php endif; ?>
			</select>
			<?php CKunenaTools::showBulkActionCats (); ?>
			<input type="submit" name="kBulkActionsGo" class="kbutton" value="<?php echo JText::_('COM_KUNENA_GO'); ?>" /></td>
		</tr>
		<!-- /Actions -->
		<?php endif; ?>
</table>
<input type="hidden" name="option" value="com_kunena" />
<input type="hidden" name="func" value="bulkactions" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
</div>
</div>