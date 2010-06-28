<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

global $kunena_icons;
?>
<div><?php $this->displayPathway(); ?></div>
<?php if ($this->headerdesc) : ?>
<div id="kforum-head" class="<?php echo isset ( $this->catinfo->class_sfx ) ? ' kforum-headerdesc' . $this->escape($this->catinfo->class_sfx) : '' ?>">
	<?php echo $this->headerdesc ?>
</div>
<?php endif ?>
<?php
$this->displayPoll();
CKunenaTools::showModulePosition( 'kunena_poll' );
$this->displayThreadActions(0);
?>

<table class="<?php echo isset ( $this->catinfo->class_sfx ) ? ' kblocktable' . $this->escape($this->catinfo->class_sfx) : '' ?>" id="kviews">
	<thead>
		<tr>
			<th class="kleft">
				<div class="ktitle-cover km">
					<span class="ktitle kl"><?php echo JText::_('COM_KUNENA_TOPIC') ?>
						<?php echo $this->escape($this->kunena_topic_title) ?>
					</span>
					<?php if ($this->favorited) : ?>
					<div class="kfavorite"></div>
					<?php endif ?>
				</div>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
			<?php
			foreach ( $this->messages as $message )
				$this->displayMessage($message);
			?>
			</td>
		</tr>
	</tbody>
</table>

<?php $this->displayThreadActions(1); ?>
<div class = "kforum-pathway-bottom">
	<?php echo $this->kunena_pathway1; ?>
</div>
<!-- F: List Actions Bottom -->

<!-- B: Category List Bottom -->
<table class="klist-bottom">
	<tr>
		<td class="klist-moderators">
		<!-- Mod List -->
		<?php
		if (count ( $this->modslist ) > 0) {
		?>
		<div class="kbox-bottomarea-modlist">
		<?php
			echo '' . JText::_('COM_KUNENA_GEN_MODERATORS') . ": ";
			$modlinks = array();
			foreach ( $this->modslist as $mod ) {
				$modlinks[] = CKunenaLink::GetProfileLink ( $mod->userid, ($this->config->username ? $this->escape($mod->username) : $this->escape($mod->name)) );
			}
			echo implode(', ', $modlinks);
		?>
		</div>
		<?php
		}
		?>
		<!-- /Mod List -->
		</td>
		<td class="klist-categories">
		<?php $this->displayForumJump();
		?>
		</td>
	</tr>
</table>
<!-- F: Category List Bottom -->