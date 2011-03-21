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

// url of current page that user will be returned to after bulk operation
$kuri = JURI::getInstance ();
$Breturn = $kuri->toString ( array ('path', 'query', 'fragment' ) );
$this->app->setUserState( "com_kunena.ActionBulk", JRoute::_( $Breturn ) );
?>
<div class="kblock">
	<div class="kheader">

		<h2><span><?php if (!empty($this->header)) echo $this->escape($this->header); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
		<table class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->escape($this->objCatInfo->class_sfx) : ''; ?>" id="kflattable">
		<?php if (!count ( $this->categories ) ) { ?>
		<tr class="krow2">
			<td class="kcol-first">
				<?php echo JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS_NONE') ?>
			</td>
		</tr>
		<?php
		} else
			$k = 0;
			foreach ( $this->categories as $leaf ) : ?>
		<tr class="k<?php echo $this->tabclass [$k^=1] ?>">
			<td class="kcol-mid kcol-ktopictitle">
				<div class="ktopic-title-cover">
				<?php echo CKunenaLink::GetCategoryPageLink('showcat', intval($leaf->catid), 1, $this->escape($leaf->catname), 'follow', 'ktopic-title km' ) ?>
				</div>
			</td>

			<td class="kcol-mid kcol-ktopicviews">
				<!-- Views -->
				<span class="ktopic-views-number"><?php echo CKunenaTools::formatLargeNumber ( ( int ) $leaf->numTopics );?></span>
				<span class="ktopic-views"> <?php echo JText::_('COM_KUNENA_DISCUSSIONS'); ?> </span>
				<!-- /Views -->
			</td>

			<?php if ($this->showposts):?>
			<td class="kcol-mid kmycount">
				<!-- Posts -->
				<span class="ktopic-views-number"><?php echo CKunenaTools::formatLargeNumber ( ( int ) $leaf->numPosts ); ?></span>
				<span class="ktopic-views"> <?php echo JText::_('COM_KUNENA_MY_POSTS'); ?> </span>
				<!-- /Posts -->
			</td>
			<?php endif; ?>

			<td class="kcol-mid kcol-ktopiclastpost">
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
						if ($this->topic_ordering == 'ASC') {
							$threadPages = ceil ( $leaf->msgcount / $this->config->messages_per_page );
							echo JText::_('COM_KUNENA_GEN_LAST_POST').': '.CKunenaLink::GetThreadPageLink ( 'view', intval($leaf->catid), intval($leaf->thread), $threadPages, intval($this->config->messages_per_page), KunenaParser::parseText ($leaf->subject), intval($leaf->msgid) );
						} else {
							echo CKunenaLink::GetThreadPageLink ( 'view', intval($leaf->catid), intval($leaf->thread), 1, intval($this->config->messages_per_page), JText::_('COM_KUNENA_GEN_LAST_POST'), intval($leaf->msgid) );
						}
						if ($leaf->uname) echo ' ' . JText::_('COM_KUNENA_GEN_BY') . ' ' . CKunenaLink::GetProfileLink ( intval($leaf->userid), $this->escape($leaf->uname), '', 'nofollow' );
						?>
					</span>
					<!-- /Latest Post -->
					<br />
					<!-- Latest Post Date -->
					<span class="ktopic-date" title="<?php echo CKunenaTimeformat::showDate($leaf->time, 'config_post_dateformat_hover'); ?>">
						<?php echo CKunenaTimeformat::showDate($leaf->time, 'config_post_dateformat'); ?>
					</span>
					<!-- /Latest Post Date -->
				</div>
			</td>

			<td class="kcol-mid">
				<?php echo CKunenaLink::GetCategoryActionLink ( 'unsubscribecat', $leaf->catid, JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY'), 'nofollow', '', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY_LONG'), '&userid='.$this->my->id ); ?>
			</td>

		</tr>
		<?php endforeach; ?>

</table>
</div>
</div>
</div>