<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
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

$tabclass = array ("row1", "row2" );
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
		<table class="kblocktable" id="kflattable">
		<?php if (!count ( $this->categories ) ) { ?>
		<tr class="krow2">
			<td class="kcol-first">
				<?php echo JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS_NONE') ?>
			</td>
		</tr>
		<?php
		} else
			$k = 0;
			foreach ( $this->categories as $category ) :
			?>
		<tr class="k<?php echo $tabclass [$k^=1] ?>">
			<td class="kcol-mid kcol-ktopictitle">
				<div class="ktopic-title-cover">
				<?php echo CKunenaLink::GetCategoryPageLink('showcat', intval($category->id), 1, $this->escape($category->name), 'follow', 'ktopic-title km' ) ?>
				</div>
			</td>

			<td class="kcol-mid kcol-ktopicviews">
				<!-- Views -->
				<span class="ktopic-views-number"><?php echo CKunenaTools::formatLargeNumber ( ( int ) $category->numTopics );?></span>
				<span class="ktopic-views"> <?php echo JText::_('COM_KUNENA_DISCUSSIONS'); ?> </span>
				<!-- /Views -->
			</td>

			<?php if ($this->showposts):?>
			<td class="kcol-mid kmycount">
				<!-- Posts -->
				<span class="ktopic-views-number"><?php echo CKunenaTools::formatLargeNumber ( ( int ) $category->numPosts ); ?></span>
				<span class="ktopic-views"> <?php echo JText::_('COM_KUNENA_MY_POSTS'); ?> </span>
				<!-- /Posts -->
			</td>
			<?php endif; ?>

			<td class="kcol-mid kcol-ktopiclastpost">
				<div class="klatest-post-info">
					<!-- Avatar -->
					<?php if ($this->config->avataroncat > 0) :
					$profile = KunenaFactory::getUser((int)$category->last_post_userid);
					$useravatar = $profile->getAvatarLink('klist-avatar', 'list');
					if ($useravatar) :
					?>
						<span class="ktopic-latest-post-avatar"> <?php echo CKunenaLink::GetProfileLink ( intval($category->last_post_userid), $useravatar ); ?></span>
					<?php endif; ?>
					<?php endif; ?>
					<!-- /Avatar -->
					<!-- Latest Post -->
					<span class="ktopic-latest-post">
						<?php
						if ($this->topic_ordering == 'ASC') {
							echo JText::_('COM_KUNENA_GEN_LAST_POST').': '.CKunenaLink::GetThreadPageLink ( 'view', intval($category->id), intval($category->last_topic), $category->last_topic_posts, intval($this->config->messages_per_page), KunenaHtmlParser::parseText ($category->last_topic_subject), intval($category->last_post_id) );
						} else {
							echo CKunenaLink::GetThreadPageLink ( 'view', intval($category->id), intval($category->last_topic_id), 0, intval($this->config->messages_per_page), JText::_('COM_KUNENA_GEN_LAST_POST'), intval($category->last_post_id) );
						}
						echo ' ' . JText::_('COM_KUNENA_GEN_BY') . ' ' . CKunenaLink::GetProfileLink ( intval($category->last_post_userid), null, '', 'nofollow' );
						?>
					</span>
					<!-- /Latest Post -->
					<br />
					<!-- Latest Post Date -->
					<span class="ktopic-date" title="<?php echo CKunenaTimeformat::showDate($category->last_post_time, 'config_post_dateformat_hover'); ?>">
						<?php echo CKunenaTimeformat::showDate($category->last_post_time, 'config_post_dateformat'); ?>
					</span>
					<!-- /Latest Post Date -->
				</div>
			</td>

			<td class="kcol-mid">
				<?php echo CKunenaLink::GetCategoryActionLink ( 'unsubscribecat', $category->id, JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY'), 'nofollow', '', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY_LONG'), '&userid='.$this->my->id ); ?>
			</td>

		</tr>
		<?php endforeach; ?>

</table>
</div>
</div>
</div>