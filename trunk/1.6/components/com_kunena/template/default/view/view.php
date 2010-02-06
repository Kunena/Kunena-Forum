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

$kunena_app = & JFactory::getApplication ();

global $kunena_icons;

?>
<div><?php $this->displayPathway(); ?></div>
<?php
		if ($this->headerdesc) {
			?>
<table id="kforum-head"
	class="<?php
			echo isset ( $this->catinfo->class_sfx ) ? ' kforum-headerdesc' . $this->catinfo->class_sfx : '';
			?>">
	<tr>
		<td><?php
			echo $this->headerdesc;
			?>
		</td>
	</tr>
</table>
<?php
		}
		$this->displayPoll();
		CKunenaTools::showModulePosition( 'kunena_poll' );
		$this->displayThreadActions();
?>

<table
	class="<?php
		echo isset ( $this->catinfo->class_sfx ) ? ' kblocktable' . $this->catinfo->class_sfx : '';
		?>" id="kviews">
	<thead>
		<tr>
			<th class="left">
			<table>
				<tr>
					<td>

					<div class="ktitle_cover km"><span class="ktitle kl"><?php
		echo JText::_('COM_KUNENA_TOPIC');
		?>
		<?php
		echo $this->kunena_topic_title;
		?>
		</span>

		<!-- Begin: Total Favorite -->
			<?php
		echo '<div class="ktotalfavorite">';
		if ($kunena_icons ['favoritestar']) {
			if ($this->favorited)
				echo '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['favoritestar'] . '" alt="*" border="0" title="' . JText::_('COM_KUNENA_FAVORITE') . '" />';
			else if ($this->totalfavorited)
				echo '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['favoritestar_grey'] . '" alt="*" border="0" title="' . JText::_('COM_KUNENA_FAVORITE') . '" />';
		} else {
			echo JText::_('COM_KUNENA_TOTALFAVORITE');
			echo $this->totalfavorited;
		}
		echo '</div>';
		?>
	<!-- Finish: Total Favorite -->

					</div>
					</td>
				</tr>
			</table>
			<?php
		//(JJ) FINISH: RECENT POSTS
		?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php
			foreach ( $this->flat_messages as $message ) {
				$this->displayMessage($message);
			}
			?>
			<div id = "k_quick_reply" style = "display : none;">
			<form id="kqr_form" action="<?php echo CKunenaLink::GetPostURL(); ?>" method="post">
				<input id="kqr_subject" type = "text" name="subject" size="50" maxlength="50" /><br />
				<textarea name="message" rows="2" cols="60"></textarea><br />
				<input type="hidden"
				name="catid" value="<?php
				echo $this->catid;
				?>" />
				<input type="hidden"
				name="id" value="<?php
				echo $this->id;
				?>" />
				<input type="hidden"
				name="action" value="post" />
				<?php echo JHTML::_( 'form.token' ); ?>
				<input id="kbut_can" type="button" name="cancel" class="kbutton"
				value="<?php echo JText::_('COM_KUNENA_GEN_CANCEL');?>"
				title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL');?>" />
				<input id="kbut_sub" type="submit" name="submit" class="kbutton"
				value="<?php echo JText::_('COM_KUNENA_GEN_CONTINUE');?>"
				title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT');?>" /><?php echo JText::_('COM_KUNENA_QMESSAGE_NOTE'); ?>
			</form>
		</div>
			</td>
		</tr>
	</tbody>
</table>

<?php $this->displayThreadActions(); ?>
<div class = "kforum-pathway-bottom">
	<?php echo $this->kunena_pathway1; ?>
</div>
<!-- F: List Actions Bottom -->

<!-- B: Category List Bottom -->
<table class="klist_bottom">
	<tr>
		<td class="klist_moderators">
		<!-- Mod List -->
		<?php
		if (count ( $this->modslist ) > 0) {
		?>
		<div class="kbox-bottomarea-modlist">
		<?php
			echo '' . JText::_('COM_KUNENA_GEN_MODERATORS') . ": ";
			$modlinks = array();
			foreach ( $this->modslist as $mod ) {
				$modlinks[] = CKunenaLink::GetProfileLink ( $this->config, $mod->userid, ($this->config->username ? $mod->username : $mod->name) );
			}
			echo implode(', ', $modlinks);
		?>
		</div>
		<?php
		}
		?>
		<!-- /Mod List -->
		</td>
		<td class="klist_categories">
		<?php $this->displayForumJump();
		?>
		</td>
	</tr>
</table>
<!-- F: Category List Bottom -->

<?php

if ($this->config->highlightcode) {
	// TODO: Implement new code hhighlighter based on mootools or similar - prior chili removed
}

?>
