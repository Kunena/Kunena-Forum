<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$tabclass = array ("row1", "row2" );
$mmm=0;
foreach ( $this->sections as $section ) :
	$htmlClassBlockTable = !empty ( $section->class_sfx ) ? ' kblocktable' . $this->escape($section->class_sfx) : '';
	$htmlClassTitleCover = !empty ( $section->class_sfx ) ? ' ktitle-cover' . $this->escape($section->class_sfx) : '';
?>
<div class="kblock kcategories-<?php echo intval($section->id) ?>">
	<div class="kheader">
		<?php if (count($this->sections) > 0) : ?>
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="catid_<?php echo intval($section->id) ?>"></a></span>
		<?php endif; ?>
		<h1><span><?php echo $this->GetCategoryLink ( $section, $this->escape($section->name) ); ?></span></h1>
		<?php if (!empty($section->description)) : ?>
		<div class="ktitle-desc km hidden-phone">
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
			<td class="kcol-first kcol-category-icon hidden-phone">
				<?php if (!empty($category->icon)) : ?>
					<i class="icon-big <?php echo $category->icon;?> <?php if ($category->getNewCount()) :?>  icon-knewchar <?php endif; ?>"></i>
				<?php else : ?>
					<?php echo $this->getCategoryLink($category, $this->getCategoryIcon($category), ''); ?>
				<?php endif; ?>
			</td>

			<td class="kcol-mid kcol-kcattitle">
			<div class="kthead-title kl">
			<?php
				// Show new posts, locked, review
				echo $this->getCategoryLink($category); ?>
				<?php
				if ($category->getNewCount()) {
					echo '<sup class="knewchar">(' . $category->getNewCount() . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ")</sup>";
				}
				if ($category->locked) {
					echo $this->getIcon ( 'kforumlocked', JText::_('COM_KUNENA_LOCKED_CATEGORY') );
				}
				if ($category->review) {
					echo $this->getIcon ( 'kforummoderated', JText::_('COM_KUNENA_GEN_MODERATED') );
				}
				?>
			</div>

		<?php if (!empty($category->description)) : ?>
			<div class="kthead-desc km hidden-phone"><?php echo KunenaHtmlParser::parseBBCode ($category->description) ?> </div>
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
				echo $this->getCategoryLink($childforum);
				echo '<span class="kchildcount ks">(' . $childforum->getTopics() . "/" . $childforum->getReplies() . ')</span>';
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
				foreach ( $category->moderators as $moderator ) {
					$modslist[] = KunenaFactory::getUser($moderator)->getLink();
				}
				echo JText::_('COM_KUNENA_MODERATORS') . ': ' . implode(', ', $modslist);
		?>
			</div>
		<?php endif; ?>
		<?php if (! empty ( $this->pending [$category->id] )) : ?>
			<div class="ks kalert">
				<?php echo JHtml::_('kunenaforum.link', 'index.php?option=com_kunena&view=topics&layout=posts&mode=unapproved&userid=0&catid='.intval($category->id), intval($this->pending [$category->id]) . ' ' . JText::_('COM_KUNENA_SHOWCAT_PENDING'), '', '', 'nofollow'); ?>
			</div>
		<?php endif; ?>
			</td>

			<td class="kcol-mid kcol-kcattopics hidden-phone">
				<span class="kcat-topics-number"><?php echo $this->formatLargeNumber ( $category->getTopics() ) ?></span>
				<span class="kcat-topics"><?php echo JText::_('COM_KUNENA_TOPICS');?></span>
			</td>

			<td class="kcol-mid kcol-kcatreplies hidden-phone">
				<span class="kcat-replies-number"><?php echo $this->formatLargeNumber ( $category->getReplies() ) ?></span>
				<span class="kcat-replies"><?php echo JText::_('COM_KUNENA_GEN_REPLIES');?> </span>
			</td>

			<?php $last = $category->getLastTopic();
			if ($last->exists()) { ?>
			<td class="kcol-mid kcol-kcatlastpost">
			<?php if ($this->config->avataroncat > 0) : ?>
			<?php
				$profile = KunenaFactory::getUser((int)$last->last_post_userid);
				$useravatar = $profile->getAvatarImage('klist-avatar', 'list');
				if ($useravatar) : ?>
					<span class="klatest-avatar hidden-phone"> <?php echo $last->getLastPostAuthor()->getLink( $useravatar, null, 'nofollow', '', null, $category->id ); ?></span>
				<?php endif; ?>
			<?php endif; ?>
			<div class="klatest-subject ks">
				<?php echo JText::_('COM_KUNENA_GEN_LAST_POST') . ': '. $this->getLastPostLink($category) ?>
			</div>

			<div class="klatest-subject-by ks hidden-phone">
			<?php
					echo JText::_('COM_KUNENA_BY') . ' ';
					echo $last->getLastPostAuthor()->getLink(null, null, 'nofollow', '', null, $category->id);
					echo '<br /><span class="nowrap" title="' . KunenaDate::getInstance($last->last_post_time)->toKunena('config_post_dateformat_hover') . '">' . KunenaDate::getInstance($last->last_post_time)->toKunena('config_post_dateformat') . '</span>';
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
<!-- Begin: Category Module Position -->
	<?php $this->displayModulePosition('kunena_section_' . ++$mmm) ?>
<!-- Finish: Category Module Position -->
<?php endforeach; ?>
