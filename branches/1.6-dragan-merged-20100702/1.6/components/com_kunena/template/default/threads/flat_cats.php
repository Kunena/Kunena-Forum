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

global $kunena_icons;

// url of current page that user will be returned to after bulk operation
$kuri = JURI::getInstance ();
$Breturn = $kuri->toString ( array ('path', 'query', 'fragment' ) );
$this->app->setUserState( "com_kunena.ActionBulk", JRoute::_( $Breturn ) );
?>
<div class="kblock">
	<div class="kheader">
		<?php if (CKunenaTools::isModerator($this->my->id)) : ?>
		<span class="kcheckbox select-toggle"><input id="kcbcheckall" type="checkbox" name="toggle" value="" /></span>
		<?php endif; ?>
		<h2><span><?php if (!empty($this->header)) echo $this->escape($this->header); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
<form action="index.php" method="post" name="kBulkActionForm">
<table class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->escape($this->objCatInfo->class_sfx) : ''; ?>" id="kflattable">
		<?php
		$k = 0;
		$counter = 0;
		if (!count ( $this->sub_categories ) ) {
		?>
		<tr class="krow2">
			<td class="first">
				<?php echo $this->func=='showcat' ? JText::_('COM_KUNENA_VIEW_NO_POSTS') : JText::_('COM_KUNENA_NO_POSTS') ?>
			</td>
		</tr>
		<?php
		} else
			foreach ( $this->sub_categories as $leaf ) :
				$leaf->name = kunena_htmlspecialchars ( $leaf->name );
				$threadPages = ceil ( $leaf->numTopics / $this->config->messages_per_page );

				if ($this->highlight && $counter == $this->highlight) :
					$k = 0;
		?>
		<tr>
			<td class="kcontenttablespacer" colspan="<?php echo intval($this->columns) ?>">&nbsp;</td>
		</tr>
		<?php endif; ?>
		<tr class="k<?php echo $this->tabclass [$k^=1] ?>">
			<td class="kcol-ktopicreplies ktd-kcol-first">
				<strong><?php echo $leaf->numTopics > 0 ? CKunenaTools::formatLargeNumber ( $leaf->numTopics -1 ) : 0; ?></strong>
				<?php echo JText::_('COM_KUNENA_DISCUSSIONS') ?>
			</td>

			<td class="kcol-ktopictitle ktd-kcol-other">
				<?php echo CKunenaLink::GetCategoryPageLink('showcat', intval($leaf->catid), 1, $this->escape($leaf->catname), 'follow') ?>
			</td>

			<td class="kcol-ktopictitle ktd-kcol-other">
				<?php  if($leaf->id_last_msg != 0) : ?>
				<div class="ktopic-title-cover">
					<?php echo CKunenaLink::GetThreadLink ( 'view', intval($leaf->catid), intval($leaf->id_last_msg), KunenaParser::parseText ($leaf->subject), '', 'follow', 'ktopic-title km' ); ?>
				</div>

				<?php if ($leaf->numPosts > $this->config->messages_per_page) : ?>
				<ul class="kpagination">
					<li class="page"><?php echo JText::_('COM_KUNENA_PAGE') ?></li>
					<li><?php echo CKunenaLink::GetThreadPageLink ( 'view', intval($leaf->catid), intval($leaf->id), 1, intval($this->config->messages_per_page), 1 ) ?></li>
					<?php if ($threadPages > 3) : ?>
					<li class="more">...</li>
					<?php $startPage = $threadPages - 2 ?>
				<?php else : $startPage = 2; endif; ?>
				</ul>
				<?php endif; ?>

				<div class="ktopic-details ks">
					<!-- By -->
					<span class="divider fltlft">|</span>
					<span class="ktopic-posted-time" title="<?php echo CKunenaTimeformat::showDate($leaf->time, 'config_post_dateformat_hover'); ?>">
						<?php echo JText::_('COM_KUNENA_TOPIC_STARTED_ON') ?>
						<?php echo CKunenaTimeformat::showDate($leaf->time, 'config_post_dateformat'); ?>&nbsp;
					</span>
					<?php if ($leaf->uname) : ?>
					<span class="ktopic-by"><?php echo JText::_('COM_KUNENA_GEN_BY') . ' ' . CKunenaLink::GetProfileLink ( intval($leaf->userid), $this->escape($leaf->uname) ); ?></span>
					<?php endif; ?>
					<!-- /By -->
				</div>
			<?php else :
				echo JText::_('COM_KUNENA_VIEW_NO_POSTS');
			endif; ?>
			</td>

			<td class="kcol-ktopicviews ktd-kcol-other">
				<!-- Views -->
				<span class="ktopic-views-number"><?php echo CKunenaTools::formatLargeNumber ( ( int ) $leaf->hits );?></span>
				<span class="ktopic-views"> <?php echo JText::_('COM_KUNENA_GEN_HITS'); ?> </span>
				<!-- /Views -->
			</td>

			<?php if ($this->showposts):?>
			<td class="kmycount ktd-kcol-other">
				<!-- Posts -->
				<span class="ktopic-views-number"><?php echo CKunenaTools::formatLargeNumber ( ( int ) $leaf->numPosts ); ?></span>
				<span class="ktopic-views"> <?php echo JText::_('COM_KUNENA_MY_POSTS'); ?> </span>
				<!-- /Posts -->
			</td>
			<?php endif; ?>

			<td class="kcol-ktopiclastpost ktd-kcol-other">
				<div class="klatest-post-info">
					<!-- Avatar -->
					<?php if ($this->config->avataroncat > 0) :
					$profile = KunenaFactory::getUser((int)$leaf->userid);
					$useravatar = $profile->getAvatarLink('klist-avatar', 'list');
					if ($useravatar) :
					?>
						<span class="ktopic-latest-post-avatar"> <?php echo CKunenaLink::GetProfileLink ( intval($leaf->userid), $useravatar ); ?></span>
					<?php endif; ?>
					<?php endif; ?>
					<!-- /Avatar -->
					<!-- Latest Post -->
					<span class="ktopic-latest-post">
						<?php
						if ($this->config->default_sort == 'asc') {
							echo CKunenaLink::GetThreadPageLink ( 'view', intval($leaf->catid), intval($leaf->thread), $threadPages, intval($this->config->messages_per_page), JText::_('COM_KUNENA_GEN_LAST_POST'), intval($leaf->id) );
						} else {
							echo CKunenaLink::GetThreadPageLink ( 'view', intval($leaf->catid), intval($leaf->thread), 1, intval($this->config->messages_per_page), JText::_('COM_KUNENA_GEN_LAST_POST'), intval($leaf->id) );
						}
						if ($leaf->uname) echo ' ' . JText::_('COM_KUNENA_GEN_BY') . ' ' . CKunenaLink::GetProfileLink ( intval($leaf->userid), $this->escape($leaf->uname), '', 'nofollow' );
						?>
					</span>
					<!-- /Latest Post -->
					<br />
					<!-- Latest Post Date -->
					<span class="ktopic-date" title="<?php echo CKunenaTimeformat::showDate($this->lastreply [$leaf->thread]->time, 'config_post_dateformat_hover'); ?>">
						<?php echo CKunenaTimeformat::showDate($leaf->time, 'config_post_dateformat'); ?>
					</span>
					<!-- /Latest Post Date -->
				</div>
			</td>

			<?php if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) : ?>
			<td class="krowmoderation ktd-kcol-first">
				<input class ="kDelete_bulkcheckboxes" type="checkbox" name="cb[<?php echo intval($leaf->id) ?>]" value="0"  />
			</td>
			<?php endif; ?>
		</tr>
		<?php endforeach; ?>

		<?php
		if ( CKunenaTools::isModerator ( $this->my->id, $this->catid ) ) :
			$appfunc = JRequest::getCmd('func');
		?>
		<!-- Moderator Bulk Actions -->
		<tr class="ksectiontableentry1">
			<td colspan="7" align="right" class="krowmoderation ktd-kcol-first">
				<select name="do" id="kBulkChooseActions" class="inputbox ks">
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
				<input type="submit" name="kBulkActionsGo" class="kbutton ks" value="<?php echo JText::_('COM_KUNENA_GO'); ?>" />
			</td>
		</tr>
		<!-- /Moderator Bulk Actions -->
		<?php endif; ?>
</table>
<input type="hidden" name="option" value="com_kunena" />
<input type="hidden" name="func" value="bulkactions" />
</form>
</div>
</div>
</div>