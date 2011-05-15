<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

$document = JFactory::getDocument ();
$document->addScriptDeclaration('// <![CDATA[
var kunena_anonymous_name = "'.JText::_('COM_KUNENA_USERNAME_ANONYMOUS').'";
// ]]>');
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

<div class="kblock">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_TOPIC') ?> <?php echo $this->escape($this->kunena_topic_title) ?></span></h2>
		<?php if ($this->favorited) : ?><div class="kfavorite"></div><?php endif ?>
	</div>
	<div class="kcontainer">
		<div class="kbody">
			<?php foreach ( $this->messages as $message ) : ?>
				<div class="kmsgindent" style="padding-left: <?php echo (count($message->indent)-3)*2 ?>%"><?php $this->displayMessage($message); ?></div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<?php $this->displayThreadActions(1); ?>

<div class = "kforum-pathway-bottom">
	<?php echo $this->kunena_pathway1; ?>
</div>
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
				$modlinks = array();
				foreach ( $this->modslist as $mod ) {
					$modlinks[] = CKunenaLink::GetProfileLink ( intval($mod->userid) );
				}
				echo implode(', ', $modlinks);
				?>
		</div>
		<?php endif; ?>
	</div>
</div>
<!-- F: List Actions Bottom -->