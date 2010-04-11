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

global $kunena_icons, $topic_emoticons;

// url of current page that user will be returned to after bulk operation
$kuri = JURI::getInstance ();
$Breturn = $kuri->toString ( array ('path', 'query', 'fragment' ) );
$this->app->setUserState( "com_kunena.ActionBulk", JRoute::_( $Breturn ) );

	?>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<form action="index.php" method="post" name="kBulkActionForm">

<table
	class="<?php
	echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : '';
	?>" id="kflattable">
	<thead>
		<tr>
			<th
				colspan="<?php
	echo $this->columns;
	?>">

		<div class="ktitle_cover km"><span class="ktitle kl">

	<?php if (!empty($this->header)) echo $this->header; ?></span></div>
	<?php if (CKunenaTools::isModerator($this->my->id)) { ?>
	<div class="kcheckbox select-toggle">
		<input id="kcbcheckall" type="checkbox" name="toggle" value="" />
	</div>
	<?php } ?>
		</th>
		</tr>
	</thead>
	<tbody>
		<?php
	$k = 0;
	$counter = 0;
	if (!count ( $this->threads )) {
		echo '<tr class="ksectiontableentry2"><td class="td-0 km center">' . ($this->func=='showcat'?JText::_('COM_KUNENA_VIEW_NO_POSTS'):JText::_('COM_KUNENA_NO_POSTS')) . '</td></tr>';
	} else
	foreach ( $this->threads as $leaf ) {
		$leaf->name = kunena_htmlspecialchars ( stripslashes ( $leaf->name ) );
		$leaf->email = kunena_htmlspecialchars ( stripslashes ( $leaf->email ) );
		if ($leaf->moved == 1) $leaf->topic_emoticon = 3;

		if ($this->highlight && $counter == $this->highlight) {
			$k = 0;
			?>
		<tr>
			<td class="kcontenttablespacer"
				colspan="<?php
			echo $this->columns;
			?>">&nbsp;
			</td>
		</tr>

		<?php
		}
		$counter ++;
		?>

		<tr
			class="k<?php
		echo $this->tabclass [$k^=1];
		if ($leaf->ordering != 0 || ($leaf->myfavorite && $this->func == 'mylatest')) {
			echo '_stickymsg';
		}

		if ($leaf->class_sfx) {
			echo ' k' . $this->tabclass [$k^1];
			if ($leaf->ordering != 0 || ($leaf->myfavorite && $this->func == 'mylatest')) {
				echo '_stickymsg';
			}
			echo $leaf->class_sfx;
		}
		?>">
			<td class="td-0 km center"><strong> <?php
		echo CKunenaTools::formatLargeNumber ( $leaf->msgcount-1 );
		?>
			</strong><?php
		echo JText::_('COM_KUNENA_GEN_REPLIES');
		?></td>

			<td class="td-2 center">
			<img src="<?php echo (isset($topic_emoticons [$leaf->topic_emoticon]) ? $topic_emoticons [$leaf->topic_emoticon] : $topic_emoticons [0]) ?>" alt="emo" />
		</td>

			<td class="td-3"><?php
			$curMessageNo = $leaf->msgcount - ($leaf->unread ? $leaf->unread - 1 : 0);
			$threadPages = ceil ( $leaf->msgcount / $this->config->messages_per_page );
			$unreadPage = ceil ( $curMessageNo / $this->config->messages_per_page );

			if ($leaf->attachments) {
				echo isset ( $kunena_icons ['topicattach'] ) ? '<img  class="attachicon" src="' . KUNENA_URLICONSPATH . $kunena_icons ['topicattach'] . '" border="0" alt="' . JText::_('COM_KUNENA_ATTACH') . '" />' : '<img class="attachicon" src="' . KUNENA_URLICONSPATH . 'attachment.gif"  alt="' . JText::_('COM_KUNENA_ATTACH') . '" title="' . JText::_('COM_KUNENA_ATTACH') . '" />';
			}
			?>
			<div class="k-topic-title-cover"><?php
			echo CKunenaLink::GetThreadLink ( 'view', $leaf->catid, $leaf->id, CKunenaTools::parseText ( stripslashes($leaf->subject) ), CKunenaTools::stripBBCode ( stripslashes($leaf->message), 500), 'follow', 'k-topic-title km' );
			?>
			<?php
			if ($leaf->favcount ) {
				if ($leaf->myfavorite) {
					echo isset ( $kunena_icons ['favoritestar'] ) ? '<img  class="favoritestar" src="' . KUNENA_URLICONSPATH . $kunena_icons ['favoritestar'] . '" border="0" alt="' . JText::_('COM_KUNENA_FAVORITE') . '" />' : '<img class="favoritestar" src="' . KUNENA_URLICONSPATH . 'favoritestar.png"  alt="' . JText::_('COM_KUNENA_FAVORITE') . '" title="' . JText::_('COM_KUNENA_FAVORITE') . '" />';
				} else if (array_key_exists ( 'favoritestar_grey', $kunena_icons )) {
					echo isset ( $kunena_icons ['favoritestar_grey'] ) ? '<img  class="favoritestar" src="' . KUNENA_URLICONSPATH . $kunena_icons ['favoritestar_grey'] . '" border="0" alt="' . JText::_('COM_KUNENA_FAVORITE') . '" />' : '<img class="favoritestar" src="' . KUNENA_URLICONSPATH . 'favoritestar.png"  alt="' . JText::_('COM_KUNENA_FAVORITE') . '" title="' . JText::_('COM_KUNENA_FAVORITE') . '" />';
				}
			}
			?>
			<?php
			if ($leaf->unread) {
					echo CKunenaLink::GetThreadPageLink ( 'view', $leaf->catid, $leaf->id, $unreadPage, $this->config->messages_per_page, '<sup><span class="newchar">&nbsp;(' . $leaf->unread . ' ' . stripslashes ( $this->config->newchar ) . ')</span></sup>', $leaf->lastread );
			}

			if ($leaf->msgcount > $this->config->messages_per_page) {
				echo '<ul class="kpagination">';
				echo '<li class="page">' . JText::_('COM_KUNENA_PAGE') . '</li>';
				echo '<li>' . CKunenaLink::GetThreadPageLink ( 'view', $leaf->catid, $leaf->id, 1, $this->config->messages_per_page, 1 ) . '</li>';

				if ($threadPages > 3) {
					echo ('<li>...</li>');
					$startPage = $threadPages - 2;
				} else {
					$startPage = 2;
				}

				for($hopPage = $startPage; $hopPage <= $threadPages; $hopPage ++) {
					echo '<li>' . CKunenaLink::GetThreadPageLink ( 'view', $leaf->catid, $leaf->thread, $hopPage, $this->config->messages_per_page, $hopPage ) . '</li>';
				}

				echo ("</ul>");
			}
		if ($leaf->locked != 0) {
			?> <!-- Locked --> <span class="topic_locked"> <?php
			echo isset ( $kunena_icons ['topiclocked'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['topiclocked'] . '" border="0" alt="' . JText::_('COM_KUNENA_GEN_LOCKED_TOPIC') . '" />' : '<img src="' . KUNENA_URLEMOTIONSPATH . 'lock.gif"  alt="' . JText::_('COM_KUNENA_GEN_LOCKED_TOPIC') . '" title="' . JText::_('COM_KUNENA_GEN_LOCKED_TOPIC') . '" />';
			?>
			</span> <!-- /Locked --> <?php
		}
		?>
			</div>
			<div class="ks">
			<!-- By -->
				<?php
		if ($this->func != 'showcat') {
			?>
			<!-- Category --> <span class="topic_category"> <?php
			echo JText::_('COM_KUNENA_CATEGORY') . ' ' . CKunenaLink::GetCategoryLink ( 'showcat', $leaf->catid, kunena_htmlspecialchars ( $leaf->catname ) );
			?>
			</span> <!-- /Category -->
			<span class="divider">|</span>
<?php 	} ?>
			<span class="topic_posted_time" title="<?php echo CKunenaTimeformat::showDate($leaf->time, 'config_post_dateformat_hover'); ?>"><?php
		echo JText::_('COM_KUNENA_POSTED_AT')?>
			<?php
			echo CKunenaTimeformat::showDate($leaf->time, 'config_post_dateformat');
		?>&nbsp;</span>

		<?php
		if ($leaf->name) {
			echo '<span class="topic_by">';
			echo JText::_('COM_KUNENA_GEN_BY') . ' ' . CKunenaLink::GetProfileLink ( $leaf->userid, $leaf->name );
			echo '</span>';
		}
		?>
			<!-- /By -->
		</div>
			</td>
			<td class="td-4 center">
			<!-- Views -->
			<span class="topic_views_number"><?php
		echo CKunenaTools::formatLargeNumber ( ( int ) $leaf->hits );
		?></span> <span class="topic_views"> <?php
		echo JText::_('COM_KUNENA_GEN_HITS');
		?> </span> <!-- /Views --></td>
		<?php if ($this->showposts):?>
			<td class="td-4 center">
			<!-- Posts -->
			<span class="topic_views_number"><?php
		echo CKunenaTools::formatLargeNumber ( ( int ) $leaf->mycount );
		?></span> <span class="topic_views"> <?php
		echo JText::_('COM_KUNENA_MY_POSTS');
		?> </span> <!-- /Posts --></td>
		<?php endif; ?>
			<td class="td-6 ks">
			<div class="klatest-post-info"><!--  Sticky   --> <?php
		if ($leaf->ordering != 0) {
			?>
			<span class="topic_sticky"> <?php
			echo isset ( $kunena_icons ['topicsticky'] ) ? '<img  src="' . KUNENA_URLICONSPATH . $kunena_icons ['topicsticky'] . '" border="0" alt="' . JText::_('COM_KUNENA_GEN_ISSTICKY') . '" />' : '<img class="stickyicon" src="' . KUNENA_URLICONSPATH . 'tsticky.gif"  alt="' . JText::_('COM_KUNENA_GEN_ISSTICKY') . '" title="' . JText::_('COM_KUNENA_GEN_ISSTICKY') . '" />';
			?>
			</span> <?php
		}
		?> <!--  /Sticky   --> <!-- Avatar --> <?php
		if ($this->config->avataroncat > 0) :
			$profile = KunenaFactory::getUser((int)$this->lastreply [$leaf->thread]->userid);
			$useravatar = $profile->getAvatarLink('klist_avatar');
			if ($useravatar) :
			?>
			<span class="topic_latest_post_avatar"> <?php
			echo CKunenaLink::GetProfileLink ( $this->lastreply [$leaf->thread]->userid, $useravatar );
			?>
			</span> <?php
			endif;
		endif;
		?> <!-- /Avatar --> <!-- Latest Post --> <span
				class="topic_latest_post"> <?php
		if ($this->config->default_sort == 'asc') {
			if ($leaf->moved == 0)
				echo CKunenaLink::GetThreadPageLink ( 'view', $leaf->catid, $leaf->thread, $threadPages, $this->config->messages_per_page, JText::_('COM_KUNENA_GEN_LAST_POST'), $this->lastreply [$leaf->thread]->id );
			else
				echo JText::_('COM_KUNENA_MOVED') . ' ';
		} else {
			echo CKunenaLink::GetThreadPageLink ( 'view', $leaf->catid, $leaf->thread, 1, $this->config->messages_per_page, JText::_('COM_KUNENA_GEN_LAST_POST'), $this->lastreply [$leaf->thread]->id );
		}

		if ($leaf->name)
			echo ' ' . JText::_('COM_KUNENA_GEN_BY') . ' ' . CKunenaLink::GetProfileLink ( $this->lastreply [$leaf->thread]->userid, stripslashes ( $this->lastreply [$leaf->thread]->name ), 'nofollow' );
		?>
			</span> <!-- /Latest Post --> <br />
			<!-- Latest Post Date --> <span class="topic_date" title="<?php echo CKunenaTimeformat::showDate($this->lastreply [$leaf->thread]->time, 'config_post_dateformat_hover'); ?>"> <?php
			echo CKunenaTimeformat::showDate($this->lastreply [$leaf->thread]->time, 'config_post_dateformat');
		?> </span> <!-- /Latest Post Date --></div>

			</td>

			<?php
		if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			?>

			<td class="td-7 center">
				<input class ="kDelete_bulkcheckboxes" type="checkbox" name="cb[<?php echo $leaf->id?>]" value="0"  />
			</td>

			<?php
		}
		?>
		</tr>

		<?php
	}
	if ( CKunenaTools::isModerator ( $this->my->id, $this->catid ) ) {
		$appfunc = JRequest::getCmd('func');
		?>
		<!-- Moderator Bulk Actions -->
		<tr class="ksectiontableentry1">
			<td colspan="7" align="right" class="td-1 ks">
				<select name="do" id="kBulkChooseActions"
				class="inputbox ks">
				<option value="">&nbsp;</option>
				<option value="bulkDel"><?php
		echo JText::_('COM_KUNENA_DELETE_SELECTED');
		?></option>
				<option value="bulkMove"><?php
		echo JText::_('COM_KUNENA_MOVE_SELECTED');
		?></option>
		<?php

		if ( $appfunc == 'profile' && $this->func == 'favorites' ) {
		?>
			<option value="bulkFavorite"><?php
					echo JText::_('COM_KUNENA_DELETE_FAVORITE');
		} elseif ( $appfunc == 'profile' && $this->func == 'subscriptions' ) {
		?>
			</option>
		<option value="bulkSub"><?php
					echo JText::_('COM_KUNENA_DELETE_SUBSCRIPTION');
		?></option>
		<?php
		}
		?>
			</select> <?php
		CKunenaTools::showBulkActionCats ();
		?> <input type="submit" name="kBulkActionsGo" class="kbutton ks"
				value="<?php
		echo JText::_('COM_KUNENA_GO');
		?>" /></td>
		</tr>
		<!-- /Moderator Bulk Actions -->
		<?php
	}
	?>
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
