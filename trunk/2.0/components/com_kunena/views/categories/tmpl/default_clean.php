<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once(KUNENA_PATH_LIB .DS. 'kunena.link.class.php');
$tabclass = array ("row1", "row2" );
$mmm=0;
foreach ( $this->sections as $section ) :
	$htmlClassBlockTable = !empty ( $section->class_sfx ) ? ' kblocktable' . $this->escape($section->class_sfx) : '';
	$htmlClassTitleCover = !empty ( $section->class_sfx ) ? ' ktitle-cover' . $this->escape($section->class_sfx) : '';
?>
<div class="kblock kcategories-<?php echo intval($section->id) ?>">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="catid_<?php echo intval($section->id) ?>"></a></span>
		<h2><span><?php echo CKunenaLink::GetCategoryLink ( 'listcat', intval($section->id), $this->escape($section->name), 'follow' ); ?></span></h2>
		<?php if (!empty($section->description)) : ?>
		<div class="ktitle-desc km">
			<?php echo KunenaHtmlParser::parseBBCode ( $section->description ); ?>
		</div>
		<?php endif; ?>
	</div>
	<div class="kcontainer" id="catid_<?php echo intval($section->id) ?>">
		<div class="kbody">
<table class="kblocktable<?php echo $htmlClassBlockTable ?>" id="kcat<?php echo intval($section->id) ?>">
		<?php if (empty ( $this->categories [$section->id] )) { echo JText::_('COM_KUNENA_GEN_NOFORUMS');
		} else {
		$k = 0;
		foreach ( $this->categories [$section->id] as $category ) {
	?>
		<tr class="k<?php echo $tabclass [$k ^= 1], isset ( $category->class_sfx ) ? ' k' . $this->escape($tabclass [$k]) . $this->escape($category->class_sfx) : '' ?>"
			id="kcat<?php echo intval($category->id) ?>">
			<td class="kcol-first kcol-category-icon">
				<?php echo CKunenaLink::GetCategoryLink ( 'showcat', intval($category->id), $this->getCategoryIcon($category) ) ?>
			</td>

			<td class="kcol-mid kcol-kcattitle">
			<div class="kthead-title kl">
			<?php
				// Show new posts, locked, review
				echo CKunenaLink::GetCategoryLink ( 'showcat', intval($category->id), $this->escape($category->name ) );
				if ($category->getNewCount()) {
					echo '<sup class="knewchar">(' . $category->getNewCount() . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ")</sup>";
				}
				if ($category->locked) {
					echo CKunenaTools::showIcon ( 'kforumlocked', JText::_('COM_KUNENA_GEN_LOCKED_FORUM') );
				}
				if ($category->review) {
					echo CKunenaTools::showIcon ( 'kforummoderated', JText::_('COM_KUNENA_GEN_MODERATED') );
				}
				?>
			</div>

		<?php if (!empty($category->description)) : ?>
			<div class="kthead-desc km"><?php echo KunenaHtmlParser::parseBBCode ($category->description) ?> </div>
		<?php endif; ?>
		<?php
			// Display subcategories
			if (! empty ( $this->categories [$category->id] )) :
		?>
			<div class="kthead-child">
			<div class="kcc-table">
			<?php foreach ( $this->categories [$category->id] as $childforum ) : ?>
			<div class="kcc-subcat km">
			<?php
				echo $this->getCategoryIcon($childforum, true);
				echo CKunenaLink::GetCategoryLink ( 'showcat', intval($childforum->id), $this->escape($childforum->name), '','', KunenaHtmlParser::stripBBCode ( $childforum->description ) );
				echo '<span class="kchildcount ks">(' . $childforum->getTopics() . "/" . $childforum->getPosts() . ')</span>';
			?>
			</div>
			<?php endforeach; ?>
			</div>
			</div>
		<?php endif; ?>
		<?php if (! empty ( $category->moderators )) : ?>
			<div class="kthead-moderators ks">
		<?php
				// get the Moderator list for display
				$modslist = array();
				foreach ( $category->moderators as $modid ) {
					$modslist[] = CKunenaLink::GetProfileLink ( intval($modid) );
				}
				echo JText::_('COM_KUNENA_GEN_MODERATORS') . ': ' . implode(', ', $modslist);
		?>
			</div>
		<?php endif; ?>
		<?php if (! empty ( $this->pending [$category->id] )) : ?>
			<div class="ks kalert">
				<?php echo CKunenaLink::GetCategoryReviewListLink ( intval($category->id), intval($this->pending [$category->id]) . ' ' . JText::_('COM_KUNENA_SHOWCAT_PENDING'), 'nofollow' ); ?>
			</div>
		<?php endif; ?>
			</td>

			<td class="kcol-mid kcol-kcattopics">
				<!-- Number of Topics -->
				<span class="kcat-topics-number"><?php echo CKunenaTools::formatLargeNumber ( $category->getTopics() ) ?></span>
				<span class="kcat-topics"><?php echo JText::_('COM_KUNENA_GEN_TOPICS');?></span>
				<!-- /Number of Topics -->
			</td>

			<td class="kcol-mid kcol-kcatreplies">
			<!-- Number of Replies -->
			<span class="kcat-replies-number"><?php echo CKunenaTools::formatLargeNumber ( $category->getPosts() ) ?></span>
			<span class="kcat-replies"><?php echo JText::_('COM_KUNENA_GEN_REPLIES');?> </span>
			<!-- /Number of Replies -->
			</td>

			<?php $last = $category->getLastPosted();
			if ($last->last_topic_id) { ?>
			<td class="kcol-mid kcol-kcatlastpost">
			<?php if ($this->config->avataroncat > 0) : ?>
			<!-- Avatar -->
			<?php
				$profile = KunenaFactory::getUser((int)$last->last_post_userid);
				$useravatar = $profile->getAvatarLink('klist-avatar', 'list');
				if ($useravatar) : ?>
					<span class="klatest-avatar"> <?php echo CKunenaLink::GetProfileLink ( intval($last->last_post_userid), $useravatar ); ?></span>
				<?php endif; ?>
			<!-- /Avatar -->
			<?php endif; ?>
			<div class="klatest-subject ks">
				<?php echo JText::_('COM_KUNENA_GEN_LAST_POST') . ': '. CKunenaLink::GetThreadPageLink ( 'view', intval($last->id), intval($last->last_topic_id), intval($last->_last_post_location), intval($this->config->messages_per_page), KunenaHtmlParser::parseText($last->last_topic_subject, 30), intval($last->last_post_id) );?>
			</div>

			<div class="klatest-subject-by ks">
			<?php
					echo JText::_('COM_KUNENA_BY') . ' ';
					echo CKunenaLink::GetProfileLink ( intval($last->last_post_userid), $this->escape($last->last_post_guest_name) );
					echo '<br /><span class="nowrap" title="' . CKunenaTimeformat::showDate ( $last->last_post_time, 'config_post_dateformat_hover' ) . '">' . CKunenaTimeformat::showDate ( $last->last_post_time, 'config_post_dateformat' ) . '</span>';
					?>
			</div>
			</td>

			<?php } else { ?>
			<td class="kcol-mid kcol-knoposts"><?php echo JText::_('COM_KUNENA_NO_POSTS'); ?></td>
			<?php } ?>
		</tr>
		<?php } } ?>
</table>
</div>
</div>
</div>
<!-- F: List Cat -->
<!-- Begin: Category Module Position -->
	<?php CKunenaTools::showModulePosition('kunena_section_' . $mmm) ?>
<!-- Finish: Category Module Position -->
<?php endforeach; ?>
