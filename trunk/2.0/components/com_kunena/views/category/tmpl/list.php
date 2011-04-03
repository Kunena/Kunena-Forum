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
defined ( '_JEXEC' ) or die ();
?>
<div id="Kunena">
<?php
$this->displayMenu ();
$this->displayLoginBox ();
$this->displayAnnouncement ();
$this->displayBreadcrumb ();
?>

<div class="klist-markallcatsread kcontainer">
	<div class="ksectionbody">
		<div class="fltlft">
			<div class="fltlft">
			<?php if (KunenaFactory::getUser()->exists()) : ?>
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" name="markAllForumsRead" method="post">
				<input type="hidden" name="view" value="category" />
				<input type="hidden" name="task" value="markread" />
				<input type="submit" class="kbutton button ks" value="<?php echo JText::_('COM_KUNENA_GEN_MARK_ALL_FORUMS_READ'); ?>" />
				<?php echo JHTML::_( 'form.token' ); ?>
			</form>
			</div>
			<div class="kmessage-buttons-row">
			<?php endif; ?>
			<?php if (!empty($this->category_manage)) echo $this->category_manage; ?>
			</div>
		</div>
		<div class="fltrt">
			<?php $this->displayForumjump(); ?>
		</div>
	</div>
</div>

<?php
if (count ( $this->categories )) {
	echo $this->loadTemplate('embed');
} else {
	$this->displayInfoMessage ();
}
$this->displayWhoIsOnline();
$this->displayStatistics();
$this->displayFooter ();
?>
</div>