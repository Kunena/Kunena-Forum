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
defined ( '_JEXEC' ) or die ();
?>
<!-- Pathway -->
<?php $this->displayPathway () ?>
<!-- / Pathway -->

<?php $this->displaySubCategories () ?>
<?php if ($this->objCatInfo->headerdesc) : ?>
<div class="kblock">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="frontstats_tbody"></a></span>
		<h2><span><?php echo JText::_('COM_KUNENA_FORUM_HEADER'); ?></span></h2>
	</div>
	<div class="kcontainer" id="frontstats_tbody">
		<div class="kbody">
			<div class="kfheadercontent">
				<?php echo KunenaParser::parseBBCode ( $this->headerdesc ); ?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- B: List Actions -->
<table class="klist-actions">
	<tr>
		<td class="klist-actions-goto">
			<a name="forumtop"> </a>
			<?php echo CKunenaLink::GetSamePageAnkerLink ( 'forumbottom', CKunenaTools::showIcon ( 'kforumbottom', JText::_('COM_KUNENA_GEN_GOTOBOTTOM') ), 'nofollow', 'kbuttongoto') ?>
		</td>
		<td class="klist-actions-forum">
			<?php
			if (isset ( $this->forum_new ) || isset ( $this->forum_markread ) || isset ( $this->thread_subscribecat )) {
				echo '<div class="kmessage-buttons-row">';
				if (isset ( $this->forum_new ))
					echo $this->forum_new;
				if (isset ( $this->forum_markread ))
					echo ' ' . $this->forum_markread;
				if (isset ( $this->thread_subscribecat ))
					echo ' ' . $this->thread_subscribecat;
				echo '</div>';
			}
			?>
		</td>
		<td class="klist-pages-all">
			<?php
			// pagination 1
			if (count ( $this->messages ) > 0) {
				$maxpages = 9 - 2; // odd number here (# - 2)
				echo $pagination = $this->getPagination ( $this->catid, $this->page, $this->totalpages, $maxpages );
			}
			?>
		</td>
	</tr>
</table>
<!-- F: List Actions -->

<?php $this->displayFlat (); ?>

<!-- B: List Actions Bottom -->
<table class="klist-actions-bottom" >
	<tr>
		<td class="klist-actions-goto">
			<a name="forumbottom"> </a>
			<?php echo CKunenaLink::GetSamePageAnkerLink ( 'forumtop', CKunenaTools::showIcon ( 'kforumtop', JText::_('COM_KUNENA_GEN_GOTOBOTTOM') ), 'nofollow', 'kbuttongoto') ?>
		</td>
		<td class="klist-actions-forum">
			<?php
			if (isset ( $this->forum_new ) || isset ( $this->forum_markread ) || isset ( $this->thread_subscribecat )) {
				echo '<div class="kmessage-buttons-row">';
				if (isset ( $this->forum_new ))
					echo $this->forum_new;
				if (isset ( $this->forum_markread ))
					echo ' ' . $this->forum_markread;
				if (isset ( $this->thread_subscribecat ))
					echo ' ' . $this->thread_subscribecat;
			echo '</div>';
			}
			?>
		</td>
		<td class="klist-pages-all">
			<?php
			// pagination 2
			if (count ( $this->messages ) > 0) {
				echo $pagination;
			}
			?>
		</td>
	</tr>
</table>
<?php
echo '<div class = "kforum-pathway-bottom">';
echo $this->kunena_pathway1;
echo '</div>';
?>
<!-- B: List Actions Bottom -->
<div class="kcontainer klist-bottom">
	<div class="kbody">
		<div class="kmoderatorslist-jump fltrt">
				<?php $this->displayForumJump (); ?>
		</div>
		<?php if (!empty ( $this->modslist ) ) : ?>
		<div class="klist-moderators">
			<?php
			echo '' . JText::_('COM_KUNENA_GEN_MODERATORS') . ": ";
			foreach ( $this->modslist as $mod ) {
				echo CKunenaLink::GetProfileLink ( intval($mod->userid) ) . '&nbsp; ';
			}
			?>
		</div>
		<?php endif; ?>
	</div>
</div>
<!-- F: List Actions Bottom -->