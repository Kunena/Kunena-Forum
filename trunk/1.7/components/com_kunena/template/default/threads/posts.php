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

$tabclass = array ("row1", "row2" );
// url of current page that user will be returned to after bulk operation
$kuri = JURI::getInstance ();
$Breturn = $kuri->toString ( array ('path', 'query', 'fragment' ) );
$this->app->setUserState( "com_kunena.ActionBulk", JRoute::_( $Breturn ) );
?>
<form action="index.php" method="post" name="kBulkActionForm">
<div class="kblock">
	<div class="kheader">
		<h2><span><?php if (!empty($this->header)) echo $this->escape($this->header); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
<table
	class="<?php
	echo isset ( $this->category->class_sfx ) ? ' kblocktable' . $this->escape($this->category->class_sfx) : '';
	?>">
		<?php
		$k = 0;
		$counter = 0;
		if (!count ( $this->messages )) { ?>
		<tr class="krow2">
			<td class="kcol-first kcol-ktopicicon"><?php echo $this->func=='showcat' ? JText::_('COM_KUNENA_VIEW_NO_POSTS') : JText::_('COM_KUNENA_NO_POSTS') ?></td>
		</tr>
		<?php
		} else
			foreach ( $this->messages as $message ) {
				$topic = $this->topics[$message->thread];
				$category = KunenaForumCategoryHelper::get($topic->category_id);
		?>
		<tr class="k<?php echo $tabclass [$k^=1];
		if ($topic->ordering != 0) {
			echo '-stickymsg';
		}
		if (!empty($this->category->class_sfx)) {
			echo ' k' . $tabclass [$k^1];
			if ($topic->ordering != 0) {
				echo '-stickymsg';
			}
			echo $this->escape($this->category->class_sfx);
		}
		if ($message->hold == 1) echo ' kunapproved';
		else if ($message->hold) echo ' kdeleted';
		?>">
			<td class="kcol-first kcol-ktopicicon">
				<?php echo CKunenaTools::topicIcon($topic) ?>
			</td>

			<td class="kcol-mid ktopictittle">
			<?php
				$curMessageNo = $topic->posts - ($topic->unread ? $topic->unread - 1 : 0);

				/*if ($message->attachments) {
					echo CKunenaTools::showIcon ( 'ktopicattach', JText::_('COM_KUNENA_ATTACH') );
				}*/
			?>
				<div class="ktopic-title-cover">
					<?php echo CKunenaLink::GetThreadLink ( 'view', intval($topic->category_id), intval($topic->id), KunenaHtmlParser::parseText ($message->subject, 30), KunenaHtmlParser::stripBBCode ($message->message), 'follow', 'ktopic-title km' ) ?>
				</div>
				<div style="display:none"><?php echo KunenaHtmlParser::parseBBCode ($message->message);?></div>
			</td>

			<td class="kcol-mid ktopictittle">
				<?php echo CKunenaLink::GetThreadLink ( 'view', intval($topic->category_id), intval($topic->first_post_id), KunenaHtmlParser::parseText ($topic->subject, 20), KunenaHtmlParser::stripBBCode ($topic->first_post_message), 'follow', 'ktopic-title km' ) ?>
				<?php if ($topic->favorite) {
						echo CKunenaTools::showIcon ( 'kfavoritestar', JText::_('COM_KUNENA_FAVORITE') );
				} ?>
				<?php
				if ($topic->unread) {
					echo CKunenaLink::GetThreadPageLink ( 'view', intval($topic->category_id), intval($topic->id), $curMessageNo, intval($this->config->messages_per_page), '<sup class="knewchar">&nbsp;(' . intval($topic->unread) . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>', intval($topic->lastread) );
				}
				if ($topic->locked != 0) {
					echo CKunenaTools::showIcon ( 'ktopiclocked', JText::_('COM_KUNENA_GEN_LOCKED_TOPIC') );
				}
				?>
				<div class="ks">
					<!-- Category -->
					<span class="ktopic-category">
						<?php echo JText::_('COM_KUNENA_CATEGORY') . ' ' . CKunenaLink::GetCategoryLink ( 'showcat', intval($topic->category_id), $this->escape( $category->name ) ) ?>
					</span>
					<!-- /Category -->
				</div>
			</td>
			<td class="kcol-mid kcol-ktopiclastpost">
				<div class="klatest-post-info">
					<!--  Sticky   -->
					<?php
					if ($topic->ordering != 0) :
						echo CKunenaTools::showIcon ( 'ktopicsticky', JText::_('COM_KUNENA_GEN_ISSTICKY') );
					endif
					?>
					<!--  /Sticky   -->
					<!-- Avatar -->
					<?php
					if ($this->config->avataroncat > 0) :
						$profile = KunenaFactory::getUser((int)$message->userid);
						$useravatar = $profile->getAvatarLink('klist-avatar', 'list');
						if ($useravatar) :
					?>
					<span class="ktopic-latest-post-avatar">
					<?php echo CKunenaLink::GetProfileLink ( intval($message->userid), $useravatar ) ?>
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

					<?php if ($message->userid) : ?>
					<br />
					<span class="ktopic-by"><?php echo JText::_('COM_KUNENA_GEN_BY') . ' ' . CKunenaLink::GetProfileLink ( intval($message->userid), $this->escape($message->name) ); ?></span>
					<?php endif; ?>
					<!-- /By -->
				</div>
			</td>
		</tr>

		<?php } ?>
		<?php  if ( $this->embedded ) : ?>
		<!-- Bulk Actions -->
		<tr class="krow1">
			<td colspan="7" class="kcol-first krowmoderation">
				<?php echo CKunenaLink::GetShowLatestLink(JText::_('COM_KUNENA_MORE'), $this->func , 'follow'); ?>
			</td>
		</tr>
		<!-- /Bulk Actions -->
		<?php endif; ?>
</table>
</div>
</div>
</div>
<input type="hidden" name="option" value="com_kunena" />
<input type="hidden" name="func" value="bulkactions" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>